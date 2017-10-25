<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        //view settings
        'view' => [
            'template_path' => __DIR__ . '/../templates/',
            'twig' => [
                //'cache' => __DIR__ . '/../cache/twig',
                'debug' => true,
            ],
        ],

        'db' => [
            // Illuminate/database configuration
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'landPrice',
            'username'  => 'shutoukin',
            'password'  => 'aobo1',
            'charset'   => 'utf8',
            // utf8_unicode_ciのデメリット:日本語の検索で、「ひらがな」「カタカナ」が混在した形でヒットしてしまう。＝余計な結果は要らん！
            'collation' => 'utf8_general_ci',
            'prefix'    => '',
        ],
        // Monolog settings
        'logger' => [
            'name' => 'Land price',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::ERROR,
        ],
    ],
];
