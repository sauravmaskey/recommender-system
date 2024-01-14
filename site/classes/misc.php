<?php
	function avg($array) {
		$avg = 0;
		foreach($array as $el) {
			$avg += $el;
		}
		return ($avg/count($array));
	}

	class recoDB {
		private $db;

		function __construct(){
			$this->db = new DBel();
		}

		public function getRating($getBy, $fid, $uid = null){
			$r = array();
			if($getBy == "film") {
				if(count($fid) > 0) {
					$ratings = $this->db->q_with_array("SELECT movieid, rating FROM user_ratings WHERE movieid IN ", $fid);

					foreach($ratings as $rating){
						if(!in_array($rating["movieid"], $r)) {
							$r[$rating["movieid"]][] = $rating["rating"];
						}
					}

					$rating = array_fill_keys($fid, 0);

					foreach(array_keys($r) as $r_) {
						$rating[$r_] = avg($r[$r_]);
					}
					$r = $rating;
				}
			} elseif ($getBy == "user") {
				$add = (!empty($uid))? "userid = " . $uid . " AND " : "";
				$ratings = $this->db->q_with_array("SELECT movieid, rating FROM user_ratings WHERE " . $add . "movieid IN ", $fid, "movieid");

				foreach($ratings as $rating) {
					$r[$rating["movieid"]] = $rating["rating"];
				}
			}
			return $r;
		}

		public function getGenresByMovieIds($ids) {
			$genre = $this->db->q_with_array("SELECT movieid, genrename FROM movie_genres JOIN movie_genre_relations USING (genreid) WHERE movieid IN ", $ids, "movieid");
			$genres = array();

			for($i=0;$i<count($genre);$i++) {
				if(!isset($genres[$genre[$i]["movieid"]])) {
					$genres[$genre[$i]["movieid"]] = $genre[$i]["genrename"];
					$genre[$i] = null;
				}
			}

			foreach($genre as $g) {
				if(!empty($g)) {
					$genres[$g["movieid"]] .= ", " . $g["genrename"];
				}
			}
			return $genres;
		}
}

	class formMaker {
		public function create($what, $how) {
			$form = '<div style="text-align:center;"><form id="'. $what .'" method="'.$how[0].'" action="'.$how[1].'">';
			if($what == "login") {
				$form .= <<<EOT
<label>Username:</label><div><input name="username" autocomplete="off" class="input"></div><label>Password:</label><div><input type="password" name="password" class="input" class=""></div><div style="text-align:center;"><input type="submit" name="submitbtn" value="Sign in"></div></form></div>
EOT;
			} elseif($what == "register") {
				$form .= <<<EOT
<label>First Name:</label><div><input name="fname" class="input"></div>
<label>Last Name:</label><div><input name="lname" class="input"></div>
<label>Username:</label><div><input name="username" class="input"></div>
<label>Email</label><div><input type="email" name="email" class="input"></div>
<label>Password:</label><div>
<input type="password" name="password" class="input"></div>
<div style="text-align:center;"><input type="submit" name="submitbtn" value="Sign up"></div></form></div>
EOT;
			}

			return $form;
		}
	}

	function prepareFilmList($f) {
		$recodb = new recoDB();

		$usrid = (!empty($_SESSION["usrid"]))? $_SESSION["usrid"] : null;
		$genres = $recodb->getGenresByMovieIds(array_column($f, "movieid"));

		$film_ids = array_keys($genres);

		$usr_stars = (!empty($usrid))? $recodb->getRating("user", $film_ids, $usrid) : null;
		$stars = $recodb->getRating("film", $film_ids);

		$user_ratings = array_fill_keys($film_ids, 0);

		foreach(array_keys($user_ratings) as $v) {
			if(isset($usr_stars[$v])) {
				$user_ratings[$v] = $usr_stars[$v];
			}
		}

		foreach(array_keys($f) as $f_) {
		    $title = number_format((float)$stars[$f[$f_]["movieid"]], 2, '.', '');
		    $width = $stars[$f[$f_]["movieid"]] * 16;
		    $fid = $f[$f_]["movieid"];
		    $original = "";

		    $clr = "";
		    if(!empty($usrid) && (in_array($fid, array_keys($usr_stars)))) {
		    	$clr = '<center><form action="rate.php" method="POST"><input name="type" value="clr" hidden><input name="fid" value="' . $f[$f_]['movieid'] .'" hidden><input type="submit" class="clr" value="Clear"></form></center>';
			}

			if($f[$f_]['originalTitle'] != $f[$f_]['primaryTitle']) {
				$original = '<br>(' . $f[$f_]['originalTitle'] . ')';
			}

			//to download posters over the internet:
			// $json = url_get_contents('http://www.omdbapi.com/?apikey=bbcbf298&i='.$f[$f_]["imdbID"]);
			// $obj = json_decode($json, true);
			// $img = ($obj["Poster"] != "N/A")?$obj["Poster"]:null;


			echo <<<EOT
<div id="ffi"><div class="ffi_container"><img src="img/posters/{$f[$f_]["imdbID"]}.jpg" class="ffi_image"><div class="overlay overlayFade"><div class="text"><div class="filmtitle">{$f[$f_]['primaryTitle']}{$original}</div><div class="title">{$f[$f_]['startYear']} {$genres[$fid]}</div><div class="title"><p><span class="stars" title="{$title}"><span style="width: {$width}px;"></span></span></p></div><div class="user-rating"><span class="t">Your Rating:</span><span class="c-rating" name="ffi-{$f[$f_]['movieid']}"></span>{$clr}</div></div></div></div></div>
EOT;
		}			
		echo "</div>";
		return $user_ratings;
	}

	function url_get_contents ($Url) {
	    if (!function_exists('curl_init')){ 
	        die('CURL is not installed!');
	    }
		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $Url);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    $output = curl_exec($ch);
		    curl_close($ch);
		    return $output;
	}

	function recommend(){
		$usrid = $_SESSION["usrid"];
		$command = escapeshellcmd("python __pyscripts/ratings.py " . $usrid);
		$command .= " 2>&1";

		$output = shell_exec($command);
		if($output[0] == "[" && !empty($output)) {
			$filtered = explode("], [", substr($output, 2, -3));

			$predictions = array();

			foreach($filtered as $fi) {
				$pr = explode(",", $fi);
				$predictions[] = array($pr[0], $pr[1]); 
			}
		} else {
			$predictions = 0;
		}
		return $predictions;
	}

	function get_recommendations() {
		$usrid = (!empty($_SESSION["usrid"]))? $_SESSION["usrid"] : null;
		if(isset($usrid)) {
			$recommendations = recommend();
			if(!empty($recommendations)) {
				$r_s = array();
				foreach($recommendations as $recommendation) {$r_s[] = $recommendation[0];}
				$dbel = new DBel();
				
				$r = $dbel->q_with_array("SELECT * FROM films WHERE movieid IN ", $r_s, "movieid");
			} else { echo "Start by rating films you have watched.";}

			return $r;
		}
	}

	function startsWith($h, $n) {
		return (strcasecmp(substr($h, 0, strlen($n)), $n));
	}

	function isort($array, $q) {
		//function to sort search and autocomplete results
		$_1 = array();
		$_2 = array();
		foreach($array as $member) {
			if(startsWith($member, $q) == 0) {
				array_push($_1, $member);
			} else {
				array_push($_2, $member);
			}
		}
		return array_merge($_1, $_2);
	}

?>