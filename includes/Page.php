<?php

class Page {
	static function header($title,$description='',$keywords='',$image='') {
		header("Link: </assets/css/material-icons.css; rel=preload; as=style");
		header("Link: </assets/css/materialize.min.css; rel=preload; as=style");
		header("Link: </assets/css/main.css; rel=preload; as=style");
		header("Link: </assets/js/main.js; rel=preload; as=script");
		header("Link: </assets/js/materialize.min.js; rel=preload; as=script");
		header("Link: </assets/images/logo.svg; rel=preload; as=image");
		header("Link: </assets/images/preloaders/pulsating_engine_32.svg; rel=preload; as=image");
		header("Link: </assets/images/preloaders/funnel_256.svg; rel=preload; as=image");
		if(empty($description)) $description = 'No description available';
		if(empty($keywords)) $keywords = 'atozflix,free,download,movies,tv,series,480p,720p,1080p';
		if($_SERVER['REQUEST_URI']=='/search'||$_SERVER['REQUEST_URI']=='/search?q=') {
			$disabled_search = ' disabled';
		} else {
			$disabled_search = '';
		}
		if(empty($image)) $image = 'https://'.$_SERVER['HTTP_HOST'].'/assets/images/logo.png';
	$html = '<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>'.$title.'</title>
<meta name="title" content="'.$title.'"/>
<meta property="og:title" content="'.$title.'" />
<meta name="description" content="'.$description.'"/>
<meta property="og:description" content="'.$description.'"/>
<meta name="keywords" content="'.$keywords.'"/>
<meta property="og:image" content="'.$image.'"/>
<link rel="apple-touch-icon" sizes="180x180" href="/assets/images/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/images/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/assets/images/icons/favicon-16x16.png">
<link rel="manifest" href="/assets/images/icons/site.webmanifest">
<link rel="mask-icon" href="/assets/images/icons/safari-pinned-tab.svg" color="#5bbad5">
<link rel="shortcut icon" href="/assets/images/icons/favicon.ico">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="msapplication-config" content="/assets/images/icons/browserconfig.xml">
<meta name="theme-color" content="#000000">
<meta name="msapplication-TileImage" content="/assets/images/ms-icon-144x144.png"/>
<meta name="theme-color" content=""/>
<base href="'.BASE_URL.'">
<link rel="stylesheet" href="/assets/css/material-icons.css" media="print" onload="this.media=\'all\'; this.onload=null;">
<link rel="stylesheet" href="/assets/css/materialize.min.css" onload="this.media=\'all\'; this.onload=null;">
<link rel="stylesheet" href="/assets/css/main.css" onload="this.media=\'all\'; this.onload=null;">
</head>
<body class="black">
<header>
<div class="navbar-fixed">
<nav class="grey darken-4">
<div class="nav-wrapper container"><a id="logo-container" href="/" class="brand-logo"><img src="/assets/images/logo.svg" class="main-logo" alt="logo"></a>
<ul class="right hide-on-med-and-down">';
if(empty($disabled_search)) 
$html .= '<li><a class="waves-effect waves-light modal-trigger" href="#search"><i class="material-icons left">search</i>Search</a></li>';
$html .='<li><a class="dropdown-trigger-menu" href="javascript:void(0)" data-target="main_menu_dd"><i class="material-icons left">list</i>Menu</a>
<ul id="main_menu_dd" class="dropdown-content">
<li><a href="/latest" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">new_releases</i> Latest Contents</a></li>
<li><a href="/trending" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">trending_up</i> Trending Contents</a></li>
<li><a href="/categories" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">subscriptions</i> Categories</a></li>
<li><a href="/genres" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">dvr</i> Genres</a></li>
<li><a href="/request" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">add_to_queue</i> Request a Content</a></li>
</ul></li>
<li><a class="dropdown-trigger-menu" href="javascript:void(0)" data-target="link_menu_dd"><i class="material-icons left">link</i>Links</a>
<ul id="link_menu_dd" class="dropdown-content">
<li><a href="/contact" class="waves-effect waves-dark white-text middled"><i class="material-icons white-text">mail</i> Contact</a></li>
<li><a href="/faq" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">help</i> F.A.Q.</a></li>
<li><a href="/tos" class="waves-effect waves-dark white-text middled"><i class="material-icons white-text">announcement</i> T.O.S.</a></li>
<li><a href="/privacy-policy" class="waves-effect waves-dark white-text middled"><i class="material-icons white-text">security</i> Privacy Policy</a></li>
<li><a href="/about" class="waves-effect waves-dark white-text middled"><i class="material-icons white-text">info_outline</i> About</a></li>
</ul>
<li><a class="dropdown-trigger-menu no-autoinit" href="javascript:void(0)" data-target="account_menu_dd"><i class="material-icons left">people</i>Account</a>
<ul id="account_menu_dd" class="dropdown-content">';
if(isLoggedIn()) {
	$admins = array(1,2,3,4,5);
	if(in_array($_SESSION['user']['id'],$admins)) {
		$html .= '<li><a href="/admin-cp/" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">dashboard</i> Admin Panel</a></li>';
	}
	$html .= '<li><a href="/user/account" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">account_box</i> Account Settings</a></li>
	<li><a href="/user/wishlist" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">playlist_add_check</i> Wishlist</a></li>
	<li><a href="/user/requests" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">ondemand_video</i> Your Requests</a></li>
	<li><a href="/user/logout" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">power_settings_new</i> Logout</a></li>';
} else {
	$html .= '<li><a href="/user/login" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">exit_to_app</i> Login</a></li>
	<li><a href="/user/register" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">person_add</i> Register</a></li>
	<li><a href="/user/forgot-password" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">vpn_key</i> Forgot Password</a></li>';
}
$html .= '</ul>
</ul>
<a href="javascript:void(0)" data-target="slide-out" class="sidenav-trigger"><i class="material-icons">menu</i></a>
</div>
</nav>
</div>
<ul id="slide-out" class="sidenav grey darken-4">
<li>
<ul class="collapsible collapsible-accordion">
<li>
<a class="collapsible-header white-text waves-effect waves-light" href="javascript:void(0)"><i class="material-icons white-text">list</i> Menu </a>
<div class="collapsible-body grey darken-4">
<ul>
<li><a href="/search" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">search</i> Advanced Search</a></li>
<li><a href="/latest" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">new_releases</i> Latest Contents</a></li>
<li><a href="/trending" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">trending_up</i> Trending Contents</a></li>
<li><a href="/categories" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">subscriptions</i> Categories</a></li>
<li><a href="/genres" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">dvr</i> Genres</a></li>
<li><a href="/request" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">add_to_queue</i> Request a Content</a></li>
</ul>
</div>
</li>
</ul>
</li>
<li>
<ul class="collapsible collapsible-accordion">
<li>
<a class="collapsible-header white-text waves-effect waves-light" href="javascript:void(0)"><i class="material-icons white-text">people</i>Account </a>
<div class="collapsible-body grey darken-4">
<ul>';
if(isLoggedIn()) {
	$admins = array(1,2,3,4,5);
	if(in_array($_SESSION['user']['id'],$admins)) {
		$html .= '<li><a href="/admin-cp/" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">dashboard</i> Admin Panel</a></li>';
	}
	$html .= '<li><a href="/user/account" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">account_box</i> Account Settings</a></li>
	<li><a href="/user/wishlist" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">playlist_add_check</i> Wishlist</a></li>
	<li><a href="/user/requests" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">ondemand_video</i> Your Requests</a></li>
	<li><a href="/user/logout" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">power_settings_new</i> Logout</a></li>';
} else {
	$html .= '<li><a href="/user/login" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">exit_to_app</i> Login</a></li>
	<li><a href="/user/register" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">person_add</i> Register</a></li>
	<li><a href="/user/forgot-password" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">vpn_key</i> Forgot Password</a></li>';
}
	$html .= '
</ul>
</div>
</li>
</ul>
</li>
<li>
<ul class="collapsible collapsible-accordion">
<li>
<a class="collapsible-header white-text waves-effect waves-light" href="javascript:void(0)"><i class="material-icons white-text">web</i> Links</a>
<div class="collapsible-body grey darken-4">
<ul>
<li><a href="/contact" class="waves-effect waves-dark white-text middled"><i class="material-icons white-text">mail</i> Contact</a></li>
<li><a href="/faq" class="waves-effect waves-dark white-text"><i class="material-icons white-text middled">help</i> F.A.Q.</a></li>
<li><a href="/tos" class="waves-effect waves-dark white-text middled"><i class="material-icons white-text">announcement</i> T.O.S.</a></li>
<li><a href="/privacy-policy" class="waves-effect waves-dark white-text middled"><i class="material-icons white-text">security</i> Privacy Policy</a></li>
<li><a href="/about" class="waves-effect waves-dark white-text middled"><i class="material-icons white-text">info_outline</i> About</a></li>
</ul>
</div>
</li>
</ul>
</li>
</ul>
</header>
<main class="container">';
if(empty($disabled_search))
$html .= '<ul class="collapsible hide-on-large-only">
<li>
<nav>
<div class="nav-wrapper">
<form method="get" action="/search" id="search_form_s">
<div class="input-field"> 
<input type="search" name="q" id="q" class="white-text" placeholder="Search here..." onkeydown="autoComplete(this.value,\'search_form_s\')" autocomplete="off" required>
<label class="label-icon no-style-label"><i class="material-icons">search</i></label>
</div>
</form>
</div>
</nav>
</li>
</ul>
<div id="search" class="modal grey darken-4">
<form method="get" action="/search" id="search_form_l">
<div class="modal-content row">
<div class="col s12"><h2>Search</h2></div>
<div class="col s12 input-field"><input type="text" name="q" id="q" onkeydown="autoComplete(this.value,\'search_form_l\')" autocomplete="off" required><label for="q">Enter keyword</label></div>
<div class="col s6 input-field"><select name="search_type" id="search_type"><option selected>Content type</option><option value="m">Movie</option><option value="s">TV Series</option></select><label for="search_type">Type</label></div>
<div class="col s6 input-field"><input type="number" name="search_year" id="search_year" data-length="4"><label for="search_year">Year</label></div>
</div>
<div class="modal-footer grey darken-4">
<button class="btn green waves-effect waves-light" type="submit">Search</button> <a href="javascript:void(0)" class="modal-close waves-effect waves-green btn">Close</a>
</div>
</form>
</div>
<div class="row">';
	echo $html;
	}
	
	static function footer($js='',$extra='') {
		echo '</div>
		</main>
		'.$extra.'
		<footer class="page-footer grey darken-4"><div class="row container center">
<div class="col s12"><div class="padding"><i class="material-icons middled">people</i> Currently '.onlineUsers().' users are online</div><br/>
<div class="padding"><a href="/search">Search</a> - <a href="/categories">Categories</a> - <a href="/request">Request</a> - <a href="/about">About US</a> - <a href="/faq">F.A.Q.</a> - <a href="/tos">T.O.S.</a> - <a href="/privacy-policy">Privacy Policy</a></div></div>
</div>
<div class="footer-copyright"><div class="container"><div class="center bold-text">&copy; Team AtoZ '.date("Y").'</div></div></div>
</footer>
<script src="/assets/js/materialize.min.js"></script>
<script src="/assets/js/main.js"></script>
'.$js.'
</body>
</html>';
	}
	
}

?>