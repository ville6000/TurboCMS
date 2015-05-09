<?php
session_start();

require 'vendor/autoload.php';
require 'config.php';
require 'core/Parser.php';
require 'core/Auth.php';

$app = new \Slim\Slim(array('templates.path' => 'core/views'));
$Auth = new \TurboCMS\Auth($settings);

// Provide variables to all views
$app->hook('slim.before', function () use ($app, $settings) {
    // Get and add site name from config
    $app->view()->appendData(array('siteName' => $settings['site_name']));

    // Get and add base URL
    $posIndex = strpos($_SERVER['PHP_SELF'], '/index.php');
    $baseUrl = substr($_SERVER['PHP_SELF'], 0, $posIndex);
    $app->view()->appendData(array('baseUrl' => $baseUrl));

    // Get and add login/logout URLs
    $app->view()->appendData(array('loginFormUrl' => $app->urlFor('login_handle')));
    $app->view()->appendData(array('logoutUrl' => $app->urlFor('logout')));
});

// Create regexp patterns for route parameters
\Slim\Route::setDefaultConditions(array(
    'token' => '[a-zA-Z0-9]+'
));

/**
 * Frontpage
 */
$app->get('/', function () use($app) {
    if (file_exists($app->config('templates.path') . '/index.html')) {
        $app->render('index.html');
    } else {
        $app->render('default.html');
    }
})->name('frontpage');

/**
 * Admin form
 */
$app->get('/admin', function () use ($app, $Auth, $settings) {
    if (!$Auth->isAuthorized()) {
        $app->redirect('login');
    }

	if (isset($_SESSION['turbo_cms_login_email'])) {
		$loginMethod = "Logged in with email " . $_SESSION['turbo_cms_login_email'];
	} else {
		$loginMethod = "Logged in with passphrase";
	}

    $parser = new \TurboCMS\Parser($settings);
    $layoutKeys = $parser->getKeys();

	$app->render(
	    'admin_form.php',
	    array(
		    'keys' => $layoutKeys,
		    'loginMethod' => $loginMethod
	    )
    );
})->name('admin');

/**
 * Handle admin form submission
 */
$app->post('/admin', function () use ($app, $Auth, $settings) {
    if (!$Auth->isAuthorized()) {
        $app->redirect('login');
    }

    $postVars = $app->request->post();
    $parser = new \TurboCMS\Parser($settings);
    $parser->createFile($postVars);

    $app->redirect($app->urlFor('frontpage'));
});

/**
 * Login form
 */
$app->get('/login', function () use ($app, $Auth) {
    // Redirect to admin if already logged in
    if ($Auth->isAuthorized()) {
        $app->redirect($app->urlFor('admin'));
    }

    $app->render('login.php');
})->name('login');

/**
 * Login with token form email message
 */
$app->get('/login/:token', function ($token) use ($app, $Auth) {
    $app->log->debug('isAuthorized: ' . $Auth->isAuthorized());
    // Redirect to admin if already logged in
    if ($Auth->isAuthorized()) {
        $app->redirect($app->urlFor('admin'));
    }

    if ($Auth->handleEmailLogin($token)) {
        // Login with token successful, redirect to admin
        $app->redirect($app->urlFor('admin'));
    } else {
        // Login with token failed
        $app->view()->appendData(array('message' => 'Login failed: login token invalid or expired.'));
        $app->render('login.php');

        return;
    }
})->name('login_token');

/**
 * Handle login form submission
 */
$app->post('/login', function () use ($app, $Auth, $settings) {
    // Redirect to admin if already logged in
    if ($Auth->isAuthorized()) {
        $app->redirect($app->urlFor('admin'));
    }

    // Get form data
    $passphrase = $app->request()->post('passphrase');
    $email = $app->request()->post('email');

    if ($passphrase) {
        // Handle login with passphrase
        if ($Auth->passphraseLogin($app->request()->post('passphrase'))) {
            $app->redirect($app->urlFor('admin'));
        } else {
            $app->view()->appendData(array('message' => 'Login failed: passphrase is wrong.'));
            $app->render('login.php');
            return;
        }
    } elseif ($email) { 
        // Handle login with email
        $token = $Auth->initEmailLogin($app->request()->post('email'));

        // Check email login status
        if ($token) {
            // Send token
            mail(
                $email,
                'Email login for ' . $settings['site_name'],
                sprintf(
                    "Use following link to login into \"%s\":\r\n%s%s",
                    $settings['site_name'],
                    $app->request->getUrl(),
                    $app->urlFor('login_token', array('token' => $token))
                )
            );

            // Display with message
            $app->view()->appendData(array('message' => 'Login link sent to email.'));
            $app->render('login.php');
            return;
        } else {
            $app->view()->appendData(array('message' => 'Login failed: email not found.'));
            $app->render('login.php');
            return;
        }
    }

    // Display login form by default
    $app->render('login.php');
})->name('login_handle');

/**
 * Logout session and redirect to frontpage
 */
$app->get('/logout', function () use ($app, $Auth) {
    $Auth->destroySession();

    $app->redirect($app->urlFor('frontpage'));
})->name('logout');

$app->run();
