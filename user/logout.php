<?php
	require("../includes/core.php");
	if(isLoggedIn()) {
	    $users->logout();
	    }
	  redirectTo('/user/');
?>