<?php 

namespace  lbs\command\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Requests;
use \Psr\Http\Message\ResponseInterface as Response;
use  lbs\command\app\models\Commande as Commande;
use  lbs\command\app\models\Item as Item;
use  lbs\command\app\utils\Writer as Writer;
use Ramsey\Uuid\Uuid as Uuid;
use Illuminate\Database\Eloquent\ModelNotFoundException;


Class CommandeController {
    private $c;

    public function __construct($c){
        $this->c = $c;
    }

    /**
     * @api {get} /commandes/ Requête pour obtenir toutes les commandes
     * @apiName GetAllCommandes
     * @apiGroup Commande
     * 
     * @apiSuccess {Json} of all commandes
     * 
     * @apiSuccessExample Success:
     *     HTTP/1.1 200 OK
     *     {
     *          "type": "collection",
     *          "count": 1750,
     *          "commandes": [
     *              {
     *                  "id": "a95144d2-1a31-458f-8665-35f571105665",
     *                  "mail_client": "Charles.Lombard@wanadoo.fr",
     *                  "date_commande": "2021-05-27T21:02:02.000000Z",
     *                  "montant": "36.00"
     *              },
     *              ...
     *     }
     * 
     * @apiErrorExample Database-Error:
     *     HTTP/1.1 500 Not Found
     *     {
     *       "error": "Internal server Error"
     *     }
     */
    /**
     * Fonction AllCommande 
     * Retourne toutes les commandes de la table commande de la base
     * Sinon retourne une erreur
     */
    
    function allCommande($rq, $rs, $args) {
        try{
            $tabCommande=[];
            $model = "lbs\command\app\models\commande";
            $requete= $model::select()->get();
            $count = $requete->count();
            $rs = $rs->withHeader('Content-Type','application/json');
            foreach ($requete as $v){
                $Commande=[];
                $Commande["id"]=$v->id;
                $Commande["mail_client"]=$v->mail;
                $Commande["date_commande"]=$v->created_at;
                $Commande["montant"]=$v->montant;
                array_push($tabCommande,$Commande);
            }
            $rs->write(json_encode([
                "type"=>'collection',
                "count"=>$count,
                "commandes"=>$tabCommande
                ]));        
          
            return Writer::json_output($rs,200,$data); 
        }catch(ModelNotFoundException $e){
            return Writer::json_error($rs, 505,"Internal server Error");
        } 
      
    }
    /**
     * @api {get} /commande/:id Requête pour les informations de la commande
     * @apiName GetCommande
     * @apiGroup Commande
     * 
     * @apiParam {String} id Command unique Id
     * 
     * @apiSuccess {Json} Information Information de la commande
     * 
     * @apiSuccessExample Success:
     *     HTTP/1.1 200 OK
     *     {
     *          "type": "resource",
     *          "commande": {
     *              "id": "a95144d2-1a31-458f-8665-35f571105665",
     *              "livraison": "2021-05-29 15:17:53",
     *              "nom": "Charles.Lombard",
     *              "mail": "Charles.Lombard@wanadoo.fr",
     *              "status": 5,
     *              "montant": "36.00"
     *          },
     *          "links": {
     *              "items": {
     *                  "href": "/commande/a95144d2-1a31-458f-8665-35f571105665/items/"
     *              },
     *              "self": {
     *                  "href": "/commande/a95144d2-1a31-458f-8665-35f571105665/"
     *              }
     *          }
     *      }
     * }
     * @apiErrorExample Missing-Token:
     *      HTTP/1.1 403 Not Found
     *      { 
     *          "type":"error",
     *          "error":403,
     *          "message":"missing Token()"
     *      }
     * @apiErrorExample Wrong-Token:
     *      HTTP/1.1 403 Not Found
     *      { 
     *          "type":"error",
     *          "error":403,
     *          "message":"Wrong Token"
     *      }
     * @apiErrorExample Commande-Not-Found:
     *      HTTP/1.1 404 Not Found
     *      { 
     *          "type":"error",
     *          "error":404,
     *          "message":"Commande inconnue"
     *      }
     */
    /**
     * Retourne les informations de base de la commande qui de l'id indiqué dans l'uri
     *   Ainsi que l'uri vers elle même et celle de ses items qui lui sont associés
     *   Le paramètre 'embed=items' peut être ajouter dans l'uri afin d'obtenir, en plus des informations de la commande, ses items associés
     * Sinon retourne une Erreur
     */
    function Commande($rq, $rs, $args) {
        $Items = false;
        $Id = $args['id'];
        $embed = $rq->getQueryParam('embed',null);
        if($embed === 'items') $Items=true;
        try {
            $data = Commande::where('id', $Id)->first();
            $requete= Commande::select(['id','livraison','nom','mail','status','montant'])->where('id','=',$Id);
            if($Items) $requete=$requete->with('items');

            $commande= $requete->firstOrFail();
            $links = [
                'items'=> ['href'=>$this->c->router->pathFor('commandeItems',['id'=>$Id])],
                'self'=> ['href'=>$this->c->router->pathFor('commande',['id'=>$Id])]
            ];
            $data = [
                'type'=> 'resource',
                'commande' => $commande->toArray(),
                'links' => $links
            ];
            return Writer::json_output($rs,200,$data);
        } catch (Exception $e) {            
            return Writer::json_error($rs, 404, "commande $id not found");
        }
    }
    /**
     * @api {get} /commande/:id/items/ Requête pour obtenir les items d'une commande
     * @apiName GetItemCommande
     * @apiGroup Commande
     * 
     * @apiParam {String} id Command unique Id
     * 
     * @apiSuccess {Json} CommandeItems Items associés à la commande
     * @apiSuccessExample Success:
     *     HTTP/1.1 200 OK
     *     {
     *          "type": "collection",
     *          "count": 2,
     *          "items": [
     *              {
     *                  "id": 3044,
     *                  "libelle": "le bucheron",
     *                  "tarif": "6.00",
     *                  "quantite": 3
     *              },
     *              {
     *                  "id": 3045,
     *                  "libelle": "le panini",
     *                  "tarif": "6.00",
     *                  "quantite": 3
     *              }
     *          ]
     *      }
     * }
     * 
     */

     /**
      * Fonction CommandeItems:
      * Retourne les items de la commandes
      * Sinon retourne une erreur s'ils ne sont pas trouvés.
      */
    function CommandeItems($rq, $rs , $args){
        $Id = $args['id'];
        try{
            $commande= Commande::select(['id'])
                    ->findOrFail($Id);
            $items = $commande->items()->select(['id','libelle','tarif','quantite'])->get();
            $data = [
                'type'=>'collection',
                'count'=>count($items),
                'items'=>$items
            ];
            return Writer::json_output($rs,200,$data);  
        }catch(ModelNotFoundException $e){
            return Writer::json_error($rs, 404,"commande $id not found");
        }     
    }
    /**
     * @api {post} /commande/ Ajout d'une commande
     * @apiName addCommande
     * @apiGroup Commande
     *  
     * @apiSuccess {Json} Json 
     * 
     * @apiErrorExample Invalide-Data:
     *   HTTP/1.1 400 Invalide
     *      { 
     *          "type":"error",
     *          "error":400
     *          "message":"Invalid Data"
     *      }
     * @apiErrorExample Missing-Data:
     *   HTTP/1.1 400 Invalide
     *      { 
     *          "type":"error",
     *          "error":400,
     *          "message":"missing data : (data)"
     *      }
     * @apiErrorExample Failed-Insert:
     *   HTTP/1.1 500 Invalide
     *      { 
     *          "type":"error",
     *          "error":500,
     *          "message":"Failed insert data"
     *      }
     * @apiDescription The body have to looks like :
     *   {   
     *      "nom" : "Lombard",
     *      "mail": "Charles.Lombard@wanadoo.fr",
     *      "livraison" : {
     *          "date": "2021-12-07",
     *          "heure": "12:30"
     *      },
     *      "items" : [
     *          { "uri": "/sandwiches/6", "q": 3,"libelle": "panini","tarif": 6.00 },
     *          { "uri": "/sandwiches/1", "q": 2,"libelle": "bucheron","tarif": 6.00}
     *      ]
     *  }
     * 
     */
    /**
     * Fonction addCommande : 
     * Retourne en format JSON les informations de la commande ajoutée
     * Vérifie si toutes les informations necessaire pour créer une commande sont présentes et filtre les données. Si l'une est manquante renvoie une erreur.
     * Si toutes les données sont présentes alors créer la nouvelle commande. Si une erreur intervient lors de l'ajout renvoie une erreur.
     * Lors de l'ajout calcul le montant de la commande avec le tarif et la quantité des items de la commande.
     * 
     */
    public function addCommande($rq,$rs,$args){
        if ($rq->getAttribute( 'has_errors' )) {
            return Writer::json_error($rs, 400, "Invalid data");
        } else {
            $command_data = $rq->getParsedBody();
            if(!isset($command_data['nom']))
                return Writer::json_error($rs,400,"missing data : nom");
            if(!isset($command_data['mail']) || !filter_var($command_data['mail']))
                return Writer::json_error($rs,400,"invalid or missing data : mail");
            if(!isset($command_data['livraison']['date']))
                return Writer::json_error($rs,400,"missing data : livraison(date)");
            if(!isset($command_data['livraison']['heure']))
                return Writer::json_error($rs,400,"missing data : livraison(heure)");
                    
            try{
                $c= new Commande();
                $c->id = Uuid::uuid4()->toString();
                $c->nom = filter_var($command_data['nom'],FILTER_SANITIZE_STRING);
                $c->mail = filter_var($command_data['mail'],FILTER_SANITIZE_EMAIL);
                $c->livraison= \DateTime::createFromFormat('Y-m-d H:i',$command_data['livraison']['date'] . ' ' . $command_data['livraison']['heure']);
                $c->token = bin2hex(random_bytes(32));
                $c->montant=0;
                if(isset($command_data['items'])){
                    $montant = 0;
                    foreach ($command_data['items'] as $item) {
                        $montant += $item['tarif']*$item['q'];
                        $i = new Item();
                        $i->uri = $item['uri'] ;
                        $i->libelle = $item['libelle'];
                        $i->tarif = $item['tarif'];
                        $i->quantite = $item['q'];
                        $i->command_id = $c->id;
                        $i->save();                
                    }
                    $c->montant= $montant;
                }
                $c->save();

                $rs = $rs->withHeader('Content-type', 'application/json');
                $rs = $rs->withHeader('Location',$this->c->router->pathFor('commande',['id'=>$c->id]));
                $data = $c;
                return Writer::json_output($rs,201,$data);                   
            }catch(ModelNotFoundException $e){
                return Writer::json_output($rs,500,'Failed insert commande');
            }
        }  
    }
    /**
     * @api {put} /commande/:id Modifier une commande
     * @apiName updateCommande
     * @apiGroup Commande
     * 
     * @apiParam {String} id Command unique Id
     * 
     * @apiSuccess {Json} Json
     *  @apiErrorExample Invalide-Data:
     *   HTTP/1.1 400 Invalide
     *      { 
     *          "type":"error",
     *          "error":400
     *          "message":"Invalid Data"
     *      }
     * @apiErrorExample Missing-Data:
     *   HTTP/1.1 400 Invalide
     *      { 
     *          "type":"error",
     *          "error":400,
     *          "message":"missing data : (data)"
     *      }
     * @apiErrorExample Failed-Insert:
     *   HTTP/1.1 500 Invalide
     *      { 
     *          "type":"error",
     *          "error":500,
     *          "message":"Failed insert data"
     *      }
     * @apiDescription The body have to looks like (update) Json format and enter the data name you want change. (name/mail/livraison/items) :
     *   {   
     *      "nom" : "Lombard",
     *      "mail": "Charles.Lombard@wanadoo.fr",
     *   }
     * 
     */ 
    /**
     * updateCommande
     * Vérifie les données voulant être modifiées et les modifie dans la base
     * Retourne les informations de la commande modifiée.
     */
    function updateCommande($rq, $rs , $args){
                    
        try{
            $command_data = $rq->getParsedBody();
            $Id = $args['id'];
            $model = "lbs\command\app\models\commande";
            $c = $model::select()->find($Id);
            if(isset($command_data['nom']))
                $c->nom = filter_var($command_data['nom'],FILTER_SANITIZE_STRING);
            if(isset($command_data['mail']) || !filter_var($command_data['mail']))
                $c->mail = filter_var($command_data['mail'],FILTER_SANITIZE_EMAIL);
            if(isset($command_data['livraison']['date']) && isset($command_data['livraison']['heure']))
                $c->livraison= \DateTime::createFromFormat('Y-m-d H:i',$command_data['livraison']['date'] . ' ' . $command_data['livraison']['heure']);
            $c->montant=0;
            if(isset($command_data['items'])){
                $montant = 0;
                foreach ($command_data['items'] as $item) {
                    $montant += $item['tarif']*$item['q'];
                    $i = new Item();
                    $i->uri = $item['uri'] ;
                    $i->libelle = $item['libelle'];
                    $i->tarif = $item['tarif'];
                    $i->quantite = $item['q'];
                    $i->command_id = $c->id;
                    $i->save();                
                }
                $c->montant= $montant;
            }
            $c->save();

            $rs = $rs->withHeader('Content-type', 'application/json');
            $rs = $rs->withHeader('Location',$this->c->router->pathFor('commande',['id'=>$c->id]));
            $data = $c;
            return Writer::json_output($rs,201,$data);                   
        }catch(ModelNotFoundException $e){
            return Writer::json_output($rs,500,'Failed update commande');
        }
    }

}