<?php
require('includes/core.php');
Page::header('Categories - '.SITE_NAME.'','List of all available categories','category,categories,movies,list,'.strtolower(SITE_NAME).'');
$categories = $db->selectRows('categories');
$categories = $categories['data'];
echo '<h1>All Categories</h1>';
    if(!empty($categories)) {
        echo '<ul class="collapsible grey darken-3">';
        foreach($categories as $category) {
            ?>
<li>
<div class="collapsible-header black"><i class="material-icons">folder</i> <?=$category->title;?></div>
      <div class="collapsible-body black"><?php
      $sub_categories = $db->selectRows('sub_categories',' WHERE category_id='.$category->id);
      $sub_categories = $sub_categories['data'];
      if(empty($sub_categories)) {
          echo '<i class="material-icons">info</i> Nothing here';
      } else {
          echo '<ul class="collection black">';
          foreach($sub_categories as $sub_category) {
              $contents = $db->countRows('SELECT * FROM posts WHERE sub_cat_id='.$sub_category->id);
          echo '<li class="collection-item black"><a href="/'.$category->link.'/'.$sub_category->link.'"><i class="material-icons middled">folder</i> '.$sub_category->title.'<span class="right">('.$contents.')</span></a></li>';
          }
          echo '</ul>';
      }
      ?></div>
</li>
            <?php
        }
        echo '</ul>';
    }
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li>Categories</li>
</ul>';
Page::footer('',$extra);