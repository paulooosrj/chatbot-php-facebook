<?php 
	 
  $client = new \GuzzleHttp\Client();
  $body = array("foo","bar");
  $res = $client->post('http://httpbin.org/post', [ 'body' => json_encode($body) ]);
  print_r($res);