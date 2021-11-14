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
$title = $form->post('title');
$show_form = '';
$error = '';
$id = $form->get('id');
$check = $db->selectRow('categories','id',$id);
if($check['result']=='failed') {
	$_SESSION['admin_msg'] = '<div class="card-panel red lighten-5 red-text"><i class="material-icons middled">error</i> Category not found</div>';
    $url = '/admin-cp/sub-categories/?id='.$id; // not found
    header("Location: $url");
}
$name = $check['data']['title'];
Page::header('Add Sub-Category - Site_Name');
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
		$column = array('title','link','category_id');
		$values = array($title,$link,$id);
		$result = $db->insertRow('sub_categories',$column,$values);
		if($result['result']=='success') {
			$show_form = 'no';
			$_SESSION['admin_msg'] = '<div class="card-panel green lighten-5 green-text"><i class="material-icons middled">check_circle</i> Sub-Category added successfuly</div>';
			echo '<meta http-equiv="refresh" content="0; url=/admin-cp/sub-categories/?id='.$id.'">';
		} else {
            $error = 'Something went wrong! Contact Admin.';
		}
	} else {
		// all fields are required
		$error = 'All fields are required!';
	}
}
if(empty($show_form)) {
	// show form
	?>
<h1>New Sub-Category</h1>
<?php
if(!empty($error)) echo "<div class=\"card-panel red-text\"><i class=\"material-icons middled\">error</i> $error</div>";
?>
<form method="post" action="/admin-cp/sub-categories/new?id=<?=$id;?>">
<div class="input-field"><input type="text" name="title" id="title"><label for="title">Title</label></div>
<button class="btn waves-effect waves-light" type="submit">Add</button>
</form>
	<?php
}
$extra = '<ul class="custom_breadcrumb">
<li><a href="/admin-cp/">Admin CP</a></li>
<li><a href="/admin-cp/categories">Categories</a></li>
<li><a href="/admin-cp/sub-categories/?id='.$id.'">'.$name.'</a></li>
<li>Add</li>
</ul>';
Page::footer('',$extra);