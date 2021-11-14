<?php
require '../../includes/core.php';
if(!isLoggedIn()) {
	$_SESSION['login_msg'] = 'You need to be logged in to access that page';
    $_SESSION['target_url'] = CURRENT_URL;
	redirectTo('/user/login');
	exit(); 
} else {
	if(!in_array($user['id'],$admins)) {
		redirectTo('/');
		exit();
	}
}
$id = $form->get('id');
$pid = $form->get('pid');
$check = $db->selectRow('posts','id',$id);
if($check['result']=='success') {
	if($user['id']!=$check['data']['user_id']) {
		$_SESSION['admin_msg'] = '<div class="card-panel red lighten-5 red-text"><i class="material-icons middled">error</i> You don\'t have permission to delete anything made by someone else</div>';
		//redirectTo('/admin-cp/');
		echo ''.$user['id'].'-'.$check['data']['user_id'].'';
		exit();
	}
	$delete = $db->deleteRow('posts','id',$id);
	if($delete['result']=='success') {
		$_SESSION['admin_msg'] = '<div class="card-panel green lighten-5 green-text"><i class="material-icons middled">check_circle</i> Post deleted successfuly</div>';
		$url = '/admin-cp/posts/?msg_id=1&id='.$pid; // successfull
	} else {
		$_SESSION['admin_msg'] = '<div class="card-panel red lighten-5 red-text"><i class="material-icons middled">error</i> Failed to delete post</div>';
		$url = '/admin-cp/posts/?msg_id=2&id='.$pid; // failed
	}
} else {
	$_SESSION['admin_msg'] = '<div class="card-panel red lighten-5 red-text"><i class="material-icons middled">error</i> Post not found</div>';
	$url = '/admin-cp/posts/?msg_id=3&id='.$pid; // not found
}
header("Location: $url");