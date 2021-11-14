<?php
// check if user is logged in by checking user variable in session
function isLoggedIn()
{
    if (isset($_SESSION['user'])) {
        return true;
    } else {
        return false;
    }
}

// redirect user to another page using php header function or html meta tag
function redirectTo($url, $type = 1, $seconds = 3)
{
    if ($type == 1) {
        header("Location: $url");
    } else {
        echo '<meta http-equiv="refresh" content="' . $seconds . '; url=' . $url . '"/>';
    }

}

// check if current environment is localhost or actual server
function isLocal()
{
    $whitelist = array(
        '127.0.0.1',
        '::1',
    );
    if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
        return false;
    } else {
        return true;
    }
}

// makes sure that current page doesn't have a php extentension at the end
function noExt()
{
    $url = CURRENT_URL;
    $path = parse_url($url, PHP_URL_PATH);
    $extension = pathinfo($path, PATHINFO_EXTENSION);
    if (!empty($extension)) {
        http_response_code(404);
        include '' . $_SERVER["DOCUMENT_ROOT"] . '/_error.php'; // provide your own HTML for the error page
        die();
    }
    $filename = pathinfo($path, PATHINFO_BASENAME);
    if (preg_match("~\bindex\b~", $filename)) {
        http_response_code(404);
        include '' . $_SERVER["DOCUMENT_ROOT"] . '/_error.php'; // provide your own HTML for the error page
        die();
    }
}

// convert string to a url friendly path
function convertToLink($string)
{
    $string = strtolower($string);
    $string = str_replace("  ", " ", $string);
    $string = str_replace(" ", "-", $string);
    $string = str_replace("@", "-", $string);
    $string = str_replace("/", "-", $string);
    $string = str_replace("\\", "-", $string);
    $string = str_replace(":", "", $string);
    $string = str_replace("\"", "", $string);
    $string = str_replace("'", "", $string);
    $string = str_replace("<", "", $string);
    $string = str_replace(">", "", $string);
    $string = str_replace(",", "", $string);
    $string = str_replace("?", "", $string);
    $string = str_replace(";", "", $string);
    $string = str_replace(".", "", $string);
    $string = str_replace("[", "", $string);
    $string = str_replace("]", "", $string);
    $string = str_replace("(", "", $string);
    $string = str_replace(")", "", $string);
    $string = str_replace("*", "", $string);
    $string = str_replace("!", "", $string);
    $string = str_replace("$", "-", $string);
    $string = str_replace("&", "and", $string);
    $string = str_replace("%", "", $string);
    $string = str_replace("#", "", $string);
    $string = str_replace("^", "", $string);
    $string = str_replace("=", "", $string);
    $string = str_replace("+", "", $string);
    $string = str_replace("~", "", $string);
    $string = str_replace("`", "", $string);

    return $string;
}

// checks if the passed link is google drive link
function isGd($link)
{
    $domain = parse_url($link, PHP_URL_HOST);
    if ($domain == 'drive.google.com') {
        return true;
    } else {
        return false;
    }
}

// checks for an imdb link
function isImdb($link)
{
    $domain = parse_url($link, PHP_URL_HOST);
    if ($domain == 'imdb.com' || $domain == 'www.imdb.com' || $domain == 'm.imdb.com') {
        return true;
    } else {
        return false;
    }
}

// extracts file's id from a google drive url
function gdToID($url)
{
    if (preg_match('/(.*)id=/m', $url)) {
        $url = preg_replace('/(.*)id=/m', '', $url);
        $url = preg_replace('/&(.*)/m', '', $url);
    } else {
        $url = preg_replace('/(.*?)file\/d\//m', '', $url);
        $url = preg_replace('/\/(.*)/m', '', $url);
    }
    return $url;
}

// generates a direct download link from a normal url (works if the file is under 200 MB)
function gdToDownload($url)
{
    $id = gdToID($url);
    return 'https://drive.google.com/uc?id=' . $id . '&export=download';
}

// generates a streamable link from normal google drive file link (only works for videos)
function gdToStream($url)
{
    $id = gdToID($url);
    return 'https://drive.google.com/file/d/' . $id . '/preview';
}

// validates an image from a remote url
function isImage($link)
{
    if (@GetImageSize($link)) {
        return true;
    } else {
        return false;
    }
}

// generates a streamable link from normal mega file link (only works for videos)
function megaToStream($url)
{
    if (preg_match('/https:\/\/mega(.*)/m', $url)) {
        $url = preg_replace('/(.*)\/file\//m', '', $url);
        return 'https://mega.nz/embed/' . $url;
    }
}

