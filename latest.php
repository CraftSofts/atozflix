<?php
require('includes/core.php');
$p_n = $form->get('p');
if(empty($p_n)) $p_n=1;
if(!is_numeric($p_n)||strlen($p_n)>4) {
    redirectTo('/');
    exit();
}
$start = ($p_n-1)*$pp;
$latest = $db->selectRows('posts',' ORDER BY time DESC LIMIT '.$start.', '.$pp.'');
$total_result = $db->countRows('SELECT * FROM posts ORDER BY time DESC');
$latest = $latest['data'];
if(empty($latest)) { redirectTo('/'); exit(); }
//$categories = $db->selectRows('categories')['data'];
Page::header('Latest Updates - '.SITE_NAME.'','Get latest updates at one place','movies,updates,tv,series,new,latest,'.strtolower(SITE_NAME).'');
?>
<div class="row">
<div class="col s12"><h1>Latest Updates</h1></div>
<?php
if(empty($latest)) {
    echo '<div class="col s12"><div class="card-panel"><i class="material-icons middled">info</i> No content here! Maybe there will be some soon!</div></div>';
} else {
    foreach($latest as $latest) {
        $type = '';
        if($latest->title=='0') {
            $type = 'TV Series';
        } else {
            $type = 'Movie';
        }
        $sub_cat = $db->selectRow('sub_categories','id',$latest->sub_cat_id)['data'];
        $cat = $db->selectRow('categories','id',$sub_cat['category_id'])['data'];
        $link = '/'.$cat['link'].'/'.$sub_cat['link'].'/'.$latest->link.'';
        ?>
<div class="col s6 m4 l3">
    <div class="card hoverable ">
    <div class="card-image">
    <a href="<?=$link;?>"><img src="/assets/images/preloaders/funnel_256.svg" data-src="/assets/images/posters/<?=$link;?>.jpg"></a>
    </div>
    <div class="card-content">
    <span class="truncate"><a href="<?=$link;?>"><?=$latest->title;?> (<?=$latest->year;?>)</a></span>
    </div>
    </div>
</div>
        <?php
    }
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
    else{echo ' <li class="waves-effect"><a href="/latest/page-'.$val.'"> '.$key.' </a></li> ';}
    }
    echo '</ul>';
    }
}
    echo '</div><div id="push"><div class="card-panel"><i class="material-icons middled green-text">lightbulb_outline</i> Subscribe to <a href="/notification">push notification</a> to get updates instantly</div></div>';
 $extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li>Latest Contents</li>
</ul>';
Page::footer('<script src="/assets/js/push.js"></script>',$extra);
?>