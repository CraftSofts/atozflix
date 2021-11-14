<?php
// required files
include('includes/core.php');
// generate page
Page::header('Push Notification - '.SITE_NAME.'','Subscribe to get push notification about the latest updates','push,notification,latest,subscribe,'.strtolower(SITE_NAME).'');
?>
<br/>
<div class="center">
<div id="status">
<img src="/assets/images/preloaders/funnel_16.svg"/> Checking...
</div>

<br>
<div class="card-panel">  <div id="perm"><button onclick="pnSubscribe()" class="btn waves effect waves light green" id="push_btn" disabled="disabled">Allow Permission</button></div>
  <div id="process"><div class="progress">
      <div class="indeterminate"></div>
  </div></div>
</div></div>

<div id="instructions" class="modal modal-fixed-footer">
<div class="modal-content grey darken-3">
<h4>How to give notification permission?</h4>
<p><strong>Steps might not be be same for all browsers. Please look up for 'site notification settings' in your browser.</strong><br>
<div class="divider"></div>
For chrome browser (or any browser) on PC:
<ol>
    <li>Make sure the address bar shows https://<?=$_SERVER['HTTP_HOST'];?> at the beginning</li>
    <li>To the left of the web address, click the icon of lock (🔒).</li>
    <li>Click Site settings</li>
    <li>Now find and click Notification</li>
    <li>Enable it</li>
    <li>Reload the page and see the result</li>
</ol><br>
<div class="divider"></div>
For chrome browser on Android:
<ol>
    <li>Make sure the address bar shows merajbd.com at the beginning</li>
    <li>Tap on the right of the address bar</li>
    <li>Tap Site settings</li>
    <li>Click on 'Notification'</li>
    <li>Enable it (Enable by checking Show notifications or similar text)</li>
    <li>Reload the page and see the result</li>
</ol>
</p>
</div>
<div class="modal-footer grey darken-3">
<div class="center"><a href="javascript:void(0)" class="btn modal-close waves-effect waves-dark" onclick="instr.close()">Okay</a></div>
</div>
</div>

<h2>Push Notification</h2>
<p>A push notification is a message that is "pushed" from backend server or application to user interface, e.g. (but not limited to) mobile applications and desktop applications.<br><div class="center"><a href="#about_push" class="btn waves-effect modal-trigger waves-light">Learn More</a></div></p>
<div id="about_push" class="modal">
    <div class="modal-content grey darken-3">
      <h4>Push Notification</h4>
      <p>Remote notifications are handled by a remote server. Under this scenario, the client application needs to be registered on the server with a unique key (e.g., a UUID). The server then fires the message against the unique key to deliver the message to the client application via an agreed client/server protocol such as HTTP or XMPP and the client displays the message received. When the push notification arrives, it can transmit short notifications and messages, set badges on application icons, blink or continuously light up the notification LED, or play alert sounds to attract user's attention. Push notifications are usually used by applications to bring information to users' attention</p>
    </div>
    <div class="modal-footer grey darken-3">
      <a href="javascript:void(0)" class="modal-close waves-effect waves-dark btn">I Understand</a>
    </div>
  </div>

</div>
<br/>
<?php
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li>Push Notification</li>
</ul>';
Page::footer('<script src="notification.js"></script>',$extra);
?>