<?php
	
	namespace Src\Bot;
	use \Src\Bot\Facebook as Facebook;

	class Callbacks {

	private static $facebook;

	public function __construct($face){
		self::$facebook = $face;
		$this->Facebook = self::$facebook;
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

	public function getClima($cidade, $estado){
		$cidade = urldecode($cidade); $estado = urldecode($estado);
		$key = "d6cab59d";
		$res = (array) json_decode(file_get_contents("https://api.hgbrasil.com/weather/?format=json&city_name={$cidade},{$estado}&key=".$key));
		$resultado = (array) $res["results"];
		$data = array(
			"temperatura" => $resultado["temp"],
			"descricao" => $resultado["description"],
			"periodo" => $resultado["currently"],
			"umidade" => $resultado["humidity"],
			"v_vento" => $resultado["wind_speedy"],
			"dia" => $resultado["date"],
			"horario" => $resultado["time"]
		);
		return $data;
	}
	
	public function callbackName($info){

		$getPessoa = $this->Facebook->get($info["user_id"]);
		return array("text" => "Seu Nome é: ".$getPessoa["nome"]);

	}

	public function callbackOi($info){

		$getPessoa = $this->Facebook->get($info["user_id"]);
		return array("text" => "Olá, tudo bem ".$getPessoa["nome"]." ?");

	}

	public function callbackBoaNoite($info){

		$pessoa = $this->Facebook->get($info["user_id"]);
		$n = explode(" ", $pessoa["nome"]);
		return array("text" => "Olá ".$n[0].", Boa Noite!!");

	}

	public function callbackRoboIgual(){

		return array("text" => "https://github.com/PaulaoDev/ChatBot-PHP-Facebook");

	}

	public function callbackClima($res){

		$resValue = explode("-", $res["extern_value"]);
		$cidade = urlencode(trim($resValue[0]));
		$estado = urlencode(trim($resValue[1]));
		if(strpos($cidade, '+')){ $cidade = str_replace('+','', $cidade); }
		if(count($estado) != 1){ $erro = "Erro ao Digitar Sigla Do Estado!"; }
		$clima = $this->getClima($cidade, $estado);
		if(!empty($clima) && !isset($erro)){
			$fraseClima = "⭕ {$clima["temperatura"]}ºC \n☁ {$clima["descricao"]} \n🕐 {$clima["periodo"]} \n🎈 Umidade: {$clima["umidade"]} \n🌀 {$clima["v_vento"]} \n📅 {$clima["dia"]} \n🕒 {$clima["horario"]}";
		}else{
			$fraseClima = "Não achei o Clima da cidade Digitada. \nTente Novamente.";
		}
		if(isset($erro)){ $fraseClima = $erro; }
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

		$dataBtn = $this->montaBotao($info["sender"]["id"], "Escolha uma Opção", array(
			array(
            	"type" => "web_url",
            	"url"  => "https://github.com/PaulaoDev/ChatBot-PHP-Facebook",
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