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
$id = $form->get("id");
$check = $db->selectRow('categories','id',$id);
if($check['result']=='failed') {
	$_SESSION['admin_msg'] = '<div class="card-panel red lighten-5 red-text"><i class="material-icons middled">error</i> Category not found</div>';
	$url = '/admin-cp/categories/'; // not found
	redirectTo($url);
	exit();
}
$name = $check['data']['title'];
Page::header('Sub-Categories - Site_Name');
$user_requests = $db->countRows('SELECT * FROM requests WHERE processed=0');
if($user_requests>0) {
	echo '<h1>User Requests</h1>
	<div class="card-panel"><i class="material-icons middled">info</i> There are '.$user_requests.' pending user <a href="/admin-cp/requests">requests</a></div>';
}
$list = $db->selectRows('sub_categories',' WHERE category_id='.$id);
$list = $list['data'];
$msg = $_SESSION['admin_msg'];
?>
<h1>All Sub-Categories</h1>
<?php
if(!empty($msg)) {
	echo $msg;
	unset($_SESSION['admin_msg']);
}

if(empty($list)) {
	echo '<div class="card-panel"><i class="material-icons middled">info</i> No Sub-categories</div>';
} else {
	foreach($list as $category) {
?>
<div class="card-panel"><a href="/admin-cp/posts/?id=<?=$category->id;?>"><?=$category->title;?></a> <?php if($user['id']==1) { ?><span class="right"><a href="/admin-cp/sub-categories/edit?id=<?=$category->id;?>&pid=<?=$id;?>"><i class="material-icons middled">edit</i></a> <a href="javascript:void(0)" onclick="openModal(<?=$category->id;?>)"><i class="material-icons middled red-text">delete</i></a></span><?php } ?></div>
<?php
	}
}
if($user['id']==1)
echo '<div class="fixed-action-btn"><a href="/admin-cp/sub-categories/new?id='.$id.'" class="btn-floating btn-large"><i class="large material-icons">add</i></a></div>
<div id="delete_modal" class="modal"><div class="modal-content"><span class="right red-text modal-close"><i class="material-icons">cancel</i></span><h4>Delete sub-category?</h4><p><div class="bold-text red-text">Are you sure to delete the selected sub-category? All posts under this category will be also deleted.</div><i class="material-icons">info</i> This action can\'t be undone!</p> <div class="modal-footer"><a class="btn waves-effect waves-light red" id="cat_id">Yes</a> <a href="javascript:void(0)" class="modal-close waves-effect waves-light btn green">No</a></div>
  </div></div>';
$script ='<script>
function openModal(id) {
document.getElementById("cat_id").setAttribute("href", "/admin-cp/sub-categories/delete?pid='.$id.'&id=" + id);
var elems = document.getElementById("delete_modal");
	var instances = M.Modal.init(elems);
	instances.open();
}
</script>';
$extra = '<ul class="custom_breadcrumb">
<li><a href="/admin-cp/">Admin CP</a></li>
<li><a href="/admin-cp/categories">Categories</a></li>
<li>'.$name.'</li>
</ul>';
Page::footer($script,$extra);
?>