<?php
// include required files
require '../../includes/core.php';
use lib\PNServer\PNDataProvider;
use lib\PNServer\PNDataProviderSQLite;
use lib\PNServer\PNDataProviderMySQL;
use lib\PNServer\PNSubscription;
use lib\PNServer\PNVapid;
use lib\PNServer\PNPayload;
use lib\PNServer\PNServer;
require_once '../../includes/lib/PNServer/PNDataProviderSQLite.php';
require_once '../../includes/lib/PNServer/PNDataProviderMySQL.php';
require_once '../../includes/lib/PNServer/PNServer.php';

// check if user logged in
if(!isLoggedIn()) {
	$_SESSION['login_msg'] = 'You need to be logged in to access that page';
    $_SESSION['target_url'] = CURRENT_URL;
	redirectTo('/user/login');
	exit();
} else {
	// check if logged in user is a admin
	if(!in_array($user['id'],$admins)) {
		redirectTo('/');
		exit();
	}
}

// initialize variables
$show_form = '';
$id = $form->get('id');
$check = $db->selectRow('sub_categories','id',$id);

// validate sub category
if($check['result']=='failed') {
	$_SESSION['admin_msg'] = '<div class="card-panel red lighten-5 red-text"><i class="material-icons middled">error</i> Category not found</div>';
    $url = '/admin-cp/categories/'; // not found
    header("Location: $url");
}
$cid = $check['data']['category_id'];
$name = $db->selectRow('categories','id',$cid);
$cat_link = $name['data']['link'];
$name = $name['data']['title'];
$sname = $check['data']['title'];
$sub_link = $check['data']['link'];

// initialize page 
Page::header('Add Post - Site_Name');
$user_requests = $db->countRows('SELECT * FROM requests WHERE processed=0');
if($user_requests>0) {
	echo '<h1>User Requests</h1>
	<div class="card-panel"><i class="material-icons middled">info</i> There are '.$user_requests.' pending user <a href="/admin-cp/requests">requests</a></div>';
}

