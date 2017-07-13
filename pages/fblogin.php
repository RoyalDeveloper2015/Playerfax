<?php
require_once __DIR__ . '/../includes/Facebook/autoload.php';

use Facebook;



$fb = new Facebook\Facebook([
'app_id' => '284975171970993', // Replace {app-id} with your app id
'app_secret' => '10a5f69b5d60f41e5dcbfa6669257f34',
'default_graph_version' => 'v2.2',
]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email','public_profile']; // Optional permissions
$loginUrl = $helper->getLoginUrl('http://localhost/playerfax/index.php?page=fb-callback', $permissions);

echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
 ?>
