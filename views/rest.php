<?php

	$url = "https://api.hgbrasil.com/weather/?format=json&city_name={$_GET["cidade"]},{$_GET["estado"]}&key=d6cab59d";
	$response = (array) json_decode(file_get_contents($url));
	$res = (array) $response["results"];
	$data = array(
		"temperatura" => $res["temp"],
		"dia" => $res["date"],
		"horario" => $res["time"],
		"descricao" => $res["description"],
		"periodo" => $res["currently"],
		"humidade" => $res["humidity"],
		"v_vento" => $res["wind_speedy"]
	);

	header("Content-Type: application/json");
	echo json_encode($data);