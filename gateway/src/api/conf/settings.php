<?php 

return [
    'settings' => [
        'displayErrorDetails'=>true,
        'debug.log' => __DIR__ . '/../log/debug.log',
        'error.log' => __DIR__ . '/../log/error.log',
        'log.level' =>  \Monolog\Logger::WARNING,
        'log.name' => 'slim.log',
    ]
];