<?php

	// Set HTTPS if not already set
	if(!isset($_SERVER['HTTPS'])) {
		$_SERVER['HTTPS'] = false;
	}

	// Get our configuration
	$configPath = __FILE__;
	$configPath = str_replace('\\', '/', $configPath);
	$configPath = substr($configPath, 0, strrpos($configPath, '/'));
	$configPath = substr($configPath, 0, strrpos($configPath, '/'));
	require_once $configPath.'/application/config.inc.php';

	if(isset($config['cookieDomain'])) {
		ini_set('session.cookie_domain', $config['cookieDomain']);
	}

	// Setup the session
	ini_set('session.gc_maxlifetime', 60 * 30); // 60 seconds * 30 minutes
	ini_set('post_max_size', '64M');
	ini_set('upload_max_filesize', '64M');
	session_name($config['session_name']);
// 	session_start();

	if(isset($_SESSION['user']['timezone'])) {
		date_default_timezone_set($_SESSION['user']['timezone']);
	}
	else {
		date_default_timezone_set("Africa/Johannesburg");
	}

	// If debugging mode, send errors to the browser
	if(DEBUG) {
		ini_set('display_errors', 1);
		error_reporting(E_ALL);
	}

	require_once SYSTEM_PATH.'core/base.inc.php';
	require_once SYSTEM_PATH.'core/application.php';
	require_once SYSTEM_PATH.'core/controller.inc.php';
	require_once SYSTEM_PATH.'core/model.inc.php';
	require_once SYSTEM_PATH.'core/plugins.inc.php';

	require_once SYSTEM_PATH.'functions.inc.php';
	require_once SYSTEM_PATH.'classes/SessionManager.php';

	if(file_exists(APPLICATION_PATH.'functions.inc.php')) {
		require_once APPLICATION_PATH.'functions.inc.php';
	}

	// Create the application
	Application::CreateApplication($config);


	// Load system plugins
	Plugins::LoadSystemPlugins();

	// Load application plugins
	Plugins::LoadApplicationPlugins();

	// Call the init hook
	Plugins::CallHook("system:init");

	$sess = new SessionManager();
	register_shutdown_function("session_write_close");

	session_start();

