<?php
	require_once '../../config.php'; 
	require_once($CFG->libdir.'/adminlib.php');
	global $CFG;
	header('Content-Type: text/html; charset=UTF-8');
	
	$PAGE->set_context(context_system::instance());
	$PAGE->set_url("{$CFG->wwwroot}/auth/nsgoogleauth/unauthorized.php");
	$PAGE->requires->jquery();
	$PAGE->requires->jquery_plugin('ui');
	$PAGE->requires->jquery_plugin('ui-css');
	
	redirect("https://mail.google.com/mail/logout", get_string('unauthorized_domain', 'auth_nsgoogleauth'));
?>
