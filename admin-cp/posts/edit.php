<?php
require '../../includes/core.php';
$id = $form->get('id');
$pid = $form->get('pid');
$show_form = '';
$check = $db->selectRow('posts','id',$id);
$post_data = $check['data'];
if($check['result']=='failed') {
	header("Location: /admin-cp/posts/?msg_id=3&id=$id");
} else {
	################################## main starts ###################################
	$sub = $check['data']['sub_cat_id']; // sub category id
	$check2 = $db->selectRow('sub_categories','id',$sub);
	$cid =$check2['data']['category_id']; // category id
	$name = $db->selectRow('categories','id',$cid);
	$name = $name['data']['title']; // cat name
	$sname = $check2['data']['title']; // sub cat name
	$post_name = $check['data']['title']; // post name
Page::header('Edit Post - Site_Name');
$user_requests = $db->countRows('SELECT * FROM requests WHERE processed=0');
if($user_requests>0) {
	echo '<h1>User Requests</h1>
	<div class="card-panel"><i class="material-icons middled">info</i> There are '.$user_requests.' pending user <a href="/admin-cp/requests">requests</a></div>';
}
if($form->method()=="POST") {
		$sub_cat_id = $form->post('sub_cat_id');
		$q480_switch = $form->isExists('q480_switch');
		$q720_switch = $form->isExists('q720_switch');
		$q1080_switch = $form->isExists('q1080_switch');
		$gd_480 = $form->post('gd_480');
		$mirror_480 = $form->post('mirror_480');
		$mirror_480_name = $form->post('mirror_480_name');
		$gd_720 = $form->post('gd_720');
		$mirror_720 = $form->post('mirror_720');
		$mirror_720_name = $form->post('mirror_720_name');
		$gd_1080 = $form->post('gd_1080');
		$mirror_1080 = $form->post('mirror_1080');
		$mirror_1080_name = $form->post('mirror_1080_name');
		$episode = $form->post('episode');
		$season = $form->post('season');
		$email = $form->post('email');
		$email2 = $form->post('email2');
		// validation starts
		if (!isset($q480_switch)&&!isset($q720_switch)&&!isset($q1080_switch)) {
			$error[] = 'Please select at least one quality to upload';
		} elseif ($q480_switch===true) {
			if(!filter_var($gd_480, FILTER_VALIDATE_URL)) {
				$error[] = 'Google drive link (480p) URL is invalid';
			} elseif(!isGd($gd_480)) {
				$error[] = 'Google drive link (480p) is not a valid google drive link';
			} elseif(!empty($mirror_480)&&empty($mirror_480_name)) {
				$error[] = 'Mirror name (480p) can\'t be empty';
			} elseif (empty($mirror_480)&&!empty($mirror_480_name)) {
				$error[] = 'Mirror (480p) URL can\'t be empty';
			} else {
				if(!filter_var($mirror_480, FILTER_VALIDATE_URL)) {
					$error[] = 'Mirror (480p) URL is invalid';
				} elseif(strlen($mirror_480_name)>10||strlen($mirror_480_name)<3) {
					$error[] = 'Mirror (480p) Name is invalid';
				}
			}
			$mirror_480 = $mirror_480.' '.$mirror_480_name;
		} elseif ($q1080_switch===true) {
			if(!filter_var($gd_1080, FILTER_VALIDATE_URL)) {
				$error[] = 'Google drive link (1080p) URL is invalid';
			} elseif(!isGd($gd_1080)) {
				$error[] = 'Google drive link (1080p) is not a valid google drive link';
			} elseif(!empty($mirror_1080)&&empty($mirror_1080_name)) {
				$error[] = 'Mirror name (480p) can\'t be empty';
			} elseif (empty($mirror_1080)&&!empty($mirror_1080_name)) {
				$error[] = 'Mirror (1080p) URL can\'t be empty';
			} else {
				if(!filter_var($mirror_1080, FILTER_VALIDATE_URL)) {
					$error[] = 'Mirror (1080p) URL is invalid';
				} elseif(strlen($mirror_1080_name)>10||strlen($mirror_1080_name)<3) {
					$error[] = 'Mirror (1080p) Name is invalid';
				}
			}
			$mirror_480 = $mirror_480.' '.$mirror_480_name;
		} elseif ($q720_switch===true) {
			if(!filter_var($gd_720, FILTER_VALIDATE_URL)) {
				$error[] = 'Google drive link (720) URL is invalid';
			} elseif(!isGd($gd_720)) {
				$error[] = 'Google drive link (720p) is not a valid google drive link';
			} elseif(!empty($mirror_720)&&empty($mirror_720_name)) {
				$error[] = 'Mirror name (720p) can\'t be empty';
			} elseif (empty($mirror_720)&&!empty($mirror_720_name)) {
				$error[] = 'Mirror (720p) URL can\'t be empty';
			} else {
				if(!filter_var($mirror_720, FILTER_VALIDATE_URL)) {
					$error[] = 'Mirror (720p) URL is invalid';
				} elseif(strlen($mirror_720_name)>10||strlen($mirror_720_name)<3) {
					$error[] = 'Mirror (720p) Name is invalid';
				}
			}
			$mirror_720 = $mirror_720.' '.$mirror_720_name;
		}
		// validation ends
		if(empty($error)) {
			$column = array('gd_480','mirror_480','gd_720','mirror_720','gd_1080','mirror_1080','email');
			$values = array($gd_480,$mirror_480.' '.$mirror_480_name,$gd_720,$mirror_720.' '.$mirror_720_name,$gd_1080,$mirror_1080.' '.$mirror_1080_name,''.$email.' '.$email2.'');
			$data = array_combine($column, $values);
			$result = $db->updateRow('posts','id',$id,$data);
			//$tt22 = $result;
			if($result['result']=='success') {
				$show_form = 'no';
				$_SESSION['admin_msg'] = '<div class="card-panel green lighten-5 green-text"><i class="material-icons middled">check_circle</i> Post edited successfuly</div>';
				echo '<meta http-equiv="refresh" content="0; url=/admin-cp/posts/?id='.$sub.'">';
			} else {
				$error[] = 'Something went wrong! Contact Admin.';
			}
		}
}
if(empty($show_form)) {
	// show form
	$data = $check['data'];
	?>
<?php
if(!empty($error)) echo "<div class=\"card-panel red-text\"><i class=\"material-icons middled\">error</i> $error</div>";
?>
<div class="row">
<div class="col s12"><h1>Update Post</h1></div>
<?php
if(!empty($error)) foreach($error as $error) echo "<div class=\"red-text col s12\"><i class=\"material-icons middled\">error</i> $error</div>";
?>
<form method="post" action="/admin-cp/posts/edit?id=<?=$id;?>&pid=<?=$pid;?>">
<div class="input-field col s12"><input type="text" name="title" id="title" value="<?=$post_data['title'];?>" readonly><label for="title">Title</label></div>
<div class="col s12 input-field"><span class="switch"><label><input type="checkbox" id="q480_switch" name="q480_switch"><span class="lever"></span></label></span> 480p Download Link</div>
<div id="q480" class="hidden">
<div class="input-field col s12"><input name="gd_480" id="gd_480" type="url" value="<?=$post_data['gd_480'];?>"><label for="gd_480">Google Drive Link (Required)</label></div>
<div class="input-field col s6"><input name="mirror_480" id="mirror_480" type="url" value="<?=explode(' ',$post_data['mirror_480'])[0];?>"><label for="mirror_480">Mirror Link (Required)</label></div>
<div class="input-field col s6"><input name="mirror_480_name" id="mirror_480_name" type="text" value="<?=explode(' ',$post_data['mirror_480'])[1];?>"><label for="mirror_480_name">Mirror Name (Required)</label></div>
</div>

<div class="col s12 input-field"><span class="switch"><label><input type="checkbox" id="q720_switch" name="q720_switch"><span class="lever"></span></label></span> 720p Download Link</div>
<div id="q720" class="hidden">
<div class="input-field col s12"><input name="gd_720" id="gd_720" type="url" value="<?=$post_data['gd_720'];?>"><label for="gd_720">Google Drive Link (Required)</label></div>
<div class="input-field col s6"><input name="mirror_720" id="mirror_720" type="url" value="<?=explode(' ',$post_data['mirror_720'])[0];?>"><label for="mirror_720">Mirror Link (Required)</label></div>
<div class="input-field col s6"><input name="mirror_720_name" id="mirror_720_name" type="text" value="<?=explode(' ',$post_data['mirror_720'])[1];?>"><label for="mirror_720_name">Mirror Name (Required)</label></div>
</div>

<div class="col s12 input-field"><span class="switch"><label><input type="checkbox" id="q1080_switch" name="q1080_switch"><span class="lever"></span></label></span> 1080p Download Link</div>
<div id="q1080" class="hidden">
<div class="input-field col s12"><input name="gd_1080" id="gd_1080" type="url" value="<?=$post_data['gd_1080'];?>"><label for="gd_1080">Google Drive Link (Required)</label></div>
<div class="input-field col s6"><input name="mirror_1080" id="mirror_1080" type="url" value="<?=explode(' ',$post_data['mirror_1080'])[0];?>"><label for="mirror_1080">Mirror Link (Required)</label></div>
<div class="input-field col s6"><input name="mirror_1080_name" id="mirror_1080_name" type="text" value="<?=explode(' ',$post_data['mirror_1080'])[1];?>"><label for="mirror_1080_name">Mirror Name (Required)</label></div>
</div>
<div class="input-field col s6"><input name="email" id="email" type="email" value="<?=explode(' ',$post_data['email'])[0];?>" required><label for="email">Storage Email Gdrive</label></div>
<div class="input-field col s6"><input name="email2" id="email2" type="email" value="<?=explode(' ',$post_data['email'])[1];?>" required><label for="email2">Storage Email Mirror</label></div>
<div class="col s12"><button class="btn waves-effect waves-light" type="submit">Update</button></div>
</form>
</div>
	<?php
}
$nav = '<ul class="custom_breadcrumb">
<li><a href="/admin-cp/">Admin CP</a></li>
<li><a href="/admin-cp/categories/">Categories</a></li>
<li><a href="/admin-cp/sub-categories/?id='.$cid.'">'.$name.'</a></li>
<li><a href="/admin-cp/posts/?id='.$sub.'">'.$sname.'</a></li>
<li><a href="javascript:void(0)">'.$post_name.'</a></li>
<li>Edit</li>
</ul>';
Page::footer('<script src="/assets/js/update.js"></script>',$nav);
#################################### main ends #######################################
}
?>