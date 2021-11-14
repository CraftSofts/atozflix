<?php
require('includes/core.php');
Page::header('Genres - '.SITE_NAME.'','Browse all contents by genres','genres,'.strtolower(SITE_NAME).'');
?>
<h1>All Genres</h1>
<ul class="collection">
<?php
    // print posts
    foreach($genres as $cat) {
        $total = $db->countRows('SELECT id FROM posts WHERE genres like \'%'.$cat.'%\'');
        echo '<li  class="collection-item black"><a href="/genres/'.strtolower($cat).'"><i class="material-icons middled">folder</i> '.$cat.' <span class="right">('.$total.')</span></a></li>';
?>
        <?php
    }
echo '</div>';
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li>Genres</li>
</ul>';
Page::footer('',$extra);
?>