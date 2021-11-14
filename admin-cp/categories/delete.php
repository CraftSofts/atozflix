<?php
require '../../includes/core.php';

isAdmin($user, $admins);

isHeadAdmin($user);

$id = $form->get('id');
$check = $db->selectRow('categories', 'id', $id);
if ($check['result'] == 'success') {
    $delete = $db->deleteRow('categories', 'id', $id);
    if ($delete['result'] == 'success') {
        setOneTimeMessage('admin_msg', '<div class="card-panel green lighten-5 green-text"><i class="material-icons middled">check_circle</i> Category deleted successfuly</div>');
        $url = '/admin-cp/categories/'; // successfull
    } else {
        setOneTimeMessage('admin_msg', '<div class="card-panel red lighten-5 red-text"><i class="material-icons middled">error</i> Failed to delete Category</div>');
        $url = '/admin-cp/categories/'; // failed
    }
} else {
    setOneTimeMessage('admin_msg', '<div class="card-panel red lighten-5 red-text"><i class="material-icons middled">error</i> Category not found</div>');
    $url = '/admin-cp/categories/'; // not found
}

header("location: $url");