// form submitted
if($form->method()=="POST") {
	$imdb_link = $form->post('imdb_link');
	$imdb_id = $imdb_link;
	$imdb_id = preg_replace('/(.*?)title\//m', '', $imdb_id);
	$imdb_id = preg_replace('/\/(.*)/m', '', $imdb_id);
	$sub_cat_id = $form->post('sub_cat_id');
	$user_id = $user['id'];
	$type = $form->post('type');
	$episode = $form->post('episode');
	$season = $form->post('season');
	$email = $form->post('email');
	$email2 = $form->post('email2');
	$q480_switch = $form->isExists('q480_switch');
	$q720_switch = $form->isExists('q720_switch');
	$q1080_switch = $form->isExists('q1080_switch');
	$gd_480 = $form->post('gd_480');
	$mirror_480 = $form->post('mirror_480');
	$mirror_480_name = $form->post('mirror_480_name');
	$gd_720 = $form->post('gd_720');
	$mirror_720 = $form->post('mirror_720');
	$mirror_720_name = $form->post('mirror_720_name');
	$gd_1080 = $form->post('gd_1080');
	$mirror_1080 = $form->post('mirror_1080');
	$mirror_1080_name = $form->post('mirror_1080_name');
	$time = date('U');

	// IMDB fetch
	$url = 'http://www.omdbapi.com/?apikey=ad692e&i='.$imdb_id;
	$contents = file_get_contents($url);
	$contents = json_decode($contents);
	$title = $contents->Title;
		if($type=='0') { if($season!='0') $title = ''.$title.' Season '.$season; }
	$year = $contents->Year;
	$link = convertToLink($title);
	//$released = $contents->Released;
	$runtime = $contents->Runtime;
	//$rated = $contents->Rated;
	$genres = $contents->Genre;
	$director = $contents->Director;
	$writer = $contents->Writer;
	$cast = $contents->Actors;
	$plot = $contents->Plot;
	//$language = $contents->Language;
	//$country = $contents->Country;
	$poster = $contents->Poster;
	//if($poster=='N/A'||empty($poser)) $poster = 'https://'.$_SERVER['HTTP_HOST'].'/assets/images/no_poster.jpg';
	//$all_ratings = $contents->Ratings; // array
	$imdb_rating = $contents->imdbRating;
	$votes = $contents->imdbVotes;
	
	// validation starts
	if(empty($imdb_link)||empty($email)) {
			// all fields are required
		$error[] = 'All fields are required';
	} elseif ($db->selectRow('posts','title',$title)['result']=='success') {
		$error[] = 'Post with the same title already exists';
	} elseif (!filter_var($imdb_link, FILTER_VALIDATE_URL)) {
		$error[] = 'IMDB Link is an invalid URL';
	} elseif (!isImdb($imdb_link)) {
		$error[] = 'The IMDB link isn\'t valid';
	} elseif (!isset($q480_switch)&&!isset($q720_switch)&&!isset($q1080_switch)) {
		$error[] = 'Please select at least one quality to upload';
	} elseif ($q480_switch===true) {
		if(!filter_var($gd_480, FILTER_VALIDATE_URL)) {
			$error[] = 'Google drive link (480p) URL is invalid';
		} elseif(!isGd($gd_480)) {
			$error[] = 'Google drive link (480p) is not a valid google drive link';
		} elseif(!empty($mirror_480)&&empty($mirror_480_name)) {
			$error[] = 'Mirror name (480p) can\'t be empty';
		} elseif (empty($mirror_480)&&!empty($mirror_480_name)) {
			$error[] = 'Mirror (480p) URL can\'t be empty';
		} else {
			if(!filter_var($mirror_480, FILTER_VALIDATE_URL)) {
				$error[] = 'Mirror (480p) URL is invalid';
			} elseif(strlen($mirror_480_name)>10||strlen($mirror_480_name)<3) {
				$error[] = 'Mirror (480p) Name is invalid';
			}
		}
		$mirror_480 = $mirror_480.' '.$mirror_480_name;
	} elseif ($q1080_switch===true) {
		if(!filter_var($gd_1080, FILTER_VALIDATE_URL)) {
			$error[] = 'Google drive link (1080p) URL is invalid';
		} elseif(!isGd($gd_1080)) {
			$error[] = 'Google drive link (1080p) is not a valid google drive link';
		} elseif(!empty($mirror_1080)&&empty($mirror_1080_name)) {
			$error[] = 'Mirror name (1080p) can\'t be empty';
		} elseif (empty($mirror_1080)&&!empty($mirror_1080_name)) {
			$error[] = 'Mirror (1080p) URL can\'t be empty';
		} else {
			if(!filter_var($mirror_1080, FILTER_VALIDATE_URL)) {
				$error[] = 'Mirror (1080p) URL is invalid';
			} elseif(strlen($mirror_1080_name)>10||strlen($mirror_1080_name)<3) {
				$error[] = 'Mirror (1080p) Name is invalid';
			}
		}
		$mirror_1080 = $mirror_1080.' '.$mirror_1080_name;
	} elseif ($q720_switch===true) {
		if(!filter_var($gd_720, FILTER_VALIDATE_URL)) {
			$error[] = 'Google drive link (720) URL is invalid';
		} elseif(!isGd($gd_720)) {
			$error[] = 'Google drive link (720p) is not a valid google drive link';
		} elseif(!empty($mirror_720)&&empty($mirror_720_name)) {
			$error[] = 'Mirror name (720p) can\'t be empty';
		} elseif (empty($mirror_720)&&!empty($mirror_720_name)) {
			$error[] = 'Mirror (720p) URL can\'t be empty';
		} else {
			if(!filter_var($mirror_720, FILTER_VALIDATE_URL)) {
				$error[] = 'Mirror (720p) URL is invalid';
			} elseif(strlen($mirror_720_name)>10||strlen($mirror_720_name)<3) {
				$error[] = 'Mirror (720p) Name is invalid';
			}
		}
		$mirror_720 = $mirror_720.' '.$mirror_720_name;
	} elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error[] = 'The email is invalid';	
	} elseif($type=='0') {
		// tv series
		if(empty($season)||empty($episode)) {
		// both are required
			$error[] = 'Episode no. and Season no. are required for TV Series';
		} elseif (strlen($season)>3||strlen($season)<1) {
			$error[] = 'Season can\'t be more than 3 or less than 1 number(s)';
		} elseif (strlen($episode)>4||strlen($episode)<1) {
			$error[] = 'Episode can\'t be more than 4 or less than 1 number(s)';
		}
	}
	// validation ends
	if(empty($error)) {
		$column = array('sub_cat_id','user_id','imdb_id','title','type','link','genres','year','imdb_rating','imdb_link','plot','cast','season','episode','gd_480','mirror_480','gd_720','mirror_720','gd_1080','mirror_1080','poster','email','runtime','director','writer','votes','time');
		if($type=='0') { if($season!='0') {  $n_link = ''.$link.'-season-'.$season; } else { $n_link = $link; $season = '1'; } } else { $n_link = ''.$link.'-'.$year.''; }
		$values = array($id,$user['id'],$imdb_id,$db->escapeString($title),$type,$n_link,$db->escapeString($genres),$year,$imdb_rating,$imdb_link,$db->escapestring($plot),$db->escapeString($cast),$season,$episode,$gd_480,$mirror_480.' '.$mirror_480_name,$gd_720,$mirror_720.' '.$mirror_720_name,$gd_1080,$mirror_1080.' '.$mirror_1080_name,$poster,$email.' '.$email2,$runtime,$db->escapeString($director),$db->escapeString($writer),$votes,$time);
		$result = $db->insertRow('posts',$column,$values);
		if($result['result']=='success') {
			$show_form = 'no';
			$_SESSION['admin_msg'] = '<div class="card-panel green lighten-5 green-text"><i class="material-icons middled">check_circle</i> Post added successfuly</div>';
			echo '<meta http-equiv="refresh" content="0; url=/admin-cp/posts/?id='.$id.'">';
				// SEND notification to users
			if($type=='0') {
				$content = 'TV Series';
			} else {
				$content = 'Movie';
			}
			$bExit = false;
			if (version_compare(phpversion(), '7.1', '<')) {
						trigger_error('At least PHP Version 7.1 is required (current Version is ' . phpversion() . ')!', E_USER_WARNING);
						$bExit = true;
			}
			$aExt = array('curl', 'gmp', 'mbstring', 'openssl', 'bcmath');
			foreach ($aExt as $strExt) {
				if (!extension_loaded($strExt)) {
					trigger_error('Extension ' . $strExt . ' must be installed!', E_USER_WARNING);
					$bExit = true;
				}
			}
			if ($bExit) {
			exit();
			}
			$oDP = new PNDataProviderSQLite(PN_SAVE_PATH);
			if (!$oDP->isConnected()) {
				exit();
			}
			$oDP->saveSubscription(
							'{'
							.'	"endpoint": "https://fcm.googleapis.com/fcm/send/f8PIq7EL6xI:APA91bFgD2qA0Goo_6sWgWVDKclh5Sm1Gf1BtYZw3rePs_GHqmC9l2N92I4QhLQtPmyB18HYYseFHLhvMbpq-oGz2Jtt8AVExmNU9R3K9Z-Gaiq6rQxig1WT4ND_5PSXTjuth-GoGggt",'
							.'	"expirationTime": "1589291569000",'
							.'	"keys": {'
							.'	"p256dh": "BEQrfuNX-ZrXPf0Mm-IdVMO1LMpu5N3ifgcyeUD2nYwuUhRUDmn_wVOM3eQyYux5vW2B8-TyTYco4-bFKKR02IA",'
							.'	"auth": "jOfywakW_srfHhMF-NiZ3Q"'
							.'	}'
							.'}'
			);	
			$oServer = new PNServer($oDP);
			$oVapid = new PNVapid(
							"mailto:@atozflix.com",
							"BIZk9xoCMdDzU9KjhRs8Pji8kodhBz9HjYWsDTH13eTOljnT0EjiwZn2vf_8ceuxzCgBmCLg6NDSNr8w4_YSnWs",
							"-bh1pwIHjNN-fsWlmhJVdSnCy882kXmc2Kws5_7_R-k"
			);
			$oServer->setVapid($oVapid);
			$oPayload = new PNPayload('New '.$content.' Uploaded', "$title is uploaded on ".SITE_NAME."! Have a look now.", '/assets/images/icons/favicon-32x32.png');
			$oPayload->setTag('updates', true);
			$oPayload->setURL('https://'.$_SERVER['HTTP_HOST'].'/'.$cat_link.'/'.$sub_link.'/'.$n_link.'');
			$oPayload->setImage('/assets/images/posters/'.$cat_link.'/'.$sub_link.'/'.$n_link.'.jpg');
			$oPayload->setSilent(false);
			//$oPayload->setSound($url_to_sound);
			$oServer->setPayload($oPayload);
			$oServer->loadSubscriptions();
			if (!$oServer->push()) {
				echo '<h2>' . $oServer->getError() . '</h2>' . PHP_EOL;
			}
			// end
			} else {
				$error[] = 'Something went wrong! Contact Admin.';
				print_r($result);
			}
		}
	}
