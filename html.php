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
		</style>
	</head>
	<body>
		<h1>twitter-robot</h1>
		<h2>Manual:</h2>
		<ol>
			<li>add admin @<i>user</i><br />del admin @<i>user</i></li>
			<li>add ban @<i>user</i><br />del ban @<i>user</i></li>
			<li>add @<i>user</i><br />del @<i>user</i></li>
			<li>add #<i>user</i><br />del #<i>user</i></li>
			<li>add <i>shortcode</i> <i>message</i><br />del <i>shortcode</i></li>
		</ol><hr />
		<ul>
			<li>Bot listens <b><sub>1</sub></b>admins.</li>
			<li>Bot ignores <b><sub>2</sub></b>bans.</li>
			<li>Bot retweets <b><sub>4</sub></b>hashtags from <b><sub>3</sub></b>users.</li>
			<li>Bot tweets <b><sub>5</sub></b>message when:<br /> recieve mentions with <b><sub>5</sub></b>shortcode in the beginnig of tweet.</li>
		</ul>
		<h2>Database</h2>
		<h3>Admins</h3>
		<ul><?php foreach (sqlarray($mysqli, 'SELECT * FROM `twbot_dm`;') as $obj) {
			echo '<li>@'.$obj->user_name.' '.$obj->user_id.' <a href="https://twitter.com/'.$obj->user_name."\">link</a></li>\n";
		} ?></ul>
		<h3>RT users</h3>
		<ul><?php foreach (sqlarray($mysqli, 'SELECT * FROM `twbot_rt`;') as $obj) {
			echo '<li>@'.$obj->user_name.' '.$obj->user_id.' <a href="https://twitter.com/'.$obj->user_name."\">link</a></li>\n";
		} ?></ul>
		<h3>BAN users</h3>
		<ul><?php foreach (sqlarray($mysqli, 'SELECT * FROM `twbot_ban`;') as $obj) {
			echo '<li>@'.$obj->user_name.' '.$obj->user_id.' <a href="https://twitter.com/'.$obj->user_name."\">link</a></li>\n";
		} ?></ul>
		<h3>RT hashtags</h3>
		<ul><?php foreach (sqlarray($mysqli, 'SELECT * FROM `twbot_hash`;') as $obj) {
			echo '<li>#'.$obj->hash.' <a href="https://twitter.com/search?q=%23'.$obj->hash."&src=hash\">link</a></li>\n";
		} ?></ul>
		<h3>Shortcuts</h3>
		<ul><?php foreach (sqlarray($mysqli, 'SELECT * FROM `twbot_short`;') as $obj) {
			echo '<li>'.$obj->short.' => '.$obj->long."</li>\n";
		} ?></ul>
		<a href="?type=api"><h2>Twitter</h2></a>
	</body>
</html>
<?php
	}
?>