<?php

	define("HOST", "HOST MYSQL");
	define("DB_NAME", "NAME DO BANCO");
	define("DB_USER", "USER");
	define("DB_PASS", "SENHA");

	$db = null;
	try {
		$db = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		echo "Erro: ".$e->getMessage();
	}