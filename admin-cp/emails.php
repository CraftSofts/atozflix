<?php
require '../includes/core.php';

isAdmin($user, $admins);

$email_db = new Db('localhost', MYSQL_ADMIN, MYSQL_PASSWORD, MYSQL_EMAILS);
Page::header('Emails - ' . SITE_NAME . '');

userRequestsMessage($db);
brokenLinksMessage($db);

$action = $form->request('action');
$id = $form->request('id');
$email = $form->request('email');
$password = $form->request('password');
$used = ($form->isExists('used')) ? 1 : 0;
$super = ($form->isExists('super')) ? 1 : 0;
$admin = $form->request('admin');
$gdrive_total = $form->request('gdrive_total');
$gdrive_used = $form->request('gdrive_used');
$mirror_total = $form->request('mirror_total');
$mirror_used = $form->request('mirror_used');

function show_admins($admins, $users)
{
    echo '<h1>Admin List</h1>
    <ul class="collection">';
    foreach ($admins as $id) {
        $admin = $users->selectRow('users', 'id', $id)['data'];
        echo '<li class="collection-item"><i class="material-icons middled">people</i> ID: ' . $admin['id'] . ' Name: ' . $admin['first_name'] . ' ' . $admin['last_name'] . '</li>';
    }
    echo '</ul>';
}

