<?php
require('../includes/core.php');
$title = 'Register - '.SITE_NAME.'';
$description = 'Register a new account at '.SITE_NAME.' to enjoy all features';
$keywords = 'register,new,create,acccount,'.strtolower(SITE_NAME).'';
$image_captcha = $_SESSION['captcha'];
unset($_SESSION['captcha']);
$form = new Form;
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
$_SESSION['FBRLH_state']=$_GET['state'];
$loginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);

// google
require_once('google/config.php');

if(isLoggedIn()) {
  redirectTo("/user/account");
  exit();
} else {
Page::header($title,$description,$keywords);
if ($form->method()=='POST') {
  $username = $form->post('username');
  $first_name = $form->post('first_name');
  $last_name = $form->post('last_name');
  $password = $form->post('password');
  $password_confirm = $form->post('password_confirm');
  $email = $form->post('email');
  $captcha = $form->post('captcha');
  $username_pattern = '/^[a-z][a-z0-9]*_?[a-z0-9]+$/i'; // username should start with alphabet and can containt alphanumeric chars and underscores. but can't contain a underscore at the end
  $name_pattern = '/^[a-zA-Z]+$/m'; // first name and last name can only contains chars
  $show_form = '';
  $error[] = '';
  $message = '';
  $script = ' ';
  if(strlen($username) < 3||strlen($username) > 20) {
    $errors[] = 'Username can\'t be less than 3 or more than 20 characters';
  } else {
    if(!preg_match($username_pattern,$username)) {
      $errors[] = 'Username can start with alphabet or can containt alphanumeric characters and a underscore';
    } else {
      if($users->isExists('username',$username)) {
        $errors[] = 'Username is not available, try choosing other username or adding numbers at the end';
      }
    }
  }
  if (strlen($first_name) < 3||strlen($first_name) > 10) {
    $errors[] = 'First name can\'t be less than 3 or more than 10 characters';
  } else {
    if(!preg_match($name_pattern,$first_name)) {
      $errors[] = 'First name can contain alphabets only';
    }  
  }
  if (strlen($last_name) < 3||strlen($last_name) > 10) {
    $errors[] = 'Last name can\'t be less than 3 or more than 10 characters';
  } else {
    if(!preg_match($name_pattern,$last_name)) {
      $errors[] = 'Last name can contain alphabets only';
    }  
  }
  if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'The email address is invalid';
  } else {
    if($users->isExists('email',$email)) {
      $errors[] = 'The email address was already registered';
    }  
  }
  if($password_confirm!==$password) {
    $errors[] = 'Both passwords doesn\'t match';
  }
  if(strlen($password) < 6||strlen($password) > 32) {
    $errors[] = 'Password can\'t be less then 6 or more than 32 characters';
  }
  if($captcha!=$image_captcha) {
    $errors[] = 'The security code didn\'t match with the image, please try again';
  }
  // check for errors
  if(empty($errors)) {
    $data = array('username'=>$username,'first_name'=>$first_name,'last_name'=>$last_name,'email'=>$email,'password'=>$password,'password_confirm'=>$password_confirm);
    if($users->addUser($data,true)) {
      $domain_name = substr(strrchr($email, "@"), 1);
      $show_form = 'no';
      echo '<div class="row">
      <div class="col s12 l8 push-l2" method="post">
          <div class="row card-panel grey darken-3">
              <div class="col s12"><h1>Register</h1></div>
              <div class="col s12"><i class="material-icons middled green-text">check_circle</i> You are now registered successfuly! An activation email has been sent to your address: <span class="bold-text">'.$email.'</span>. Please check your <a href="https://'.$domain_name.'">inbox</a>. After activating your accaount, you can login.</div>
          </div>
      </div>';
    } else {
      $message = '<ul><li><i class="material-icons middled red-text">error</i> Something went wrong! Please contact system administration.</li></ul>';
    }
  } else {
    $message = '<ul>';
    foreach($errors as $error)
    $message .= '<li><i class="material-icons middled red-text">error</i> '.$error.'</li>';
    $message .= '</ul>';
  }
}
  if(empty($show_form)) {
    if(!empty($form->get('email'))) { $autofill_email = ' value="'.$form->get('email').'"'; } else { $autofill_email = ''; }
    if(!empty($form->get('username'))) { $autofill_username = ' value="'.$form->get('username').'"'; } else { $autofill_username = ''; }
?>
<div class="row">
    <form class="col s12 l8 push-l2" method="post">
        <div class="row card-panel grey darken-3">
            <div class="col s12"><h1>Register</h1></div>
            <?php if(!empty($message)) echo '<div class="col s12">'.$message.'</div>'; ?>
            <div class="input-field col s12">
            <i class="material-icons prefix">person</i>
            <input id="username" name="username" type="text"<?=$autofill_username;?> required>
          <label for="username">Username</label>
          <div id="username_check"></div>
            </div>
            <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">person_outline</i>
            <input id="first_name" name="first_name" type="text" required>
          <label for="first_name">First Name</label>
          <div id="first_name_check"></div>
            </div>
            <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">person_outline</i>
            <input id="last_name" name="last_name" type="text" required>
          <label for="last_name">Last Name</label>
          <div id="last_name_check"></div>
            </div>
            <div class="input-field col s12">
            <i class="material-icons prefix">email</i>
            <input id="email" name="email" type="email"<?=$autofill_email;?> required>
          <label for="email">Email</label>
          <div id="email_check"></div>
            </div>
            <div class="input-field col s12">
            <i class="material-icons prefix">lock</i>
            <input id="password" name="password" type="password" required>
          <label for="password">Password</label>
          <div id="password_check"></div>
            </div>
            <div id="hidden-div">
          <div class="bold-text">Password should contain:</div>
          <p id="letter" class="red-text">A <b>lowercase</b> letter</p>
          <p id="capital" class="red-text">A <b>capital (uppercase)</b> letter</p>
          <p id="number" class="red-text">A <b>number</b></p>
          <p id="special" class="red-text">A <b>special character</b></p>
          <p id="length" class="red-text">Minimum <b>6 characters</b></p>
          </div>
            <div class="input-field col s12">
            <i class="material-icons prefix">lock</i>
            <input id="password_confirm" name="password_confirm" type="password" required>
          <label for="password_confirm">Password Confirm</label>
          <div id="password_confirm_check"></div>
            </div>
            <div class="input-field col s12"><p><label><input type="checkbox" id="show"/><span>Show Passwords</span></label></p></div>
            <div class="input-field col s6"><i class="material-icons prefix">receipt</i> <input id="captcha" name="captcha" type="text" required>
          <label for="captcha">Code</label></div>
            <div class="col s6 valign-wrapper"><img src="/captcha" id="captcha_image"> <button id="reload_btn" class="btn waves-effect waves-light right" type="button"><i class="material-icons">refresh</i></button></div>
            <div class="col s12"><p><i class="material-icons middled">done_all</i> By registering, you are agreeing with <a href="/tos" target="_blank">our terms of service <i class="material-icons middled">launch</i></a> and <a href="/privacy-policy" target="_blank">privacy policy <i class="material-icons middled">launch</i></a></p></div>
            <div class="col s12"><button type="submit" name="btn_login" class="btn waves-effect waves-light">Register</button></div>
        <div class="col s12"><h1>Connect With</h1></div>
        <div class="col s6"><a href="<?=$google_client->createAuthUrl();?>">Google</a></a></div>
        <div class="col s6"><a href="<?=$loginURL;?>">Facebook</a></div>
        <div class="col s12"><h1>Account</h1></div>
        <div class="col s6"><a href="/user/login">Login</a></div>
        <div class="col s6"><a href="/user/forgot-password">Forgot Password?</a></div>
    </form>
</div>
<?php
  $script = '<script src="/assets/js/register.js"></script>';
  }
}
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/user">User</a></li>
<li>Register</li>
</ul>';
Page::footer($script,$extra);
?>