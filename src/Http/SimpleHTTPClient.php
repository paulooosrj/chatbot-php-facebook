<?php
/**
 * Class for simplifying HTTP requests by wrapping cURL workflow.
 * 
 */
namespace Src\Http;

class Post {
    
    public function __construct(string $url, array $data){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        $params = http_build_query($values);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$params); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        return $server_output;
        curl_close ($ch);

    }

}