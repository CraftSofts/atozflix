<?php
require("../includes/core.php");
// get values
$type = $_SESSION['type'];
$email = $_SESSION['email'];
$fb_id = $_SESSION['fb_id'];
$username= $_SESSION['username'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$photo = $_SESSION['photo'];
$red = false;
$redirect_url = $_SESSION['target_url'];
$redirect_url = str_replace(".php","",$redirect_url);
unset($_SESSION['target_url']);
if(!empty($redirect_url))  {
    $red = true;
}
// check if logged in via social network or not
if(!isset($type)) {
    //echo 'Nothing was set in session ';
redirectTo('/');
exit();
} else {
if($type=='fb') {
    $check = $users->isExists('fb_id',$fb_id);
    if($check===false){
        // doesn't exists, register
        $params[] = 'fb_id';
        $values[] = $fb_id;
        if(!empty($first_name)) {
            $params[] = 'first_name';
            $values[] = $first_name;
        }
        if(!empty($last_name)) {
            $params[] = 'last_name';
            $values[] = $last_name;
        }
        if(!empty($email)) {
            $params[] = 'email';
            $values[] = $email;
        }
        $params[] = 'activated';
        $values[] = '1';
        $params[] = 'time';
        $values[] = date("U");
        $users->socialAdd($params,$values);
    }
    // exists
    $users->socialLogin('facebook',$fb_id);
    if($red===false) {
        $_SESSION['social_login'] = 'You are logged in with Facebook successfully';
        $url = '/user/';
    } else {
        $url = $redirect_url;
    }
    redirectTo($url);
    //echo 'Facebook';
} elseif($type=='google') {
    $check = $users->isExists('email',$email);
    if($check===false){
        // doesn't exists, register
        $params[] = 'email';
        $values[] = $email;
        if(!empty($first_name)) {
            $params[] = 'first_name';
            $values[] = $first_name;
        }
        if(!empty($last_name)) {
            $params[] = 'last_name';
            $values[] = $last_name;
        }
        $params[] = 'activated';
        $values[] = '1';
        $params[] = 'time';
        $values[] = date("U");
        $users->socialAdd($params,$values);
    }
     // exists
     $users->socialLogin('google',$email);
     if($red===false) {
        $_SESSION['social_login'] = 'You are logged in with Google successfully';
        $url = '/user/';
    } else {
        $url = $redirect_url;
    }
    redirectTo($url);
     //echo 'Google';
     /*print_R($_SESSION);
     print_r($users->isExists('email',$email))*/
} else {
    redirectTo('/');
    exit();
}
}
?>