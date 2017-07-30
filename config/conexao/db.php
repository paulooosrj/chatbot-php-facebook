<?php

	define("HOST", "seu banco");
	define("DB_NAME", "neural");
	define("DB_USER", "user");
	define("DB_PASS", "senha");

	$db = null;
	try {
		$db = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		echo "Erro: ".$e->getMessage();
	}