<?php
	$server = strtolower($_SERVER['SERVER_NAME']);

	// Include the boostrap script
	require_once 'system/bootstrap.inc.php';
	
	// ?????
	cleanGetArray();
	
	// Call the router class to determine the module, controller and action to fire
	require_once 'system/router.inc.php';
	$routeData = Router::DetermineRouteData();
	
	// Call the hook after the route has been determined
	Plugins::CallHook('system:route_set', array('route' => $routeData));
	
	// Ajax requests only call the class->method, do not put in a theme
	if(isset($_GET['ajax']) || isset($_POST['ajax'])) {
		require_once $routeData->buildPath();
		if(!class_exists($routeData->class)) {
			die('Unable to find class ['.$routeData->class.']');
		}
		else {
			$object = new $routeData->class;
			call_user_func_array(array($object, $routeData->method), $routeData->params);
		}
	}
	else {
		// Prep the html content to be displayed within the theme
		$controller = null;
		ob_start();
		require_once $routeData->buildPath();
		if(!class_exists($routeData->class)) {
			echo 'Unable to find class ['.$routeData->class.']';
		}
		else {
			$controller = new $routeData->class;
			call_user_func_array(array($controller, $routeData->method), $routeData->params);
			
			// Check if the controller is asking for a different theme
			$overrideTheme = $controller->GetTheme();
			if($overrideTheme && file_exists($config['path_themes']."{$overrideTheme}/master.php")) {
				$config['theme'] = $overrideTheme;
			}
		}
		$page_content = ob_get_clean();
		
		define('THEME', $config['theme']);

		$routeData->theme_path = $config['path_themes'].THEME.'/';
		$routeData->theme_url = WEB_PATH_D.'themes/'.THEME.'/';
		
		$theme_path = $routeData->theme_path;
		$theme_url = $routeData->theme_url;
		
		ob_start();
		require $config['path_themes'].THEME.'/master.php';
		$html = ob_get_clean();
		echo $html;
	}
