<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require('includes/Form.php');
require('includes/Db.php');
$db = new Db('localhost','atozujzn_admin','merajbd6','atozujzn_movies');
$form = new Form();
$id = $form->get('id');
if(preg_match('/\/(.*)/m',$id)) {
    $id = explode('/',$id,3);
    $cat = $id[0];
    $sub = $id[1];
    $post = str_replace('.jpg','',$id[2]);
    $cat_check = $db->selectRow('categories','link',$cat);
    $sub_check = $db->selectRow('sub_categories','link',$sub);
    $post_check = $db->selectRow('posts','link',$post);
    if($cat_check['result']=='success' && $sub_check['result']=='success' && $post_check['result']=='success') {
        $data = $post_check['data'];
        $poster = $data['poster'];
        header('Content-type: image/jpeg');
        //echo file_get_contents($poster);
        $w = trim($_GET['w']);
        $h = trim($_GET['h']);
        if(empty($w)) $w = 400;
        if(empty($h)) $h = 500;
        list($width, $height) = getimagesize($poster);
        $create = imagecreatetruecolor($w, $h); 
        $img = imagecreatefromjpeg($poster); 
        $newwidth = $w;
        $newheight = $h;
        imagecopyresized($create, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        imagejpeg($create, null, 100);
    }
}
?>