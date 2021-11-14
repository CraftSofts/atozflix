<?php
require('../includes/core.php');
if(!isLoggedIn()) {
    $_SESSION['login_msg'] = 'You need to be logged in to view wishlist';
    $_SESSION['target_url'] = CURRENT_URL;
    redirectTo('/user/login');
    exit();
}
Page::header('Wishlist - '.SITE_NAME.'');
echo '<div class="row">';
$wishlist = $users->selectRow('users','id',$user['id'])['data']['wishlist'];
$wishlist = explode('|',$wishlist);
foreach($wishlist as $id) {
    if($db->selectRow('posts','id',$id)['result']=='success') {
    $latest[] = $db->selectRow('posts','id',$id)['data'];
    $count = $count+1;
    }
}
echo '<div class="col s12"><h1>Wishlist ('.$count.'/50)</h1></div>';
    if(empty($latest)) {
        echo '<div class="col s12"><div class="card-panel"><i class="material-icons middled">info</i> Nothing wishlisted! Try adding some.</div></div>';
    } else {
    foreach($latest as $latest) {
        $sub_cat = $db->selectRow('sub_categories','id',$latest['sub_cat_id'])['data'];
        $cat = $db->selectRow('categories','id',$sub_cat['category_id'])['data'];
        $link = '/'.$cat['link'].'/'.$sub_cat['link'].'/'.$latest['link'].'';
        ?>
<div class="col s6 m4 l3">
    <div class="card hoverable ">
    <div class="card-image">
    <a href="<?=$link;?>"><img src="/assets/images/preloaders/funnel_256.svg" data-src="/assets/images/posters/<?=$link;?>.jpg"></a>
    </div>
    <div class="card-content">
    <span class="truncate"><a href="<?=$link;?>"><?=$latest['title'];?> (<?=$latest['year'];?>)</a></span>
    </div>
    </div>
</div>
        <?php
    }
}
echo '</div>';
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/user">User</a></li>
<li>Wishlist</li>
</ul>';
Page::footer('',$extra);