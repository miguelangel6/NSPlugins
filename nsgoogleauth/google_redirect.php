<?php
	require_once dirname(__FILE__)."../../../config.php"; 
	global $CFG, $DB, $USER;
	global $redirect;
	
	require_once "{$CFG->dirroot}/auth/nsgoogleauth/lib/autoload.php";
	
	$authMethod='nsgoogleauth';
	
	if(isset($_GET['code'])){
		$client = new Google_Client();
		$authplugin = get_auth_plugin('nsgoogleauth');
		$clientsecret = $authplugin->config->googleclientsecret;
		$clientid = $authplugin->config->googleclientid;
		$config_data='{"web":
						{"client_secret":"'.$clientsecret.'",
						"client_id":"'.$clientid.'",
						"auth_uri":"https://accounts.google.com/o/oauth2/auth",
						"token_uri":"https://accounts.google.com/o/oauth2/token",
						"redirect_uris":["'.$CFG->wwwroot.'/auth/nsgoogleauth/google_redirect.php"],
						"javascript_origins":["'.$CFG->wwwroot.'"]
						}
					}';
		$client->setAuthConfig($config_data);
		$client->authenticate($_GET['code']);
		//$access_token = $client->getAccessToken();
		
		$userOauth = new Google_Service_Oauth2($client);
		$user_info = $userOauth->userinfo->get();
		$authplugin = get_auth_plugin('nsgoogleauth');
		$domains_allowed = str_replace(" ", "", $authplugin->config->domain);
		$domains = explode(",",$domains_allowed);
		if(in_array($user_info->hd, $domains)){
			if($user_info->verifiedEmail){
				$email=strtolower($user_info->email);
				$search = "@".$user_info->hd;
				$username = str_replace($search, "", $email);
				if($authplugin->config->domain){
					if($user = $DB->get_record_sql("SELECT * FROM {$CFG->prefix}user WHERE auth like '{$authMethod}' AND (LOWER(username) LIKE '$username')")){ //Hay un usuario con el mismo nombre para el dominio;
						$USER = get_complete_user_data('username', $user->username);
						add_to_log(SITEID, 'user', 'login', "view.php?id=$USER->id&course=" . SITEID, $USER->id, 0, $USER->id);
						update_user_login_times();
						set_moodle_cookie($USER->username);
						set_login_session_preferences();
						header('Location: '.$CFG->wwwroot);
					}elseif($user = $DB->get_record_sql("SELECT * FROM {$CFG->prefix}user WHERE auth like '{$authMethod}' AND (LOWER(email) LIKE '$email')")){ //Hay un usuario con el mismo correo;
						$USER = get_complete_user_data('username', $user->username);
						add_to_log(SITEID, 'user', 'login', "view.php?id=$USER->id&course=" . SITEID, $USER->id, 0, $USER->id);
						update_user_login_times();
						set_moodle_cookie($USER->username);
						set_login_session_preferences();
						header('Location: '.$CFG->wwwroot);
					}elseif($authplugin->config->createuser){
						$user = new StdClass();
						$user->auth = $authMethod;
						$user->confirmed = 1;
						$user->mnethostid = 1;
						$user->email = $user_info->email;
						$user->username = $username;
						$user->password = "%not allowed%";
						$user->lastname = $user_info->familyName;
						$user->firstname = $user_info->givenName;
						$user->id = $DB->insert_record('user', $user);
							
						
						//Ya creado y obligado a cambiar la contraseña o no, se crea el usuario
						$USER = get_complete_user_data('id', $user->id);
						add_to_log(SITEID, 'user', 'login', "view.php?id=$USER->id&course=" . SITEID, $USER->id, 0, $USER->id);
						update_user_login_times();
						set_moodle_cookie($USER->username);
						set_login_session_preferences();
						header('Location: '.$CFG->wwwroot);//Se redirige al usuario a la página inicial de Moodle
					}else{
						header("Location: {$CFG->wwwroot}/auth/nsgoogleauth/error.php");
					}
				}
			}else{
				header("Location: {$CFG->wwwroot}/login/index.php");
			}
		}else{
			//header("Location: {$CFG->wwwroot}/login/index.php");
			header("Location: {$CFG->wwwroot}/auth/nsgoogleauth/unauthorized.php");
			
		}
	}
	
