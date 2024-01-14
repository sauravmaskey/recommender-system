<?php
 	require_once("config.php");
 	require_once("incls/header.php");
	
	$usrid = (!empty($user))? $_SESSION["usrid"] : null;
 
 	$dbel = new DBel();
 	
	$n = (isset($_GET["i"]))? $_GET["i"] : 24;
 	$films = $dbel->q("SELECT * FROM films ORDER BY RAND() LIMIT :nu", 2, array(":nu" => $n));

 	$user = (!empty($user))? $dbel->q("SELECT username FROM users WHERE id = :uid", 1, array(":uid" => $usrid)) : null;

 	if(!empty($films)) {
?><div id="wrapper"><div id="content"><div id="header"><div class="head"><?php if(!empty($user)) { echo("Hi, " . $user[0]["username"] . "! Rate films you have watched:");} else {echo("Register to get started.");} ?></div><div>Showing <?php echo($n); ?> Movies &middot; <a href="?i=50">50</a> &middot; <a href="?i=100">100</a> &middot; <a href="?i=150">150</a> &middot; <a href="?i=200">250</a></div></div><div id="list"><?php
		$user_ratings = prepareFilmList($films);
		$r = get_recommendations();
 	} else {
 		echo <<<EOT
Please <a href=".">try again</a> or check later.
EOT;
 	}
?><?php if(empty($user)) { echo '<div id="tscreen"><div class="modal"><span class="close">&times;</span><br><span class="message"></span> </div></div>'; } ?> </div><div id="sidebar"><?php if(!empty($r)) {?><div class="head">Recommended:</div><div class="sidebar_list"><?php prepareFilmList($r); }?></div></div><script type="text/javascript">var s=[<?php echo implode(',', $user_ratings); ?>];</script><?php require_once("incls/footer.php");?> 