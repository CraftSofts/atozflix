<?php
require '../../includes/core.php';
isAdmin($user, $admins);
isHeadAdmin($user);

$title = $form->post('title');
$show_form = '';
$error = '';

Page::header('Add Category - ' . SITE_NAME . '');

if ($form->method() == "POST") {
    // form submitted
    if (!empty($title)) {
        // proceed
        $link = convertToLink($title);
        $column = array('title', 'link');
        $values = array($title, $link);
        $result = $db->insertRow('categories', $column, $values);
        if ($result['result'] == 'success') {
            setOneTimeMessage('admin_msg', '<div class="card-panel green lighten-5 green-text"><i class="material-icons middled">check_circle</i> Category added successfuly</div>');
            echo '<meta http-equiv="refresh" content="0; url=/admin-cp/categories/">';
        } else {
            $error = 'Something went wrong! Contact Admin.';
            print_r($column);
            print_r($values);
            print_r($result);
        }
    } else {
        // all fields are required
        $error = 'All fields are required!';
    }
}
if (empty($show_form)) {
    // show form
    $data = $check['data'];
    ?>
<h1>New Category</h1>
<?php
if (!empty($error)) {
        echo "<div class=\"card-panel red-text\"><i class=\"material-icons middled\">error</i> $error</div>";
    }

    ?>
<form method="post" action="/admin-cp/categories/new">
<div class="input-field"><input type="text" name="title" id="title"><label for="title">Title</label></div>
<button class="btn waves-effect waves-light" type="submit">Add</button>
</form>
	<?php
}
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/admin-cp/">Admin CP</a></li>
<li><a href="/admin-cp/categories/">Categories</a></li>
<li>Add</li>
</ul>';
Page::footer('', $extra);