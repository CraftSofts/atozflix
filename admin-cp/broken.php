<?php
// include required files
require '../includes/core.php';

// check if the logged in user is an admin or not
isAdmin($user, $admins);

// if a id of any request has been sent via url
if ($form->isExists('id')) {
    // if the id is valid update the post
    if ($db->selectRow('posts', 'id', $form->post('id'))['result'] == 'success') {
        if ($db->updateRow('posts', 'id', $form->post('id'), array('broken' => '0'))['result'] == 'success') {
            setOneTimeMessage('broken_msg','<div class="card-panel green lighten-5 green-text"><i class="material-icons middled">check_circle</i> Request processed successfuly</div>');
        } else {
            setOneTimeMessage('broken_msg','<div class="card-panel red lighten-5 red-text"><i class="material-icons middled">check_circle</i> Failed to process request</div>');
        }
    }
}

// set page's html header
Page::header('Broken Links - '.SITE_NAME.'');

// get a list of posts with broken links
$broken_posts = $db->selectRows('posts', ' WHERE broken=1');
$broken_posts = $broken_posts['data'];
$msg = $_SESSION['broken_msg'];

userRequestsMessage($db);
?>
<h1>Pending Requests</h1>
<?php
showOneTimeMessage('broken_msg');
showOneTimeMessage('request_msg');
if (empty($broken_posts)) {
    echo '<div class="card-panel><i class="material-icons middled">info</i> No pending requests found! Good job <i class="material-icons middled">thumb_up</i></div>';
} else {
    foreach ($broken_posts as $request) {
        if ($request->type == 0) {
            $type = 'TV Series';
        } else {
            $type = 'Movie';
        }
        echo '<form method="post" action="/admin-cp/broken">
    <div class="card-panel"><i class="material-icons middled">label</i> Name: ' . $request->title . '<br/>
    <i class="material-icons middled">edit</i> Fix: <a href="/admin-cp/posts/edit?id=' . $request->id . '">Edit</a><br/>
    <i class="material-icons middled">hourglass_full</i> Action: <input type="hidden" name="id" value="' . $request->id . '"/><button type="submit" class="btn waves-effect waves-light"><i class="material-icons middled">check</i> Fixed</button></div>
    </form>';
    }
}

$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/admin-cp/">Admin CP</a></li>
<li>Broken Links</li>
</ul>';
Page::footer($script, $extra);
?>