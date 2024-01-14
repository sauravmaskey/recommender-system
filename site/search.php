<?php
 	require_once("config.php");

 	$q = addslashes($_GET["q"]);

 	require_once("incls/header.php");

 	$q = stripslashes($q);
 	$usrid = (!empty($user))? $_SESSION["usrid"] : null;
 
 	$dbel = new DBel();

 	$needle = "%".$q."%";

 	$films = $dbel->q("SELECT * FROM films WHERE primaryTitle LIKE :needle LIMIT 8", 1, array(":needle" => $needle));
?><div id="wrapper"><div id="content"><div id="header"><div class="head"><?php
 	if(!empty($films)) {
?>You searched for "<?php echo $q ?>".<div class="right"><a href="">Advanced Search</a></div></div></div><div id="list"><?php
		$user_ratings = prepareFilmList($films);
 	} else {
 		echo <<<EOT
Couldn't find anything for "{$q}".<div class="right"><a href="">Advanced Search</a></div></div></div><br><div class="head">Some popular titles:</div><div id="list">
EOT;
		$q = $dbel->q("(SELECT `movieid`, COUNT(`movieid`) AS `occurence` FROM `user_ratings` GROUP BY `movieid` ORDER BY `occurence` DESC LIMIT 18) ORDER BY RAND() LIMIT 12;");
		$f = array();
		foreach($q as $a) {
			$f[] = $a["movieid"];
		}

		$films = $dbel->q_with_array("SELECT * FROM films WHERE movieid IN ", $f, "movieid");

		$user_ratings = prepareFilmList($films); 
	}
?></div><?php if(empty($user)) { echo '<div id="tscreen"><div class="modal"><span class="close">&times;</span><br><span class="message"></span> </div></div>'; } ?><div id="sidebar"></div></div><script type="text/javascript">var s=[<?php echo implode(',', $user_ratings); ?>];</script><?php require_once("incls/footer.php");?> 