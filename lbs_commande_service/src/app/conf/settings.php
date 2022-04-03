<?php 

return [
    'settings' => [
        'displayErrorDetails'=>true,
        'db' =>  parse_ini_file(__DIR__ . '/dbconf.ini'),
        'debug.log' => __DIR__ . '/../log/debug.log',
        'error.log' => __DIR__ . '/../log/error.log',
        'log.level' =>  \Monolog\Logger::WARNING,
        'log.name' => 'slim.log',
    ]
];