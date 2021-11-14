<?php
require '../includes/core.php';

isAdmin($user, $admins);

Page::header('Admin Control Panel - '.SITE_NAME.'');

$total_posts = $db->countRows('SELECT * FROM posts');
$total_requests = $db->countRows('SELECT * FROM requests');
$total_broken = $db->countRows('SELECT * FROM posts WHERE broken=1');
$search_db = new Db('localhost', MYSQL_ADMIN, MYSQL_PASSWORD, MYSQL_SEARCHES);
$total_searches = $search_db->countRows('SELECT * FROM searches');
$email_db = new Db('localhost', MYSQL_ADMIN, MYSQL_PASSWORD, MYSQL_EMAILS);
$total_emails = $email_db->countRows('SELECT * FROM emails');

echo '<h1>Welcome ' . $user['first_name'] . ' ' . $user['last_name'] . '</h1>
';
userRequestsMessage($db);
brokenLinksMessage($db);
?>
<ul class="collection grey darken-4">
    <li class="collection-item avatar grey darken-4">
	<i class="material-icons circle">folder</i>
      <span class="title"><a href="/admin-cp/categories/">Categories</a></span>
      <p>Manage all categories, posts from here.<br/>
	  Total Posts: <?=$total_posts;?></p>
    </li>
	<li class="collection-item avatar grey darken-4">
	<i class="material-icons circle">email</i>
      <span class="title"><a href="/admin-cp/emails">Emails</a></span>
      <p>View and use emails from here.<br/>
	  Total Emails: <?=$total_emails;?></p>
    </li>
	<li class="collection-item avatar grey darken-4">
	<i class="material-icons circle">add_to_queue</i>
      <span class="title"><a href="/admin-cp/requests">User Requests</a></span>
      <p>View all user requests and process them.<br/>
	  Total Requests: <?=$total_requests;?></p>
    </li>
	<li class="collection-item avatar grey darken-4">
	<i class="material-icons circle">link</i>
      <span class="title"><a href="/admin-cp/broken">Broken Links</a></span>
      <p>View all broken links and fix them by reuploading and adding new links.<br/>
	  Total Broken Links: <?=$total_broken;?></p>
    </li>
	<li class="collection-item avatar grey darken-4">
	<i class="material-icons circle">search</i>
      <span class="title"><a href="/admin-cp/searches">User Searches</a></span>
      <p>View what are people looking for most in the search, try to upload those contents<br/>
	  Total Searched Keywords: <?=$total_searches;?></p>
    </li>
</ul>
<h1>Usefull Links</h1>
<div class="collection">
<a href="http://111.90.150.31/" class="collection-item"><i class="material-icons circle middled">link</i> Netflix Contents Downloads</a>
<a href="https://yts.am" class="collection-item"><i class="material-icons circle middled">link</i> YTS (For movies)</a>
<a href="http://dl.mellimovies.com/" class="collection-item"><i class="material-icons circle middled">link</i> Mell Movies (FTP Server)</a>
<a href="http://uk1.alserver.art/" class="collection-item"><i class="material-icons circle middled">link</i> Al Server (FTP Server)</a>
<a href="http://s8.bitdl.ir/" class="collection-item"><i class="material-icons circle middled">link</i> BIT DL (FTP Server)</a>
<a href="http://dl3.3rver.org/" class="collection-item"><i class="material-icons circle middled">link</i> 3rver (FTP Server)</a>
<a href="https://dash.cloudflare.com" class="collection-item"><i class="material-icons circle middled">link</i> Domain Control Panel</a>
<a href="https://heliohost.org/login" class="collection-item"><i class="material-icons circle middled">link</i> Hosting Control Panel</a>
</div>
<?php
if ($user['id'] == 1) {
    echo '<p class="card-panel">
Email: group.atoz.global@gmail.com<br/>
Password: atozgroup123<br/>
<a href="https://my.freenom.com">Freenom</a></p>';
}

Page::footer('', '<div class="hide-on-med-and-down"><nav>
<div class="nav-wrapperrow">
<div class="col s12">
<a class="breadcrumb" href="/admin-cp/">Admin CP</a>
</div>
</div>
</nav></div>');
?>