<?php 

namespace  web\catalogue\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Requests;
use \Psr\Http\Message\ResponseInterface as Response;
use  web\catalogue\app\models\Commande as Commande;
use  web\catalogue\app\models\Item as Item;
use  web\catalogue\app\utils\Writer as Writer;
use Ramsey\Uuid\Uuid as Uuid;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

Class Controller {
    private $c;

    public function __construct($c){
        $this->c = $c;
    }

    public function categories($rq,$rs,$args){
        $client = new Client([
            'base_url' => 'http://api.catalogue.local:19055/items/categories',
            'timeout' => 2.0,
        ]);
        $response = $client->request('GET', 'categorie');
        $body = $response->getBody() ;
        $sandwiches = json_decode($response->getBody());
        $page = 'home';
        if(isset($_GET['p'])){
            $page = $_GET['p'];
        }

        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
        $twig = new \Twig\Environment($loader,[
            'cache' => false,
        ]);
        
        echo $twig->render('sand.twig', ['sandwiches' => $sandwiches]);
        
    }

    public function sandwichs($rq,$rs,$args){
        /*$client = new Client([
            'base_url' => 'http://api.catalogue.local:19055/items/sandwiches',
            'timeout' => 2.0,
        ]);
        $response = $client->request('GET', 'sandwiches');
        $body = $response->getBody() ;
        $categories = json_decode($response->getBody());*/
        $page = 'home';
        if(isset($_GET['p'])){
            $page = $_GET['p'];
        }

        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
        $twig = new \Twig\Environment($loader,[
            'cache' => false,
        ]);
        
        echo $twig->render('categ.twig', ['categories' => $categories]);
        

    }
}