<?php

require(__DIR__ . '/../vendor/autoload.php');
use \Firebase\JWT\JWT;

$ids = [];

function getID() {
    global $ids;

    $randomId = substr(md5(mt_rand()), 0, 7);

    if ( in_array($randomId, $ids) ){
        return getID();
    }else{
        array_push($ids, $randomId);
        return $randomId;
    }
}

class Gaze {

    private $privateKey;

    function __construct() {
        $this->privateKey = file_get_contents(__DIR__ . "/../private.key");
    }

    public function emit($name, $payload, $role = null){
        $MINUTES_VALID = 5;

        $jwt = JWT::encode([
            "role" => "server",
            "exp" => time() + 60 * $MINUTES_VALID,
        ], $this->privateKey, 'RS256');
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost:8000/event");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type:application/json', 
            'Authorization: Bearer ' . $jwt
        ]);
        curl_exec($ch);
        curl_close($ch);

        // TODO: check op niet 200 return een fout
    }

    function generateClientToken($clientRoles = []){
        $MINUTES_VALID = 5;

        return JWT::encode([
            "roles" => $clientRoles,
            "jti" => getID(),
            "exp" => time() + 60 * $MINUTES_VALID,
        ], $this->privateKey, 'RS256');
    }
}
