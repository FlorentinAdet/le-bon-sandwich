<?php
namespace lbs\fab\app\middlewares;

use \Psr\Http\Message\ServerRequestInterface as Requests;
use \Psr\Http\Message\ResponseInterface as Response;
use \Respect\Validation\Validator as v;

class CommandValidator{
    public static function create_validator(){
        return[
            'nom_client' => v::StringType()->alpha('-\''),
            'mail_client'=> v::email(),
            'livraison'=>[
                'date'=>v::date('d-m-Y')->min('now'),
                'heure'=>v::date('H:i')
            ],
            'items'=> v::arrayVal()->each(v::arrayVal()
                ->key('uri',v::StringType())
                ->key('q', v::intVal())
                ->key('libelle',v::StringType())
                ->key('tarif',v::floatVal())
            )
        ];
    }
}