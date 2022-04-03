<?php

require_once __DIR__ . '/../src/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Requests;
use \Psr\Http\Message\ResponseInterface as Response;

$settings = require_once __DIR__ . '/../src/api/conf/settings.php';
$errors = require_once __DIR__ . '/../src/api/conf/errors.php';
$dependencies= require_once __DIR__ . '/../src/api/conf/deps.php';

$container= new \Slim\Container(array_merge($settings,$dependencies,$errors));
$app = new \Slim\App($container);

$db = new Illuminate\Database\Capsule\Manager();

$db->addConnection(($app->getContainer())->settings['db']); 
$db->setAsGlobal();
$db->bootEloquent();  
 
$app->post('/auth','lbs\auth\api\controller\LBSAuthController:authenticate')
    ->setName('auth');

$app->get('/check','lbs\auth\api\controller\LBSAuthController:check')
    ->setName('check');

    $app->get('/test','lbs\auth\api\controller\LBSAuthController:test')
    ->setName('test');


$app->run();
