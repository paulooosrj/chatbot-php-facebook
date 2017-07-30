<?php
	
	namespace Src\Bot;
	use Src\Bot\Callbacks as Callbacks;
	use Src\Bot\Facebook as Facebook;

	require_once 'config/botConfig/config.php';

	class BotCore {

		private static $key;
		private static $token;
		private static $logger;
		private static $endpoint;
		private static $dominio;
		private static $callbacks;

		// Pattern Singleton

    	public static function getInstance(){
        	static $instance = null;
        	if (null === $instance) {
            	$instance = new static();
        	}
       	 	return $instance;
		}

    	protected function __construct(){
    		self::$key = BOT_KEY;
    		self::$token = BOT_TOKEN;
    		self::$endpoint = BOT_ENDPOINT;
    		self::$dominio = BOT_DOMINIO;
    		self::$callbacks = new Callbacks(new Facebook(BOT_KEY));
    	}	

    	// Configs do ChatBot

		public function logger($cond){
			if(!empty(self::$endpoint) && $cond == true){
				self::$logger = $cond;
			}else{
				self::$logger = false;
				echo "Configure o acesso ao seu Servidor De Websockets !!";
			}
		}

		// Envia o Log Pelo Pusher
		public function MsgPusher($msg){
			/*$canal = "chatbotphp";
  			$post = new \Src\Http\Post("http://chatbotphp.ga/server-websocket", array(
				"canal" => $canal,
    			"data" => $msg
    		));*/
		}

		public function sendApi($d){
			/* KEY DA PAGINA GERADO NO MESSENGER NO FACEBOOK DEVELOPERS */
			$key = self::$key;
			$request = new \App\Request\Request();
			// Rest do Chatbot
			$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$key;
			// Iniciando o Envio.
			$request->Post([
				"url" => $url,
				"header" => "Content-Type: application/json",
				"data" => json_encode($d)
			]);
		}

		public function eventsTrigger($id, $text, $user){
			/* MENSAGEM PARA ENVIAR CASO NAO EXISTA NA MEMORIA DO ROBO */
			$mensagemDefault = 'Digite "help" para ver os Comandos!!';
			/* SISTEMA DE MEMORIA DO ROBO */
			$neuros = (array) json_decode(file_get_contents(self::$dominio."neuros"));
			/* TRATA A MENSAGEM */
			$trataNeuro = trim(strtolower($text));
			/* DADOS A SER ENVIADO PELO BOT */
			$dataInfo = array("recipient" => array("id" => $id));
			/* VERIFICA SE EXISTE A MENSAGEM NA MEMORIA DO ROBO */
			if(isset($neuros[$trataNeuro])){
				$funcao = $neuros[$trataNeuro];
				$dataInfo["message"] = self::$callbacks->$funcao($user);
				$this->sendApi($dataInfo);
			}else{
				$keys = explode(" ", $trataNeuro);
				$search = trim($keys[0]);
				if(isset($neuros[$search])){
					$funcao = $neuros[$search];
					$user["extern_value"] = trim(str_replace($search, "",$trataNeuro));
					$dataInfo["message"] = self::$callbacks->$funcao($user);
				}
				if(empty($dataInfo["message"])){
					$dataInfo["message"] = array("text" => $mensagemDefault);
				}
				$this->sendApi($dataInfo);		
			}
		}

		public function trataMensagem($msg){
			/* PEGA TODAS INFORMAÇOES ENVIADAS PELO FACEBOOK */
			$senderID = $msg["sender"]["id"];
			$recipientID = $msg["recipient"]["id"];
			$timeOfMessage = $msg["timestamp"];
			$message = $msg["message"];
			$messageID = $message["mid"];
			$messageText = $message["text"];
			$attachments = $message["attachments"];
			// ENVIA LOGS
			if(isset($messageText)){
				$infos = array();
				$infos["message"] = $messageText;
				$infos["time"] = $timeOfMessage;
				$infos["message_id"] = $messageID;
				$infos["user_id"] = $senderID;
				$infos["page_id"] = $recipientID;
				$infos["token_access"] = self::$token;
				$this->MsgPusher(json_encode($infos));
				// INICIA O TRATAMENTO PARA ENVIO param1: id, param2: mensagem
				$this->eventsTrigger($senderID, $messageText, $infos);
			}
		}

		public static function getMessage(){
			return json_decode(file_get_contents('php://input'), true);
		}

		public function Run(){
			// RECEBE AS INFOS
			$receive = $this::getMessage();
			if(isset($receive)){
				$log = json_encode($receive);
				file_put_contents("logs.txt", "{$log}\n", FILE_APPEND);
			}
			// INICIA O TRATAMENTO DE MENSAGEM POR MENSAGEM
			if(isset($receive["entry"]) && $receive["object"] == "page"){
				foreach ($receive['entry'] as $key => $entry) {	
					$pageID = $entry["id"];
					$timeOfEvent = $entry["time"];
					foreach($entry["messaging"] as $k => $event){
						if(isset($event['message'])){ 
							$this->trataMensagem($event); 
						} else if(isset($event['postback'])){
							$id = $event["sender"]["id"];
							$text = $event['postback']['payload'];
							$this->eventsTrigger($id, $text, $event);
						}
					}
				}
			}
		}

}