<?php

	include 'config/db.php';

	unset($_GET['url']);
	$methods = array("POST", "GET");

	function is_json($str){ 
    	return json_decode($str) != null;
	}

	function getFacebookPessoa($id, $key){
		$payloadFB = "https://graph.facebook.com/v2.6/{$id}?access_token=".$key;
		$response = (array) json_decode(file_get_contents($payloadFB));
		$data = array(
			"nome" => $response["first_name"]." ".$response["last_name"],
			"imagem" => $response["profile_pic"],
			"localizacao" => $response["locale"],
			"sexo" => $response["gender"]
		);
		return $data;
	}

	function filterGet($ar){
		$filtrado = array();
		foreach ($ar as $key => $value) {
			if(!is_json($value)){
				$f = trim(htmlentities(strip_tags(addslashes($value))));
				$filtrado[$key] = $f;
			}else{
				$filtrado[$key] = $value;
			}
		}
		return $filtrado;
	}

	$_GET = filterGet($_GET);
	$metodo = $_GET["method"];

	if(!isset($metodo)): http_response_code(404); die("erro"); endif;
	if(!in_array($metodo, $methods)): http_response_code(404); die("erro"); endif;

	if($metodo == "GET"){
		$d = (array) json_decode($_GET["fields"]); extract($d);
		$conn = $db->prepare("SELECT * FROM users WHERE id_user=:id");
		$conn->bindValue(":id", $id);
		$conn->execute();
		header("Content-Type: application/json");
		if($conn->rowCount() > 0){
			echo json_encode($conn->fetchAll(PDO::FETCH_ASSOC));
		}else{
			json_encode(array("msg" => "erro"));
		}
	} else if($metodo == "POST"){
		//echo json_decode($_GET["fields"]);
		$d = (array) json_decode($_GET["fields"]); extract($d);
		$in = $db->prepare("INSERT INTO users(id_user, infos) VALUES(:id, :infos)");
		$in->bindValue(":id", $id);
		$in->bindValue(":infos", json_encode(getFacebookPessoa($id, $key)));
		$in->execute();
	}