<?php
//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(1);
$site_name = 'AtoZFlix';
$whitelist = array(
    '127.0.0.1',
    '::1'
);
if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
    // production server
    define('PN_SAVE_PATH','/home/atozujzn/public_html/includes');
    define('USER_ADMIN','atozujzn_admin');
    define('USER_MOVIES','atozujzn_movies');
    define('USER_EMAILS','atozujzn_emails');
    define('USER_USERS','atozujzn_movies_users');
    define('USER_SEARCHES','atozujzn_movies_searches');
    define('USER_PASSWORD','password');
} else {
    // localhost
    define('PN_SAVE_PATH','C:/xampp/htdocs/movies/includes');
    define('USER_ADMIN','root');
    define('USER_MOVIES','movies');
    define('USER_EMAILS','emails');
    define('USER_USERS','users');
    define('USER_SEARCHES','searches');
    define('USER_PASSWORD','');
}
$admins = array(1,2,3,4,5,12);
$pp = 12; // items per page
$req_domain = $_SERVER['HTTP_HOST'];
$prt = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$site_url = $prt.$req_domain;
$current_url = "".(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']==='on'?"https":"http")."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."";
define('SITE_URL', $site_url);
define('CURRENT_URL', $current_url);
define('SITE_NAME', $site_name);
define('SITE_DOMAIN', $req_domain);
define('BASE_URL', 'https://'.$_SERVER['HTTP_HOST']);
require(__DIR__.'/Db.php');
require(__DIR__.'/Users.php');
require(__DIR__.'/functions.php');
require(__DIR__.'/Form.php');
require(__DIR__.'/phpmailer/mailer.php');
session_start();
$genres = array('Action','Adventure','Action-Comedy','Animation','Biography','Comedy','Comedy-Romance','Crime','Drama','Family','Fantasy','Horror','History','Melodrama','Mystery','Romance','Sci-Fi','Superhero','Thriller','Western');
require(__DIR__.'/Page.php');
$users = new Users('localhost',USER_ADMIN,USER_PASSWORD,USER_USERS);
if(isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
}
$db = new Db('localhost',USER_ADMIN,USER_PASSWORD,USER_MOVIES);
noExt();
$form = new Form();