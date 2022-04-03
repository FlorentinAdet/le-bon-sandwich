<?php

namespace lbs\command\app\utils;

use \Psr\Http\Message\ServerRequestInterface as Requests;
use \Psr\Http\Message\ResponseInterface as Response;

class Writer{

    public static function json_output($rs, $code, $data=null){
        $rs = $rs->withStatus($code)
                 ->withHeader('Content-Type','application/json;charset=utf-8');
                 if(!is_null($data)) $rs->getBody()->write(json_encode($data));
                 return $rs;
    }
    public static function json_error($rs,$code,$message,$url=null){
        $rs = $rs->withStatus($code)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
                $data = ['type'=>'error',
                'error' => $code,
                'message' => $message];
                if($url) $data['redirect']=$url;
                $rs->getBody()->write(json_encode($data));
                return $rs;
    }
}