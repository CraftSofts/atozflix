<?php
require('includes/core.php');
$cat = $form->get('cat');
$sub = $form->get('sub');
$post = $form->get('post');
$cat = $db->selectRow('categories','link',$cat);
if($cat['result']=='failed') { redirectTo('/'); exit(); }
$sub = $db->selectRow('sub_categories','link',$sub,' AND category_id='.$cat['data']['id']);
if($sub['result']=='failed') { redirectTo('/'); exit(); }
// views
$vw = 'a2z_pviews';
if(isset($_COOKIE[$vw])) {
$split = explode(':',$_COOKIE[$vw ]);
//setcookie($vw, '', time() - 1, "/", "", true, true);
$view_data = $db->selectRow('posts','link',$post,' AND sub_cat_id='.$sub['data']['id'])['data'];
$id = $view_data['id'];
$views = (int) $view_data['views'] + 1;
if (!in_array($id,$split)) {
    $value = ''.$get_vw.''.$id.':';
    $expiry = time()+3153600000;
    $domain = 'merajbd.com';
    setcookie($vw, $value, $expiry, "/", "", true, true);
    $db->updateRow('posts','id',$id,array('views'=>$views));
}
} else {
    $value = ''.$id.':';
    $expiry = time()+3153600000;
    setcookie($vw, $value, $expiry, "/", "", true, true);
    $db->updateRow('posts','id',$id,array('views'=>$views));
}
$post = $db->selectRow('posts','link',$post,' AND sub_cat_id='.$sub['data']['id']);
if($post['result']=='failed') { redirectTo('/'); exit(); }

// imdb data update process
$post_time = $post['data']['last_updated'];
if(empty($post_time)) $post_time = 0;
$diff = time()-$post_time;
$calc = round($diff/86400); // check days
if($calc>90) { // update info if last updated more than 90 days ago
// update imdb infos
$imdb_id = $post['data']['imdb_link'];
$imdb_id = preg_replace('/(.*?)title\//m', '', $imdb_id);
$imdb_id = preg_replace('/\/(.*)/m', '', $imdb_id);
$update_url = 'http://www.omdbapi.com/?apikey=ad692e&i='.$imdb_id;
$contents_new = file_get_contents($update_url);
$contents_new = json_decode($contents_new);
$imdb_rating_new = $contents_new->imdbRating;
$update_data = array('imdb_rating'=>$imdb_rating_new,'last_updated'=>time());
$db->updateRow('posts','id',$post['data']['id'],$update_data);
}
$cat = $cat['data'];
$sub = $sub['data'];
$post = $post['data'];
$link = '/'.$cat['link'].'/'.$sub['link'].'/'.$post['link'].'';
$type = '';
if($post['type']=='0') {
    $type = 'TV Series';
    $type_des = 'all episodes';
} else {
    $type = 'Movie';
    $type_des = 'full movie';
}
$title2kw = convertToLink($post['title']);
$title2kw = str_replace('-',',',$title2kw);
if($post['type']=='1') { $des = ' or watch online'; $kw = ',watch,online'; } else { $des = ' '; $kw = ''; }
Page::header(''.$post['title'].' - '.SITE_NAME.'','Download'.$des.' '.$post['title'].' '.$type_des.' for free',''.$title2kw.',download'.$kw.',free,480p,720p,1080p,hd,full,hd,google,drive,gdrive','https://'.$_SERVER['HTTP_HOST'].'/assets/images/posters'.$link.'.jpg');
echo '<div class="row">
<div class="col s12 m12 l12"><h1>'.$post['title'].'</h1></div>';
            $mega = 0;
            $author = $db->selectRow('users','id',$post['user_id']);
            $author = $author['data'];
            $wish = $users->selectRow('users','id',$user['id'])['data'];
            $wish = $wish['wishlist'];
            if(!empty($wish)) {
                $wish = explode('|',$wish);
                if(in_array($post['id'],$wish)) {
                    $wish_btn = '━ Remove from wishlist';
                } else {
                    $wish_btn = '✚ Add to wishlist';
                }
            } else {
                $wish_btn = '✚ Add to wishlist';
            }
            $plot = $post['plot'];
            if(empty($plot)) $plot = 'N/A';
            $director = $post['director'];
            if(empty($director)) $director = 'N/A';
            $writer = $post['writer'];
            if(empty($writer)) $writer = 'N/A';
            $runtime = $post['runtime'];
            if(empty($runtime)) { $runtime = 'N/A'; } else { $runtime = ''.$runtime.' minutes'; }
            ?>
    <div class="col s12 m6 l6"><div class="poster"><img src="/assets/images/preloaders/funnel_256.svg" data-src="/assets/images/posters/<?=$cat['link'];?>/<?=$sub['link'];?>/<?=$post['link'];?>.jpg" class="responsive-img poster materialboxed" data-caption="<?=$post['title'];?>"></div></div>
    <div class="col s12 m6 l6">
        <p><i class="material-icons middled">book</i> <strong>Plot:</strong> <?=$plot;?></p>
        <?php
        if($post['type']=='0') {
        $check = $db->countRows('SELECT * FROM posts WHERE title=\''.$post['title'].'\' AND season=\''.$post['season'].'\'');
        if($check>1) {
            $episodes = $db->selectRows('posts',' WHERE title=\''.$post['title'].'\' AND season=\''.$post['season'].'\'')['data'];
            foreach($episodes as $episode) {
                $link[] = '<a href="/'.$cat['link'].'/'.$sub['link'].'/'.convertToLink($post['title']).'-s'.$post['season'].'-e'.$episode->episode.'">'.$episode->episode.'</a>';
            }
            $link = implode(', ',$link);
            echo ' <p><i class="material-icons middled">collections_bookmark</i> <strong>Season:</strong> '.$post['season'].' <strong>Episode:</strong> '.$link.'</p>';
        } else {
        ?>
        <p><i class="material-icons middled">collections_bookmark</i> <strong>Season:</strong> <?=$post['season'];?> <strong>Episode:</strong> <?=$post['episode'];?></p>
        <?php }
        }
        ?>
        <p><i class="material-icons middled">assignment_ind</i> <strong>Cast:</strong> <?=$post['cast'];?></p>
        <p><i class="material-icons middled">directions</i> <strong>Director:</strong> <?=$director;?></p>
        <p><i class="material-icons middled">slideshow</i> <strong>Writer:</strong> <?=$writer;?></p>
        <p><i class="material-icons middled">dvr</i> <strong>Genre:</strong> <?php
        $genres = explode(',',$post['genres']);
        $genre_link = '';
        $items = count($genres);
        $i = 0;
        foreach($genres as $genre) {
            $genre = trim($genre);
            if(++$i === $items) {
                if(!empty($genre))
                $genre_link .= '<a href="/genres/'.strtolower($genre).'">'.$genre.'</a>';
            } else {
                if(!empty($genre))
                $genre_link .= '<a href="/genres/'.strtolower($genre).'">'.$genre.'</a>, ';
            }
        }
            echo $genre_link;
        ?></p>
        <p><i class="material-icons middled">date_range</i> <strong>Release year:</strong> <?=$post['year'];?></p>
        <p><i class="material-icons middled">camera_roll</i> <strong>Length:</strong> <?=$runtime;?><?php if($post['type']=='0') echo ' each'; ?></p>
        <p><i class="material-icons middled">stars</i> <strong>IMDB Rating:</strong> <a href="<?=$post['imdb_link'];?>" target="_blank"><?=$post['imdb_rating'];?></a>/10</p>
        <p><i class="material-icons middled">remove_red_eye</i> <strong>Total Views:</strong> <?=$post['views'];?></p>
    </div>