if(empty($show_form)) {
	// show form
	?>
<div class="row">
<div class="col s12"><h1>New Post</h1></div>
<?php
if(!empty($error)) foreach($error as $error) echo "<div class=\"red-text col s12\"><i class=\"material-icons middled\">error</i> $error</div>";
?>
<form method="post" action="/admin-cp/posts/new?id=<?=$id;?>" id="form">
<div class="input-field col s12"><input name="imdb_link" id="imdb_link" type="url" value="<?=$imdb_link;?>"><label for="imdb_link" required>IMDB Link</label></div>
<div class="input-field col s12">
	<select id="type" name="type" required>
		<option value="1" selected>Movie</option>
		<option value="0">TV Series</option>
	</select>
    <label>Type</label>
</div>
<div class="hidden" id="tv_series">
<div class="input-field col s6"><input name="season" id="season" type="number" data-length="3" value="<?=$season;?>"><label for="season">Season No.</label></div>
<div class="input-field col s6"><input name="episode" id="episode" type="text" data-length="5" value="<?=$episode;?>"><label for="episode">Episode  No.</label></div>
</div>
<div class="col s12 input-field"><span class="switch"><label><input type="checkbox" id="q480_switch" name="q480_switch"><span class="lever"></span></label></span> 480p Download Link</div>
<div id="q480" class="hidden">
<div class="input-field col s12"><input name="gd_480" id="gd_480" type="url" value="<?=$gd_480;?>"><label for="gd_480">Google Drive Link (Required)</label></div>
<div class="input-field col s6"><input name="mirror_480" id="mirror_480" type="url" value="<?=$mirror_480;?>"><label for="mirror_480">Mirror Link (Required)</label></div>
<div class="input-field col s6"><input name="mirror_480_name" id="mirror_480_name" type="text" value="Mega"><label for="mirror_480_name">Mirror Name (Required)</label></div>
</div>

<div class="col s12 input-field"><span class="switch"><label><input type="checkbox" id="q720_switch" name="q720_switch"><span class="lever"></span></label></span> 720p Download Link</div>
<div id="q720" class="hidden">
<div class="input-field col s12"><input name="gd_720" id="gd_720" type="url" value="<?=$gd_720;?>"><label for="gd_720">Google Drive Link (Required)</label></div>
<div class="input-field col s6"><input name="mirror_720" id="mirror_720" type="url" value="<?=$mirror_720;?>"><label for="mirror_720">Mirror Link (Required)</label></div>
<div class="input-field col s6"><input name="mirror_720_name" id="mirror_720_name" type="text" value="Mega"><label for="mirror_720_name">Mirror Name (Required)</label></div>
</div>

<div class="col s12 input-field"><span class="switch"><label><input type="checkbox" id="q1080_switch" name="q1080_switch"><span class="lever"></span></label></span> 1080p Download Link</div>
<div id="q1080" class="hidden">
<div class="input-field col s12"><input name="gd_1080" id="gd_1080" type="url" value="<?=$gd_1080;?>"><label for="gd_1080">Google Drive Link (Required)</label></div>
<div class="input-field col s6"><input name="mirror_1080" id="mirror_1080" type="url" value="<?=$mirror_1080;?>"><label for="mirror_1080">Mirror Link (Required)</label></div>
<div class="input-field col s6"><input name="mirror_1080_name" id="mirror_1080_name" type="text" value="Mega"><label for="mirror_1080_name">Mirror Name (Required)</label></div>
</div>
<div class="input-field col s6"><input name="email" id="email" type="email" value="<?=$email;?>" required><label for="email">Storage Email Gdrive</label></div>
<div class="input-field col s6"><input name="email2" id="email2" type="email" value="" required><label for="email2">Storage Email Mirror</label></div>
<div class="col s12"><button class="btn waves-effect waves-light" type="submit">Add</button></div>
  </form>
</div>
	<?php
}
$nav = '<ul class="custom_breadcrumb">
<li><a href="/admin-cp/">Admin CP</a></li>
<li><a href="/admin-cp/categories/">Categories</a></li>
<li><a href="/admin-cp/sub-categories/?id='.$cid.'">'.$name.'</a></li>
<li><a href="/admin-cp/posts/?id='.$id.'">'.$sname.'</a></li>
<li>Add</li>
</ul>';
Page::footer('<script src="/assets/js/main.js"></script><script src="/assets/js/posts.js"></script>',$nav);