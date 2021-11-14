<?php
// include the core file
require('../includes/core.php');
// set variables
$form = new Form;
$email = $form->request('email');
$captcha = $form->request('captcha');
$image_captcha = $_SESSION['captcha'];
unset($_SESSION['captcha']);
$title = 'Forgot Password - '.SITE_NAME.'';
$description = 'Reset your account password at '.SITE_NAME.' to regain access of your account';
$keywords = 'forgot,password,reset,acccount,'.strtolower(SITE_NAME).'';
$script = '';
$show_form = '';
$error = '';
// show page header
Page::header($title,$description,$keywords);
// check if user is logged in
if(isLoggedIn()) {
    // user can't perform this action while are logged in
    echo '<div class="row">
    <div class="col s12 l8 push-l2">
    <div class="row card-panel red lighten-5">
    <div class="col s12"><h1>Reset Passsword</h1></div>
    <p><i class="material-icons middled red-text">error</i> Your can not reset your password while you are logged in. You need to <a href="/user/logout">logout</a> first.</p>
    </div></div></div>';
} else {
    // proceed with password resetting proccess
    if($form->method()=='POST') {
        // form was submitted
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // email was invalid
            $error = '<i class="material-icons middled red-text">error</i> The email is invalid! Please input your registered email address.';
        } elseif ($captcha!=$image_captcha) {
            // code was invalid
            $error = '<i class="material-icons middled red-text">error</i> The code didn\'t match with the image.';
        } elseif (!$users->isExists('email',$email)) {
            // email is not registered
            $error = '<i class="material-icons middled red-text">error</i> The email is not registered here! Please input your registered email address.';
        } else {
            $show_form = 'no';
            // good to go
            $users->resetPassword($email);
            // let user know
            $domain_name = substr(strrchr($email, "@"), 1);
            echo '<div class="row">
            <div class="col s12 l8 push-l2" method="post">
            <div class="row card-panel grey darken-3">
            <div class="col s12"><h1>Reset Passsword</h1></div>
            <div class="col s12"><i class="material-icons middled green-text">check_circle</i> An link for resetting your account password has been sent to yor email address: <span class="">'.$email.'</span>. Please check your <a href="https://'.$domain_name.'">inbox</a>.</div>
            </div></div></div>';
        }
    }
        // show form
        if(empty($show_form)) {
            $script = '<script>
            var reloadBtn = document.getElementById("reload_btn");
            reloadBtn.addEventListener("click", function() {
                var img = document.getElementById("captcha_image");
                img.setAttribute("src","/captcha?rand=" + Math.random());
            });
            </script>';
            if(!empty($form->get('email'))) { $autofill_email = ' value="'.$form->get('email').'"'; } else { $autofill_email = ''; }
?>
<div class="row">
    <form class="col s12 l8 push-l2" method="post" action="/user/forgot-password">
        <div class='row card-panel grey darken-3'>
            <div class='col s12'><h1>Reset Passsword</h1></div>
            <?php if(empty($error)) { ?>
            <div class='col s12'><i class="material-icons middled">info</i> Enter your email address which you used to register your account</div>
        <?php } else { ?>
        <div class="col s12"><?=$error;?></div>
        <?php } ?>
           <div class='input-field col s12'>
            <i class="material-icons prefix">email</i>
            <input id="email" name="email" type="email"<?=$autofill_email;?>>
          <label for="email">Email</label>
            </div>
            <div class="input-field col s6"><i class="material-icons prefix">receipt</i> <input id="captcha" name="captcha" type="text" required><label for="captcha">Code</label></div>
<div class="col s6 valign-wrapper"><img src="/captcha" id="captcha_image"> <button id="reload_btn" class="btn waves-effect waves-light right" type="button"><i class="material-icons">refresh</i></button></div>
            <div class="col s12"><button type='submit' name='btn_login' class='btn waves-effect waves-light'>Reset</button></div>
            <div class="col s12"><h1>Account</h1></div>
        <div class="col s6"><a href="/user/login">Login</a></div>
        <div class="col s6"><a href="/user/register">Register</a></div>
</div>
<?php
    }
}
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/user">User</a></li>
<li><a href="javascipt:void(0)">Forgot Password</a></li>
</ul>';
Page::footer('',$extra);
?>