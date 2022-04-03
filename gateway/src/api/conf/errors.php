<?php

use \Psr\Http\Message\ServerRequestInterface as Requests;
use \Psr\Http\Message\ResponseInterface as Response;

return [
    'notFoundHandler'=>function(\Slim\Container $c ) {
        return function( $rq, $rs ) use($c) {
            
            $uri = $rq->getUri();

            $rs = $rs->withStatus( 400 )
                    ->withHeader('Content-Type', 'application/json');
            $rs->write(json_encode(['type'=>'error',
                                    'error'=>400,
                                    "message"=>"$uri : malformed uri - request not recognized"]));
            $c->get('logger.error')->error("GET $uri : malformed uri");
            return $rs ;
        };
    },
    
    'notAllowedHandler' => function( $c ) {
        return function( $req, $resp , $methods ) {
            $method = $req->getMethod();
            $uri = $req->getUri();
            $resp= $resp->withStatus( 405 )
                        ->withHeader('Content-Type','application/json')
                        ->withHeader('Allow', implode(',', $methods) )
                        ->write(json_encode([
                            'type'=>'error',
                            'error'=>405,
                            "message"=> "method $method not allowed for uri $uri - (should be ".implode(', ',$methods).')'
                        ]));
            return $resp ;
        };
    },
    'phpErrorHandler' => function(\Slim\Container $c ) {
        return function( $req, $resp , $error ) use($c){
            $resp= $resp->withStatus( 500 ) 
                        ->withStatus('Content-Type', 'application/json')
                        ->write(json_encode([
                            'type'=>'error',
                            'error'=>500,
                            "message"=> "internal server error: {$error->getMessage()}",
                            "trace"=> $error->getTraceAsString(),
                            "file"=> $error->getFile(). " line: ".$error->getLine()]));
            return $resp ;
        };
    }
];
  /*
    $c[ 'notFoundHandler' ]= function( $c ) {
        return function( $req, $rs ) {
            $rs = $rs->withStatus(400)
                    ->withHeader('Content-Type','json');
            $rs->write(json_encode([
                "type"=>'error',
                "error"=>400,
                "msg"=> "URI mal formée dans la requête"
            ]));
            return $rs ;
        } ;
    };
    
    $c[ 'notAllowedHandler' ]= function( $c ) {
    return function( $req, $resp , $methods ) {
        $resp= $resp
        ->withStatus( 405 )
        ->withHeader('Allow', implode(',', $methods) );
        $resp->getBody()
        ->write( 'méthode permises :' .
        implode(',', $methods) ) ;
        return $resp ;
        };
    }; 
   */
    
?>