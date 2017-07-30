<?php

	require_once __DIR__ ."/vendor/autoload.php";
	require_once 'config/botConfig/config.php';

	use \App\RouterKhan\RouterKhan as Router;
	use \App\Container\ServiceContainer as Container;
	use \Src\Bot\BotCore as BotCore;
	use \Src\Bot\Callbacks as Callbacks;
	use \Src\Bot\Facebook as Facebook;

	$router = Router::getInstance();
	$container = Container::Build();
	$container->set('request', new \App\Request\Request());

	$router->get('/', function($req, $res){
		echo "Ola Mundo!!";
	});

	$router->get('/debug', function($req, $res){
		header('Content-Type: application/json');
		echo file_get_contents('neural/debug.json');
	});

	// DEFINE AS ROTAS

	$router->get('/server', function($req, $res){
		include 'views/serversocket.html';
	});

	$router->post('/server-websocket', function($req, $res){
		file_put_contents('debug.txt', json_encode($_POST));
		$canal = strip_tags(addslashes($_POST['canal']));
		$data = json_encode($_POST['data']);
		$res = file_get_contents("https://gentle-ocean-75288.herokuapp.com/api/socket?canal=".$canal."&data=".urlencode($data));
		echo $res;
	});

	$router->get('/webhook', function($req, $res) {

		// VERIFICAÇAO DO FACEBOOK
		$challenge = $_REQUEST['hub_challenge'];
		$verify_token = $_REQUEST['hub_verify_token'];
		// Senha Default para configurar no Webhook no Developers
		$token_access = "minhasenha123";
		// VERIFICACAO DE ACESSO A PARTIR DA SENHA
		if ($verify_token === $token_access) {
    		$res->send($challenge);
    		$res->sendStatus(200);
		}else{
			throw new Exception("Error Processing Webhook", 1);
			$res->sendStatus(403);
		}

	});

	$router->post("/webhook", function($req, $res){
		// Cria o Robo
		$BotCore = BotCore::getInstance();
		$BotCore->logger(true);
		// Bot Inicia
		$BotCore->Run();
	});

	// Pega as rotas das Frases Para Callback
	$router->get("/neuros", function($req, $res){

		header("Content-Type: application/json");
		echo file_get_contents('neural/neuro-system.json');

	});

	// ROTA PARA TESTAR O ROBO FEITO
	$router->params('/teste/{id}/{msg}', function($req, $res) use($container){
		$request = $container->get('request');
		$id = $req->params('id');
		$msg = $req->params('msg');
		$callback = new Callbacks(new Facebook(BOT_KEY));
		$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.BOT_KEY;
		$response = $request->Post([
			"url" => $url,
			"header" => "Content-Type: application/json",
			"data" => json_encode([
				"recipient" => array("id" => $id),
				"message" => $callback->$msg(array("user_id" => $id))
			])
		]);
	});

	$router->Run();