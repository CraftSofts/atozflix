<?php
require '../../includes/core.php';

isAdmin($user, $admins);

Page::header('Categories - ' . SITE_NAME . '');

userRequestsMessage($db);
brokenLinksMessage($db);

$list = $db->selectRows('categories');
$list = $list['data'];
?>
<h1>All Categories</h1>
<?php
showOneTimeMessage('admin_msg');
if (empty($list)) {
    echo '<div class="card-panel"><i class="material-icons middled">info</i> No categories</div>';
} else {
    foreach ($list as $category) {
        ?>
<div class="card-panel"><a href="/admin-cp/sub-categories/?id=<?=$category->id;?>"><?=$category->title;?></a> <?php if ($user['id'] == 1) {?><span class="right"><a href="/admin-cp/categories/edit?id=<?=$category->id;?>"><i class="material-icons middled">edit</i></a> <a href="javascript:void(0)" onclick="openModal(<?=$category->id;?>)"><i class="material-icons middled red-text">delete</i></a></span><?php }?></div>
<?php
}
}

if ($user['id'] == 1) {
    echo '<div class="fixed-action-btn"><a href="/admin-cp/categories/new" class="btn-floating btn-large waves-effect waves-light"><i class="large material-icons">add</i></a></div>
<div id="delete_modal" class="modal"><div class="modal-content"><span class="right red-text modal-close"><i class="material-icons">cancel</i></span><h4>Delete category?</h4><p><div class="bold-text red-text">Are you sure to delete the selected category? Sub-categories and posts under this category will be also deleted.</div><i class="material-icons">info</i> This action can\'t be undone!</p> <div class="modal-footer"><a class="btn waves-effect waves-light red" id="cat_id">Yes</a> <a href="javascript:void(0)" class="modal-close waves-effect waves-light btn green">No</a></div>
  </div></div>';
}

$script = '<script>
function openModal(id) {
document.getElementById("cat_id").setAttribute("href", "/admin-cp/categories/delete?id=" + id);
var elems = document.getElementById("delete_modal");
	var instances = M.Modal.init(elems);
	instances.open();
}
</script>';
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/admin-cp/">Admin CP</a></li>
<li>Categories</li>
</ul>';
Page::footer($script, $extra);
?>