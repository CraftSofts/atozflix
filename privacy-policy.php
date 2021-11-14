<?php
// required files
include('includes/core.php');
// generate page
Page::header('Privacy Policy - '.SITE_NAME.'','Learn about our privacy policies','privacy,policy,'.strtolower(SITE_NAME).'');
?>
<h1>Privacy Policy</h1>
<p><span class="bold-text">Introduction:</span> A Privacy Policy is a statement or a legal document that states how a company or website collects, handles and processes data of its customers and visitors. It explicitly describes whether that information is kept confidential, or is shared with or sold to third parties.</p>
<p><span class="bold-text">Data We Collect:</span> We basically collects data within two following conditions:<br/>
<ol>
<li>
Once you register:
<ul class="browser-default">
<li>Your email</li>
<li>Your name</li>
<li>Social account ID (Optional)</li>
<li>Your search history in our site</li>
</ul>
</li>
<li>
When you are not logged in:
<ul class="browser-default">
<li>Your search history in our site</li>
</ul>
</li>
</ol></p>
<p><span class="bold-text">How we use the data:</span> We use your data for some essential functionality of the site. For example:<br/>
<ul class="browser-default">
<li>When you register/login in this site, we use your email/username/social account ID or token to identify you.</li>
<li>When you search for something in this site, we store your searched keywords to improve our service. So that we will know which contents are users looking for most. Then we will share those contents if possible.</li>
</ul></p>
<p><span class="bold-text">How we process and protect your data:</span> We really do care about your data. It's our resposiblity that we make sure Your personal data stays personal. We took all necessary steps to make sure your to protect your personal information from any 3rd parties. We implemented highest security measures possible within our ability. We don't store user's personal information to anywhere else than our server.</p>
<p><span class="bold-text">Sharing your data:</span> We feel proud to say that, we don't share any of yours data with someone else. Since we don't show ads, we don't have to share anything with those ad companies. Also We don't use any kind of trackers to track you, which basically every other organisations/ad providers do. You are also welcome you to verify that with any tracker checking softwares/programs.</p>
<?php
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li>Privacy Policy</li>
</ul>';
Page::footer('',$extra);
?>