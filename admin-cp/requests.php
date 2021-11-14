<?php
require '../includes/core.php';
isAdmin($user, $admins);

if ($form->isExists('post') && $form->isExists('id')) {
    if ($db->selectRow('requests', 'id', $form->post('id'))['result'] == 'success') {
        if ($db->updateRow('requests', 'id', $form->post('id'), array('processed' => 1, 'post_id' => $form->post('post')))['result'] == 'success') {
            $_SESSION['admin_msg'] = '<div class="card-panel green lighten-5 green-text"><i class="material-icons middled">check_circle</i> Request processed successfuly</div>';
        } else {
            $_SESSION['admin_msg'] = '<div class="card-panel red lighten-5 red-text"><i class="material-icons middled">check_circle</i> Failed to process request</div>';
        }
    }
}

Page::header('User Requests - Site_Name');
// paginating
$pp = 10;
$p_n = $form->get('p');
if (empty($p_n)) {
    $p_n = 1;
}

$start = ($p_n - 1) * $pp;
$pending_list = $db->selectRows('requests', ' WHERE processed=0 LIMIT ' . $start . ', ' . $pp . '');
$total_result = $db->countRows('SELECT * FROM requests WHERE processed=0');
$pending_list = $pending_list['data'];

$total = $db->countRows('SELECT * FROM requests');
$pending = $db->countRows('SELECT * FROM requests WHERE processed=0');
$done = $db->countRows('SELECT * FROM requests WHERE processed=1');
?>
<h1>Info</h1>
    <div class="card-panel"><i class="material-icons middled">info</i> There are total <?=$total;?> requests, <?=$pending;?> pending requests, <?=$done;?> processed requests</div>
<h1>Pending Requests</h1>
<?php
showOneTimeMessage('admin_msg');
if (empty($pending_list)) {
    echo '<div class="card-panel"><i class="material-icons middled">info</i> No pending requests found! Good job <i class="material-icons middled">thumb_up</i></div>';
} else {
    foreach ($pending_list as $request) {
        if ($request->type == 0) {
            $type = 'TV Series';
        } else {
            $type = 'Movie';
        }
        echo '<form method="post" action="/admin-cp/requests">
    <div class="card-pane"><i class="material-icons middled">label</i> Name: ' . $request->title . '<br/>
    <i class="material-icons middled">compare</i> Type: ' . $type . '<br/>
    <i class="material-icons middled">date_range</i> Year: ' . $request->year . '<br/>
    <i class="material-icons middled">search</i> Search: <a href="https://google.com/search?q=' . $request->title . '+' . $request->year . '">Google</a><br/>
    <i class="material-icons middled">hourglass_full</i> Processed: <input type="hidden" name="id" value="' . $request->id . '"/> <span class="input-field inline"><input type="number" name="post" id="post" required/><label for="post">Post ID</label></span> <button type="submit" class="btn waves-effect waves-light">Done</button><br/></div>
    </form>';
    }
}

// show pagination
if ($total_result > $pp) {
    echo '<ul class="pagination center">';
    $pages = pagination($total_result, $p_n, $pp);
    if (is_array($pages)) {
        foreach ($pages as $key => $val) {
            if ($val == $p_n) {echo ' <li class="active"><a href="javascript:void(0)"> ' . $key . ' </a></li> ';} else {echo ' <li class="waves-effect"><a href="/admin-cp/requests?p=' . $val . '"> ' . $key . ' </a></li> ';}
        }
        echo '</ul>';
    }
}

$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/admin-cp/">Admin CP</a></li>
<li>User Requests</li>
</ul>';
Page::footer($script, $extra);
?>