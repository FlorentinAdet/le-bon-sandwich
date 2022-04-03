<?php

namespace lbs\auth\api\controller;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException ;
use Firebase\JWT\BeforeValidException;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\auth\api\models\User;
use lbs\auth\api\utils\Writer;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class LBSAuthController
{
    public function test($rq,$rs,$args){
        echo 'yes';
        $data = [
            'access-token' => "oi",
        ];

        return Writer::json_output($rs, 200, $data);
        return $rs;
    }
    public function check(Request $rq, Response $rs, $args): Response{
        $secret = "oui";
        try 
        {
            $h = $rq->getHeader('Authorization')[0] ;
            $tokenstring = sscanf($h, "Bearer %s")[0] ;
            $token = JWT::decode($tokenstring, new Key($secret,'HS512'));            
        } catch (ExpiredException $e) {

        } catch (SignatureInvalidException $e) {

        } catch (BeforeValidException $e) {

        } catch (\UnexpectedValueException $e) {
        
        }
        $data = [
            'username' => $token->upr->username,
            'email' => $token->upr->email,
            "level" => $token->upr->level,
        ];
        return Writer::json_output($rs, 200, $data);
    }

    public function authenticate(Request $rq, Response $rs, $args): Response {

        $secret = "oui"; 
        //$secret = $this->container->settings['secret'];

        if (!$rq->hasHeader('Authorization')) {

            $rs = $rs->withHeader('WWW-authenticate', 'Basic realm="commande_api api" ');
            return Writer::json_error($rs, 401, 'No Authorization header present');
        };

        $authstring = base64_decode(explode(" ", $rq->getHeader('Authorization')[0])[1]);
        list($email, $pass) = explode(':', $authstring);
        echo $pass;
        try {
            $user = User::select('id', 'email', 'username', 'passwd', 'refresh_token', 'level')
                ->where('email', '=', $email)
                ->firstOrFail();

            if ($pass != $user->passwd)
                throw new \Exception("password check failed");


        } catch (ModelNotFoundException $e) {
            $rs = $rs->withHeader('WWW-authenticate', 'Basic realm="lbs auth" ');
            return Writer::json_error($rs, 401, 'Erreur authentification');
        } catch (\Exception $e) {
            $rs = $rs->withHeader('WWW-authenticate', 'Basic realm="lbs auth" ');
            return Writer::json_error($rs, 401, 'Erreur authentification');
        }


        $token = JWT::encode(['iss' => 'http://api.auth.local/auth',
            'aud' => 'http://api.backoffice.local',
            'iat' => time(),
            'exp' => time() + (12 * 30 * 24 * 3600),
            'upr' => [
                'email' => $user->email,
                'username' => $user->username,
                'level' => $user->level
            ]],
            $secret, 'HS512');

        $user->refresh_token = bin2hex(random_bytes(32));
        $user->save();
        $data = [
            'access-token' => $token,
            'refresh-token' => $user->refresh_token
        ];

        return Writer::json_output($rs, 200, $data);


    }

}

?>