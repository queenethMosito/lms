<?php
	/**
	 * This file will set up the configiration for the website
	 *
	 * The following needs to be defined by the end of the script:
	 *
	 * @var application The application folder to be used
	 * @var root_web The web address
	 */

	$server = strtolower($_SERVER["SERVER_NAME"]);
	$config = array();

	// Get the application root
	$path = str_replace("\\", "/", __FILE__);
	$path = substr($path, 0, strrpos($path, "/"));
	$path = substr($path, 0, strrpos($path, "/") + 1);

	$config['root_physical'] = $path;
	
	$config['server'] = $server;
	$config['theme'] = 'default'; // Default theme
	$config['session_name'] = 'LMS'; // Default session name
	$config['title'] = 'Learner Management System';
	$config['debug'] = false;
	$config['root_web'] = "http://{$config['server']}/";
	$config['theme'] = 'lms';
	$config['ssl'] = false;

	// TODO: Get the mysql information in here as well
	
	switch($server) {
		// Local
		case 'lms.localhost.com':
			define('GOOGLE_ANALYTICS', false);
			$config['debug'] = true;

			$config['db']['default']['type'] = 'MySQL';
			$config['db']['default']['server'] = 'localhost';
			$config['db']['default']['username'] = 'dev';
			$config['db']['default']['password'] = 'dev';
			$config['db']['default']['database'] = 'lms';

			$config['storage_path'] = $path.'documents/';
			break;
	
		default:
			die('Website not configured for host : <b>'.$server.'</b>');
	}

	// Set up some more defines
	$config['path_system'] = "{$config['root_physical']}system/";
	$config['storage_path'] = "{$config['root_physical']}documents/";
	$config['path_classes'] = "{$config['path_system']}classes/";
	$config['path_modules'] = "{$config['path_system']}modules/";
	$config['path_themes'] = "{$config['root_physical']}themes/";
	$config['path_views'] = "{$config['path_system']}views/";

	// Setup some defines
	if(!isset($_SERVER['SERVER_PORT'])) $_SERVER['SERVER_PORT'] = 80;
	define("WEB_PATH", $config['root_web']);
	if($config['ssl']) {
		define("WEB_PATH_S", substr($config['root_web'], 0, 4).substr($config['root_web'], 4));
		define("WEB_PATH_D", substr($config['root_web'], 0, 4).($_SERVER['SERVER_PORT'] != 80 ? "s" : "").substr($config['root_web'], 4));
	}
	else {
		define("WEB_PATH_S", $config['root_web']);
		define("WEB_PATH_D", $config['root_web']);
	}
	define("FRAMEWORK_PATH", $config['root_physical']);
	define("SYSTEM_PATH", FRAMEWORK_PATH.'system/');
	define("APPLICATION_PATH", FRAMEWORK_PATH.'application/');
	define("CLASS_PATH", SYSTEM_PATH."classes/");
	define("CONTOLLERS_PATH", APPLICATION_PATH.'controllers/');
	define("MODELS_PATH", APPLICATION_PATH.'models/');
	define("COMPONENTS_PATH", APPLICATION_PATH.'components/');
	define("VIEWS_PATH", APPLICATION_PATH.'views/');
	define("STORAGE_PATH", $config['storage_path']);


	define("SITE_TITLE", $config['title']);
	define("DEFAULT_THEME", $config['theme']);

	if(!defined("GOOGLE_ANALYTICS")) {
		define("GOOGLE_ANALYTICS", true);
	}
	if(!defined("DEBUG")) {
		define("DEBUG", $config['debug']);
	}
