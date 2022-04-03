<?php
namespace lbs\command\app\middlewares;

use \Psr\Http\Message\ServerRequestInterface as Requests;
use \Psr\Http\Message\ResponseInterface as Response;
use \Respect\Validation\Validator as v;

class CommandValidator{
    public static function create_validator(){
        return[
            'nom' => v::StringType()->alpha('-\''),
            'mail'=> v::email(),
            'livraison'=>[
                'date'=>v::date('Y-m-d'),
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