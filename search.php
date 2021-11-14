<?php
require('includes/core.php');
$type_q = '';
$year_q = '';
$q = $form->get('q');
$type = $form->get('search_type');
if(!empty($type)) {
    if($type=='s'||$type=='m') {
        if($type=='s') {
            $type_q = ' AND type=\'0\'';
        } elseif($type=='m') {
            $type_q = ' AND type=\'1\'';
        }
    }
}
$year = $form->get('search_year');
if(!empty($year)) {
    if(is_numeric($year) && strlen($year)==4) {
        $year_q = ' AND year='.$db->escapeString($year).'';
    }
}
$p_n = $form->get('p');
if(empty($p_n)) $p_n=1;
if(!is_numeric($p_n)||strlen($p_n)>4) {
    redirectTo('/');
    exit();
}
$start = ($p_n-1)*$pp;
$posts = $db->selectRows('posts',' WHERE title like \'%'.$db->escapeString($q).'%\''.$type_q.$year_q.' LIMIT '.$start.', '.$pp.'');
$total_result = $db->countRows('SELECT * FROM posts WHERE title like \'%'.$db->escapeString($q).'%\''.$type_q.$year_q.'');
$latest = $posts['data'];
if(!empty($q)&&strlen($q)>1) {
    $db2 = new Db('localhost',MYSQL_ADMIN,MYSQL_PASSWORD,MYSQL_SEARCHES);
    $check = $db2->selectRow('searches','keyword',$db2->escapeString(strtolower($q)));
    if($check['result']=='success') {
        $db2->updateRow('searches','keyword',$db2->escapeString($q),array('total'=>$check['data']['total']+1));
    } else {
        $db2->insertRow('searches',array('keyword','time'),array($db2->escapeString(strtolower($q)),date("U")));
    }
    if($total_result==1) {
        foreach($latest as $latest) {
            $sub_cat = $db->selectRow('sub_categories','id',$latest->sub_cat_id)['data'];
            $cat = $db->selectRow('categories','id',$sub_cat['category_id'])['data'];
            $link = '/'.$cat['link'].'/'.$sub_cat['link'].'/'.$latest->link.'';
        }
        redirectTo($link);
        exit();
    }
Page::header('Search result for '.$q.' - '.SITE_NAME.'','Search result for '.$q.'',''.strtolower($q).',search,find,'.strtolower(SITE_NAME).'');
?>
<div class="row">
<div class="col s12"><h1><?=$total_result;?> contents found for <strong><?=$q;?></strong></h1></div>
<?php
if(empty($latest)) {
    echo '<div class="col s12"><div class="card-panel"><i class="material-icons middled">info</i> No content found for "'.$q.'"!</div></div>';
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
    else{echo ' <li class="waves-effect"><a href="/search?q='.$q.'&p='.$val.'&search_type='.$type.'&search_year='.$year.'"> '.$key.' </a></li> ';}
    }
    echo '</ul>';
    }
}
echo '</div>';
} else {
	Page::header('Search - '.SITE_NAME.'','Search for the content you are looking for right away','search,find,movie,new,latest');
	// show form
    ?>
<div class="row">
<div class="col s12"><h1>Search</h1></div>
<form method="get" acton="/search" id="search_form_m">
<div class="col s12 input-field"><input type="text" name="q" id="q" onkeydown="autoComplete(this.value,'search_form_s')" autocomplete="off" required><label for="q">Enter keyword</label></div>
<div class="col s6 input-field"><select name="search_type" id="search_type"><option selected>Content type</option><option value="m" selected>Movie</option><option value="s">TV Series</option></select><label for="search_type">Type</label></div>
<div class="col s6 input-field"><input type="number" name="search_year" id="search_year" data-length="4"><label for="search_year">Year</label></div>
<div class="col s12"><button class="btn waves-effect waves-light" type="submit">Search</button></div>
</form>
</div>
	<?php
}
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li>Search</li>
</ul>';
Page::footer('',$extra);
?>