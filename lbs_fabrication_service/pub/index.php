<?php

require_once __DIR__ . '/../src/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Requests;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\fab\app\middlewares\CommandValidator as CommandValidator;
use \DavidePastore\Slim\Validation\Validation as Validator;

$settings = require_once __DIR__ . '/../src/app/conf/settings.php';
$errors = require_once __DIR__ . '/../src/app/conf/errors.php';
$dependencies= require_once __DIR__ . '/../src/app/conf/deps.php';

$container= new \Slim\Container(array_merge($settings,$dependencies,$errors));
$app = new \Slim\App($container);

$db = new Illuminate\Database\Capsule\Manager();

$db->addConnection(($app->getContainer())->settings['db']); 
$db->setAsGlobal();
$db->bootEloquent();   


$app->get('/commandes[/]','lbs\fab\app\controller\Controller:allCommande' )
    ->setName('commandes');

//->add(\lbs\command\app\middlewares\Token::class .':check');

$app->run();


