<?php
require('includes/core.php');
Page::header(''.SITE_NAME.' - Stay Entertained','Home of free entertainments. Download movies and tv series easily. No ads, verified contents and google drive links + mirror.','movies,tv,series,free,download,latest,720p,1080p,480p,no,ads,ggogle,drive,gdrive,'.strtolower(SITE_NAME).'');
$latest = $db->selectRows('posts',' ORDER BY id DESC LIMIT 0, 12');
$latest = $latest['data'];
//$trending = $db->selectRows('posts',' ORDER BY views DESC LIMIT 0, 3')['data'];
$categories = $db->selectRows('categories');
$categories = $categories['data'];
?>
<div class="row">
<h1>Hello</h1>
<p>First, I want to apologize for not keeping my promise. I ran into some personal issues. This month I couldn't pay my broadband bill on time. So I had to wait half of this month to manage the bill until I get the connection back. Also, I got some assignents from my college. So I need to complete them ASAP. For those reasons, I was unable to work on AtoZFix. But I promise you that I will work on it whenever I get time after doing assignments. It may take a week to complete the assignments. Please be understanding my current situation. THANKS FOR BEING WITH US. AtoZFlix will be back soon with new updates and contents. Thank you again for your time.
<br/><br/><span class="small">Regards,</span><br/>Meraj-Ul Islam</p>
<div class="col s12"><h1>Latest Updates</h1></div>
<?php
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
    echo '<div class="col s12 center"><a href="/latest" class="btn waves-effect waves-light">More Updates</a></div>
    <div class="col s12 black"><h1>Categories</h1></div>';
    if(!empty($categories)) {
        echo '<div class="col s12"><ul class="collapsible black">';
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
        echo '</ul></div>';
    }
    echo '</div><div id="push"><div class="card-panel"><i class="material-icons middled green-text">lightbulb_outline</i> Subscribe to <a href="/notification">push notification</a> to get updates instantly</div></div>';
Page::footer('<script src="/assets/js/push.js"></script>');
?>