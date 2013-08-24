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
		header("Location: index.php?type=debug");
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
		<h1>twitter-api</h1>
		<h2>Last:</h2>
		<h3>Mentions</h3>
		<ul><?php foreach (twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/statuses/mentions_timeline.json', '?count=20') as $out) {
			echo '<li>'.$out->text.' '.$out->id.' '.$out->created_at."</li>\n";
		} ?></ul>
		<h3>DMs</h3>
		<ul><?php foreach (twitteraccess($config, 'GET', 'https://api.twitter.com/1.1/direct_messages.json', '?count=20') as $out) {
			echo '<li>'.$out->text.' '.$out->id.' '.$out->created_at."</li>\n";
		} ?></ul>
		<a href="?"><h2>Database</h2></a>
	</body>
</html>
<?php
	}
?>