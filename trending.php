<?php
require('includes/core.php');
Page::header('Trending Contents - '.SITE_NAME.'', 'View trending contents','trending,top,most,'.strtolower(SITE_NAME).'');
echo '<div class="row">';
$latest = $db->selectRows('posts',' ORDER BY views DESC LIMIT 0, '.$pp.'')['data'];
echo '<div class="col s12"><h1>Trending Contents</h1></div>';
if(empty($latest)) {
    echo '<div class="col s12"><div class="card-panel"><i class="material-icons middled">info</i> No content here! Maybe there will be some soon!</div></div>';
} else {
foreach($latest as $latest) {
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
echo '</div>';
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li>Trending Contents</li>
</ul>';
Page::footer('',$extra);