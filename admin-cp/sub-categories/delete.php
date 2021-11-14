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
if($user['id']!=1) {
	redirectTo('/admin-cp/');
}
$id = $form->get('id');
$pid = $form->get('pid');
$check = $db->selectRow('sub_categories','id',$id);
if($check['result']=='success') {
	$posts = $db->selectRows('posts',' WHERE sub_cat_id='.$id);
	foreach ($posts as $post) {
		$db->deleteRow('posts','id',$post['id']);
	}
	$delete = $db->deleteRow('sub_categories','id',$id);
	if($delete['result']=='success') {
		$_SESSION['admin_msg'] = '<div class="card-panel green lighten-5 green-text"><i class="material-icons middled">check_circle</i> Sub-Category deleted successfuly</div>';
		$url = '/admin-cp/sub-categories/?id='.$pid.''; // successfull
	} else {
		$_SESSION['admin_msg'] = '<div class="card-panel red lighten-5 red-text"><i class="material-icons middled">error</i> Failed to delete Sub-Category</div>';
		$url = '/admin-cp/sub-categories/?id='.$pid.''; // failed
	}
} else {
	$_SESSION['admin_msg'] = '<div class="card-panel red lighten-5 red-text"><i class="material-icons middled">error</i> Sub-Category not found</div>';
	$url = '/admin-cp/sub-categories/?id='.$pid.''; // not found
}
header("Location: $url");