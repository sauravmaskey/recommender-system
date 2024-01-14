<?php
	require_once("config.php");

	if(isset($_POST["q"]) && trim($_POST["q"]) !== "") {
		header("Content-Type: application/json");

		$dbel = new DBel();

		$q = "%" . $_POST["q"] . "%";

		$sql = $dbel->q("SELECT `primaryTitle` FROM `films` WHERE `primaryTitle` LIKE :needle",1, array(":needle" => $q));

		$list = array();

		if (!empty($sql)) {
		    foreach($sql as $row) {
		    	$list[] = $row["primaryTitle"];
		    }
		}

		echo (json_encode(array_slice(isort($list, $q), 0,6)));
	} else {
		redirect();
	}
?>