<?php
if(!session_id()) {
    session_start();
}
// Include the autoloader provided in the SDK
require_once 'vendor/facebook/graph-sdk/src/Facebook/autoload.php';

// Include required libraries
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

$appId = 'ID'; //Facebook App ID
$appSecret = 'SECRETE'; //Facebook App Secret
$redirectURL = 'https://'.$_SERVER['HTTP_HOST'].'/user/facebook/'; //Callback URL
$fbPermissions = array('email');  //Optional permissions

$fb = new Facebook(array(
'app_id' => $appId,
'app_secret' => $appSecret,
'default_graph_version' => 'v8.0',
));

// Get redirect login helper
$helper = $fb->getRedirectLoginHelper();
$_SESSION['FBRLH_state']=$_GET['state'];

// Try to get access token
try {
// Already login
if (isset($_SESSION['facebook_access_token'])) {
$accessToken = $_SESSION['facebook_access_token'];
} else {
$accessToken = $helper->getAccessToken();
}

if (isset($accessToken)) {
if (isset($_SESSION['facebook_access_token'])) {
$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
} else {
// Put short-lived access token in session
$_SESSION['facebook_access_token'] = (string) $accessToken;

// OAuth 2.0 client handler helps to manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Exchanges a short-lived access token for a long-lived one
$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;

// Set default access token to be used in script
$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
}

// Redirect the user back to the same page if url has "code" parameter in query string
if (isset($_GET['code'])) {

// Getting user facebook profile info
try {
$profileRequest = $fb->get('/me?fields=id,birthday,name,first_name,last_name,email,link,gender,locale,picture');
//year (YYYY) or the month + day (MM/DD)
$fbUserProfile = $profileRequest->getGraphNode()->asArray();
// Here you can redirect to your Home Page.
// processing
$_SESSION['type'] = 'fb';
$_SESSION['email'] = $fbUserProfile['email'];
$_SESSION['username'] = $fbUserProfile['username'];
$_SESSION['first_name'] = $fbUserProfile['first_name'];
$_SESSION['last_name'] = $fbUserProfile['last_name'];
$_SESSION['photo'] = 'http://graph.facebook.com/'.$fbUserProfile['id'].'/picture?height=1280&width=720';
$_SESSION['fb_id'] = $fbUserProfile['id'];
header('Location: /user/social');
// end processing
} catch (FacebookResponseException $e) {
echo 'Graph returned an error: ' . $e->getMessage();
session_destroy();
// Redirect user back to app login page
header("Location: ./");
exit;
} catch (FacebookSDKException $e) {
echo 'Facebook SDK returned an error: ' . $e->getMessage();
exit;
}
}
} else {
// Get login url

$loginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);
header("Location: " . $loginURL);

}
} catch (FacebookResponseException $e) {
echo 'Graph returned an error: ' . $e->getMessage();
exit;
} catch (FacebookSDKException $e) {
echo 'Facebook SDK returned an error: ' . $e->getMessage();
exit;
}
//print_r($_REQUEST);
//print_r($_SESSION);
?>