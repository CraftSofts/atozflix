<?php
$dirname = "";
$images = glob($dirname."*.svg");

foreach($images as $image) {
echo 'File: '.$image.' <img src="'.$image.'"/><br>';
}