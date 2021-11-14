<?php
// required files
require_once("includes/core.php");
// handle errors
if(isset($_GET['error'])){
if($_GET['error'] == 400){
$title = 'Error 400 - BAD REQUEST!';
$message = 'The server didn\'t understand the request.';
}elseif($_GET['error'] == 401){
$title = 'UNAUTHORIZED!';
$message = 'You are not unauthorized to view the page requested.';
}elseif($_GET['error'] == 403){
$title = 'ACCESS FORBIDDEN!';
$message = 'Access for this request was denied. You should not see this page until you did something wrong. Please don\'t do this.';
}elseif($_GET['error'] == 404){
$title = 'NOT FOUND!';
$message = 'The page or file you are looking for was not found! Maybe the file removed or doesn\'t exists or you mistyped the URL. Sorry for the trouble. You can search for anything from the navigation menu.';
}elseif($_GET['error'] == 406){
$title = 'NOT ACCEPTABLE!';
$message = 'The resource cannot be displayed! ';
}elseif($_GET['error'] == 500){
$title = 'INTERNAL SERVER ERROR!';
$message = 'The server encountered an unexpected condition which prevented it from fulfilling the request.';
}elseif($_GET['error'] == 502){
$title = 'BAD GATEWAY!';
$message = 'The server, while acting as a gateway or proxy, received an invalid response from the upstream server.';
}elseif($_GET['error'] == 504){
$title = 'GATEWAY TIMEOUT!';
$message = 'The server, while acting as a gateway or proxy, did not receive a timely response from the upstream server.';
} else {
$title = 'NOT FOUND!';
$message = 'The page or file you are looking for was not found! Maybe the file removed or doesn\'t exists or you mistyped the URL. Sorry for the trouble. You can search for anything from the navigation menu.';
}
} else {
$title = 'NOT FOUND!';
$message = 'The page or file you are looking for was not found! Maybe the file removed or doesn\'t exists or you mistyped the URL. Sorry for the trouble. You can search for anything from the navigation menu.';
}
// generate page
Page::header(''.$title.' - '.SITE_NAME.'');
?>
<h2><?=$title;?></h2>
<img class="responsive-img center" src="/assets/images/oops.png"/>
<div class="card-panel">
<i class="material-icons middled">error</i> <?=$message;?>
</div>
<br>
<?php
Page::footer();
?>