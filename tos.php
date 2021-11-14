<?php
// required files
include('includes/core.php');
// generate page
Page::header('Terms of Service - '.SITE_NAME.'','View terms of services','atoz,terms,of,service,tos,'.strtolower(SITE_NAME).'');
?>
<h1>Terms of Services</h1>
<p>We, AtoZ Team provides links of entertainment contents such as movies and tv series which are already available across the internet. We don't host any of these contents. These types of contents often refered as Pirated or Illegal contents. But we are not responsible for any of these being pirated. You may avoid opur site if these type of contents are illegal in your region.</p>
<p>We may need to set cookies in your browser for providing some services such as user logins. But we don't use cookies for providing ad services or promotions. Only necessary cookies are set.</p>
<p>We dont store any personal data unless they are essential for the site to function properly.</p>
<p>We don't share our user's data with any 3rd party in any condition. Even for providing personalized ads or contents.</p>
<p>We may enforce users sometimes to be logged in. That's because in some functionality user's need to identified. Wishlist for example. But users can also enjoy basic functionalities without being logged.</p>
<p>If you don't feel safe to enter any personal details like: email, passwords etc here, you can use social account logins. Social Logins (Facebook&trade; and Google&trade;) are passwordless logins, which are based on social account's tokens and some other informations. It's also easy to use those service if you don't to use your keyboard.</p>
<p>We believe to make a safer internet environmet for every age of person. As a part of this, we don't share any explict adult (18+) contents, however some contents may still contain some adult scenes. We believe they are necessary for the sake of the story flow.</p>
<p class="center bold-text">If you think the terms are not aplicable for you, We suggest you to leave this site and avoid it. You can also add this site to your local machine's blacklist or firewall's blacklist. We respect everyone's opinion.</p>
<?php
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li>Terms of Services</li>
</ul>';
Page::footer('',$extra);
?>