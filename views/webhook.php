<?php

		include "config/config.php";
		include "views/callbacks.php";

  		function MsgPusher($msg){
  			$canal = "chatbotphp";
  			$event = "logger";
			//open connection
			$url = ENDPOINT."?key=".KEY_PUSHER."&secret=".KEY_SECRET_PUSHER."&app_id=".APP_ID_PUSHER."&canal={$canal}&event={$event}&msg={$msg}";
			$exec = file_get_contents($url);
  		}

		function sendApi($d){
			// Iniciando o Envio.
			$options = array(
  				'http' => array(
    				'method'  => 'POST',
    				'content' => json_encode($d),
    				'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
    			)
			);
			$context  = stream_context_create($options);
   			if (!empty($d['message'])): file_get_contents(API_URL, false, $context); endif;
		}

		function eventsTrigger($id, $text, $user){
			/* MENSAGEM PARA ENVIAR CASO NAO EXISTA NA MEMORIA DO ROBO */
			$mensagemDefault = 'Digite "help" para ver os Comandos!!';
			/* SISTEMA DE MEMORIA DO ROBO */
			$neuros = (array) json_decode(file_get_contents('./neural/neuro-system.json'));
			/* TRATA A MENSAGEM */
			$trataNeuro = trim(strtolower($text));
			/* DADOS A SER ENVIADO PELO BOT */
			$dataInfo = array(
				"recipient" => array("id" => $id)
			);
			/* VERIFICA SE EXISTE A MENSAGEM NA MEMORIA DO ROBO */
			if(isset($neuros[$trataNeuro])){
				$funcao = $neuros[$trataNeuro];
				$dataInfo["message"] = $funcao($user);
				sendApi($dataInfo);
			}else{
				$keys = explode(" ", $trataNeuro);
				$search = trim($keys[0]);
				if(isset($neuros[$search])){
					$funcao = $neuros[$search];
					$user["extern_value"] = trim(str_replace($search, "",$trataNeuro));
					$dataInfo["message"] = $funcao($user);
				}
				if(empty($dataInfo["message"])){
					$dataInfo["message"] = array("text" => $mensagemDefault);
				}
				sendApi($dataInfo);		
			}
		}
		function trataMensagem($msg){
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
				$infos["token_access"] = KEY;
				if(LOG_ACTIVE): MsgPusher(json_encode($infos)); endif;
				// INICIA O TRATAMENTO PARA ENVIO param1: id, param2: mensagem
				eventsTrigger($senderID, $messageText, $infos);

			}
		}

		// VERIFICAÇAO DO FACEBOOK
		$challenge = $_REQUEST['hub_challenge'];
		$verify_token = $_REQUEST['hub_verify_token'];
		// Senha Default para configurar no Webhook no Developers
		$token_access = TOKEN_ACCESS;
		// VERIFICACAO DE ACESSO A PARTIR DA SENHA
		if ($verify_token === $token_access) { echo $challenge; }
		// RECEBE AS INFOS
		$receive = json_decode(file_get_contents('php://input'), true);
		// INICIA O TRATAMENTO DE MENSAGEM POR MENSAGEM
		if(isset($receive["entry"]) && $receive["object"] == "page"){
			foreach ($receive['entry'] as $key => $entry) {
				$pageID = $entry["id"];
				$timeOfEvent = $entry["time"];
				foreach($entry["messaging"] as $k => $event){
					if(isset($event['message'])){
						trataMensagem($event);
					} else {
						if(isset($event["postback"]) && isset($event["postback"]["payload"])){
							$id = $event["sender"]["id"];
							$ev = $event["postback"]["payload"];
							$neuros = (array) json_decode(file_get_contents('./neural/neuro-system.json'));
							if(isset($neuros[$ev])){
								$fun = $neuros[$ev];
								$dataInfo = array(
									"recipient" => array("id" => $id),
									"message" => $fun($event)
								);
								sendApi($dataInfo);
							}
						}
					}
				}
			}
		}