<?php
// required files
include('includes/core.php');
// generate page
Page::header('About - '.SITE_NAME.'','Various info about the site','about,us,'.strtolower(SITE_NAME).'');
?>
<h1>About <?=SITE_NAME;?></h1>
<div class="center"><img src="/assets/images/download_buttons.jpg" alt="Download Buttons" class="responsive-img materialboxed" data-caption="Download Buttons"/></div>
<p>Ever faced this situations? Where you are confused about real download links? Well, we are here to save you from such problems. We provide very easy 2 click download method for movies/tv series. No fake links! Seriously! So you stay entertained non-stop.</p>
<h1>Our Motto</h1>
<p>We are also movie freaks. When we wanted to download something, we used to search google for them. Then when we found the content, we get cofused about download links. Sometimes we couldn't find the real download links, sometimes to get the real download links we had to go through lots of pages, sometimes links were broken and mostly the download servers were real slow and sometimes there were no download links at all (just fake links). So we made a plan to make our own site including following features: </p>
<ul class="browser-default">
<li>Clean and simple user interface</li>
<li>Fast and responsive</li>
<li>Custom crafted software</li>
<li>Faster dowanload server</li>
<li>Mirror links for each files</li>
<li>HD or better quality videos (No cam RIPs)</li>
<li>No explict adult (18+) contents.</li>
<li>Wishlist system <a href="/user/wishlist"><i class="material-icons middled">launch</i></a></li>
<li>Push Notification updates <a href="/notification"><i class="material-icons middled">launch</i></a></li>
<li>User can request for contents <a href="/request"><i class="material-icons middled">launch</i></a></li>
<li>Broken link reporting</li>
<li>Highest security for protecting user's privacy</li>
<li>No ADS! (We may implement optional ads later, but not forced ads)</li>
<li>No tracking</li>
</ul>
<h1>Our Team</h1>
<div class="card-panel">
<p class="bold-text">Meraj-Ul Islam</p>
<p>CEO, Founder, Programmer, Designer</p>
<p>From Bangladesh </p>
</div>

<div class="card-panel">
<p class="bold-text">Fahim Mahmud</p>
<p>Contributor, Tester</p>
<p>From Bangladesh</p>
</div>

<div class="card-panel">
<p class="bold-text">Fardul Islam</p>
<p>Storage (Online) Manager, Contributor</p>
<p>From Bangladesh</p>
</div>

<div class="card-panel">
<p class="bold-text">MD Anamul Hossain</p>
<p>Adviser</p>
<p>From Bangladesh</p>
</div>

<div class="card-panel">
<p class="bold-text">Al Jubaid Fahim</p>
<p>Contributor, Tester</p>
<p>From Bangladesh</p>
</div>

<div class="card-panel">
<p class="bold-text">Qang Anh</p>
<p>Contributor</p>
<p>From Vietnam</p>
</div>
<?php
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li>About</li>
</ul>';
Page::footer('',$extra);
?>