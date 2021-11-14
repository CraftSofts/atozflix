<?php
require('includes/core.php');
if(!isLoggedIn()) {
    $_SESSION['login_msg'] = 'You need to be logged in to make a request';
    $_SESSION['target_url'] = '/request';
    redirectTo('/user/login');
    exit();
}
Page::header("Request - ".SITE_NAME."","Request for a Movie or TV Series","movie,request,tv series");
echo '<div class="col s12 l8 push-l2">
<div class="row card-panel grey darken-3">
<div class="col s12"><h1>Request for Content</h1></div>';
$last = $db->selectRow('requests','user_id',$user['id'],' ORDER BY id DESC');
if($last['result']=='success') {
    $last_time = $last['data']['time'];
    $current_time = date('U');
    $diff = $current_time - $last_time;
    if($diff<86400) {
        $not_eligable = '1';
    }
}
$show_form = '';
//$errors[] = '';
$user_id = $user['id'];
$type = $form->post('type');
$title = $form->post('title');
$year = $form->post('year');
$captcha = $form->post('captcha');
$image_captcha = $_SESSION['captcha'];
unset($_SESSION['captcha']);
if($form->method()=='POST') {
    if(strlen($title)>50||strlen($title)<1) {
        $errors[] = 'The name\'s length is invalid';
    } elseif ($type>1) {
        $errors[] = 'The content type is invalid';
    } elseif (!ctype_digit($year)) {
        $errors[] = 'The year should be a number';
    } elseif (strlen($year)!=4) {
        $errors[] = 'The year should be a 4 digit number';
    } elseif($captcha!=$image_captcha) {
        $errors[] = 'The security code didn\'t match with the image';
    } elseif($not_eligable==1) {
        $errors[] = 'You can only request for one content within every 24 hour. Your last request was made '.calculateAgo($diff).'. Please try again later.';
    } else {
        $columns = array('user_id','type','title','year','time');
        $values = array($user_id,$type,$title,$year,date("U"));
        $result = $db->insertRow('requests',$columns,$values);
        if($result['result']=='success') {
            $show_form = 'no';
            echo '<div class="card-panel green lighten-5 green-text"><i class="material-icons middled">check_circle</i> Request submitted successfuly!</div>
            <div class="center"><a href="/" class="btn waves-light waves-effect">Home Page</a></div>';
        } else {
            $errors[] = 'Request can\'t be processed at the moment';
        }
    }
}
if(empty($show_form)) {
    if(!empty($errors)) {
        echo '<ul>';
        foreach ($errors as $error){
            echo '<li><i class="material-icons middled red-text">error</i> '.$error.'</li>';
        }
        echo '</ul>';
    }
?>
<p class="col s12"><span class="bold-text">Please Read:</span> It's not possible to process just any request. Before requesting, please check first if the content is already available here. Use search bar to search and check. However, We can't process a request if:
<ul class="browser-default">
<li>The content has not been released yet</li>
<li>The content was not published anywhere in the internet</li>
<li>The quality of that content is not good enough (happens for new contents only), we only share contents which are at least HD or better in quality</li>
</ul>
Also keep in mind that you can request for only one content per day (24 hour). It's hard to process all user's requests.</p><br/>
<form action="/request" method="post">
<div class="input-field col s12"><i class="material-icons prefix">label</i> <input name="title" id="title" type="text" value="<?=$title;?>" required autofocus/><label for="title">Name</label></div>
<div class="input-field col s12">
<i class="material-icons prefix">compare</i> <select id="type" name="type" required>
		<option value="1" selected>Movie</option>
		<option value="0">TV Series</option>
	</select>
    <label>Type</label>
</div>
<div class="input-field col s12"><i class="material-icons prefix">date_range</i> <input name="year" id="year" type="number" data-length="4" value="<?=$year;?>" required><label for="year">Year</label></div>
<div class="valign-wrapper col s12"><div class="input-field"><i class="material-icons prefix">receipt</i> <input id="captcha" name="captcha" type="text" required>
          <label for="captcha">Code</label></div> <img src="/captcha" id="captcha_image"> <button id="reload_btn" class="btn waves-effect waves-light right" type="button"><i class="material-icons middled">refresh</i></button></div>
<div class="input-field col s12"><button type="submit" class="green btn waves-effect waves-light"><i class="material-icons middled">send</i> Send</button></div>
</form>
</div>
</div>
<?php
}
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li>Request a Content</li>
</ul>';
Page::footer('<script>
var reloadBtn = document.getElementById("reload_btn");
reloadBtn.addEventListener("click", function() {
    var img = document.getElementById("captcha_image");
    console.log(img);
    img.setAttribute("src","/captcha?rand=" + Math.random());
});
var year = document.getElementById("year");
M.CharacterCounter.init(year);
</script>',$extra);
?>