<div class="col s12"><h1>Download</h1></div>
<?php if(!empty($post['gd_480'])) {
    $mirror_480 = explode(' ',$post['mirror_480']);
    $mirror_link = $mirror_480[0];
    $mirror_name = $mirror_480[1];
    $stream_gd['480'] = $post['gd_480'];
    if($mirror_name=='mega'||$mirror_name=='Mega'||$mirror_name=='MEGA') { $mega = 1; $stream_mirror['480'] = $mirror_link; }
    ?>
    <div class="col s12"></div>
        <div class="col s12"><span class="padding"><a href="<?php if($post['type']=='0') { echo $post['gd_480']; } else {  echo gdToDownload($post['gd_480']); } ?>" class="btn waves-effect waves-light" target="_blank"><i class="material-icons middled">file_download</i> GDrive (480p)</a></span> <span class="padding"><a href="<?=$mirror_link;?>" class="btn waves-effect waves-light" target="_blank"><i class="material-icons middled">file_download</i> <?=$mirror_name;?> (480p)</a></span></div>
<?php } ?>
<?php if(!empty($post['gd_720'])) {
    $mirror_720 = explode(' ',$post['mirror_720']);
    $mirror_link = $mirror_720[0];
    $mirror_name = $mirror_720[1];
    $stream_gd['720'] = $post['gd_720'];
    if($mirror_name=='mega'||$mirror_name=='Mega'||$mirror_name=='MEGA') { $mega = 1; $stream_mirror['720'] = $mirror_link; }
    ?>
        <div class="col s12"><span class="padding"><a href="<?php if($post['type']=='0') { echo $post['gd_720']; } else {  echo gdToDownload($post['gd_720']); } ?>" class="btn waves-effect waves-light" target="_blank"><i class="material-icons middled">file_download</i> GDrive (720p)</a></span> <span class="padding"><a href="<?=$mirror_link;?>" class="btn waves-effect waves-light" target="_blank"><i class="material-icons middled">file_download</i> <?=$mirror_name;?> (720p)</a></span></div>
<?php } ?>
<?php if(!empty($post['gd_1080'])) {
    $mirror_1080 = explode(' ',$post['mirror_1080']);
    $mirror_link = $mirror_1080[0];
    $mirror_name = $mirror_1080[1];
    $stream_gd['1080'] = $post['gd_1080'];
    if($mirror_name=='mega'||$mirror_name=='Mega'||$mirror_name=='MEGA') { $mega = 1; $stream_mirror['1080'] = $mirror_link; }
    ?>
        <div class="col s12"><span class="padding"><a href="<?php if($post['type']=='0') { echo $post['gd_1080']; } else {  echo gdToDownload($post['gd_1080']); } ?>" class="btn waves-effect waves-light" target="_blank"><i class="material-icons middled">file_download</i> GDrive (1080p)</a></span> <span class="padding"><a href="<?=$mirror_link;?>" class="btn waves-effect waves-light"><i class="material-icons middled" target="_blank">file_download</i> <?=$mirror_name;?> (1080p)</a></span></div>
<?php }
if($mega==1) echo '<p class="col s12"><i class="material-icons middled">info</i> To download files from Mega, use Mega app</p>'
?>
<?php
if($post['type']=='1') {
    echo '<div class="col s12"><h1>Watch Online <sup><small>beta</small></sup></h1></div>';
    $lastKey = key(array_slice($stream_gd, -1, 1, true));
    $link = gdToStream($stream_gd[$lastKey]);
    $headers = get_headers($link);
    $header = substr($headers[0], 9, 3);
    if($header==200) {
        $url = $link;
    } else {
    if(!empty($stream_mirror)) {
        $lastKey = key(array_slice($stream_mirror, -1, 1, true));
        $link = megaToStream($stream_mirror[$lastKey]);
        $headers = get_headers($link);
        $header = substr($headers[0], 9, 3);
        if($header==200) {
            $url = $link;
        }
    }
    }
    if(!empty($url)) {
?>
<div class="col s12">
<div class="video-container">
<iframe id="stream" width="400" height="300" data-src="<?=$url;?>" frameborder="0" allowfullscreen></iframe>
<div id="loader"><div class="progress"><div class="indeterminate"></div></div></div>
</div></div>
<p class="col s12"><i class="material-icons middled">info</i> This feature is experimental, and might not work properly. In this case, please download the content instead.</p>
<?php
} else {
    echo '<p class="col s12"><i class="material-icons middled">info</i> This content can not be played online</p>';
}
}
?>
<div class="col s12"><h1>Wishlist</h1></div>
<div class="col s12"><button id="wish_btn" value="<?=$post['id'];?>" class="btn waves-effect waves-light"><?=$wish_btn;?></button><div id="wish"></div></div>
<div class="col s12"><h1>Report Broken Link</h1></div>
<div class="col s12"><div id="broken"><i class="material-icons middled">help</i> Link not working? <a id="broken_report" href="javascript:void(0)">click here</a> to report this link as broken.</div></div>
<div class="col s12"><h1>Notice</h1></div>
<div class="col s12"><i class="material-icons middled">info</i> We do not host those content in our server. We collect and share download links of those contents from the internet. We are not responsible for anything happens on those links. They can remove contents any time. In that case you may report the broken links and we will fix them ASAP. Also dowanloading such contents maybe illegal in your country, so proceed on your own risk. You can red our <a href="/tos">terms of service</a> to learn more.</div>
<div id="push"><div class="col s12"><h1>Tips</h1></div><div class="col s12"><div class="card-panel"><i class="material-icons middled green-text">lightbulb_outline</i> Subscribe to <a href="/notification">push notification</a> to get updates instantly</div></div>
</div>
            <?php
if(isLoggedIn()) {
	if(in_array($user['id'],$admins)) {
echo '<div class="fixed-action-btn"><a href="/admin-cp/posts/edit?id='.$post['id'].'" class="btn-floating btn-large"><i class="large material-icons">edit</i></a></div>';
	}
}
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/'.$cat['link'].'">'.$cat['title'].'</a></li>
<li><a href="/'.$cat['link'].'/'.$sub['link'].'">'.$sub['title'].'</a></li>
<li>'.$post['title'].'</li>
</ul>';
Page::footer('<script src="/assets/js/content.js"></script><script src="/assets/js/push.js"></script>',$extra);