<?php
// DIC configuration

$container = $app->getContainer();

// View
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig($c['settings']['view']['template_path'], $c['settings']['view']['twig']);
    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $c['request']->getUri()));
    $view->addExtension(new Twig_Extension_Debug());
   // $view->addExtension(new Bookshelf\TwigExtension($c['flash']));
    return $view;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};
// mysql
$container['db'] = function ($c) {
    $settings = $c->get('settings')['db'];
    $mysqli = new mysqli(
        $settings['host'],
        $settings['username'],
        $settings['password'],
        $settings['database']);
    if ($mysqli->connect_error) {
        die("MySql error: " . $mysqli->connect_error);
    } else {
        $mysqli->set_charset("utf8");
    }
    return $mysqli;
};
// controllers for business in this site
$container['Price\PrefectureController'] = function ($c) {
    return new Price\PrefectureController($c['view'], $c['router'], $c['db']);
};
