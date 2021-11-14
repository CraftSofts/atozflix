<?php
require('../includes/core.php');
$title = 'User - '.SITE_NAME.'';
$description = 'Login or Register a new account at '.SITE_NAME.' to enjoy all features';
$keywords = 'user,login,acccount,'.strtolower(SITE_NAME).'';
Page::header($title,$description,$keywords);
echo '<div class="col s12 l8 push-l2">
<div class="card">
<div class="card-content">
<span class="card-title">Welcome User</span>';
if(isset($_SESSION['social_login'])) {
    echo '<p><i class="material-icons green-text middled">check_circle</i> '.$_SESSION['social_login'].'</p>';
    unset($_SESSION['social_login']);
}
if(isLoggedIn()) {
?>
<p><i class="material-icons middled green-text">check_circle</i> You are currently logged in. Enjoy all features without any interruptions.</p></div>
<div class="card-action"><a href="/user/account" class="btn-flat waves-effect waves-dark"><i class="material-icons middled">account_box</i> Account</a> <a href="/user/wishlist" class="btn-flat waves-effect waves-dark"><i class="material-icons middled">playlist_add_check</i> Wishlist</a> <a href="/user/logout" class="btn-flat waves-effect waves-dark"><i class="material-icons middled">power_settings_new</i> Logout</a></div>
<?php
} else {
?>
<p><i class="material-icons middled">info</i> You are currently not logged in. Please login to enjoy all features.</p></div>
<div class="card-action"><a href="/user/login" class="btn-flat waves-effect waves-dark"><i class="material-icons middled">exit_to_app</i> Login</a> <a href="/user/register" class="btn-flat waves-effect waves-dark"><i class="material-icons middled">person_add</i> Register</a> <a href="/user/forgot-password" class="btn-flat waves-effect waves-dark"><i class="material-icons middled">vpn_key</i> Forgot Password?</a></div>
<?php
}
echo '</div>
</div>';
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li>User</li>
</ul>';
Page::footer('',$extra);
?>