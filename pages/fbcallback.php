<?php
ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.
$cookieParams = session_get_cookie_params(); // Gets current cookies params.
session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], false, true);
session_start();

require_once __DIR__ . '/../includes/Facebook/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => '284975171970993', // Replace {app-id} with your app id
  'app_secret' => '10a5f69b5d60f41e5dcbfa6669257f34',
  'default_graph_version' => 'v2.9',
  ]);

$helper = $fb->getRedirectLoginHelper();

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$fb->setDefaultAccessToken($accessToken);
$response = $fb->get('/me?locale=en_US&fields=name,email,');
$userNode = $response->getGraphUser();
var_dump(
    $userNode->getField('email'), $userNode['email']
);

if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
  exit;
}

// Logged in
echo '<h3>Access Token</h3>';
var_dump($accessToken->getValue());

print_r("\n");
try {
  $response = $fb->get('/me?fields=email',(string)$accessToken);
} catch ( Facebook\Exceptions\FacebookResponseException $e) {
  echo "Graph returned an error: " . $e->getMessage();
}
$user = $response->getGraphUser();
print_r($user);

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
echo '<h3>Metadata</h3>';
var_dump($tokenMetadata);

// Validation (these will throw FacebookSDKException's when they fail)
// $tokenMetadata->validateAppId(284975171970993); // Replace {app-id} with your app id
// // If you know the user ID this access token belongs to, you can validate it here
// //$tokenMetadata->validateUserId('123');
// $tokenMetadata->validateExpiration();
//
// if (! $accessToken->isLongLived()) {
//   // Exchanges a short-lived access token for a long-lived one
//   try {
//     $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
//   } catch (Facebook\Exceptions\FacebookSDKException $e) {
//     echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
//     exit;
//   }
//
//   echo '<h3>Long-lived</h3>';
//   var_dump($accessToken->getValue());
// }



$_SESSION['fb_access_token'] = '';
// $_SESSION['fb_access_token'] = (string) $accessToken;

// User is logged in with a long-lived access token.
// You can redirect them to a members-only page.
//header('Location: https://example.com/members.php');
// $_SESSION['fb_access_token'] = (string) $accessToken;

 ?>
