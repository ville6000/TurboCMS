<?php
session_start();

require 'vendor/autoload.php';
require 'config.php';
require 'core/Parser.php';
require 'core/Auth.php';

$app = new \Slim\Slim(array('templates.path' => 'core/views'));
$Auth = new \TurboCMS\Auth($settings);

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
        $app->response->redirect('login');
    }

    $parser = new \TurboCMS\Parser($settings);
    $layoutKeys = $parser->getKeys();
    $app->render('admin_form.php', array('keys' => $layoutKeys));
});

/**
 * Handle admin form submission
 */
$app->post('/admin', function () use ($app, $Auth, $settings) {
    if (!$Auth->isAuthorized()) {
        $app->response->redirect('login');
    }

    $postVars = $app->request->post();
    $parser = new \TurboCMS\Parser($settings);
    $parser->createFile($postVars);

    $app->response->redirect($app->urlFor('frontpage'));
});

/**
 * Login form
 */
$app->get('/login', function () use ($app) {
    $app->render('login.php');
});

/**
 * Handle login form submission
 */
$app->post('/login', function () use ($app, $Auth, $settings) {
    if ($Auth->passphraseLogin($app->request()->post('passphrase'))) {
        $app->response->redirect('admin');
    } else {
        $app->render('login.php');
    }
});

$app->run();