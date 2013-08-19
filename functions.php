<?php
	// DUPLICATES TWEETS
	function tw_duplicate($mysqli, $out) {
		if ( $result = $mysqli->query('SELECT * FROM `twbot_tw` WHERE id = '.$out->id.';') ) {
			while ( $obj = $result->fetch_object() ) {
				return true;
			}
		}
	}

	// ACCESS MySQL and return ARRAY or FALSE
	function sqlarray($mysqli, $query) {
		if ( $result = $mysqli->query($query) ) {
			if ( is_object( $result ) ) {
				while ( $obj = $result->fetch_object() ) {
					$array[] = $obj;
				}
			} else {
				return false;
			}
		}
		return $array;
	}

	// ACCESS TWITTER
	function twitteraccess($config, $method, $url, $data) {
		if ($method == 'POST') {
			$twitter = new TwitterAPIExchange($config);
			$return = json_decode($twitter->buildOauth($url, $method)->setPostfields($data)->performRequest());
			return $return;
		}
		if ($method == "GET") {
			$twitter = new TwitterAPIExchange($config);
			$return = json_decode($twitter->setGetfield($data)->buildOauth($url, $method)->performRequest());
			if ( $url == 'https://api.twitter.com/1.1/search/tweets.json' ) {
				foreach ($return as $tweets) {
					return $tweets;
				}
			} else {
				return $return;
			}
		}
	}
?>