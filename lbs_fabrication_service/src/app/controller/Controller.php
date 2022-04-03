<?php 

namespace  lbs\fab\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Requests;
use \Psr\Http\Message\ResponseInterface as Response;
use  lbs\fab\app\models\Commande as Commande;
use  lbs\fab\app\models\Item as Item;
use  lbs\fab\app\utils\Writer as Writer;
use Ramsey\Uuid\Uuid as Uuid;

Class Controller {
    private $c;

    public function __construct($c){
        $this->c = $c;
    }
    /**
     * Fonction allCommande
     * Retourne des commandes en fonctions des paramètres précisés :
     * Parammètre URI :
     *      page=num : retourne les commandes entre 10*num-1 10*num nième commande / num = INT
     *      size=num : retourne les commandes entre size*num-1 size*num nième commande / num = INT
     *      s=num    : retourne les commandes ayant le status s / num = INT 
     */
    function allCommande($rq, $rs, $args) {
        $tabCommande=[];
        $model = "lbs\\fab\app\models\commande";
        $page = intval($rq->getQueryParam('page',null));
        if($page == null){
        $requete= $model::select()->skip(0)->take(10)->get();
        }else{
            $requete= $model::select()->skip($page*10+1)->take(10)->get();
        }
        $count = $requete->count();
        $rs = $rs->withHeader('Content-Type','application/json');
        $statu = $rq->getQueryParam('s',null);
        foreach ($requete as $c){
            if(!isset($statu)){
                $Commande=[];
                $Commande["command"]["id"]=$c->id;
                $Commande["command"]["nom"]=$c->nom;
                $Commande["command"]["created_at"]=$c->created_at;
                $Commande["command"]["livraison"]=$c->livraison;
                $Commande["command"]['status']=$c->status;
                $Commande["links"]['self']['href']="/commande/".$c->id;
                array_push($tabCommande,$Commande);
            }else{
                if($statu == $c->status){
                    $Commande=[];
                    $Commande["command"]["id"]=$c->id;
                    $Commande["command"]["nom"]=$c->nom;
                    $Commande["command"]["created_at"]=$c->created_at;
                    $Commande["command"]["livraison"]=$c->livraison;
                    $Commande["command"]['status']=$c->status;
                    $Commande["links"]['self']['href']="/commande/".$c->id;
                    array_push($tabCommande,$Commande);
                }
            }
        }
        $size = intval($rq->getQueryParam('size',null));
        $next = $page+1;
        $prev = $page-1;
        if(!isset($size)){
            $size = 10;
        }
        $links['next']['href'] =  "/commandes/?page=".$next."&size=".$size;
        $links['prev']['href'] =  "/commandes/?page=".$prev."&size=".$size;
        $links['last']['href'] =  "/commandes/?page=1&size=".$size;
        $links['first']['href'] = "/commandes/?page=5&size=".$size;

        $rs->write(json_encode([
            "type"=>'collection',
            "count"=>$count,
            "size"=>$size,
            "links"=>$links,
            "commandes"=>$tabCommande,
            ]));        
        return $rs;
    }
    
}