if ($action == "add") {
    if ($form->isExists('submit')) {
        // process form
        $column = array('email', 'password', 'super', 'admin', 'gdrive_total', 'gdrive_used', 'mirror_total', 'mirror_used');
        $values = array($email, $password, $super, $admin, $gdrive_total, $gdrive_used, $mirror_total, $mirror_used);
        $email_db->insertRow('emails', $column, $values);
        ?>
<i class="material-icons green-text middled">check_circle</i> Email added<br/>
<a href="/admin-cp/emails">Go Back</a>
<?php
} else {
// show form
?>
<h1>Add New Email</h1>
<form action="/admin-cp/emails?action=add" method="post">
<div class="input-field">
<input type="email" name="email" id="email">
<label for="email">Email</label>
</div>
<div class="input-field">
<input type="text" name="password" id="password">
<label for="password">Password</label>
</div>
<div class="input-field">
<p>
<label>
    <input type="checkbox" id="super" name="super"/>
        <span>Unllimited Google Drive?</span>
    </label>
</p>
</div>
<div class="input-field">
<input type="number" name="admin" id="admin">
<label for="admin">Admin ID</label>
</div>
<div class="input-field">
<input type="number" name="gdrive_total" id="gdrive_total" value="15">
<label for="gdrive_total">Googl Drive Total (GB)</label>
</div>
<div class="input-field">
<input type="text" name="gdrive_used" id="gdrive_used" value="0">
<label for="gdrive_used">Googl Drive Used (GB)</label>
</div>
<div class="input-field">
<input type="number" name="mirror_total" id="mirror_total"  value="0">
<label for="mirror_total">Mirror Total (GB)</label>
</div>
<div class="input-field">
<input type="text" name="mirror_used" id="mirror_used" value="0">
<label for="mirror_used">Mirror Used (GB)</label>
</div>
<button type="submit" name="submit" id="submit" class="btn waves-effect waves-light">Add</button>
</form>
<h1>Admin List</h1>
<?php
show_admins($admins, $users);
    }

} elseif ($action == "edit") {
    if ($form->isExists('submit')) {
// process form
        $data = array('email' => $email, 'password' => $password, 'used' => $used, 'super' => $super, 'admin' => $admin, 'gdrive_total' => $gdrive_total, 'gdrive_used' => $gdrive_used, 'mirror_total' => $mirror_total, 'mirror_used' => $mirror_used);
        $email_db->updateRow('emails', 'id', $id, $data);
        ?>
<i class="material-icons green-text middled">check_circle</i> Email edited<br/>
<a href="/admin-cp/emails">Go Back</a>
<?php
} else {
        $details = $email_db->selectRow('emails', 'id', $id)['data'];
// show form
        ?>
<h1>Edit Email</h1>
<form action="/admin-cp/emails?action=edit&id=<?=$id;?>" method="post">
<div class="input-field">
<input type="email" name="email" id="email" value="<?=$details['email'];?>" readonly>
<label for="email">Email</label>
</div>
<div class="input-field">
<input type="text" name="password" id="password" value="<?=$details['password'];?>">
<label for="password">Password</label>
</div>
<div class="input-field">
<p>
<label>
    <input type="checkbox" id="used" name="used" <?php if ($details['used'] == 1) {
            echo "checked";
        }
        ?>/>
        <span>Storages are full?</span>
    </label>
</p>
</div>
<div class="input-field">
<p>
<label>
    <input type="checkbox" id="super" name="super" <?php if ($details['super'] == 1) {
            echo "checked";
        }
        ?>/>
        <span>Unllimited Google Drive?</span>
    </label>
</p>
</div>
<div class="input-field">
<input type="number" name="admin" id="admin" value="<?=$details['admin'];?>">
<label for="admin">Admin ID</label>
</div>
<div class="input-field">
<input type="number" name="gdrive_total" id="gdrive_total" value="15" value="<?=$details['gdrive_total'];?>">
<label for="gdrive_total">Googl Drive Total (GB)</label>
</div>
<div class="input-field">
<input type="text" name="gdrive_used" id="gdrive_used" value="<?=$details['gdrive_used'];?>">
<label for="gdrive_used">Googl Drive Used (GB)</label>
</div>
<div class="input-field">
<input type="text" name="mirror_total" id="mirror_total" value="<?=$details['mirror_total'];?>">
<label for="mirror_total">Mirror Total (GB)</label>
</div>
<div class="input-field">
<input type="number" name="mirror_used" id="mirror_used" value="<?=$details['mirror_used'];?>">
<label for="mirror_used">Mirror Used (GB)</label>
</div>
<button type="submit" name="submit" id="submit" class="btn waves-effect waves-light">Edit</button>
</form>
<?php
show_admins($admins, $users);
    }
} elseif ($action == "remove") {

} elseif ($action == "used") {
    $used_emails_total = $email_db->countRows('SELECT * FROM emails WHERE admin!=0');
    $used_emails = $email_db->selectRows('emails', ' WHERE admin!=0');
    ?>
<h1>Used Emails (<?=$used_emails_total;?>)</h1>
<ul class="collapsible">
<?php
foreach ($used_emails['data'] as $emails) {
        $unlimited = ($emails->super == '1') ? 'Yes' : 'No';
        $gd_free = $emails->gdrive_total - $emails->gdrive_used;
        $mirr_free = $emails->mirror_total - $emails->mirror_used;
        echo '<li>
    <div class="collapsible-header"><i class="material-icons">email</i> ' . $emails->email . '</div>
    <div class="collapsible-body">
    <div class="input-field">
    <i class="material-icons prefix">email</i>
    <input id="email' . $emails->id . '" type="email" value="' . $emails->email . '" readonly>
    <label for="email' . $emails->id . '">Email</label>
    </div>
    <div class="input-field">
    <i class="material-icons prefix">lock</i>
    <input id="password' . $emails->id . '" type="text" value="' . $emails->password . '" readonly>
    <label for="password' . $emails->id . '">Password</label>
    </div>
    <a href="/admin-cp/emails?action=unused&use=' . $emails->id . '" class=btn waves-effect waves-light><i class="material-icons middled">check</i> Use</a> <a href="/admin-cp/emails?action=edit&id=' . $emails->id . '" class=btn waves-effect waves-light><i class="material-icons middled">edit</i> Edit</a><br/>
    <i class="material-icons middled">all_inclusive</i> Unlimited: ' . $unlimited . ' <span class="right"><i class="material-icons middled">people</i> Used by: ' . $emails->admin . '</span><br/>
    <i class="material-icons middled">storage</i> Google Drive: Total: ' . $emails->gdrive_total . ' GB Used: ' . $emails->gdrive_used . ' GB Free: ' . $gd_free . ' GB<br/>
    <i class="material-icons middled">storage</i> Mirror: Total: ' . $emails->mirror_total . ' GB Used: ' . $emails->mirror_used . ' GB Free: ' . $mirr_free . ' GB</div>
    </li>';
    }
    echo '</ul>
<div class="fixed-action-btn"><a href="/admin-cp/emails?action=add" class="btn-floating btn-large"><i class="large material-icons">add</i></a></div>';
    ?>
<?php
} elseif ($action == "unused") {
    if ($form->get('use')) {
        $data = array('admin' => $user['id'], 'used' => 1);
        $email_db->updateRow('emails', 'id', $form->get('use'), $data);
    }
    $unused_emails = $email_db->selectRows('emails', ' WHERE admin=0');
    $unused_emails_total = $email_db->countRows('SELECT * FROM emails WHERE admin=0');
    ?>
<h1>Unused Emails (<?=$unused_emails_total;?>)</h1>
<ul class="collapsible">
<?php
foreach ($unused_emails['data'] as $emails) {
        $unlimited = ($emails->super == '1') ? 'Yes' : 'No';
        $gd_free = $emails->gdrive_total - $emails->gdrive_used;
        $mirr_free = $emails->mirror_total - $emails->mirror_used;
        echo '<li>
    <div class="collapsible-header"><i class="material-icons">email</i> ' . $emails->email . '</div>
    <div class="collapsible-body">
    <div class="input-field">
    <i class="material-icons prefix">email</i>
    <input id="email' . $emails->id . '" type="email" value="' . $emails->email . '" readonly>
    <label for="email' . $emails->id . '">Email</label>
    </div>
    <div class="input-field">
    <i class="material-icons prefix">lock</i>
    <input id="password' . $emails->id . '" type="text" value="' . $emails->password . '" readonly>
    <label for="password' . $emails->id . '">Password</label>
    </div>
    <a href="/admin-cp/emails?action=unused&use=' . $emails->id . '" class=btn waves-effect waves-light><i class="material-icons middled">check</i> Use</a> <a href="/admin-cp/emails?action=edit&id=' . $emails->id . '" class=btn waves-effect waves-light><i class="material-icons middled">edit</i> Edit</a><br/>
    <i class="material-icons middled">all_inclusive</i> Unlimited: ' . $unlimited . '<br/>
    <i class="material-icons middled">storage</i> Google Drive: Total: ' . $emails->gdrive_total . ' GB Used: ' . $emails->gdrive_used . ' GB Free: ' . $gd_free . ' GB<br/>
    <i class="material-icons middled">storage</i> Mirror: Total: ' . $emails->mirror_total . ' GB Used: ' . $emails->mirror_used . ' GB Free: ' . $mirr_free . ' GB</div>
    </li>';
    }
    echo '<ul/>
<div class="fixed-action-btn"><a href="/admin-cp/emails?action=add" class="btn-floating btn-large"><i class="large material-icons">add</i></a></div>';
} else {
    $used_emails_total = $email_db->countRows('SELECT * FROM emails WHERE admin!=0');
    $unused_emails_total = $email_db->countRows('SELECT * FROM emails WHERE admin=0');
    $my_emails_total = $email_db->countRows('SELECT * FROM emails WHERE admin=' . $user['id'] . '');
    $my_emails = $email_db->selectRows('emails', ' WHERE admin=' . $user['id'] . '');
    $total_emails = $email_db->countRows('SELECT * FROM emails');
    echo '<div class="fixed-action-btn"><a href="/admin-cp/emails?action=add" class="btn-floating btn-large"><i class="large material-icons">add</i></a></div>';

    ?>
<h1>All Emails (<?=$total_emails;?>)</h1>
<div class="collection">
<a href="/admin-cp/emails?action=used" class="collection-item"><i class="material-icons middled">folder</i> Used Emails (<?=$used_emails_total;?>)</a>
<a href="/admin-cp/emails?action=unused" class="collection-item"><i class="material-icons middled">folder</i> Unused Emails (<?=$unused_emails_total;?>)</a>
</div>
<h1>My Emails  (<?=$my_emails_total;?>)</h1>
<ul class="collapsible">
<?php
foreach ($my_emails['data'] as $emails) {
        $unlimited = ($emails->super == '1') ? 'Yes' : 'No';
        $gd_free = $emails->gdrive_total - $emails->gdrive_used;
        $mirr_free = $emails->mirror_total - $emails->mirror_used;
        echo '<li>
    <div class="collapsible-header"><i class="material-icons">email</i> ' . $emails->email . '</div>
    <div class="collapsible-body">
    <div class="input-field">
    <i class="material-icons prefix">email</i>
    <input id="email' . $emails->id . '" type="email" value="' . $emails->email . '" readonly>
    <label for="email' . $emails->id . '">Email</label>
    </div>
    <div class="input-field">
    <i class="material-icons prefix">lock</i>
    <input id="password' . $emails->id . '" type="text" value="' . $emails->password . '" readonly>
    <label for="password' . $emails->id . '">Password</label>
    </div>
    <a href="javascript:void(0)" onclick="copyContent(\'email' . $emails->id . '\')" class=btn waves-effect waves-light"><i class="material-icons middled">content_copy</i> Email</a> <a href="javascript:void(0)" onclick="copyContent(\'password' . $emails->id . '\')" class=btn waves-effect waves-light"><i class="material-icons middled">content_copy</i> Password</a> <a href="/admin-cp/emails?action=edit&id=' . $emails->id . '" class=btn waves-effect waves-light><i class="material-icons middled">edit</i> Edit</a><br/>
    <i class="material-icons middled">storage</i> Google Drive: Total: ' . $emails->gdrive_total . ' GB Used: ' . $emails->gdrive_used . ' GB Free: ' . $gd_free . ' GB<br/>
    <i class="material-icons middled">storage</i> Mirror: Total: ' . $emails->mirror_total . ' GB Used: ' . $emails->mirror_used . ' GB Free: ' . $mirr_free . ' GB</div>
    </li>';
    }
    echo '<ul/>';
}
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li><a href="/admin-cp/">Admin CP</a></li>
<li>Emails</li>
</ul>';
$scr = '<script>
function copyContent(id) {
    var copyText = document.getElementById(id);
    copyText.select();
    //copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    M.toast({html: \'<i class="material-icons middled green-text">check_circle</i> Copied to clipboard!\'});
  }
</script>';
Page::footer($scr, $extra);
?>