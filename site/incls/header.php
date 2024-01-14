<!DOCTYPE html>
<html>
<head>
	<title>Recommendation System</title>  
	<meta charset='utf-8'>
  	<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<link rel="stylesheet" type="text/css" href="styles.css">
	<link rel="stylesheet" type="text/css" href="rating.min.css">
	<link rel="stylesheet" type="text/css" href="jquery-ui.css">
</head>
<body><header><nav><div id="search"><form action="search.php" id="s" method="GET"><input id="q" placeholder="Search..." name="q" value="<?php if(isset($q)) {echo stripslashes($q);} ?>" autocomplete="off"><input type="submit" id="hsubmit" value="GO"></form><div style="margin: 10px"><a href="index.php">Home</a> &middot;<?php if(empty($user)) { ?> <a href="login.php">Login</a> &middot; <a href="register.php">Register</a><?php } else { ?> <a href="profile.php">My Ratings</a> &middot; <a href="login.php?out">Logout</a> <?php } ?></div></div></nav></header>