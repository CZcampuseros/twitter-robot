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
			while ( $obj = $result->fetch_object() ) {
				$array[] = $obj;
			}
		}
		return $array;
	}

	// HASHTAGs
	foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_hash`;') as $obj ) {
		$url = 'https://api.twitter.com/1.1/search/tweets.json';
		$getfield = '?q=#'.$obj->hash.'&result_type=recent&count=20';
		$requestMethod = 'GET';
		$twitter = new TwitterAPIExchange($config);
		foreach (json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest()) as $xout) {
			foreach ($xout as $out) {
				foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_rt`;') as $usr ) {
					if ( tw_duplicate($mysqli, $out) !== true && !empty($out->id) && $usr->user_id == $out->user->id ) {
						$url = 'https://api.twitter.com/1.1/statuses/retweet/'.$out->id.'.json';
						$postfields = array('id' => $out->id);
						$requestMethod = 'POST';
						$twitter = new TwitterAPIExchange($config);
						$json = json_decode($twitter->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest());
						$result = $mysqli->query("INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->user->id."', '".$out->user->screen_name."', '".$out->text."', '#".$obj->hash."');");
					}
				}
			}
		}
	}

	// DMs
	$url = 'https://api.twitter.com/1.1/direct_messages.json';
	$getfield = '?count=20';
	$requestMethod = 'GET';
	$twitter = new TwitterAPIExchange($config);
	foreach (json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest()) as $out) {
		if ( tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			// ADD ADMIN USER
			if ( preg_match('/^add admin @.*/i', $out->text) ) {
				$target = explode('@', $out->text);
				$target = explode(' ', $target[1]);
				$target = $target[0];

				$url = 'https://api.twitter.com/1.1/users/search.json';
				$getfield = '?q='.$target;
				$requestMethod = 'GET';
				$twitter = new TwitterAPIExchange($config);
				foreach (json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest()) as $user) {
					$duplicate = 0;
					foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_dm` WHERE user_id = '.$user->id.';') as $obj ) {
						$duplicate = 1;
					}
					if ($duplicate !== 1) {
						$url = 'https://api.twitter.com/1.1/direct_messages/new.json';
						$postfields = array('text' => "Success! Admin @".$target." added.", 'user_id' => $out->sender->id);
						$requestMethod = 'POST';
						$twitter = new TwitterAPIExchange($config);
						mysqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_dm` (`user_id`, `user_name`) VALUES ('".$user->id."', '".$user->screen_name."');");
						$json = json_decode($twitter->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest());
					}
				}
			}

			// DEL ADMIN USER
			if ( preg_match('/^del admin @.*/i', $out->text) ) {
				$target = explode('@', $out->text);
				$target = explode(' ', $target[1]);
				$target = $target[0];

				$url = 'https://api.twitter.com/1.1/users/search.json';
				$getfield = '?q='.$target;
				$requestMethod = 'GET';
				$twitter = new TwitterAPIExchange($config);
				foreach (json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest()) as $user) {
					$url = 'https://api.twitter.com/1.1/direct_messages/new.json';
					$postfields = array('text' => "Success! Admin @".$target." deleted.", 'user_id' => $out->sender->id);
					$requestMethod = 'POST';
					$twitter = new TwitterAPIExchange($config);
					mysqlarray($mysqli, "DELETE FROM `twbot_dm` WHERE user_id = ".$user->id.";");
					$json = json_decode($twitter->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest());
				}
			}

			// ADD USER
			if ( preg_match('/^add @.*/i', $out->text) ) {
				$target = explode('@', $out->text);
				$target = explode(' ', $target[1]);
				$target = $target[0];

				$url = 'https://api.twitter.com/1.1/users/search.json';
				$getfield = '?q='.$target;
				$requestMethod = 'GET';
				$twitter = new TwitterAPIExchange($config);
				foreach (json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest()) as $user) {
					$duplicate = 0;
					foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_rt` WHERE user_id = '.$user->id.';') as $obj ) {
						$duplicate = 1;
					}
					if ($duplicate !== 1) {
						$url = 'https://api.twitter.com/1.1/direct_messages/new.json';
						$postfields = array('text' => "Success! @".$target." added.", 'user_id' => $out->sender->id);
						$requestMethod = 'POST';
						$twitter = new TwitterAPIExchange($config);
						sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_rt` (`user_id`, `user_name`) VALUES ('".$user->id."', '".$user->screen_name."');");
						$json = json_decode($twitter->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest());
					}
				}
			}

			// DEL USER
			if ( preg_match('/^del @.*/i', $out->text) ) {
				$target = explode('@', $out->text);
				$target = explode(' ', $target[1]);
				$target = $target[0];

				$url = 'https://api.twitter.com/1.1/users/search.json';
				$getfield = '?q='.$target;
				$requestMethod = 'GET';
				$twitter = new TwitterAPIExchange($config);
				foreach (json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest()) as $user) {
					$url = 'https://api.twitter.com/1.1/direct_messages/new.json';
					$postfields = array('text' => "Success! @".$target." deleted.", 'user_id' => $out->sender->id);
					$requestMethod = 'POST';
					$twitter = new TwitterAPIExchange($config);
					sqlarray($mysqli, "DELETE FROM `twbot_rt` WHERE user_id = ".$user->id.";");
					$json = json_decode($twitter->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest());
				}
			}

			// ADD HASH
			if ( preg_match('/^add #.*/i', $out->text) ) {
				$target = explode('#', $out->text);
				$target = explode(' ', $target[1]);
				$target = $target[0];

				$duplicate = 0;
				foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_hash` WHERE hash = '.$hash.';') as $obj ) {
					$duplicate = 1;
				}
				if ($duplicate !== 1) {
					$url = 'https://api.twitter.com/1.1/direct_messages/new.json';
					$postfields = array('text' => "Success! #".$target." added.", 'user_id' => $out->sender->id);
					$requestMethod = 'POST';
					$twitter = new TwitterAPIExchange($config);
					sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_hash` (`id`, `hash`) VALUES (NULL, '".$target."');");
					$json = json_decode($twitter->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest());
				}
			}

			// DEL HASH
			if ( preg_match('/^del #.*/i', $out->text) ) {
				$target = explode('#', $out->text);
				$target = explode(' ', $target[1]);
				$target = $target[0];

				$url = 'https://api.twitter.com/1.1/users/search.json';
				$getfield = '?q='.$target;
				$requestMethod = 'GET';
				$twitter = new TwitterAPIExchange($config);
				foreach (json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest()) as $user) {
					$url = 'https://api.twitter.com/1.1/direct_messages/new.json';
					$postfields = array('text' => "Success! #".$target." deleted.", 'user_id' => $out->sender->id);
					$requestMethod = 'POST';
					$twitter = new TwitterAPIExchange($config);
					sqlarray($mysqli, "DELETE FROM `twbot_hash` WHERE hash = '".$target."';");
					$json = json_decode($twitter->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest());
				}
			}

			sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'dm');");
		}
	}

	// MENTIONs
	$url = 'https://api.twitter.com/1.1/statuses/mentions_timeline.json';
	$getfield = '?count=20';
	$requestMethod = 'GET';
	$twitter = new TwitterAPIExchange($config);
	foreach (json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest()) as $out) {
		if ( tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->user->id."', '".$out->sender->screen_name."', '".$out->text."', 'me');");
		}
	}
?>