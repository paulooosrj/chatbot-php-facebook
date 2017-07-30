<?php
	
	namespace Src\Bot;

	class Callbacks {

	public function __construct(){
		return $this;
	}

	public function montaBotao($id, $txt, $btns){

		return array( 
			"attachment" => array(
      			"type" => "template",
      			"payload" => array(
        			"template_type" => "button",
        			"text" => $txt,
        			"buttons" => $btns
        )));

	}
	
	public function callbackName($info){

		$getPessoa = Facebook::get($info["user_id"]);
		return array("text" => "Seu Nome é: ".$getPessoa["nome"]);

	}

	public function callbackOi($info){

		$getPessoa = Facebook::get($info["user_id"]);
		return array("text" => "Olá, tudo bem ".$getPessoa["nome"]." ?");

	}

	public function callbackBoaNoite($info){

		$pessoa = Facebook::get($info["user_id"]);
		$n = explode(" ", $pessoa["nome"]);
		return array("text" => "Olá ".$n[0].", Boa Noite!!");

	}

	public function callbackRoboIgual(){

		return array("text" => "https://github.com/PaulaoDeveloper/ChatBot-Messenger-PHP");

	}

	public function callbackClima($res){


		$resValue = explode(" ", $res["extern_value"]);
		$clima = (array) json_decode(file_get_contents("http://chatbotphp.ga/rest?cidade={$resValue[0]}&estado={$resValue[1]}"));
		$fraseClima = "⭕ {$clima["temperatura"]}ºC \n☁ {$clima["descricao"]} \n🕐 {$clima["periodo"]} \n🎈 Umidade: {$clima["humidade"]} \n🌀 {$clima["v_vento"]} \n📅 {$clima["dia"]} \n🕒 {$clima["horario"]}";
		//.' ºC'
		return array("text" => $fraseClima);

	}

	public function callbackProcurar($user){

		$s = urlencode($user["extern_value"]);
		$response = (array) json_decode(file_get_contents("https://pt.wikipedia.org/w/api.php?action=query&list=search&origin=*&srsearch={$s}&format=json"));
		$res = (array) $response["query"];
		$res = (array) $res["search"][0];
		$res['snippet'] = trim(strip_tags($res['snippet']));
		return array("text" => "{$res['title']}: \n{$res['snippet']}");

	}

	public function callbackYoutube($user){

		$video = urlencode($user["extern_value"]);
		$response = (array) json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/search?part=snippet,id&type=video&q={$video}&key=AIzaSyCNhqVjoxDfgX7WlNDvQaf3PLHQI8uxFwk"));
		$res = (array) $response["items"][0];
		$res = (array) $res["id"];
		$res = $res["videoId"];

		if(!empty($res)){
			return array("text" => "https://youtu.be/".$res);
		}else{
			return array("text" => "Nenhum video foi Encontrado!!");
		}

	}

	public function callbackLogs(){

		return array("text" => "https://".$_SERVER['HTTP_HOST']."/logs");

	}

	public function callbackComecar($info){

		$estado[$info["sender"]["id"]] = "comecando";
		$dataBtn = $this->montaBotao($info["sender"]["id"], "Escolha uma Opção", array(
			array(
            	"type" => "web_url",
            	"url"  => "https://github.com/PaulaoDeveloper/ChatBot-Messenger-PHP",
            	"title"=> "Repositorio"
          	),
          	array(
            	"type" =>    "postback",
            	"title" =>   "Continuar Conversa",
            	"payload" => "continua_conversa"
          	)
		));

		//$pessoa = getFacebookPessoa($info["sender"]["id"]);
		//$n = explode(" ", $pessoa["nome"]);
		return $dataBtn;

	}

	public function callbackContinuaConversa($info){

		$id = $info["sender"]["id"];
		return array("text" => "\n Estado: ".json_encode($estado));
		//callbackOi(array("user_id" => $id));

	}

	public function help(){

		return array("text" => "↪ Meu Nome \n↪ Oi \n↪ Boa Noite \n↪ Fazer um robo igual \n↪ /clima 'cidade' 'estado em sigla' \n↪ /procurar 'algo para pesquisar' \n↪ /youtube 'Procurar Video No Youtube'");

	}

}