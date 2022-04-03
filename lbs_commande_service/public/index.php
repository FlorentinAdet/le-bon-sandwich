<?php

require_once __DIR__ . '/../src/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Requests;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\command\app\middlewares\CommandValidator as CommandValidator;
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


//Création des différentse routes de l'api commande 
$app->get('/commandes[/]','lbs\command\app\controller\CommandeController:allCommande' )
    ->setName('commandes');

$app->get('/commande/{id}[/]','lbs\command\app\controller\CommandeController:Commande' )
    ->add(lbs\command\app\middlewares\Token::class .':check') //midlewares permettant de vérifier si le token correspond
    ->setName('commande');

$app->get('/commande/{id}/items[/]','lbs\command\app\controller\CommandeController:CommandeItems')
    ->add(lbs\command\app\middlewares\Token::class .':check') // Middleware permettant de vérifier si le token correspond
    ->setName('commandeItems');

$app->put('/commande/{id}[/]',\lbs\command\app\controller\CommandeController::class . ':updateCommande');

$app->post('/commande[/]',\lbs\command\app\controller\CommandeController::class . ':addCommande')
    ->add(new Validator(CommandValidator::create_validator())); // Middleware qui vérifi si les données du Body correspondant à celles attendues

$app->run();


