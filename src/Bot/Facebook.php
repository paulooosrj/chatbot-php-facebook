<?php

namespace Src\Bot;

class Facebook{
    
    private static $key;
    private static $cacheFileName;
    private static $cacheFile;
    
    public function __construct($key){
        
        self::$key           = $key;
        self::$cacheFileName = "neural/facebook-cache.json";
        self::$cacheFile     = (array) json_decode(file_get_contents(self::$cacheFileName));
        
    }
    
    public function get($id){

        $token_id = md5($id);

        if (empty(self::$cacheFile)) {
            self::$cacheFile = array();
        }

        if (isset(self::$cacheFile[$token_id])) {
            return (array) self::$cacheFile[$token_id];
        } else {
            $payloadFB = "https://graph.facebook.com/v2.6/{$id}?access_token=" . self::$key;
            $response  = (array) json_decode(file_get_contents($payloadFB));
            $data = array(
                "nome" => $response["first_name"] . " " . $response["last_name"],
                "imagem" => $response["profile_pic"],
                "localizacao" => $response["locale"],
                "sexo" => $response["gender"]
            );
            self::$cacheFile[$token_id] = $data;
            file_put_contents(self::$cacheFileName, json_encode(self::$cacheFile));
            return $data;
        }
    }

}