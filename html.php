<?php
	$pass = trim(htmlspecialchars(htmlspecialchars_decode($_POST['pass'], ENT_NOQUOTES), ENT_NOQUOTES));

	if ( empty($pass) && empty($_SESSION['login']) ) {
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="robots" content="noindex">
		<title>twitter-robot</title>
	</head>
	<body>
		<h1>twitter-robot</h1>
		<form method="post">
			<label for="pass">Password: </label>
			<input type="password" id="pass" name="pass" size="12" maxlength="12"></input>
			<input type="submit" value="Login"></input></span>
		</form>
	</body>
</html>
<?php
	}
	if ( !empty($pass) ) {
		if ( md5($pass) == $config['htpass'] ) { $_SESSION['login'] = 'logged'; }
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: index.php");
		header("Connection: close");
		exit();
	}
	if ( !empty($_SESSION['login']) ) {
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="robots" content="noindex">
		<title>twitter-robot</title>
		<style>
			body,h1,h2,h3,ul,ol,hr { margin: 0px; }
			hr { width: 100px; }
		</style>
		<script>
			function HideAndShow(divId) {
				if(document.getElementById(divId).style.display == 'none') {
					document.getElementById(divId).style.display='block';
				} else {
					document.getElementById(divId).style.display = 'none';
				}
			}
		</script>
	</head>
	<body>
		<h1>twitter-robot</h1>
		<h2><a onclick="javascript:HideAndShow('how');" href="#">Manual</a></h2>
		<div class="hide" style="display: none;" id="how"><ol>
			<li>add admin @<i>user</i><br />del admin @<i>user</i></li>
			<li>add ban @<i>user</i><br />del ban @<i>user</i></li>
			<li>add rt @<i>user</i><br />del rt @<i>user</i></li>
			<li>add rt #<i>tag</i><br />del rt #<i>tag</i></li>
			<li>add me @<i>user</i><br />del me @<i>user</i></li>
			<li>add <i>shortcode</i> <i>message</i><br />del <i>shortcode</i></li>
		</ol><hr />
		<ul>
			<li>Bot listens <b><sub>1</sub></b>admins.</li>
			<li>Bot ignores <b><sub>2</sub></b>bans.</li>
			<li>Bot retweets <b><sub>4</sub></b>hashtags from <b><sub>3</sub></b>users.</li>
			<li>Bot tweets <b><sub>6</sub></b>message when recieves mentions (max. 90 chars.)<br /> from <b><sub>5</sub></b>users with <b><sub>6</sub></b>shortcode in the beginnig of tweet.</li>
		</ul></div><hr />
		<h2><a onclick="javascript:HideAndShow('dm')" href="#">Admins</a></h2>
		<ul class="hide" style="display: none;" id="dm"><?php foreach (sqlarray($mysqli, 'SELECT * FROM `twbot_dm`;') as $obj) {
			echo '<li>@'.$obj->user_name.' '.$obj->user_id.' <a href="https://twitter.com/'.$obj->user_name."\">link</a></li>\n";
		} ?></ul>
		<h2><a onclick="javascript:HideAndShow('rtu')" href="#">RT @</a></h2>
		<ul class="hide" style="display: none;" id="rtu"><?php foreach (sqlarray($mysqli, 'SELECT * FROM `twbot_rt`;') as $obj) {
			echo '<li>@'.$obj->user_name.' '.$obj->user_id.' <a href="https://twitter.com/'.$obj->user_name."\">link</a></li>\n";
		} ?></ul>
		<h2><a onclick="javascript:HideAndShow('rth')" href="#">RT #</a></h2>
		<ul class="hide" style="display: none;" id="rth"><?php foreach (sqlarray($mysqli, 'SELECT * FROM `twbot_hash`;') as $obj) {
			echo '<li>#'.$obj->hash.' <a href="https://twitter.com/search?q=%23'.$obj->hash."&src=hash\">link</a></li>\n";
		} ?></ul>
		<h2><a onclick="javascript:HideAndShow('meu')" href="#">ME @</a></h2>
		<ul class="hide" style="display: none;" id="meu"><?php foreach (sqlarray($mysqli, 'SELECT * FROM `twbot_me`;') as $obj) {
			echo '<li>@'.$obj->user_name.' '.$obj->user_id.' <a href="https://twitter.com/'.$obj->user_name."\">link</a></li>\n";
		} ?></ul>
		<h2><a onclick="javascript:HideAndShow('short')" href="#">Shortcuts</a></h2>
		<ul class="hide" style="display: none;" id="short"><?php foreach (sqlarray($mysqli, 'SELECT * FROM `twbot_short`;') as $obj) {
			echo '<li>'.$obj->short.' => '.$obj->long."</li>\n";
		} ?></ul>
		<h2><a onclick="javascript:HideAndShow('ban')" href="#" href="#">BAN users</a></h2>
		<ul class="hide" style="display: none;" id="ban"><?php foreach (sqlarray($mysqli, 'SELECT * FROM `twbot_ban`;') as $obj) {
			echo '<li>@'.$obj->user_name.' '.$obj->user_id.' <a href="https://twitter.com/'.$obj->user_name."\">link</a></li>\n";
		} ?></ul>
		<h2><a onclick="javascript:HideAndShow('log')" href="#" href="#">LOG</a></h2>
		<ul class="hide" style="display: none;" id="log"><?php foreach (sqlarray($mysqli, 'SELECT * FROM `twbot_tw` ORDER BY timestamp DESC;') as $obj) {
			echo '<li>'.$obj->text.'<br />'.$obj->timestamp.' <b>'.$obj->type.'</b> @'.$obj->user_name."</li>\n";
		} ?></ul><hr />
		<h2><a href="?type=cron&redirect=index">Cron</a></h2>
		<h2><a href="?type=api">Twitter</a></h2>
	</body>
</html>
<?php
	}
?>
