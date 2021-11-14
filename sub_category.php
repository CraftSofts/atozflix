<?php
require('includes/core.php');
$cat = $form->get('cat');
$sub = $form->get('sub');
$cat = $db->selectRow('categories','link',$cat);
if($cat['result']=='failed') redirectTo('/');
$sub = $db->selectRow('sub_categories','link',$sub,' AND category_id='.$cat['data']['id']);
if($sub['result']=='failed') redirectTo('/');
$cat = $cat['data'];
$sub = $sub['data'];
$p_n = $form->get('p');
if(empty($p_n)) $p_n=1;
if(!is_numeric($p_n)||strlen($p_n)>4) {
    redirectTo('/');
    exit();
}
$start = ($p_n-1)*$pp;
$total_result = $db->countRows('SELECT * FROM posts WHERE sub_cat_id='.$sub['id'].' ORDER BY time DESC');
$posts = $db->selectRows('posts',' WHERE sub_cat_id='.$sub['id'].' ORDER BY time DESC LIMIT '.$start.', '.$pp.'');
$posts = $posts['data'];
Page::header(''.$sub['title'].' - '.SITE_NAME.'','All contents from '.strtolower($sub['title']).'',''.strtolower($sub['title']).',contents');
echo '<div class="row">
<div class="col s12"><h1>'.$sub['title'].'</h1></div>';
    if(!empty($posts)) {
        foreach($posts as $post) {
            $type = '';
            if($post->title=='0') {
                $type = 'TV Series';
            } else {
                $type = 'Movie';
            }
            $link = '/'.$cat['link'].'/'.$sub['link'].'/'.$post->link.'';
            ?>
<div class="col s6 m4 l3">
    <div class="card hoverable ">
    <div class="card-image">
    <a href="<?=$link;?>"><img src="/assets/images/preloaders/funnel_256.svg" data-src="/assets/images/posters/<?=$link;?>.jpg"></a>
    </div>
    <div class="card-content">
    <span class="truncate"><a href="<?=$link;?>"><?=$post->title;?> (<?=$post->year;?>)</a></span>
    </div>
    </div>
</div>
            <?php
        }
    } else {
        echo '<div class="col s12"><div class="card-panel"><i class="material-icons middled">info</i> No content here! Maybe there will be some soon!</div></div>';
    }
if($total_result>$pp){
    echo '<ul class="pagination center col s12">';
    $pages=pagination($total_result,$p_n,$pp);
    if(is_array($pages))
    {
    foreach($pages as $key => $val)
    {
    if($val == $p_n)
    {echo ' <li class="active"><a href="javascript:void(0)"> '.$key.' </a></li> ';}
    else{echo ' <li class="waves-effect"><a href="/'.$cat['link'].'/'.$sub['link'].'/page-'.$val.'"> '.$key.' </a></li> ';}
    }
    echo '</ul>';
    }
}
if(isLoggedIn()) {
	if(in_array($user['id'],$admins)) {
echo '<div class="fixed-action-btn"><a href="/admin-cp/posts/new?id='.$sub['id'].'" class="btn-floating btn-large"><i class="large material-icons">add</i></a></div>';
	}
}
echo '</div>';
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/'.$cat['link'].'">'.$cat['title'].'</a></li>
<li>'.$sub['title'].'</li>
</ul>';
Page::footer('',$extra);