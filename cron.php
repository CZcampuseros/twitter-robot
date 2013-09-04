<?php
	// MENTIONs
	foreach (twitteraccess($mysqli, $config, 'GET', 'https://api.twitter.com/1.1/statuses/mentions_timeline.json', '?count=20') as $out) {
		$target = explode(' ', $out->text);
		$short = $target[1];
		unset($target[0], $target[1]);
		$target = implode(' ', $target);
		if (strlen($target) > 100) {
			$target = substr($target, 0, 90).'...';
		}


		// SHORTCUTS
		foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_short`;') as $obj ) {
			foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_me`;') as $usr ) {
				if ( $usr->user_id == $out->user->id && StrToLower($short) == StrToLower($obj->short) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
					$tw = twitteraccess($mysqli, $config, 'POST', 'https://api.twitter.com/1.1/statuses/update.json', array('status' => $obj->long.' '.$target.' (via @'.$out->user->screen_name.')', 'in_reply_to_status_id' => $out->id));
					if ($tw !== 'error') {
						sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->user->id."', '".$out->user->screen_name."', '".$out->text."', 'short');");
					}
				}
			}
		}


		// OTHERS
		if ( $tw !== 'error' && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->user->id."', '".$out->user->screen_name."', '".$out->text."', 'me');");
		}
	}


	// HASHTAGs
	foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_hash`;') as $obj ) {
		foreach ( twitteraccess($mysqli, $config, 'GET', 'https://api.twitter.com/1.1/search/tweets.json', '?q=#'.$obj->hash.'&result_type=recent&count=20') as $out ) {
			foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_rt`;') as $usr ) {
				if ( $usr->user_id == $out->user->id && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
					$tw = twitteraccess($mysqli, $config, 'POST', 'https://api.twitter.com/1.1/statuses/retweet/'.$out->id.'.json', array());
					if ($tw !== 'error') {
						sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->user->id."', '".$out->user->screen_name."', '".$out->text."', '#".$obj->hash."');");
					}
				}
			}
		}
	}


	// DMs
	foreach (twitteraccess($mysqli, $config, 'GET', 'https://api.twitter.com/1.1/direct_messages.json', '?count=20') as $out) {
		// ADD ADMIN USER
		if ( preg_match('/^add admin @.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('@', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			foreach (twitteraccess($mysqli, $config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
				if (entry_duplicate($mysqli, 'SELECT * FROM `twbot_dm` WHERE user_id = '.$user->id.';') !== 1 ) {
					$tw = twitteraccess($mysqli, $config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Admin @".$target." added.", 'user_id' => $out->sender->id));
					if ($tw !== 'error') {
						sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_dm` (`user_id`, `user_name`) VALUES ('".$user->id."', '".$user->screen_name."');");
						sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'add admin');");
					}
				}
			}
		}


		// DEL ADMIN USER
		if ( preg_match('/^del admin @.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('@', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			foreach (twitteraccess($mysqli, $config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
				$tw = twitteraccess($mysqli, $config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Admin @".$target." deleted.", 'user_id' => $out->sender->id));
				if ($tw !== 'error') {
					sqlarray($mysqli, "DELETE FROM `twbot_dm` WHERE user_id = ".$user->id.";");
					sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'del admin');");
				}
			}
		}


		// ADD BAN USER
		if ( preg_match('/^add ban @.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('@', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			foreach (twitteraccess($mysqli, $config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
				if (entry_duplicate($mysqli, 'SELECT * FROM `twbot_ban` WHERE user_id = '.$user->id.';') !== 1 ) {
					$tw = twitteraccess($mysqli, $config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Ban @".$target." added.", 'user_id' => $out->sender->id));
					if ($tw !== 'error') {
						sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_ban` (`user_id`, `user_name`) VALUES ('".$user->id."', '".$user->screen_name."');");
						sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'add ban');");
					}
				}
			}
		}


		// DEL BAN USER
		if ( preg_match('/^del ban @.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('@', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			foreach (twitteraccess($mysqli, $config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
				$tw = twitteraccess($mysqli, $config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Ban @".$target." deleted.", 'user_id' => $out->sender->id));
				if ($tw !== 'error') {
					sqlarray($mysqli, "DELETE FROM `twbot_ban` WHERE user_id = ".$user->id.";");
					sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'del ban');");
				}
			}
		}


		// ADD RT
		if ( preg_match('/^add rt @.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('@', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			foreach (twitteraccess($mysqli, $config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
				if (entry_duplicate($mysqli, 'SELECT * FROM `twbot_rt` WHERE user_id = '.$user->id.';') !== 1 ) {
					$tw = twitteraccess($mysqli, $config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Retweets @".$target." added.", 'user_id' => $out->sender->id));
					if ($tw !== 'error') {
						sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_rt` (`user_id`, `user_name`) VALUES ('".$user->id."', '".$user->screen_name."');");
						sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'add rt @');");
					}
				}
			}
		}


		// DEL RT
		if ( preg_match('/^del rt @.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('@', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			foreach (twitteraccess($mysqli, $config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
				$tw = twitteraccess($mysqli, $config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Retweets @".$target." deleted.", 'user_id' => $out->sender->id));
				if ($tw !== 'error') {
					sqlarray($mysqli, "DELETE FROM `twbot_rt` WHERE user_id = ".$user->id.";");
					sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'del rt @');");
				}
			}
		}


		// ADD ME
		if ( preg_match('/^add me @.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('@', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			foreach (twitteraccess($mysqli, $config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
				if (entry_duplicate($mysqli, 'SELECT * FROM `twbot_me` WHERE user_id = '.$user->id.';') !== 1 ) {
					$tw = twitteraccess($mysqli, $config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Mentions @".$target." added.", 'user_id' => $out->sender->id));
					if ($tw !== 'error') {
						sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_me` (`user_id`, `user_name`) VALUES ('".$user->id."', '".$user->screen_name."');");
						sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'add me @');");
					}
				}
			}
		}


		// DEL ME
		if ( preg_match('/^del me @.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('@', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			foreach (twitteraccess($mysqli, $config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
				$tw = twitteraccess($mysqli, $config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Mentions @".$target." deleted.", 'user_id' => $out->sender->id));
				if ($tw !== 'error') {
					sqlarray($mysqli, "DELETE FROM `twbot_me` WHERE user_id = ".$user->id.";");
					sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'del me @');");
				}
			}
		}


		// ADD HASH
		if ( preg_match('/^add rt #.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('#', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			if (entry_duplicate($mysqli, 'SELECT * FROM `twbot_hash` WHERE hash = '.$target.';') !== 1) {
				$tw = twitteraccess($mysqli, $config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Retweet #".$target." added.", 'user_id' => $out->sender->id));
				if ($tw !== 'error') {
					sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_hash` (`id`, `hash`) VALUES (NULL, '".$target."');");
					sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'add rt #');");
				}
			}
		}


		// DEL HASH
		if ( preg_match('/^del rt #.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('#', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			$tw = twitteraccess($mysqli, $config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Retweet #".$target." deleted.", 'user_id' => $out->sender->id));
			if ($tw !== 'error') {
				sqlarray($mysqli, "DELETE FROM `twbot_hash` WHERE hash = '".$target."';");
				sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'del rt #');");
			}
		}


		// ADD SHORTCUT
		if ( preg_match('/^add [a-z0-9]* .*/i', $out->text) && !preg_match('/^add admin .*/i', $out->text) && !preg_match('/^add ban .*/i', $out->text) && !preg_match('/^add rt .*/i', $out->text) && !preg_match('/^add me .*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode(' ', $out->text);
			$short = explode(' ', $target[1]);
			$short = $short[0];
			unset($target[0], $target[1]);
			$target = implode(' ', $target);

			if (entry_duplicate($mysqli, 'SELECT * FROM `twbot_short` WHERE short = '.$short.';') !== 1) {
				$tw = twitteraccess($mysqli, $config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Shortcut ".$short." added.", 'user_id' => $out->sender->id));
				if ($tw !== 'error') {
					sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_short` (`short`, `long`) VALUES ('".$short."', '".trim($target)."');");
					sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'add short');");
				}
			}
		}


		// DEL SHORTCUT
		if ( preg_match('/^del [a-z0-9]*/i', $out->text) && !preg_match('/^del admin .*/i', $out->text) && !preg_match('/^del ban .*/i', $out->text) && !preg_match('/^del rt .*/i', $out->text) && !preg_match('/^del me .*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode(' ', $out->text);
			$short = explode(' ', $target[1]);
			$short = $short[0];
			unset($target[0], $target[1]);
			$target = implode(' ', $target);

			$tw = twitteraccess($mysqli, $config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Shortcut ".$short." deleted.", 'user_id' => $out->sender->id));
			if ($tw !== 'error') {
				sqlarray($mysqli, "DELETE FROM `twbot_short` WHERE short = '".$short."';");
				sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'del short');");
			}
		}


		// OTHERS
		if ( $tw !== 'error' && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'dm');");
		}
	}
?>