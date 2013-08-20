<?php
	$startmicrotime = MicroTime(1);


	// MENTIONs
	foreach (twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/statuses/mentions_timeline.json', '?count=20') as $out) {
		if ( tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			echo '';

			// OTHERS
			sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->user->id."', '".$out->sender->screen_name."', '".$out->text."', 'me');");
		}
	}


	// HASHTAGs
	foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_hash`;') as $obj ) {
		foreach ( twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/search/tweets.json', '?q=#'.$obj->hash.'&result_type=recent&count=20') as $out ) {
			foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_rt`;') as $usr ) {
				if ( $usr->user_id == $out->user->id && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
					twitteraccess($config, 'POST', 'https://api.twitter.com/1.1/statuses/retweet/'.$out->id.'.json', array());
				}
			}
			sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->user->id."', '".$out->user->screen_name."', '".$out->text."', '#".$obj->hash."');");
		}
	}


	// DMs
	foreach (twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/direct_messages.json', '?count=20') as $out) {
		// ADD ADMIN USER
		if ( preg_match('/^add admin @.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('@', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			foreach (twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
				$duplicate = 0;
				foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_dm` WHERE user_id = '.$user->id.';') as $obj ) {
					$duplicate = 1;
				}
				if ($duplicate !== 1) {
					twitteraccess($config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Success! Admin @".$target." added.", 'user_id' => $out->sender->id));
					sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_dm` (`user_id`, `user_name`) VALUES ('".$user->id."', '".$user->screen_name."');");
					sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'add admin');");
				}
			}
		}


		// DEL ADMIN USER
		if ( preg_match('/^del admin @.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('@', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			foreach (twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
				twitteraccess($config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Success! Admin @".$target." deleted.", 'user_id' => $out->sender->id));
				sqlarray($mysqli, "DELETE FROM `twbot_dm` WHERE user_id = ".$user->id.";");
				sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'del admin');");
			}
		}


		// ADD USER
		if ( preg_match('/^add @.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('@', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			foreach (twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
				$duplicate = 0;
				foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_rt` WHERE user_id = '.$user->id.';') as $obj ) {
					$duplicate = 1;
				}
				if ($duplicate !== 1) {
					twitteraccess($config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Success! @".$target." added.", 'user_id' => $out->sender->id));
					sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_rt` (`user_id`, `user_name`) VALUES ('".$user->id."', '".$user->screen_name."');");
					sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'add @');");
				}
			}
		}


		// DEL USER
		if ( preg_match('/^del @.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('@', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			foreach (twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/users/search.json', '?q='.$target) as $user) {
				twitteraccess($config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Success! @".$target." deleted.", 'user_id' => $out->sender->id));
				sqlarray($mysqli, "DELETE FROM `twbot_rt` WHERE user_id = ".$user->id.";");
				sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'del @');");
			}
		}


		// ADD HASH
		if ( preg_match('/^add #.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('#', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			$duplicate = 0;
			foreach ( sqlarray($mysqli, 'SELECT * FROM `twbot_hash` WHERE hash = '.$target.';') as $obj ) {
				$duplicate = 1;
			}
			if ($duplicate !== 1) {
				twitteraccess($config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Success! #".$target." added.", 'user_id' => $out->sender->id));
				sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_hash` (`id`, `hash`) VALUES (NULL, '".$target."');");
				sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'add #');");
			}
		}


		// DEL HASH
		if ( preg_match('/^del #.*/i', $out->text) && tw_duplicate($mysqli, $out) !== true && !empty($out->id) ) {
			$target = explode('#', $out->text);
			$target = explode(' ', $target[1]);
			$target = $target[0];

			twitteraccess($config, 'POST', 'https://api.twitter.com/1.1/direct_messages/new.json', array('text' => "Success! #".$target." deleted.", 'user_id' => $out->sender->id));
			sqlarray($mysqli, "DELETE FROM `twbot_hash` WHERE hash = '".$target."';");
			sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'del #');");
		}


		// OTHERS
		sqlarray($mysqli, "INSERT INTO `".$config['database']."`.`twbot_tw` (`id`, `user_id`, `user_name`, `text`, `type`) VALUES ('".$out->id."', '".$out->sender->id."', '".$out->sender->screen_name."', '".$out->text."', 'dm');");
	}


	$stopmicrotime = MicroTime(1);
	printf ("\nT: %01.2f sec\n", ($stopmicrotime-$startmicrotime));
?>