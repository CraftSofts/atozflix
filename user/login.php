<?php
// include the core file
require('../includes/core.php');
// set variables
$form = new Form;
$title = 'Login - '.SITE_NAME.'';
$description = 'Login at '.SITE_NAME.' to enjoy all features';
$keywords = 'login,acccount,'.strtolower(SITE_NAME).'';
$username = $form->post('user');
$password = $form->post('password');
$script = '';
$remember = '';
// facebook
require_once(''.__DIR__.'/facebook/vendor/facebook/graph-sdk/src/Facebook/autoload.php');
// Include required libraries
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
// set variables
$appId = '648279382486368'; //Facebook App ID
$appSecret = '04e1f66eb3b10a4924a7a12787afe772'; //Facebook App Secret
$redirectURL = 'https://atozflix.com/user/facebook/'; //Callback URL
$fbPermissions = array('email');  //Optional permissions
// initialize
$fb = new Facebook(array(
'app_id' => $appId,
'app_secret' => $appSecret,
'default_graph_version' => 'v8.0',
));
// Get redirect login helper
$helper = $fb->getRedirectLoginHelper();
$_SESSION['FBRLH_state']=$form->post('state');
$loginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);

// google
require_once('google/config.php');

if($form->isExists('remember')||!empty($form->post('remember'))) {
    $remember = true;
} else {
    $remember = false;
}
// check if user is logged in
if(isloggedIn()) {
    // redirect to login page
    redirectTo('/user/');
    exit();
} else {
    $show_form = '';
    // processs login
    if($form->method()=='POST') {
        // process form
        if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
            // email
            $param = 'email';
        } else {
            // username
            $param = 'username';
        }
        $check = $users->login($username,$password,$remember);
        // print header 
    Page::header($title,$description,$keywords);
        if($check['result']=='success') {
            $redirect_url = $_SESSION['target_url'];
            $redirect_url = str_replace(".php","",$redirect_url);
            unset($_SESSION['target_url']);
            if(!empty($redirect_url))  {
                $targrt_url ='You will be redirected to the requested page now. <a href="'.$redirect_url.'">Click here</a> if you are not redirected in few seconds.'.redirectTo($redirect_url,2).'<br/><div class="progress"><div class="indeterminate"></div></div>';
            } else {
                $targrt_url = 'You may visit the <a href="/user/account">account</a> page now.';
            }
            // successful
            ?>
<div class="row">
    <div class="col s12 l8 push-l2">
        <div class="row card-panel">
            <div class="col s12"><h1>Login</h1></div>
            <p class="col s12"><i class="green-text material-icons middled">check_circle</i> You are logged in successfuly! <?=$targrt_url;?></p>
        </div>
    </div>
</div>
            <?php
            $show_form = 'no';
        } else {
            $message = '<div class="col s12"><i class="red-text material-icons middled">error</i> '.$check['reason'].'</div>';
            }
    } else {
        // print header 
        Page::header($title,$description,$keywords);
    }
        // show form
        if(empty($show_form)) {
            $script = '<script>
                var show = document.getElementById("show");
                var password = document.getElementById("password");
                show.addEventListener("change", function() {
                    if(password.type === "password" && show.checked === true) {
                        password.type = "text";
                    } else {
                        password.type = "password";
                    }
                });
                </script>';
        ?>
<div class="row">
    <form class="col s12 l8 push-l2" method="post">
        <div class='row card-panel grey darken-3'>
            <div class='col s12'><h1>Login</h1></div>
            <?php
            if(!empty($_SESSION['login_msg'])) echo '<div class="col s12"><p><i class="material-icons middled">info</i> '.$_SESSION['login_msg'].'</p></div>';
            if(!empty($message)) echo $message; 
          ?>
            <div class='input-field col s12'>
            <i class="material-icons prefix">person</i>
            <input id="user" type="text" name="user">
          <label for="user">Email or Username</label>
            </div>
            <div class='input-field col s12'>
            <i class="material-icons prefix">lock</i>
            <input id="password" type="password" name="password">
          <label for="password">Password</label>
            </div>
            <div class="input-field col s12"><p><label><input type="checkbox" class="checkbox" id="show"/><span>Show Passwords</span></label></p>
            <p><label><input type="checkbox" class="checkbox" id="remember" name="remember"/><span>Remember this browser</span></label></p></div>
            <div class="col s12"><button type='submit' name='btn_login' class='btn waves-effect waves-light'>Login</button></div>
        <div class="col s12"><h1>Connect With</h1></div>
        <div class="col s6"><a href="<?=$google_client->createAuthUrl();?>">Google</a></a></div>
        <div class="col s6"><a href="<?=$loginURL;?>">Facebook</a></div>
        <div class="col s12"><h1>Account</h1></div>
        <div class="col s6"><a href="/user/register">Register</a></div>
        <div class="col s6"><a href="/user/forgot-password">Forgot Password?</a></div>
    </form>
</div>
<?php
        }
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/user">User</a></li>
<li>Login</li>
</ul>';
    Page::footer($script,$extra);
}
?>