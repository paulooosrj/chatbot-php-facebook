<?php
	
	namespace Src\Bot;
	//require_once 'Callback.php';

	class BotCore{

		private static $key;
		private static $token;
		private static $pusher;
		private static $logger;
		private static $endpoint;
		private static $dominio;
		private static $callbacks = null;
		static $botInstance = null;

		// Pattern Singleton

    	public static function Create(){
        	if (null === self::$botInstance) {
            	self::$botInstance = new BotCore();
        	}
       	 	return self::$botInstance;
		}

		public function getKey(){ return self::$key; }

    	protected function __construct(){
    		if(self::$callbacks === null){
    			self::$callbacks = Callbacks::Create();
    		}
    	}	
    	private function __clone(){}
    	private function __wakeup(){}

		public function setKey($key){ self::$key = $key; }

		public function setToken($token){ self::$token = $token; }

		public function logger($cond){
			if(!empty(self::$pusher) && count(self::$pusher) == 3 && $cond == true){
				self::$logger = $cond;
			}else{
				self::$logger = false;
				echo "Configure o acesso ao seu Pusher !!";
			}
		}

		public function setDominio($dominio){ self::$dominio = $dominio; }

		public function setCallbacks($callback){
			self::$callbacks = $callback;
		}

		public static function configPusher(array $config){
			self::$pusher = $config;
		}
		
		public function endpoint($point){
			self::$endpoint = $point;
		}

		// Envia o Log Pelo Pusher
		public function MsgPusher($msg){
			$canal = "chatbotphp";
  			$event = "logger";
  			$req_url = self::$endpoint."/".self::$pusher["key"]."/".self::$pusher["secret"]."/".self::$pusher["app_id"]."/".$canal."/".$event."/".$msg;
			$exec = file_get_contents($req_url);
		}

		public function sendApi($d){
			/* KEY DA PAGINA GERADO NO MESSENGER NO FACEBOOK DEVELOPERS */
			$key = self::$key;
			// Rest do Chatbot
			$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$key;
			// Iniciando o Envio.
			$client = new \GuzzleHttp\Client(['headers' => [
				'Content-Type' => 'application/json'
			]]);
			file_put_contents('neural/debug.json', json_encode($d));
			// Envia Requisicao
			if (!empty($d['message'])){
  				$client->post($url, array('body' => json_encode($d)));
  			} 
		}

		public function eventsTrigger($id, $text, $user){
			/* MENSAGEM PARA ENVIAR CASO NAO EXISTA NA MEMORIA DO ROBO */
			$mensagemDefault = 'Digite "help" para ver os Comandos!!';
			/* SISTEMA DE MEMORIA DO ROBO */
			$neuros = (array) json_decode(file_get_contents(self::$dominio."/neuros"));
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

		public function Run(){
			// RECEBE AS INFOS
			$receive = json_decode(file_get_contents('php://input'), true);
			// INICIA O TRATAMENTO DE MENSAGEM POR MENSAGEM
			if(isset($receive["entry"]) && $receive["object"] == "page"){
				foreach ($receive['entry'] as $key => $entry) {	
					$pageID = $entry["id"];
					$timeOfEvent = $entry["time"];
					foreach($entry["messaging"] as $k => $event){
						if(isset($event['message'])){ 
							$this->trataMensagem($event); 
						}
					}
				}
			}
		}

}