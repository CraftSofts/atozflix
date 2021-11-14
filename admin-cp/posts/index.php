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
$check = $db->selectRow('sub_categories','id',$id);
if($check['result']=='failed') {
	$_SESSION['admin_msg'] = '<div class="card-panel red lighten-5 red-text"><i class="material-icons middled">error</i> Category not found</div>';
	$url = '/admin-cp/categories/'; // not found
	redirectTo($url);
	exit();
}
$cid = $check['data']['category_id'];
$name = $db->selectRow('categories','id',$cid);
$name = $name['data']['title'];
$sname = $check['data']['title'];
Page::header('Posts - Site_Name');
$user_requests = $db->countRows('SELECT * FROM requests WHERE processed=0');
if($user_requests>0) {
	echo '<h1>User Requests</h1>
	<div class="card-panel"><i class="material-icons middled">info</i> There are '.$user_requests.' pending user <a href="/admin-cp/requests">requests</a></div>';
}
$pp = 10;
$p_n = $form->get('p');
if(empty($p_n)) $p_n=1;
$start = ($p_n-1)*$pp;
if($user['id']==1) {
	$list = $db->selectRows('posts',' WHERE sub_cat_id='.$id.' LIMIT '.$start.', '.$pp.'');
} else {
	$list = $db->selectRows('posts',' WHERE sub_cat_id='.$id.' AND user_id='.$user['id'].' LIMIT '.$start.', '.$pp.'');
}
$total_result = $db->countRows('SELECT * FROM posts WHERE sub_cat_id='.$id.'');
$msg = $_SESSION['admin_msg'];
?>
<h1>Posts</h1>
<?php
if(!empty($msg)) {
	echo $msg;
	unset($_SESSION['admin_msg']);
}

if(empty($list['data'])) {
	echo '<div class="card-panel"><i class="material-icons middled">info</i> No Posts</div>';
} else {
	$list = $list['data'];
	foreach($list as $category) {
?>
<div class="card-panel"><?=$category->id;?>. <?=$category->title;?> <span class="right"><a href="/admin-cp/posts/edit?id=<?=$category->id;?>&pid=<?=$id;?>"><i class="material-icons middled">edit</i></a> <a href="javascript:void(0)" onclick="openModal(<?=$category->id;?>)"><i class="material-icons middled red-text">delete</i></a></span></div>
<?php
	}
}
// show pagination
if($total_result>$pp){
    echo '<ul class="pagination center">';
    $pages=pagination($total_result,$p_n,$pp);
    if(is_array($pages))
    {
    foreach($pages as $key => $val)
    {
    if($val == $p_n)
    {echo ' <li class="active"><a href="javascript:void(0)"> '.$key.' </a></li> ';}
    else{echo ' <li class="waves-effect"><a href="/admin-cp/posts/?id='.$id.'&p='.$val.'"> '.$key.' </a></li> ';}
    }
    echo '</ul>';
    }
}
echo '<div class="fixed-action-btn"><a href="/admin-cp/posts/new?id='.$id.'" class="btn-floating btn-large"><i class="large material-icons">add</i></a></div>
<div id="delete_modal" class="modal"><div class="modal-content"><span class="right red-text modal-close"><i class="material-icons">cancel</i></span><h4>Delete posts?</h4><p><div class="bold-text red-text">Are you sure to delete the selected post?</div><i class="material-icons">info</i> This action can\'t be undone!</p> <div class="modal-footer"><a class="btn waves-effect waves-light red" id="cat_id">Yes</a> <a href="javascript:void(0)" class="modal-close waves-effect waves-light btn green">No</a></div>
  </div></div>';
$script ='<script>
function openModal(id) {
document.getElementById("cat_id").setAttribute("href", "/admin-cp/posts/delete?pid='.$id.'&id=" + id);
var elems = document.getElementById("delete_modal");
	var instances = M.Modal.init(elems);
	instances.open();
}
</script>';
$nav = '<ul class="custom_breadcrumb">
<li><a href="/admin-cp/">Admin CP</a></li>
<li><a href="/admin-cp/categories/">Categories</a></li>
<li><a href="/admin-cp/sub-categories/?id='.$cid.'">'.$name.'</a></li>
<li>'.$sname.'</li>
</ul>';
Page::footer($script,$nav);
?>