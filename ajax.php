<?php
require('includes/core.php');
use lib\PNServer\PNDataProvider;
use lib\PNServer\PNDataProviderSQLite;
use lib\PNServer\PNDataProviderMySQL;
use lib\PNServer\PNSubscription;
use lib\PNServer\PNVapid;
use lib\PNServer\PNPayload;
use lib\PNServer\PNServer;
require_once 'includes/lib/PNServer/PNDataProviderSQLite.php';
require_once 'includes/lib/PNServer/PNDataProviderMySQL.php';
require_once 'includes/lib/PNServer/PNServer.php';
$action = $form->request('action');
if($action=='wish') {
    if(isLoggedIn()) {
        $id = $form->get('id');
        $check = $db->selectRow('posts','id',$id)['result'];
        if($check=='success') {
            $wish = $users->selectRow('users','id',$user['id'])['data'];
            $wishlist = $wish['wishlist'];
            $data = explode('|',$wishlist);
            $data2 = $data;
            if(in_array($id,$data)) {
                if (($key = array_search($id, $data)) !== false) {
                    unset($data[$key]);
                }
                $data = implode('|',$data);
                $data = array('wishlist'=>$data);
                if($users->updateRow('users','id',$user['id'],$data)['result']=='success') {
                    echo ' <i class="material-icons middled">check_circle</i> Removed from <a href="/user/wishlist">wishlist</a> !
                    <script>
                    document.getElementById("wish_btn").textContent = \'✚ Add to wishlist\';
                    </script>';
                } else {
                    echo '<i class="material-icons middled">error</i> Failed to remove from wishlist!';
                }
            } else {
                foreach($data2 as $id2) {
                    if($db->selectRow('posts','id',$id2)['result']=='success')
                    $count = $count+1;
                }
                if($count>=50) {
                    echo '<i class="material-icons middled">error</i> Your wishlist is full! You can add maximum 50 conntent to your wishlist. Please remove some of them to list new items.';
                } else {
                    $new_data = ''.$wishlist.'|'.$id.'';
                    $data = array('wishlist'=>$new_data);
                    if($users->updateRow('users','id',$user['id'],$data)['result']=='success') {
                        echo '<i class="material-icons middled">check_circle</i> Added to <a href="/user/wishlist">wishlist</a> !
                        <script>
                        document.getElementById("wish_btn").textContent = \'━ Remove from wishlist\';
                        </script>';
                    } else {
                        echo '<i class="material-icons middled">error</i> Failed to add in wishlist!';
                    }
                }
            }
        }
    } else {
        echo '<i class="material-icons middled">error</i> You need to be <a href="/user/login">logged</a> in !';
    }
} elseif ($action=='broken') {
    $id = $form->get('id');
    $check = $db->selectRow('posts','id',$id)['result'];
    if($check=='success') {
        $verify = $db->selectRow('posts','id',$id,' AND broken="1"')['result'];
        if($verify=='failed') {
            $data = array('broken'=>1);
            if($db->updateRow('posts','id',$id,$data)['result']=='success') {
                echo '<i class="material-icons middled">check_circle</i> Broken link reported! Admin will fix the links as soon as they see. Please be patient. Thank you for reporting.';
            } else {
                echo '<i class="material-icons middled">error</i> Failed to report broken link!';
            }
        } else {
            echo '<i class="material-icons middled">check_circle</i> Broken link reported! Admin will fix the links as soon as they see. Please be patient. Thank you for reporting.';
        }
    }
} elseif($action=='check_username') {
    $username_pattern = '/^[a-z][a-z0-9]*_?[a-z0-9]+$/i';
    $check = $users->isExists('username',$form->get('username'));
    if(strlen($form->get('username'))<3) {
        echo '<span class="red-text"><i class="material-icons middled">warning</i> The username can\'t be less than 3 characters</span>';
     } elseif(strlen($form->get('username'))>20) {
        echo '<span class="red-text"><i class="material-icons middled">warning</i> The username can\'t be more than 20 characters!</span>';
    } elseif(!preg_match($username_pattern,$form->get('username'))) {
        $errors[] = 'Username can start with alphabet and can containt alphanumeric characters and a underscore';
    } elseif($check) {
        echo '<span class="red-text"><i class="material-icons middled">warning</i> The username is already taken. Try adding numbers to make your username unique or choose any other username!</span>';
    } else {
        echo '<span class="green-text"><i class="material-icons middled">check_circle</i> Username is available</span>';
    }
} elseif ($action=='search') {
    $q = $form->get('q');
    $posts = $db->selectRows('posts',' WHERE title like \'%'.$db->escapeString($q).'%\' LIMIT 0, 10');
    if($posts['result']=='success') {
        foreach($posts['data'] as $data)
        $options[] = $data->title;
        echo json_encode((array)$options);
    } else {
        echo json_encode(array(null));
    }
} elseif ($form->method()=='POST') {
$result = array();
// only serve POST request containing valid json data
if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
	if (isset($_SERVER['CONTENT_TYPE'])	&& trim(strtolower($_SERVER['CONTENT_TYPE']) == 'application/json')) {
		// get posted json data
		if (($strJSON = trim(file_get_contents('php://input'))) === false) {
			$result['msg'] = 'invalid JSON data!';
		} else {
			$oDP = new PNDataProviderSQLite(PN_SAVE_PATH);
			if ($oDP->saveSubscription($strJSON) !== false) {
                $result['msg'] = 'subscription saved on server!';
                // send welcome
				$bExit = false;	
            $oServer = new PNServer($oDP);
            $oVapid = new PNVapid(
                "mailto:contact@atozflix.com",
                "BIZk9xoCMdDzU9KjhRs8Pji8kodhBz9HjYWsDTH13eTOljnT0EjiwZn2vf_8ceuxzCgBmCLg6NDSNr8w4_YSnWs",
                "-bh1pwIHjNN-fsWlmhJVdSnCy882kXmc2Kws5_7_R-k"
            );
            $oServer->setVapid($oVapid);
            $oPayload = new PNPayload('Welcome to '.SITE_NAME.'', "You are successfully subscribed to push notification. From now on, you will get new content notifications right here. Thanks for subscribing.", '/assets/images/icons/favicon-32x32.png');
            $oPayload->setTag('updates', true);
            $oPayload->setURL('https://'.$_SERVER['HTTP_HOST'].'/');
            $oServer->setPayload($oPayload);
            $oPayload->setSilent(false);
            //
            $oServer->addSubscription(PNSubscription::fromJSON($strJSON));
            if (!$oServer->push()) {
                //echo '<h2>' . $oServer->getError() . '</h2>' . PHP_EOL;
            }
			} else {
				$result['msg'] = 'error saving subscription!';
			}
        }
	} else {
		$result['msg'] = 'invalid content type!';
	}
} else {
	$result['msg'] = 'no post request!';
}
// let the service-worker know the result
echo json_encode($result);

}