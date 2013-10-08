<?php
require 'vendor/autoload.php';
require 'config.php';
require 'core/Parser.php';

$app = new \Slim\Slim(
    array(
         'debug' => true,
         'templates.path' => 'core/views',
    )
);

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
$app->get('/admin', function () use ($app, $settings) {
    $parser = new \TurboCMS\Parser($settings);
    $layoutKeys = $parser->getKeys();
    $app->render('admin_form.php', array('keys' => $layoutKeys));
});

/**
 * Handle admin form submission
 */
$app->post('/admin', function () use ($app, $settings) {
    $postVars = $app->request->post();
    $parser = new \TurboCMS\Parser($settings);
    $parser->createFile($postVars);

    $app->response->redirect($app->urlFor('frontpage'));
});

$app->run();