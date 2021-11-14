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
$title = $form->post('title');
$show_form = '';
$error = '';
$check = $db->selectRow('sub_categories','id',$id);
if($check['result']=='failed') {
	$_SESSION['admin_msg'] = '<div class="card-panel red lighten-5 red-text"><i class="material-icons middled">error</i> Sub-Category not found</div>';
    $url = '/admin-cp/categories/'; // not found
    header("Location: $url");
} else {
	$name = $db->selectRow('categories','id',$pid);
	$name = $name['data']['title'];
	$sname = $check['data']['title'];
Page::header('Edit Category - Site_Name');
$user_requests = $db->countRows('SELECT * FROM requests WHERE processed=0');
if($user_requests>0) {
	echo '<h1>User Requests</h1>
	<div class="card-panel"><i class="material-icons middled">info</i> There are '.$user_requests.' pending user <a href="/admin-cp/requests">requests</a></div>';
}
if($form->method()=="POST") {
	// form submitted
	if(!empty($title)) {
		// proceed
		$link = convertToLink($title);
		$data = array('title'=>$title,'link'=>$link);
		$result = $db->updateRow('sub_categories','id',$id,$data);
		if($result['result']=='success') {
			$_SESSION['admin_msg'] = '<div class="card-panel green lighten-5 green-text"><i class="material-icons middled">check_circle</i> Sub-Category edited successfuly</div>';
			echo '<meta http-equiv="refresh" content="0; url=/admin-cp/sub-categories/?id='.$pid.'">';
		} else {
			$show_form = 'no';
			$error = 'Something went wrong! Contact Admin.';
		}
	} else {
		// all fields are required
		$error = 'All fields are required!';	
	}
}
if(empty($show_form)) {
	// show form
	$data = $check['data'];
	?>
<h1>Edit Category</h1>
<?php
if(!empty($error)) echo "<div class=\"card-panel red-text\"><i class=\"material-icons middled\">error</i> $error</div>";
?>
<form method="post" action="/admin-cp/sub-categories/edit?id=<?=$id;?>&pid=<?=$pid;?>">
<div class="input-field"><input type="text" name="title" value="<?=$data['title'];?>" id="title"><label for="title">Title</label></div>
<button class="btn waves-effect waves-light" type="submit">Update</button>
</form>
	<?php
}
$nav = '<ul class="custom_breadcrumb">
<li><a href="/admin-cp/">Admin CP</a></li>
<li><a href="/admin-cp/categories/">Categories</a></li>
<li><a href="/admin-cp/sub-categories/?id='.$pid.'">'.$name.'</a></li>
<li><a href="javascript:void(0)">'.$sname.'</a></li>
<li>Edit</li>
</ul>';
Page::footer('',$nav);
}
?>