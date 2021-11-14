<?php
// include core file
require('../includes/core.php');
// set variables
$form = new Form;
$email = $form->request('email');
$key = $form->request('key');
$captcha = $form->request('captcha');
$email = $form->request('email');
$image_captcha = $_SESSION['captcha'];
unset($_SESSION['captcha']);
$message = '';
$title = '';
$script = '';
Page::header('Account Activation - '.SITE_NAME, 'Activate your account to after registering here');
// chek if user is logged in
if(isLoggedIn()) {
    // user is visiting an invalid link
    echo '<div class="row">
    <div class="col s12 l8 push-l2">
    <div class="row card-panel grey darken-3">
    <div class="col s12"><h1>Activate Account</h1></div>
    <p><i class="material-icons middled red-text">error</i> Your have visited an invalid link. Please go to <a href="/">home page</a></p>
    </div></div></div>';
} else {
    // process resending of activation email
     if($form->get('resend')=='email') {
        if(!empty($form->get('email'))) { $autofill_email = ' value="'.$form->get('email').'"'; } else { $autofill_email = ''; }
         // received request for resending activation email
         $title = 'Resend Activation - '.SITE_NAME.'';
         Page::header($title);
         // if form submitted
         if ($form->method()=='POST') {
             // try to resend activation
			echo '<div class="row">
			<div class="col s12 l8 push-l2">
			<div class="row card-panel grey darken-3">
			<div class="col s12"><h1>Resend Activation</h1></div>
			<div class="col s12">';
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				echo '<i class="material-icons middled red-text">error</i> Sorry, The email was invalid! Please <a href="/user/activate?resend=email">Try again</a>.';
			} else {
                // check that email isn't activated
				if($users->isExists('email',$email,' AND activated=\'0\'')) {
					if($captcha==$image_captcha) {
						$users->resendActivation($email);
						$domain_name = substr(strrchr($email, "@"), 1);
						echo '<i class="material-icons middled green-text">check_circle</i> An activation email has been sent to your address: <span class="bold-text">'.$email.'</span>. Please check your <a href="https://'.$domain_name.'">inbox</a>.';
					} else {
						echo '<i class="material-icons middled red-text">error</i> Sorry, The code didn\'t match with the image! Please <a href="/user/activate?resend=email">Try again</a>.';
					}
				} else {
					echo '<i class="material-icons middled red-text">error</i> Sorry, The email was already activated. Please proceed <a href="/user/login">login</a>.<br/>';
				}
			}
			echo '</div>
			</div>
			</div></div>';
         } else {
             // show form
             echo '<div class="row">
            <form method="post" action="/user/activate?resend=email" class="col s12 l8 push-l2">
            <div class="row card-panel grey darken-3">
            <div class="col s12"><h1>Resend Activation</h1></div>
            <div class="input-field col s12"><i class="material-icons prefix">email</i> <input type="email" name="email" id="email"'.$autofill_email.' required autocomplete="email"><label for="email">Email</label></div>
            <p class="col s12">If you didn\'t get an email with activation link after creating an account, please enter your email here to get it again.</p>
            <div class="input-field col s6"><i class="material-icons prefix">receipt</i> <input id="captcha" name="captcha" type="text" required><label for="captcha">Code</label></div>
            <div class="col s6 valign-wrapper"><img src="/captcha" id="captcha_image"> <button id="reload_btn" class="btn waves-effect waves-light right" type="button"><i class="material-icons">refresh</i></button></div>
            <div class="col s12"><button type="submit" class="btn waves-light waves-effect">Resend</button></div>
            </div>
            </form>
            </div>';
            $script = '<script>
            var reloadBtn = document.getElementById("reload_btn");
            reloadBtn.addEventListener("click", function() {
                var img = document.getElementById("captcha_image");
                console.log(img);
                img.setAttribute("src","/captcha?rand=" + Math.random());
            });
            </script>';
         }
     } else {
         // process activation
         if (!empty($form->get('email'))&&!empty($form->get('key'))) {
            // activation process
            $title = 'Account Activation - '.SITE_NAME.'';
            Page::header($title);
            echo '<div class="row card-panel grey darken-3">
            <div class="col s12 l8 push-l2"> 
            <div class="col s12"><h1>Account Activation</h1></div>
            <div class="col s12">';
            if(isLoggedin()) {
                echo '<i class="material-icons middled">error</i> Sorry, You are currently logged in! Can not send activation email right now<br/>.';
            } else {
                if(!$users->isExists('email',$email)) {
                    echo '<i class="material-icons middled red-text">error</i> Sorry, The email wasn\'t resgistered here!<br/>';
                } elseif($users->isExists('email',$email,' AND activated=1')) {
                    echo '<i class="material-icons middled">info</i> The account is already activated! Please proceed <a href="/user/login">login</a>.<br/>';
                } elseif (!$users->isExists('keyhash',$key)) {
                    echo '<i class="material-icons middled red-text">error</i> Sorry, The keyhash is invalid!<br/>';
                } else {
                    if($users->activateUser($email,$keyhash)) {
                        echo '<i class="material-icons middled green-text">check_circle</i> The account has been activated successfuly! Please proceed <a href="/user/login">login</a>.<br/>';
                    } else {
                        echo '<i class="material-icons middled red-text">error</i> Sorry, the account couldn\'t be activated! Please contact system administration.<br/>';
                    }
                }
            }
            echo '</div></div></div>';
        } else {
            redirectTo("/user/");
            exit();
        }
     }
}
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/user">User</a></li>
<li>Account Activation</li>
</ul>';
Page::footer($script,$extra);