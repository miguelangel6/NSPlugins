<?php
	if((isset($_GET["ssogoogle"]))&&($_GET["ssogoogle"])) {
		require_once dirname(__FILE__)."../../../config.php";
		global $CFG;
		include_once ("{$CFG->dirroot}/auth/nsgoogleauth/auth.php");
		$auth_nsgoogle = new auth_plugin_nsgoogleauth();
		$auth_nsgoogle->oauth2_google();
	}else{
		print_r("Parameter Missing");
	}