<?php

return [
    'dbhost' => function(\Slim\Container $c){
        $config = parse_ini_file($c->settings['db']);
        return $config['host'];
    },
    'logger.debug' => function(\Slim\Container $c){
        $log = new \Monolog\Logger($c->settings['log.name']);
        $log->pushHandler(new \Monolog\Handler\StreamHandler($c->settings['debug.log'], $c->settings['log.level']));
        return $log;
    },
    'logger.error' => function(\Slim\Container $c){
        $log = new \Monolog\Logger($c->settings['log.name']);
        $log->pushHandler(new \Monolog\Handler\StreamHandler($c->settings['error.log'], $c->settings['log.level']));
        return $log;
    },
];