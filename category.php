<?php
require('includes/core.php');
$cat = $form->get('cat');$cat = $db->selectRow('categories','link',$cat);
if($cat['result']=='failed') { redirectTo('/'); exit(); }
$category = $cat['data'];
Page::header(''.$category['title'].' - '.SITE_NAME.' ','View all contents under "'.$category['title'].'"',''.$category['title'].',category,movies,tv,series,'.strtolower(SITE_NAME).','.strtolower($category['title']).'');
echo '<h1>'.$category['title'].'</h1>';
    if(!empty($category)) {
      $sub_categories = $db->selectRows('sub_categories',' WHERE category_id='.$category['id']);
      $sub_categories = $sub_categories['data'];
      if(empty($sub_categories)) {
          echo '<i class="material-icons">info</i> Nothing here';
      } else {
          echo '<ul class="collection">';
          foreach($sub_categories as $sub_category) {
              $contents = $db->countRows('SELECT * FROM posts WHERE sub_cat_id='.$sub_category->id);
          echo '<li class="collection-item black"><a href="/'.$category['link'].'/'.$sub_category->link.'"><i class="material-icons middled">folder</i> '.$sub_category->title.'<span class="right">('.$contents.')</span></a></li>';
          }
          echo '</ul>';
      }
    }
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li>'.$category['title'].'</li>
</ul>';
Page::footer('',$extra);