// returns pagination links
function pagination($total, $current, $per_page)
{
    if ($total >= $perpage) {
        $totalpages = round($total / $per_page);
        //if($current>$totalpages) $current = 1;
        $totalpages2 = $total / $per_page;
        if ($totalpages2 > $totalpages) {
            $totalpages = $totalpages + 1;
        }

        //$pagesar['First'] = "1";
        if ($current > 1) {
            $cd = $current - 1;
            $pagesar['<i class="material-icons middled">chevron_left</i>'] = $cd;
            if ($cd == 1) {
                $st = 1;
            } else if ($cd == 2) {
                $st = $cd - 1;
            } else if ($cd >= 3) {
                $st = $cd - 2;
            }

            for ($i = $st; $i <= $cd; $i++) {
                $pagesar[$i] = $i;
            }
        }
        if ($totalpages > $current + 4) {
            $finalstop = $current + 4;
        } else {
            $finalstop = $totalpages;
        }

        for ($j = $current; $j <= $finalstop; $j++) {
            $pagesar[$j] = $j;
        }
        if ($totalpages > $current) {
            $pagesar['<i class="material-icons middled">chevron_right</i>'] = $current + 1;
        }

        //$pagesar['Last'] = $totalpages;
    } else {
        $pagesar['1'] = "1";
    }
    return $pagesar;
}


// sort numbers in K,M,B etc
function shortNumber($n, $precision = 1)
{
    if ($n < 900) {
        // 0 - 900
        $n_format = number_format($n, $precision);
        $suffix = '';
    } else if ($n < 900000) {
        // 0.9k-850k
        $n_format = number_format($n / 1000, $precision);
        $suffix = 'K';
    } else if ($n < 900000000) {
        // 0.9m-850m
        $n_format = number_format($n / 1000000, $precision);
        $suffix = 'M';
    } else if ($n < 900000000000) {
        // 0.9b-850b
        $n_format = number_format($n / 1000000000, $precision);
        $suffix = 'B';
    } else {
        // 0.9t+
        $n_format = number_format($n / 1000000000000, $precision);
        $suffix = 'T';
    }
    // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
    // Intentionally does not affect partials, eg "1.50" -> "1.50"
    if ($precision > 0) {
        $dotzero = '.' . str_repeat('0', $precision);
        $n_format = str_replace($dotzero, '', $n_format);
    }
    return $n_format . $suffix;
}

// count online users based on sessions
function onlineUsers()
{
    $i = 0;
    $path = session_save_path();
    if (trim($path) == "") {
        return false;
    }
    $d = dir($path);
    while (false !== ($entry = $d->read())) {
        if ($entry != "." and $entry != "..") {
            if (time() - filemtime($path . "/$entry") < 1 * 60) {
                $i++;
            }
        }
    }
    $d->close();
    return $i + 1;
}

// sets a one time session message
function setOneTimeMessage($name,$message) {
    $_SESSION[$name] = $message;
}

// shows a one time session message which was set previously and destructs after showing once
function showOneTimeMessage($name) {
    $message = $_SESSION[$name];
    if(isset($message)) {
        echo $message;
        unset($_SESSION[$name]);
    }
}

// sets a one time session redirect url
function setOneTimeUrl($name,$url) {
    $_SESSION[$name] = $url;
}

// shows a one time session message which was set previously and destructs after showing once
function oneTimeRedirect($name) {
    $url = $_SESSION[$name];
    if(isset($url)) {
        header("Location: $url");
        unset($_SESSION[$name]);
    }
}

//check if logged in user is an admin or not
function isAdmin($user,$admins) {
    if(!isLoggedIn()) {
        setOneTimeMessage('login_msg','You need to be logged in to access that page');
        setOneTimeUrl('target_url', CURRENT_URL);
        redirectTo('/user/login');
        exit(); 
    } else {
        if(!in_array($user['id'],$admins)) {
            setOneTimeMessage('common_message','You do not have enogh permission to access that page');
            redirectTo('/');
            exit();
        }
    }
}

// check if the logged in admin is the head admin or not
function isHeadAdmin($user) {
    if($user['id']!=1) {
        setOneTimeMessage('common_message','You do not have enogh permission to access that page');
        redirectTo('/admin-cp/');
        exit();
    }
}

function userRequestsMessage($db) {
    $user_requests = $db->countRows('SELECT * FROM requests WHERE processed=0');
    if ($user_requests > 0) {
        echo '<h1>User Requests</h1>
        <div class="card-panel"><i class="material-icons middled">info</i> There are ' . $user_requests . ' pending user <a href="/admin-cp/requests">requests</a></div>';
    }
}

function brokenLinksMessage($db) {
    $broken_links = $db->countRows('SELECT * FROM posts WHERE broken=1');
    if ($broken_links > 0) {
        echo '<h1>Broken Links</h1>
        <div class="card-panel"><i class="material-icons middled">info</i> There are ' . $broken_links . ' <a href="/admin-cp/broken">broken links</a> reported by users</div>';
    }
}

