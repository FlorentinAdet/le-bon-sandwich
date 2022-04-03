<?php 

namespace  gateway\api\controller;

use \Psr\Http\Message\ServerRequestInterface as Requests;
use \Psr\Http\Message\ResponseInterface as Response;
use  gateway\api\models\Commande as Commande;
use  gateway\api\models\Item as Item;
use  gateway\api\utils\Writer as Writer;
use Ramsey\Uuid\Uuid as Uuid;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

Class Controller {
    private $c;

    public function __construct($c){
        $this->c = $c;
    }
    
    public function authentification($rq, $rs, $args) {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://api.auth.local:19580',
            'timeout'  => 2.0,
        ]);
        $response = $client->request('GET', 'auth');
        $code = $response->getStatusCode();
        $contentType = $response->getHeader('Content-Type');
        $body = $response->getBody() ;
        $json = json_decode($response->getBody()) ;

        return $json;
    }
    public function commandes($rq, $rs,$args){
        $client = new Client([
            'base_uri' => 'http://api.commande.local:19080',
            'timeout'  => 2.0,
        ]);
        $response = $client->request('GET', 'commandes');
        $code = $response->getStatusCode();
        $contentType = $response->getHeader('Content-Type');
        $body = $response->getBody() ;
        $json = json_decode($response->getBody()) ;
        return $json;
    }

}