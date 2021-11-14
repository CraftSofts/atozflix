<?php
class Users extends Db {
    function __construct($servername,$username,$password,$dbname) {
        parent::__construct($servername,$username,$password,$dbname);
        // if user logged in or not via session
        if(!isset($_SESSION['user'])) {
            // check if cookie is set or not
            if(isset($_COOKIE['user'])) {
                // now check if cookie is valid
                $info = explode(':',$_COOKIE['user']); // get user id and password from cookie as array
                $user_id = base64_decode(trim($info[0])); // get user id as plain text
                $otp = trim($info[1]); // get the password
                // prepare the sql
                $sql = " AND activated='1'";
                $check = $this->isExists('id',$user_id,$sql); // check if infos are correct
                if($check) {
                    $data = $this->selectRow('users','id',$user_id);
                    if(password_verify($otp,$data['data']['otp'])) {
                        //login user
                        $data = $this->selectRow('users','id',$user_id);
                        $_SESSION['user'] = (array) $data['data'];
                    } else {
                        // the cookie is invalid, so it must be removed
                        setcookie('user', '', time() - 1, "/", "", true, true);
                    }
                } else {
                    // the cookie is invalid, so it must be removed
                    setcookie('user', '', time() - 1, "/", "", true, true);
                }
            }
        }
    }
    function login($username,$password,$remember=false) {
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            // user entered username
            $param = 'username';
        } else {
            // user entered an email adddress
            $param = 'email';
        }
        // check if username or email exists
        $check = $this->selectRow('users',$param,$username);
        if($check['result']=='success') {
            // username/email exists
            if($check['data']['activated']==0) {
                return $this->result(0,'This account is not active yet. You need to activate the account from the email you received after registration. Didn\'t receive any email? <a href="https://'.$_SERVER['HTTP_HOST'].'/user/activate?resend=email&email='.$check['data']['email'].'">Get activation email again</a>.'); // not activated yet
            } else {
                if (password_verify($password, $check['data']['password'])===true) {
                    // login credentials are correct
                    $_SESSION['user'] = (array) $check['data']; // set session
                    unset($_SESSION['login_msg']);
                    $otp = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,20);
                    $this->updateUser($param,$username,array('otp'=>$this->encryptPassword($otp)));
                    if($remember===true) {
                        //user wants to set cookie
                        $value = ''.base64_encode($check['data']['id']).':'.$otp.'';
                        setcookie('user', $value, time() + (86400 * 15), "/", "", true, true);
                    }
                    return $this->result();
                } else {
                    //wrong credentials were provided
                    $email = $check['data']['email'];
                    if(!empty($email)) { $param = '?email='.$email; } else { $param = ''; }
                    return $this->result(0,'The password is wrong. Please try again. <a href="/user/forgot-password'.$param.'">Forgot password</a>?'); // 1 for wrong user/pass
                }
            }
        } else {
            // user doesn't exists
            return $this->result(0,'Sorry, there is no account associated with the info you provided! Want to <a href="/user/register?'.$param.'='.$username.'">sign up</a> for this account?'); // 2 for non-exist user
        }
    }

    function socialLogin($vendor,$id) {
        if($vendor=='facebook') {
            $param = 'fb_id';
        } else {
            $param = 'email';
        }
        $check = $this->selectRow('users',$param,$id);
        $_SESSION['user'] = (array) $check['data'];
        unset($_SESSION['login_msg']);
    }
    function socialAdd($column,$values) {
        $this->insertRow('users',$column,$values);
    }
    function logout() {
        unset($_SESSION['user']);
        unset($user);
    }
    function addUser($params,$email_activation=false) {
            $new_values[] = $params['username'];
            $new_values[] = $params['first_name'];
            $new_values[] = $params['last_name'];
            $new_values[] = $params['email'];
            $new_values[] = $this->encryptPassword($params['password']);
            // email activation
            if($email_activation===true) {
                $new_values[] = '0';
                $keyhash = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,10);
                $new_values[] = $keyhash;
                $new_values[] = date("U");
                $new_params = array('username','first_name','last_name','email','password','activated','keyhash','time');
                $this->insertRow('users',$new_params,$new_values);
                $this->sendActivation($params['email'],$keyhash);
                return true;
                
            } else {
                $new_values[] = '1';
                $new_params = array('username','first_name','last_name','email','password','activated');
                $this->insertRow('users',$new_params,$new_values);
                return true;
            }
    }
    function encryptPassword($password) {
    	return password_hash($password, PASSWORD_DEFAULT);
    }
    function updateUser($param,$value,$data) {
        $user = $this->isExists($param,$value);
        if($user) {
            $this->updateRow('users',$param,$value,$data);
            return true;
        } else {
            return false;
        }
    }
    function activateUser($email,$keyhash) {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            $user = $this->isExists('email',$email,' AND activated=\'0\'');
            if($user) {
                $this->updateRow('users','email',$email,array('activated'=>'1','keyhash'=>''));
                return true;
            } else {
                return false;
            }
        }
    }
    function isExists($column,$data,$extra='') {
        $check = $this->selectRow('users',$column,$data,$extra);
        if($check['result']=='success') {
            return true;
        } else {
            return false;
        }
    }
    function resendActivation($email) {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            $user = $this->selectRow('users','email',$email);
            if($user['result']=='success') {
                if($user['data']['activated']==0) {
                    $keyhash = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,10);
                    $this->sendActivation($email,$keyhash);
                        return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
    function sendActivation($email,$keyhash) {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            $subject = "Activate your account at ".SITE_NAME."";
            $body = "<h1>Account Activation</h1>
            <p>You are now a member of <strong>".SITE_NAME."</strong>. To confirm your account, you need to activate your account. In order to activate, please click on the following link.<br/><br/>
            <a style=\"padding: 8px; background-color: #008000; font-weight: bold; color: #ffffff; text-transform: uppercase; text-decoration: none; border-radius: 4px;\" href=\"https://".$_SERVER['HTTP_HOST']."/user/activate?email=".$email."&key=".$keyhash."\">Activate Account</a><br/><br/>
            <strong>Note:</strong> If you didn't register this account, simply ignore and delete this mail.<br/><br/>
            Regards,<br/>
            Admin of <strong>".SITE_NAME."</strong></p>";
            $this->updateRow('users','email',$email,array('keyhash'=>$keyhash));
            $this->sendEmail($email,$subject,$body);
            $this->updateRow('users','email',$email,array('keyhash'=>$keyhash));
            return true;
        }
    }
    function sendEmail($to,$subject,$body) {
        $name = $this->selectRow('users','email',$email)['data']['username'];
        if(empty($name)) $name = $email;
        $message = "<html>
        <head>
        <title>$subject</title>
        </head>
        <body>
        $body
        <table>
        </body>
        </html>";
        email($to,$name,$subject,$message);
            return true;
    }
    function resetPassword($email) {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            if($this->isExists('email',$email)) {
                $keyhash = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,10);
                $subject = 'Reset password at '.SITE_NAME.'';
                $body = "<h1>Password Reset</h1>
                <p>We received a request to rerset your account password at ".SITE_NAME.". To reset your password, please click on the following link.<br/><br/>
                <a style=\"padding: 8px; background-color: #008000; font-weight: bold; color: #ffffff; text-transform: uppercase; text-decoration: none; border-radius: 4px;\" href=\"https://".$_SERVER['HTTP_HOST']."/user/account?action=reset_password&email=".$email."&key=".$keyhash."\">Reset Your Password</a><br/><br/>
                <strong>Note:</strong> If you didn't request this email, simply ignore and delete this mail.<br/><br/>
                Regards,<br/>
                Admin of <strong>".SITE_NAME."</strong></p>";
                $this->sendEmail($email,$subject,$body);
                $this->updateUser('email',$email,array('keyhash'=>$keyhash));
            } else {
                return false;
            }
        }
    }
    function getInfo($data,$column) {
        if($this->isExists($column,$data)) {
            $data = $this->selectRow('users',$column,$data);
            return $data['data'];
        }
    }
    function removeUser($id) {
        if($this->deleteRow('users','id',$id)) {
            return true;
        } else {
            return false;
        }
    }
}