<?php
require '../includes/core.php';

isAdmin($user,$admins);

Page::header('User Searches - '.SITE_NAME.'');
// paginating
$pp = 30;
$p_n = $form->get('p');
if(empty($p_n)) $p_n=1;
$start = ($p_n-1)*$pp;
$now = date("U");
$db2 = new Db('localhost',MYSQL_ADMIN,MYSQL_PASSWORD,MYSQL_SEARCHES);
$top = $db2->selectRows('searches',' ORDER BY total DESC LIMIT 0, 10')['data'];
?>
<div class="row">
<div class="col s12"><h1>Top 10 Keywords</h1></div>
<?php
foreach ($top as $top) {
    if(!empty($top))
echo '<div class="col s6"><a href="/search?q='.$top->keyword.'">'.$top->keyword.' ('.$top->total.')</a></div>';
}
// count today's keywords
$compare = 60*60*24;
$diff = $now - $compare;
$today = $db2->selectRows('searches',' WHERE time BETWEEN '.$diff.' AND '.$now.' ORDER BY total DESC LIMIT '.$start.', '.$pp.'');
$data = $today['data'];
$total_result = $db->countRows('SELECT * FROM searches WHERE time BETWEEN '.$diff.' AND '.$now.'');
if(!empty($data)) {
    echo '<div class="col s12"><h1>Today\'s Most Popular Keywords</h1></div>';
    foreach($data as $keyword) {
        echo '<div class="col s6"><a href="/search?q='.$keyword->keyword.'">'.$keyword->keyword.' ('.$keyword->total.')</a></div>';
    }
}
// show pagination
if($total_result>$pp){
    echo '<ul class="pagination center col s12">';
    $pages=pagination($total_result,$p_n,$pp);
    if(is_array($pages))
    {
    foreach($pages as $key => $val)
    {
    if($val == $p_n)
    {echo ' <li class="active"><a href="javascript:void(0)"> '.$key.' </a></li> ';}
    else{echo ' <li class="waves-effect"><a href="/admin-cp/requests?p='.$val.'"> '.$key.' </a></li> ';}
    }
    echo '</ul>';
    }
}
// last 7 days
$compare = 60*60*24*7;
$diff = $now - $compare;
$week = $db2->selectRows('searches',' WHERE time BETWEEN '.$diff.' AND '.$now.' ORDER BY total DESC LIMIT '.$start.', '.$pp.'');
$data = $week['data'];
$total_result = $db->countRows('SELECT * FROM searches WHERE time BETWEEN '.$diff.' AND '.$now.'');
if(!empty($data)) {
    echo '<div class="col s12"><h1>Last 7 Day\'s Most Popular Keywords</h1></div>';
    foreach($data as $keyword) {
        echo '<div class="col s6"><a href="/search?q='.$keyword->keyword.'">'.$keyword->keyword.' ('.$keyword->total.')</a></div>';
    }
}
// show pagination
if($total_result>$pp){
    echo '<ul class="pagination center col s12">';
    $pages=pagination($total_result,$p_n,$pp);
    if(is_array($pages))
    {
    foreach($pages as $key => $val)
    {
    if($val == $p_n)
    {echo ' <li class="active"><a href="javascript:void(0)"> '.$key.' </a></li> ';}
    else{echo ' <li class="waves-effect"><a href="/admin-cp/requests?p='.$val.'"> '.$key.' </a></li> ';}
    }
    echo '</ul>';
    }
}
// last 30 days
$compare = 60*60*24*7;
$diff = $now - $compare;
$month = $db2->selectRows('searches',' WHERE time BETWEEN '.$diff.' AND '.$now.' ORDER BY total DESC LIMIT '.$start.', '.$pp.'');
$data = $month['data'];
$total_result = $db->countRows('SELECT * FROM searches WHERE time BETWEEN '.$diff.' AND '.$now.'');
if(!empty($data)) {
    echo '<div class="col s12"><h1>Last 30 Day\'s Most Popular Keywords</h1></div>';
    foreach($data as $keyword) {
        echo '<div class="col s6"><a href="/search?q='.$keyword->keyword.'">'.$keyword->keyword.' ('.$keyword->total.')</a></div>';
    }
}
// show pagination
if($total_result>$pp){
    echo '<ul class="pagination center col s12">';
    $pages=pagination($total_result,$p_n,$pp);
    if(is_array($pages))
    {
    foreach($pages as $key => $val)
    {
    if($val == $p_n)
    {echo ' <li class="active"><a href="javascript:void(0)"> '.$key.' </a></li> ';}
    else{echo ' <li class="waves-effect"><a href="/admin-cp/requests?p='.$val.'"> '.$key.' </a></li> ';}
    }
    echo '</ul>';
    }
}
?>

<?php
echo '</div>';
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/admin-cp/">Admin CP</a></li>
<li>Searches</li>
</ul>';
Page::footer($script,$extra);
?>