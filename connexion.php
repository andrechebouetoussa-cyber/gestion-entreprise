<?php

try {
	$pdo = new PDO("mysql:host=localhost;dbname=entreprise;charset=utf8", "root", "");
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $ex) {
	die("erreur de connexion: " . $ex->getMessage());
}


?>