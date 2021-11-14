<?php
require('includes/core.php');
$p_n = $form->get('p');
$p_n = str_replace('.php','',$p_n);
if(empty($p_n)) $p_n=1;
if(!is_numeric($p_n)||strlen($p_n)>4) {
    redirectTo('/');
    exit();
}
$start = ($p_n-1)*$pp;
$genre = $form->get('genre');
$genre = str_replace('.php','',$genre);
if(!in_array($genre,array_map('strtolower',$genres))) {
    redirectTo('/');
    exit();
}
$posts = $db->selectRows('posts',' WHERE genres like \'%'.$genre.'%\' ORDER BY time DESC LIMIT '.$start.', '.$pp.'');
$total_result = $db->countRows('SELECT * FROM posts WHERE genres like \'%'.$genre.'%\'');
$latest = $posts['data'];
if(preg_match('/\-/m', $genre)) {
    $name = explode('-',$genre);
    $name = ''.ucfirst($name[0]).'-'.ucfirst($name[1]).'';
} else {
    $name = ucfirst($genre);
}
Page::header(''.$name.' - '.SITE_NAME.'','Browse all '.$name.' contents','genres,'.$name.','.$name.'');
?>
<div class="row">
<div class="col s12"><h1><?=$name;?></h1></div>
<?php
if(empty($latest)) {
    echo '<div class="col s12"><div class="card-panel"><i class="material-icons middled">info</i> No contents here! Maybe there will be some soon!</div></div>';
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
if($total_result>$pp){
    echo '<ul class="pagination center col s12">';
    $pages=pagination($total_result,$p_n,$pp);
    if(is_array($pages))
    {
    foreach($pages as $key => $val)
    {
    if($val == $p_n)
    {echo ' <li class="active"><a href="javascript:void(0)"> '.$key.' </a></li> ';}
    else{echo ' <li class="waves-effect"><a href="/genres/'.$genre.'/page-'.$val.'"> '.$key.' </a></li> ';}
    }
    echo '</ul>';
    }
}
echo '</div>';
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/genres">Genres</a></li>
<li>'.$name.'</li>
</ul>';
Page::footer('',$extra);
?>