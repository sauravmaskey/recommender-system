<?php		
	require_once("config.php");

	if($_POST["type"] == "clr" && !empty($user)) {
		$usrid = $_SESSION["usrid"];
		$dbel = new DBel();
		$result = $dbel->q("DELETE FROM user_ratings WHERE userid = :uid AND movieid = :fid", 2, array(":uid" => $usrid, ":fid" => $_POST["fid"]));
		redirect($_SERVER['HTTP_REFERER']);
	} else {
		$_POST = json_decode(file_get_contents('php://input'), true);

		if(empty($_POST)) {
			redirect();
		} else {
			if(!empty($user)) {
				if(!empty($_POST["type"])) {
					if($_POST["type"] == "rate") {
						$usrid = $_SESSION["usrid"];
						$dbel = new DBel();
						$result = $dbel->q("INSERT INTO user_ratings (userid,movieid,rating) VALUES (:usrid,:fid,:r) ON DUPLICATE KEY UPDATE rating = :rating", 4, array(":usrid" => $usrid, ":fid"=>$_POST["fid"], ":r"=>$_POST["r"], ":rating"=>$_POST["r"]));
					}
				}
			}
		}
	}
?>