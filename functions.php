<?php
	// DUPLICATES TWEETS
	function tw_duplicate($mysqli, $out) {
		if ( $result = $mysqli->query('SELECT * FROM `twbot_tw` WHERE id = '.$out->id.';') ) {
			while ( $obj = $result->fetch_object() ) {
				return true;
			}
		}
		if ( $result = $mysqli->query('SELECT * FROM `twbot_ban` WHERE user_id = '.$out->sender->id.';') ) {
			while ( $obj = $result->fetch_object() ) {
				return true;
			}
		}
	}

	function entry_duplicate($mysqli, $query) {
		$duplicate = 0;
		if ( $result = $mysqli->query($query) ) {
			while ( $obj = $result->fetch_object() ) {
				$duplicate = 1;
			}
		}
		return $duplicate;
	}

	// ACCESS MySQL and return ARRAY or FALSE
	function sqlarray($mysqli, $query) {
		if ( $result = $mysqli->query($query) ) {
			if ( is_object( $result ) ) {
				while ( $obj = $result->fetch_object() ) {
					$array[] = $obj;
				}
			}
		}
		return $array;
	}

	// ACCESS TWITTER
	function twitteraccess($mysli, $config, $method, $url, $data) {
		if ($method == 'POST') {
			$twitter = new TwitterAPIExchange($config);
			$return = json_decode($twitter->buildOauth($url, $method)->setPostfields($data)->performRequest());
			if (is_array($return->errors) || is_string($return->errors)) {
				return 'error';
				if (is_array($return->errors)) {
					$mysqli->query("INSERT INTO `".$config['database']."`.`twbot_err` (`message`, `code`) VALUES ('".$return->errors->message."', '".$return->errors->code."',);");
				} elseif (is_string($return->errors)) {
					$mysqli->query("INSERT INTO `".$config['database']."`.`twbot_err` (`message`, `code`) VALUES ('".$return->errors->message."', '".$return->errors->code."',);");
				} else {
					$mysqli->query("INSERT INTO `".$config['database']."`.`twbot_err` (`message`, `code`) VALUES ('".$return->errors."', '',);");
				}
			} else {
				return $return;
			}
		}
		if ($method == "GET") {
			$twitter = new TwitterAPIExchange($config);
			$return = json_decode($twitter->setGetfield($data)->buildOauth($url, $method)->performRequest());
			if ( $url == 'https://api.twitter.com/1.1/search/tweets.json' || $url == 'https://api.twitter.com/1.1/users/show.json' ) {
				foreach ($return as $tweets) {
					return $tweets;
					if (is_array($return->errors)) {
						$mysqli->query("INSERT INTO `".$config['database']."`.`twbot_err` (`message`, `code`) VALUES ('".$return->errors->message."', '".$return->errors->code."',);");
					} elseif (is_string($return->errors)) {
						$mysqli->query("INSERT INTO `".$config['database']."`.`twbot_err` (`message`, `code`) VALUES ('".$return->errors->message."', '".$return->errors->code."',);");
					} else {
						$mysqli->query("INSERT INTO `".$config['database']."`.`twbot_err` (`message`, `code`) VALUES ('".$return->errors."', '',);");
					}
				}
			} else {
				return $return;
				if (is_array($return->errors)) {
					$mysqli->query("INSERT INTO `".$config['database']."`.`twbot_err` (`message`, `code`) VALUES ('".$return->errors->message."', '".$return->errors->code."',);");
				} elseif (is_string($return->errors)) {
					$mysqli->query("INSERT INTO `".$config['database']."`.`twbot_err` (`message`, `code`) VALUES ('".$return->errors->message."', '".$return->errors->code."',);");
				} else {
					$mysqli->query("INSERT INTO `".$config['database']."`.`twbot_err` (`message`, `code`) VALUES ('".$return->errors."', '',);");
				}
			}
		}
	}
?>