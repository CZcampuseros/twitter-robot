<?php
    ob_start();
    chdir(dirname($_SERVER['SCRIPT_FILENAME']));
    session_start();
    error_reporting(E_ALL ^ E_NOTICE);

    $type = trim(htmlspecialchars(htmlspecialchars_decode($_GET['type'], ENT_NOQUOTES), ENT_NOQUOTES));

    if ( $type == 'cron' || $type == 'debug' ) {
        include('config.php');

        if ( !empty($config['server']) && !empty($config['username']) && !empty($config['password']) && !empty($config['database']) ) {
            $mysqli = new mysqli($config['server'], $config['username'], $config['password'], $config['database']);
        }

        require_once('twitter.php');

        if ( $type == 'debug' ) {
            include('html.php');
        }
        if ( $type == 'cron' ) {
            include('cron.php');
        }
    } else {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: http://czcampuseros.eu/");
        header("Connection: close");
    }
?>