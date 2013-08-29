<?php
    ob_start();
    chdir(dirname($_SERVER['SCRIPT_FILENAME']));
    session_start();
    error_reporting(0);
    $startmicrotime = MicroTime(1);

    $type = trim(htmlspecialchars(htmlspecialchars_decode($_GET['type'], ENT_NOQUOTES), ENT_NOQUOTES));
    $redirect = trim(htmlspecialchars(htmlspecialchars_decode($_GET['redirect'], ENT_NOQUOTES), ENT_NOQUOTES));

    include('config.php');
    include('functions.php');

    if ( !empty($config['server']) && !empty($config['username']) && !empty($config['password']) && !empty($config['database']) ) {
        $mysqli = new mysqli($config['server'], $config['username'], $config['password'], $config['database']);
    }

    require_once('twitter.php');

    if ( $type == 'cron' ) {
        include('cron.php');
        if ( $redirect == 'index' ) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: index.php");
            header("Connection: close");
        }
    } elseif ( $type == 'api' ) {
        include('html_api.php');
    } else {
        include('html.php');
    }
    $stopmicrotime = MicroTime(1);
    //printf ("T: %01.2f sec", ($stopmicrotime-$startmicrotime));
?>