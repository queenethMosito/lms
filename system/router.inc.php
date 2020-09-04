<?php
	class RouteData {
		public $module;
		public $module_path;
		public $page;
		public $params;
		public $theme_path;
		public $theme_url;
		public $method;
		public $direct;
		public $class;
		
		public function __construct() {			
			$this->module = 'home';
			$this->module_path = CONTOLLERS_PATH.'home';
			$this->page = 'index';
			$this->params = array();
			$this->theme_path = '';
			$this->theme_url = '';
			$this->method = 'indexAction';
			$this->direct = isset($_GET['direct']);
			$this->class = 'Home_Index_Controller';
		}
		
		public function buildPath() {
			return $this->module_path."/".$this->page.".php";
		}
	}
	
	class Router {
		public static function DetermineRouteData() {
			// Prepare the route data object instance
			$routeData = new RouteData();
			
			// Set mode to 0 for module
			$mode = 0;
	
			// Get the route from the _GET
			$route = isset($_GET['route']) ? $_GET['route'] : '';
	
			// Clean so users cant type in ../
			while(strpos($route, '../') !== false) {
				$route = str_replace('../', '', $route);
			}
	
			// Prepare some variables
			$data = explode('/', $route);
			$base = CONTOLLERS_PATH;
			$baseModule = '';
	
			// Run through each segmant and update where needed
			foreach($data as $segment) {
				// Trim segment
				$segment = trim($segment);
				
				// Ignore blank segments
				if(!$segment) continue;
				
				// Segment in lowercase
				$segmentLower = strtolower($segment);
				
				// Checking for module
				if($mode == 0) {
					$current = $base.$segmentLower.'/';
					if(!is_file($current) && file_exists($current)) {
						$baseModule = $baseModule == '' ? $segmentLower : $baseModule.'/'.$segmentLower;	
						$routeData->module = $baseModule;
						$routeData->module_path = substr($current, 0, -1);
						$base = $current;
					}
					else {
						$mode = 1;
					}
				}
				
				// Checking for file
				if($mode == 1) {
					$current = $routeData->module_path.'/'.$segmentLower.'.php';
					$mode = 2;
					if(is_file($current)) {
						$className = str_replace(array('-', '_', '/'), array('', '', ''), $routeData->module).'_'
							.str_replace(array('-', '_'), array('', ''), $segmentLower).'_Controller';
						$routeData->page = $segmentLower;
						$routeData->class = $className;
						continue;
					}
				}
				
				// Method
				if($mode == 2) {
					$mode = 3;
					require_once $routeData->buildPath();
					$className = $routeData->class;
					if(class_exists($className)) {
						$routeData->class = $className;
						$methodName = str_replace('-', '_', $segmentLower).'Action';
						if(is_callable(array($className, $methodName), false)) {
							$routeData->method = $methodName;
							continue;
						}
					}
				}
				
				// Params
				if($mode == 3) {
					$routeData->params[] = $segment;	
				}
			} // End of foreach segment
			
			// If we couldn't find a module or controller, set the rest as params
			/*if($mode == 1 || $mode == 2) {	
				$class = str_replace('-', '_', $routeData->page).'_Controller';
				if(class_exists($class)) {
					$routeData->class = $class;
					$obj = new $class;
					$methodName = str_replace('-', '_', $segment);
					if(method_exists($obj, $methodName)) {
						$routeData->method = $methodName;
					}
				}
			}*/
			
			// Check if logged in
			if(isset($_SESSION['user'])) {
				if($routeData->module == 'home' && $routeData->page == 'index') {
					$routeData->module = 'client';
					$routeData->module_path = CONTOLLERS_PATH.'client';
					$routeData->class = 'Client_Index_Controller';
				}
			}
			else {
				// Dont allow to client data
				if($routeData->module == 'client' || substr($routeData->module, 0, 7) == 'client/') {				
					$routeData->module = 'login';
					$routeData->module_path = CONTOLLERS_PATH.'home';
					$routeData->page = 'login';
					$routeData->class = 'Home_Login_Controller';
					$routeData->method = 'indexAction';
				}
			}
			
			// Return the route data
			return $routeData;
		}
	}
