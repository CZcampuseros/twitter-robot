<?php
	function tw_duplicate($mysqli, $out) {
		if ( $result = $mysqli->query('SELECT * FROM `twbot_tw` WHERE id = '.$out->id.';') ) {
			while ( $obj = $result->fetch_object() ) {
				return true;
			}
		}
	}
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
	function twitteraccess($config, $method, $url, $data) {
		if ($method == 'POST') {
			$twitter = new TwitterAPIExchange($config);
			return json_decode($twitter->buildOauth($url, $method)->setPostfields($data)->performRequest());
		}
		if ($method == "GET") {
			$twitter = new TwitterAPIExchange($config);
			return json_decode($twitter->setGetfield($data)->buildOauth($url, $method)->performRequest());
		}
	}

	// HASHTAGs
	foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_hash`;') as $obj ) {
		foreach ( twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/search/tweets.json', '?q=#'.$obj->hash.'&result_type=recent&count=20') as $xout ) {
			foreach ($xout as $out) {
				foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_rt`;') as $usr ) {
					if ( tw_duplicate($mysqli, $out) !== true && !empty($out->id) && $usr->user_id == $out->user->id ) {
						twitteraccess($config, 'POST', 'https://api.twitter.com/1.1/statuses/retweet/'.$out->id.'.json', array());
						sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->user->id."', '".$out->user->screen_name."', '".$out->text."', '#".$obj->hash."');");
					}
				}
			}
		}
	}

	// DMs
	foreach (twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/direct_messages.json', '?count=20') as $out) {
		if ( tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			// ADD ADMIN USER
			if ( preg_match('/^add admin @.*/i', $out->text) ) {
				$target = explode('@', $out->text);
				$target = explode(' ', $target[1]);
				$target = $target[0];

				foreach (twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
					$duplicate = 0;
					foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_dm` WHERE user_id = '.$user->id.';') as $obj ) {
						$duplicate = 1;
					}
					if ($duplicate !== 1) {
						sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_dm` (`user_id`, `user_name`) VALUES ('".$user->id."', '".$user->screen_name."');");
						twitteraccess($config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Success! Admin @".$target." added.", 'user_id' => $out->sender->id));
					}
				}
			}

			// DEL ADMIN USER
			if ( preg_match('/^del admin @.*/i', $out->text) ) {
				$target = explode('@', $out->text);
				$target = explode(' ', $target[1]);
				$target = $target[0];

				foreach (twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
					sqlarray($mysqli, "DELETE FROM `twbot_dm` WHERE user_id = ".$user->id.";");
					twitteraccess($config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Success! Admin @".$target." deleted.", 'user_id' => $out->sender->id));
				}
			}

			// ADD USER
			if ( preg_match('/^add @.*/i', $out->text) ) {
				$target = explode('@', $out->text);
				$target = explode(' ', $target[1]);
				$target = $target[0];

				foreach (twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
					$duplicate = 0;
					foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_rt` WHERE user_id = '.$user->id.';') as $obj ) {
						$duplicate = 1;
					}
					if ($duplicate !== 1) {
						sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_rt` (`user_id`, `user_name`) VALUES ('".$user->id."', '".$user->screen_name."');");
						twitteraccess($config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Success! @".$target." added.", 'user_id' => $out->sender->id));
					}
				}
			}

			// DEL USER
			if ( preg_match('/^del @.*/i', $out->text) ) {
				$target = explode('@', $out->text);
				$target = explode(' ', $target[1]);
				$target = $target[0];

				foreach (twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
					sqlarray($mysqli, "DELETE FROM `twbot_rt` WHERE user_id = ".$user->id.";");
					twitteraccess($config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Success! @".$target." deleted.", 'user_id' => $out->sender->id));
				}
			}

			// ADD HASH
			if ( preg_match('/^add #.*/i', $out->text) ) {
				$target = explode('#', $out->text);
				$target = explode(' ', $target[1]);
				$target = $target[0];

				$duplicate = 0;
				foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_hash` WHERE hash = '.$target.';') as $obj ) {
					$duplicate = 1;
				}
				if ($duplicate !== 1) {
					sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_hash` (`id`, `hash`) VALUES (NULL, '".$target."');");
					twitteraccess($config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Success! #".$target." added.", 'user_id' => $out->sender->id));
				}
			}

			// DEL HASH
			if ( preg_match('/^del #.*/i', $out->text) ) {
				$target = explode('#', $out->text);
				$target = explode(' ', $target[1]);
				$target = $target[0];

				sqlarray($mysqli, "DELETE FROM `twbot_hash` WHERE hash = '".$target."';");
				twitteraccess($config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Success! #".$target." deleted.", 'user_id' => $out->sender->id));
			}

			sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'dm');");
		}
	}

	// MENTIONs
	foreach (twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/statuses/mentions_timeline.json', '?count=20') as $out) {
		if ( tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->user->id."', '".$out->sender->screen_name."', '".$out->text."', 'me');");
		}
	}
?>