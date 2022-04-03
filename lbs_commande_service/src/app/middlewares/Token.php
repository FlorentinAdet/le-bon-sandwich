<?php 

namespace  lbs\command\app\middlewares;

use \Psr\Http\Message\ServerRequestInterface as Requests;
use \Psr\Http\Message\ResponseInterface as Response;
use  lbs\command\app\utils\Writer as Writer;
use  lbs\command\app\models\Commande as Commande;

use Illuminate\Database\Eloquent\ModelNotFoundException;

Class Token {
    public function check($rq,$rs,$next){
        $token = null;
        $token = $rq->getQueryParam('token',null);
        if(is_null($token)){
            $api_header = $rq->getHeader('X-lbs-token');
            $token = (isset($api_header[0]) ? $api_header[0] : null);
        }
        if(empty($token)){
            return Writer::json_error($rs,403,"missing Token($token)");
        }
        $commande_id = $rq->getAttribute('route')->getArgument('id');
        $command = null;

        try{
            $command = Commande::where('id','=',$commande_id)
                    ->firstOrFail();
            if($command->token !== $token){
                return Writer::json_error($rs, 403,"Wrong token");
            }
        }catch(ModelNotFoundException $e){
            return Writer::json_error($rs, 404,'commande inconnue');
        }

        $rq = $rq->withAttribute('command',$command);
        return $next($rq,$rs);
    }
}