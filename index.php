<?php
    $type = trim(htmlspecialchars(htmlspecialchars_decode($_GET['type'], ENT_NOQUOTES), ENT_NOQUOTES));
    if ($type == 'cron' || $type == 'debug') {
        require_once('twitter.php');
        include('config.php');
        $twitter = new TwitterAPIExchange($config);
        if ($type == 'debug') {
            include('html.php');
        }
    } else {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: http://czcampuseros.eu/");
        header("Connection: close");
    }
?>