<?php

//ini_set('display_errors', true);
//ini_set('display_startup_errors', true);
//error_reporting(E_ALL);

define('URL_UPLOADS_USERS', 'https://playerfax.com/uploads/users/');
define('URL_UPLOADS_PLAYERS', 'https://playerfax.com/uploads/players/');
define('URL_UPLOADS_MEDIA', 'https://playerfax.com/uploads/media/');

$doc_root = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '/home/player/public_html';

define('ABS_PATH', realpath(dirname(__FILE__)) . '/');
define('REL_PATH', $doc_root . '/');
define('UPLOADS_USERS', 'uploads/users/');
define('UPLOADS_PLAYERS', 'uploads/players/');
define('UPLOADS_MEDIA', 'uploads/media/');

date_default_timezone_set('America/Phoenix');

$db_host_main = 'localhost';
$db_user_main = 'player_mainuser';
$db_pass_main = 'P*#{7[-NH,0sqh5YWz:t';
$db_name_main = 'player_main';


