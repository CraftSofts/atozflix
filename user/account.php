<?php
require('../includes/core.php');
// set variables
$form = new Form;
$email = trim($_REQUEST['email']);
$key = trim($_REQUEST['key']);
$action = $form->get('action');
$username = $form->post('username');
$last_name = $form->post('last_name');
$password = $form->post('password');
$new_password = $form->post('new_password');
$new_password_confirm = $form->post('new_password_confirm');
$email = $form->request('email');
$key = $form->request('key');
$script = '';
// reset password
if($action=='reset_password') {
    if(isloggedIn()) {
        redirectTo('/user');
        exit();
    }
    $message = '';
    // page title
    $title = 'Reset Password - '.SITE_NAME.'';
    //generate html header
    Page::header($title);
    // user is eligable to change password
    if($users->isExists('email',$email)&&$users->isExists('keyhash',$key)) {
        // form was submmited for resetting password
        if($form->method()=='POST') {
            // check if the old password is correct
            if(strlen($new_password) < 6 || strlen($new_password) > 32) {
                // new password is in invalid length
                $message = 'Password can\'t be less than 6 or more than 32 characters';
            } elseif ($new_password_confirm!=$new_password) {
                // both passwords are not matching
                $message = 'Both passwords should be same';
            } else {
                // reset password
                $users->updateUser('email',$email,array('password'=>$users->encryptPassword($new_password),'keyhash'=>''));
                // let user know
                echo '<div class="row">
                <div class="col s12 l8 push-l2">
                <div class="row card-panel">
                <div class="col s12"><h1>Reset Password</h1></div>
                <p class="col s12"><i class="material-icons middled green-text">check_circle</i> Your password has been saved successfuly! You can proceed to <a href="/user/login">login</a> now.</p>
                </div>
                </div>
                </div>';
                $show_form = 'no';
            }
        }
            if(empty($show_form)) {
            $script = '<script>
        var show = document.getElementById("show");
        var newPassword = document.getElementById("new_password");
        var newPasswordConfirm = document.getElementById("new_password_confirm");
        show.addEventListener("change", function() {
            if(newPassword.type === "password" && show.checked === true) {
                newPassword.type = "text";
                newPasswordConfirm.type = "text";
            } else {
                newPassword.type = "password";
                newPasswordConfirm.type = "password";
            }
        });
        </script>';
            ?>
        <div class="row">
        <form action="/user/account?action=reset_password" method="post" class="col s12 l8 push-l2">
        <div class="row card-panel grey darken-3">
        <div class="col s12"><h1>Reset Password</h1></div>
        <div class="col s12"><p><i class="material-icons middled">info</i> Enter a new password. For better security, choose a strong and unique password.</p></div>
        <?php if(!empty($message)) {
            echo '<div class="col s12"><i class="material-icons middled red-text">error</i> '.$message.'</div>';
        } ?>
        <div class="input-field col s12"><label for="new_password">New Password</label><input type="password" name="new_password" id="new_password" required/></div>
        <div class="input-field col s12"><label for="new_password_confirm">Confirm New Password</label><input type="password" name="new_password_confirm" id="new_password_confirm" required/></div>
        <div class="input-field col s12"><p><label><input type="checkbox" id="show"/><span>Show Passwords</span></label></p></div>
        <input type="hidden" name="email" value="<?=$email;?>">
        <input type="hidden" name="key" value="<?=$key;?>">
        <div class="col s12"><button type="submit" class="btn waves-light waves-effect">Reset</button></div>
        </div>
        </form>
        </div>
            <?php
            }
    } else {
        // user is visiting an invalid link
        echo '<div class="row">
        <div class="col s12 l8 push-l2">
        <div class="row card-panel">
        <div class="col s12"><h1>Reset Password</h1></div>
        <p class="col s12"><i class="material-icons middled">error</i> Your have visited an invalid link. Please go to <a href="/">home page</a></p>
        </div>
        </div>
        </div>';
    }
} else { // change password
    // check if user is logged in
    $messages = array();
    $errors = array();
    $show_form = '';
    $show_form2 = '';
    $message2 = '';
    if(isLoggedIn()) {
        // page title
        $title = 'Account - '.SITE_NAME.'';
        //generate html header
        Page::header($title);
        // set variables
        $db_username = $user['username'];
        $db_last_name = $user['last_name'];
        $db_password = $user['password'];
        $db_email = $user['email'];
        // form was submmited for resetting password
            if($form->isExists('change_password')) {
                // form was submitted, change password
                if(password_verify($password, $user['password'])===false&&!empty($db_password)) {
                    // the current password is wrong
                    $messages[] = 'The current password is wrong';
                } elseif (strlen($new_password) < 6 || strlen($new_password) > 32 ) {
                    // new password is in invalid length
                    $messages[] = 'The password can\'t be less than 6 or more than 32 characters';
                } elseif ($new_password_confirm!=$new_password) {
                    // both passwords are not matching
                    $messages[] = 'Both passwords should be same';
                } elseif(password_verify($new_password, $user['password'])===true&&!empty($db_password)) {
                    // the new and current password are same
                    $messages[] = 'Current password can\'t be saved as new one. Please enter a different password than your current one.';
                } else {
                    // change password
                    $users->updateUser('email',$user['email'],array('password'=>$users->encryptPassword($new_password)));
                    // let user know
                    echo '<div class="row">
                    <div class="col s12 l8 push-l2">
                    <div class="row card-panel">
                    <div class="col s12"><h1>Change Password</h1></div>
                    <p class="col s12"><i class="material-icons middled green-text">check_circle</i> Your password has been changed successfuly! You need to <a href="/user/login">login</a> again.</p>
                    </div></div></div>';
                    $users->logout();
                    $show_form = 'no';
                }
            } elseif($form->isExists('update_profile')) {
                // update profile
                if(empty($db_username)) {
                    $username_pattern = '/^[a-z][a-z0-9]*_?[a-z0-9]+$/i';
                    if(strlen($username) < 3||strlen($username) > 20) {
                        $errors[] = 'Username can\'t be less than 3 and more than 20 characters';
                    } else {
                        if(!preg_match($username_pattern,$username)) {
                            $errors[] = 'Username can start with alphabet and can containt alphanumeric characters and a underscore';
                        } else {
                            if($users->isExists('username',$username)) {
                                $errors[] = 'Username is not available, try choosing other username or adding numbers at the end';
                            }
                        }
                    }
                }
                if(empty($db_last_name)) {
                    $name_pattern = '/^[a-zA-Z]+$/m';
                    if (strlen($last_name) < 3||strlen($last_name) > 10) {
                        $errors[] = 'Last name can\'t be less than 3 and more than 10 characters';
                    } else {
                        if(!preg_match($name_pattern,$last_name)) {
                            $errors[] = 'Last name can contain alphabets only';
                        }  
                    }
                }
                if(empty($db_email)) {
                    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = 'The email address is invalid';
                    } else {
                        if($users->isExists('email',$email)) {
                            $errors[] = 'The email address was already registered';
                        }  
                    }
                }
                if(empty($errors)) {
                    // save changes
                    if(!empty($user['fb_id'])) {
                        $param = 'fb_id';
                        $id = $user['fb_id'];
                    } else {
                        $param = 'email';
                        $id = $user['email'];
                    }
                    $values = array();
                    if(!empty($username)) $values['username'] = $username;
                    if(!empty($last_name)) $values['last_name'] = $last_name;
                    if(!empty($email)) $values['email'] = $email;
                    $users->updateUser($param,$id,$values);
                    $form2 = 'no';
                    $users->logout();
                    $message2 = '<div class="row">
                    <div class="col s12 l8 push-l2">
                    <div class="row card-panel grey darken-3">
                    <div class="col s12"><h1>Complete Profile</h1></div>
                    <div class="col s12"><i class="material-icons middled green-text">check_circle</i>  Your profile is updated successfuly. Thanks for updating. You need to <a href="/user/login">login</a> again.</div></div></div></div>';
                }
            }
            if(empty($show_form)) {
                $script = '<script>
                var show = document.getElementById("show");
                var password = document.getElementById("password");
                var newPassword = document.getElementById("new_password");
                var newPasswordConfirm = document.getElementById("new_password_confirm");
                show.addEventListener("change", function() {
                    if(password.type === "password" && show.checked === true) {
                        password.type = "text";
                        newPassword.type = "text";
                        newPasswordConfirm.type = "text";
                    } else {
                        password.type = "password";
                        newPassword.type = "password";
                        newPasswordConfirm.type = "password";
                    }
                });
                </script>';
                ?>
                <div class="row">
        <form action="/user/account" method="post" class="col s12 l8 push-l2">
        <div class="row card-panel grey darken-3">
        <div class="col s12"><h1>Change Password</h1></div>
        <?php if(!empty($messages)) {
            echo '<ul class="col s12">';
            foreach ($messages as $message) echo '<li><i class="material-icons middled red-text">error</i> '.$message.'</li>';
            echo '</ul>';
        }
        if(!empty($db_password)) { ?>
        <div class="input-field col s12"><label for="password">Current Password</label><input type="password" id="password" name="password" required autocomplete="password"/></div>
        <?php } else { ?>
            <div class="col s12"><i class="material-icons middled">info</i> Set a password for this account (optional). For better security, choose a strong and a unique password.</div>
        <?php
        $script = '<script>
        var show = document.getElementById("show");
        var newPassword = document.getElementById("new_password");
        var newPasswordConfirm = document.getElementById("new_password_confirm");
        show.addEventListener("change", function() {
            if(newPassword.type === "password" && show.checked === true) {
                newPassword.type = "text";
                newPasswordConfirm.type = "text";
            } else {
                newPassword.type = "password";
                newPasswordConfirm.type = "password";
            }
        });
        </script>';
    } ?>
        <div class="input-field col s12"><label for="new_password">New Password</label><input type="password" name="new_password" id="new_password" required/></div>
        <div class="input-field col s12"><label for="new_password_confirm">Confirm New Password</label><input type="password" name="new_password_confirm" id="new_password_confirm" required/></div>
        <div class="input-field col s12"><p><label><input type="checkbox" id="show"/><span>Show Passwords</span></label></p></div>
        <div class="col s12"><button type="submit" class="btn waves-light waves-effect green" name="change_password">Change</button> <a href="/user/logout" class="btn waves-light waves-effect deep-orange">Logout</a></div>
        </div>
        </form>
        </div>
        <?php
            }
            if(empty($db_username)||empty($db_last_name)||empty($db_email)) {
                if(empty($show_form2)) {
                    echo ' <div class="row">
                    <form action="/user/account" method="post" class="col s12 l8 push-l2">
                    <div class="row card-panel grey darken-3">
                    <div class="col s12"><h1>Complete Profile</h1></div>
                    <div class="col s12"><i class="material-icons middled">info</i> You should complete your profile by adding following informations for a better accessibility in this site (optional).</div>';
                    if(!empty($errors)) {
                        echo '<ul class="col s12">';
                        foreach ($errors as $error) echo '<li><i class="material-icons middled red-text">error</i> '.$error.'</li>';
                        echo '</ul>';
                    }
                    if(empty($db_username)) echo '<div class="input-field col s12"><label for="username">Username</label><input type="text" name="username" id="username" required/></div>';
                    if(empty($db_last_name)) echo '<div class="input-field col s12"><label for="last_name">Last Name</label><input type="text" name="last_name" id="last_name" required/></div>';
                    if(empty($db_email)) echo '<div class="input-field col s12"><label for="email">Email</label><input type="email" name="email" id="email" required/></div>';
                    echo '<div class="col s12"><button type="submit" class="btn waves-light waves-effect" name="update_profile">Update</button></div>
                    </div>
                    </form>
                    </div>';
                }
            }
            if(!empty($message2)) echo $message2;
    } else {
        // user is not logged in, redirect to the login page with a message
        $_SESSION['login_msg'] = 'You need to be logged in to access account page';
        $_SESSION['target_url'] = '/user/account';
        redirectTo('/user/login');
        exit();
    }
}
// show footer elements
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/user">User</a></li>
<li>Account</li>
</ul>';
Page::footer($script,$extra);
?>