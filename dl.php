<?php
// turn off warnings
error_reporting(E_ALL ^ E_WARNING || E_NOTICE);

// get official devices list from github
$devices = file_get_contents("https://raw.githubusercontent.com/DotOS/official_devices-pie/master/devices.json");
$devices = json_decode($devices);
$device = $_GET['device'];
$links = '';
$devices_list[] = array();
// init an array and store device names + sf dir
foreach($devices as $devices) {
    $links .=  '<a href="?device='.$devices->codename.'">'.$devices->name.'</a><br/>';
    $devices_list[$devices->codename] = $devices->sf_dir;
}
if(empty($device)) {
echo $links;
} else {
// get sf page for specific device
$contents = file_get_contents($devices_list[$device]);

// a new dom object
$dom = new domDocument; 
   
// load the html into the object
$dom->loadHTML($contents); 

// discard white space
$dom->preserveWhiteSpace = false; 

// get all releases
foreach($dom->getElementsByTagName('span') as $link) {
    if(preg_match('/\Wzip/m',$link->nodeValue)) {
    preg_replace('/\s(.*)/m','',$link->nodeValue);
    $download_link = ''.$devices_list[$device].'/'.$link->nodeValue.'?viasf=1';
    $download_link = str_replace('sourceforge.net','master.dl.sourceforge.net',$download_link);
    $download_link = str_replace("/projects/","/project/",$download_link);
    $download_link = str_replace("/files/","/",$download_link);
    $download_links[$link->nodeValue] = $download_link;
    }
}

array_shift($download_links);
foreach ($download_links as $name => $link) {
echo '<a href="'.$link.'">'.$name.'</a><br/>';
}
}
