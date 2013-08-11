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
			<input type="password" id="pass" name="pass" size="12" maxlength="12" ></input>
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
			body,h1,h2,ul { margin: 0px; }
		</style>
	</head>
	<body>
		<h1>twitter-robot</h1>
		<h2>Last mentions:</h2>
		<ul>
		<?php
			$url = 'https://api.twitter.com/1.1/statuses/mentions_timeline.json';
			$getfield = '?count=20';
			$requestMethod = 'GET';
			$twitter = new TwitterAPIExchange($config);
			foreach (json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest()) as $out) {
				echo '<li>'.$out->created_at.' "'.$out->text.'" '.$out->id.'</li>';
			}
		?>
		</ul>
		<h2>Last DMs:</h2>
		<ul>
		<?php
			$url = 'https://api.twitter.com/1.1/direct_messages.json';
			$getfield = '?count=20';
			$requestMethod = 'GET';
			foreach (json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest()) as $out) {
				echo '<li>'.$out->created_at.' "'.$out->text.'" '.$out->id.'</li>';
			}
		?>
		</ul>
	</body>
</html>
<?php
	}
?>