<?php
	require("config.php");
	
	if(isset($_POST["submitbtn"])) {
		$username = htmlentities(preg_replace('/\s\s+/', ' ', ($_POST["username"])));
		$password = htmlentities(preg_replace('/\s\s+/', ' ', ($_POST["password"])));

		$db = new DBel();
		$check = $db->q("SELECT id,firstname,lastname FROM users WHERE username = :u AND password = :p", 2, array(":u"=>$username, ":p"=>$password));
		if(count($check) === 1) {
			$_SESSION["usrid"] = $check[0]["id"];
			$_SESSION["fname"] = $check[0]["firstname"]; 
			$_SESSION["lname"] = $check[0]["lastname"]; 

			redirect();
		}
	} elseif(isset($_GET["out"])) {
		session_destroy();
		redirect();
	}

	require("incls/header.php");

	$f = new formMaker();

	echo $f->create("login", array("POST", "login.php"));
	require_once("incls/footer.php");
?>