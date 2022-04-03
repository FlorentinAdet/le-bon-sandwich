<?php

require_once __DIR__ . '/../src/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Requests;
use \Psr\Http\Message\ResponseInterface as Response;

$settings = require_once __DIR__ . '/../src/api/conf/settings.php';
$errors = require_once __DIR__ . '/../src/api/conf/errors.php';
$dependencies= require_once __DIR__ . '/../src/api/conf/deps.php';

$container= new \Slim\Container(array_merge($settings,$dependencies,$errors));
$app = new \Slim\App($container);

$app->get('/auth','gateway\api\controller\Controller:authentification' )
    ->setName('auth');

$app->get('/commmandes','gateway\api\controller\Controller:commandes' )
    ->setName('commandes');

$app->run();

?>