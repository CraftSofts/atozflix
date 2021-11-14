<?php
// include the core file
require('../includes/core.php');
if(!isLoggedIn()) {
    // user is not logged in, redirect to the login page with a message
    $_SESSION['login_msg'] = 'You need to be logged in to view your requests';
    $_SESSION['target_url'] = CURRENT_URL;
    redirectTo('/user/login');
    exit();
}
$total_pending = $db->countRows('SELECT * FROM requests WHERE processed=0 AND user_id='.$user['id']);
$total_processed = $db->countRows('SELECT * FROM requests WHERE processed=1 AND user_id='.$user['id']);
$requests_processed = $db->selectRows('requests',' WHERE user_id='.$user['id'].' AND processed=1')['data'];
$requests_pending = $db->selectRows('requests',' WHERE user_id='.$user['id'].' AND processed=0')['data'];
Page::header('Requests - '.SITE_NAME.'');
echo '<h1>Pending Requests ('.$total_pending.')</h1>
<ul class="collection">';
if($total_pending<1) {
    echo '<li class="collection-item"><i class="material-icons middled">info</i> No pending requests!</li>';
} else {
    foreach($requests_pending as $request) {
        echo '<div class="col s12"><div class="card-panel"><i class="material-icons middled">hourglass_full</i> '.$request->title.' ('.$request->year.')</div></div>';
    }
}
echo '</ul>';
if($total_processed>0) {
    echo '<h1>Processed Requests ('.$total_processed.')</h1>
    <ul class="collection">';
    foreach($requests_processed as $request2) {
        $post = $db->selectRow('posts','id',$request2->post_id);
        if($post['result']=='success') {
        $sub_cat = $db->selectRow('sub_categories','id',$post['data']['sub_cat_id'])['data'];
        $cat = $db->selectRow('categories','id',$sub_cat['category_id'])['data'];
        $link = '/'.$cat['link'].'/'.$sub_cat['link'].'/'.$post['data']['link'].'';
        echo '<li class="collection-item"><i class="material-icons middled green-text">check_circle</i> <a href="'.$link.'">'.$post['data']['title'].'</a></li>';
        }
    }
}
echo '</ul>';
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/user">User</a></li>
<li>Requests</li>
</ul>';
Page::footer('',$extra);