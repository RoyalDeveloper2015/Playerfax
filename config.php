<?php

//ini_set('display_errors', true);
//ini_set('display_startup_errors', true);
//error_reporting(E_ALL);

// define('URL_UPLOADS_USERS', 'https://playerfax.com/uploads/users/');
define('URL_UPLOADS_USERS', 'http://localhost/playerfax/uploads/users/');
define('URL_UPLOADS_PLAYERS', 'https://playerfax.com/uploads/players/');
define('URL_UPLOADS_MEDIA', 'https://playerfax.com/uploads/media/');

$doc_root = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '/home/player/public_html';

define('ABS_PATH', realpath(dirname(__FILE__)) . '/');
define('REL_PATH', $doc_root . '/');
define('UPLOADS_USERS', 'uploads/users/');
define('UPLOADS_PLAYERS', 'uploads/players/');
define('UPLOADS_MEDIA', 'uploads/media/');

// define('FB_APP_ID','284975171970993');
// define('FB_APP_SECRET','10a5f69b5d60f41e5dcbfa6669257f34');

// define('FB_APP_ID','326610554461411');
// define('FB_APP_SECRET','a17d1c5457572221ed02677752866a02');
$server_url = $_SERVER['HTTP_REFERER'];
define('FB_CALLBACK',$server_url);
define('FB_LOGIN_URL','http://localhost/playerfax/index.php?page=getFbInfo');


date_default_timezone_set('America/Phoenix');

$db_host_main = '127.0.0.1';
$db_user_main = 'root';
$db_pass_main = '';
$db_name_main = 'player_main';
