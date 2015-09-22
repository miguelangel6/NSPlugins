<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Authentication Plugin: nsgoogleauth Authentication
 * Just does a simple check against the moodle database.
 *
 * @package    auth_nsgoogleauth
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author	   Germán L. Rojas Muñoz & Fabián A. Batioja Acosta
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

/**
 * nsgoogleauth authentication plugin.
 *
 * @package    auth
 * @subpackage nsgoogleauth
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author 	   Germán L. Rojas Muñoz & Fabián A. Batioja Acosta
 */
class auth_plugin_nsgoogleauth extends auth_plugin_base {

    /**
     * The name of the component. Used by the configuration.
     */
    const COMPONENT_NAME = 'auth_nsgoogleauth';
    const LEGACY_COMPONENT_NAME = 'auth/nsgoogleauth';

    /**
     * Constructor.
     */
    function auth_plugin_nsgoogleauth() {
        $this->authtype = 'nsgoogleauth';
        $config = get_config(self::COMPONENT_NAME);
        $legacyconfig = get_config(self::LEGACY_COMPONENT_NAME);
        $this->config = (object)array_merge((array)$legacyconfig, (array)$config);
    }
	function print_oauth2_google(){
		global $CFG;
		print "<style>
				.btn_google{
				  -webkit-transform: translateZ(0);
				  transform: translateZ(0);
				  -webkit-transition-duration: 0.5s;
				  transition-duration: 0.5s;
				  -webkit-transition-property: color, background-color;
				  transition-property: color, background-color;
				  background: rgb(255,255,255); /* Old browsers */
				  background: -moz-linear-gradient(top,  rgba(255,255,255,1) 0%, rgba(224,224,224,1) 100%); /* FF3.6+ */
				  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(255,255,255,1)), color-stop(100%,rgba(224,224,224,1))); /* Chrome,Safari4+ */
				  background: -webkit-linear-gradient(top,  rgba(255,255,255,1) 0%,rgba(224,224,224,1) 100%); /* Chrome10+,Safari5.1+ */
				  background: -o-linear-gradient(top,  rgba(255,255,255,1) 0%,rgba(224,224,224,1) 100%); /* Opera 11.10+ */
				  background: -ms-linear-gradient(top,  rgba(255,255,255,1) 0%,rgba(224,224,224,1) 100%); /* IE10+ */
				  background: linear-gradient(to bottom,  rgba(255,255,255,1) 0%,rgba(224,224,224,1) 100%); /* W3C */
				  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e0e0e0',GradientType=0 ); /* IE6-9 */
				  border:2px solid #E0E0E0;
				  border-radius:10px;
				  max-width:300px;
				}
				
				div.btn_google:hover{
				  cursor: pointer;
				  -webkit-animation-name: hvr-back-pulse;
				  animation-name: hvr-back-pulse;
				  -webkit-animation-duration: 1s;
				  animation-duration: 1s;
				  -webkit-animation-delay: 0.5s;
				  animation-delay: 0.5s;
				  -webkit-animation-timing-function: linear;
				  animation-timing-function: linear;
				  -webkit-animation-iteration-count: infinite;
				  animation-iteration-count: infinite;
				  background: white;
				  background: #EFEFEF;
				  color: white;
				}
				
				
				.btn_google img, .btn_google h3{
				  display:inline-block;
				  vertical-align:middle;
				}
				
				.btn_google img{
				  width:50px;
				  height:50px;
				}
				
				.btn_google h3{
				  text-shadow: 0 2px 2px #FFFFFF;
				  font-weight: 400;
				  margin-top: 0.1em;
				  margin-bottom: 0.3em;
				  margin-left: 0.5em;
				  color: #777777;
				  font-family: 'Open Sans', sans-serif;
				}
				
				</style>
				
				<header>
				  <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
				</header>
				<a href=\"{$CFG->wwwroot}/auth/nsgoogleauth/handler.php?ssogoogle=1\">
				  <div class=\"btn_google\">
				    <img src=\"http://temas.s3.amazonaws.com/nivel7/social-google-box-icon.png\">
				    <h3>Ingresa con google</h3>
				  </div>
				</a>";
	}
    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist. (Non-mnet accounts only!)
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password) {
        return false; //The user never use the Moodle form for this auth plugin
    }
	public function oauth2_google(){
		global $CFG, $DB, $USER;
		require_once "{$CFG->dirroot}/auth/nsgoogleauth/lib/autoload.php";
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
		$client->addScope(Google_Service_Oauth2::PLUS_LOGIN);
		$client->addScope(Google_Service_Oauth2::PLUS_ME);
		$client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);
		$client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
		$client->setRedirectUri("{$CFG->wwwroot}/auth/nsgoogleauth/google_redirect.php");
		
		
		$auth_url = $client->createAuthUrl();
		header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
	}
    
    function prevent_local_passwords() {
        return true;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal() {
        return false;
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    function change_password_url() {
        return null;
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    function can_reset_password() {
        return false;
    }

    /**
     * Returns true if plugin can be manual set.
     *
     * @return bool
     */
    function can_be_nsgoogleauthly_set() {
        return true;
    }

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $config An object containing all the data for this page.
     * @param string $error
     * @param array $user_fields
     * @return void
     */
    function config_form($config, $err, $user_fields) {
        include 'config.html';

    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     *
     * @param stdClass $config
     * @return void
     */
    function process_config($config) {
        // Set to defaults if undefined.
    	if (!isset ($config->googleclientid)) {
    		$config->googleclientid = '';
    	}
    	if (!isset ($config->googleclientsecret)) {
    		$config->googleclientsecret = '';
    	}
    	if (!isset ($config->createuser)) {
    		$config->createuser = '';
    	}
    	if (!isset($config->domain)) {
    		$config->domain = '';
    	}

        // Save settings.
        set_config('googleclientid', $config->googleclientid, self::COMPONENT_NAME);
        set_config('googleclientsecret', $config->googleclientsecret, self::COMPONENT_NAME);
        set_config('createuser', $config->createuser, self::COMPONENT_NAME);
        set_config('domain', $config->domain, self::COMPONENT_NAME);
       
        return true;
    }

}


