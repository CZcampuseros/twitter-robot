<?php
	function tw_duplicate($mysqli, $out) {
		if ( $result = $mysqli->query('SELECT * FROM `twbot_tw` WHERE id = '.$out->id.';') ) {
			while ( $obj = $result->fetch_object() ) {
				return true;
			}
		}
	}

	$url = 'https://api.twitter.com/1.1/direct_messages.json';
	$getfield = '?count=20';
	$requestMethod = 'GET';
	$twitter = new TwitterAPIExchange($config);
	foreach (json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest()) as $out) {
		if ( tw_duplicate($mysqli, $out) !== true ) {
			$url = 'https://api.twitter.com/1.1/direct_messages/new.json';
			$postfields = array('text' => date('U'), 'user_id' => $out->sender->id);
			$requestMethod = 'POST';
			$twitter = new TwitterAPIExchange($config);
			$json = json_decode($twitter->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest());

			$result = $mysqli->query("INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'dm');");
		}
	}

	$url = 'https://api.twitter.com/1.1/statuses/mentions_timeline.json';
	$getfield = '?count=20';
	$requestMethod = 'GET';
	$twitter = new TwitterAPIExchange($config);
	foreach (json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest()) as $out) {
		if ( tw_duplicate($mysqli, $out) !== true ) {
			$result = $mysqli->query("INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->user->id."', '".$out->sender->screen_name."', '".$out->text."', 'me');");
		}
	}
?>