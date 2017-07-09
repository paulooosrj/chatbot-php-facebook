<?php

	require_once __DIR__ ."/vendor/autoload.php";
	require_once 'config/botConfig/config.php';

	use \NoahBuscher\Macaw\Macaw as Route;
	use \Src\Bot\BotCore as BotCore;
	use \Src\Bot\Callbacks as Callbacks;
	use \Src\Bot\Facebook as Facebook;

	Route::get('/debug', function(){
		header('Content-Type: application/json');
		echo file_get_contents('neural/debug.json');
	});

	// DEFINE AS ROTAS

	Route::get('/server', function(){
		include 'views/serversocket.html';
	});

	Route::post('/server-websocket', function(){
		file_put_contents('debug.txt', json_encode($_POST));
		$canal = strip_tags(addslashes($_POST['canal']));
		$data = json_encode($_POST['data']);
		$res = file_get_contents("https://gentle-ocean-75288.herokuapp.com/api/socket?canal=".$canal."&data=".urlencode($data));
		echo $res;
	});

	Route::get('/webhook', function() {

		// VERIFICAÇAO DO FACEBOOK
		$challenge = $_REQUEST['hub_challenge'];
		$verify_token = $_REQUEST['hub_verify_token'];
		// Senha Default para configurar no Webhook no Developers
		$token_access = "minhasenha123";
		// VERIFICACAO DE ACESSO A PARTIR DA SENHA
		if ($verify_token === $token_access) {
    		echo $challenge;
    		http_response_code(200);
		}else{
			die("Error");
			http_response_code(403);
		}

	});

	Route::post("/webhook", function(){
		// Cria o Robo
		$BotCore = BotCore::getInstance();
		$BotCore->logger(true);
		// Passa Callbacks junto com Api Rest do Facebook OO
		$BotCore->setCallbacks(new Callbacks(new Facebook(BOT_KEY)));
		// Bot Inicia
		$BotCore->Run();
	});

	// Pega as rotas das Frases Para Callback
	Route::get("/neuros", function(){

		header("Content-Type: application/json");
		echo file_get_contents('neural/neuro-system.json');

	});

	// ROTA PARA TESTAR O ROBO FEITO
	Route::get('/teste/(:any)/(:any)', function($id, $msg){

		$callback = new Callbacks(new Facebook(BOT_KEY));
		print_r($callback->$msg(array("user_id" => $id)));
		$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.BOT_KEY;
		$client = new \GuzzleHttp\Client(['headers' => [
			'Content-Type' => 'application/json'
		]]);
		$response = $client->post($url, array('body' => json_encode(array(
			"recipient" => array("id" => $id),
			"message" => $callback->$msg(array("user_id" => $id))
		))));

	});

	Route::dispatch();