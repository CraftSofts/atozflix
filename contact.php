<?php
// include core file
include_once('includes/core.php');

// get form inputs
$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];
$captcha = $_POST['captcha'];
$image_captcha = $_SESSION['captcha'];
unset($_SESSION['captcha']);
// page name
$self = '/contact';
if(isloggedIn()) {
    $name_field = 'value="'.$user['first_name'].' '.$user['last_name'].'" readonly ';
    $email_field = 'value="'.$user['email'].'" readonly ';
}

// initialize main page contents
$tt = 'Contact - '.SITE_NAME.'';
$des = 'If you need any help, have any query, bug reports or problems contact the site admin through here';
$kw = 'contact,us,'.strtolower(SITE_NAME).'';
Page::header($tt,$des,$kw);
echo '<div class="col s12 l8 push-l2">
<div class="row card-panel grey darken-3">
<div class="col s12"><h1>Contact</h1></div>';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if(empty($name)||empty($message)||empty($email)) {
      $error[] = 'Please fill all the required fields';
    } else {
      if(strlen($name)<3) {
           $error[] = 'The name is too short';
        } elseif (strlen($name)>50) {
            $error[] = 'The name is too long! You are kidding, right?';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error[] = 'The email you provided is invalid';
        } elseif (strlen($message)<10) {
            $error[] = 'Your message is too short';
        } elseif (strlen($message)>1000) {
            $error[] = 'Your message is too large';
        } elseif($captcha!=$image_captcha) {
            $error[] = 'The security code didn\'t match with the image';
            //echo ''.$captcha.' - '.$image_captcha.'';
		} else {
$subject = 'Message from '.$name.' ('.$email.') ('.$_SERVER['HTTP_HOST'].')';
$content = strip_tags($message);

if(email('email@gmail.com','Meraj',$subject,$content)) {
    echo '<div class="col s12"><i class="material-icons middled green-text">check</i> Your message has been sent successfully!</div>
    <div class="center col s12"><a href="/" class="btn waves-effect waves-light">Home Page</a></div>';
} else {
    echo '<div class="col s12"><i class="material-icons middled red-text">error</i> Something went wrong! Please wait for 1 hour and try again.</div>
    <div class="center col s12"><a href="/" class="btn waves-effect waves-light">Home Page</a></div>';
}
        }
    }

if(!empty($error)) {
    echo '<div class="col s12">Please correct your following errors and try again</div>
    <ul class="col s12">';
    foreach ($error as $err)
    echo '<li><i class="material-icons middled">error</i> '.$err.'</li>';
    echo '</ul>
          <div class="col s12 center"><a href="'.$self.'" class="btn waves-effect waves-light">Go Back</a></div>';
}
} else {
?>
<p class="col s12"><i class="material-icons middled">help</i> If you have to ask or tell something, want to report bugs, give us suggestions or complain about something, contact us from right here.</p>
<form action="<?=$self;?>" method="post">
<div class="col s12 input-field"><input type="text" name="name" <?=$name_field;?>required><label for="name">Your Name</lable></div>

<div class="col s12 input-field"><input type="email" name="email" <?=$email_field;?>required><label for="email">Your Email</lable></div>

<div class="col s12 input-field"><textarea class="materialize-textarea" name="message" required></textarea><label for="message">Your Message</lable></div>

<div class="col s12 valign-wrapper"><div class="input-field inline"><i class="material-icons prefix">receipt</i> <input id="captcha" name="captcha" type="text" required><label for="captcha">Code</label></div> &nbsp;<img src="/captcha" id="captcha_image"> &nbsp;<button id="reload_btn" class="btn waves-effect waves-light right" type="button"><i class="material-icons middled">refresh</i></button></div>

<div class="col s12"><button type="submit" name="submit" class="green btn waves-effect waves-light"><i class="material-icons middled">send</i> Send</button> <button type="reset" name="reset" class="btn waves-effect waves-dark"><i class="material-icons middled">undo</i> Reset</button></div>
</form>
<?php
}
echo '<br/>';
$script = '<script>
            var reloadBtn = document.getElementById("reload_btn");
            reloadBtn.addEventListener("click", function() {
                var img = document.getElementById("captcha_image");
                img.setAttribute("src","/captcha?rand=" + Math.random());
            });
            </script>';
 $extra = '</div>
 </div>
 <ul class="custom_breadcrumb">
 <li><a href="/">Home</a></li>
<li>Contact</li>
</ul>';
Page::footer($script,$extra);
?>