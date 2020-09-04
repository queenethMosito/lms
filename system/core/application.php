<?php
if(!defined("_CORE_APPLICATION_INC_PHP_"))
{
	define("_CORE_APPLICATION_INC_PHP_", 1.0);
	
	// TODO: Decide if we really want an application class or not. Will be a mission to take it out though :-/
	
	final class Application
	{
		private $connections;
		private $db_default;
		private $db_logs;
		
		const LOG_ERROR = "Error";
		const LOG_INFO = "Info";
		const LOG_CRITICAL = "Critical";
		const LOG_WARNING = "Warning";
		
		
		private function __construct($config)
		{
			// Setup the database connections
			$connections = array();
			foreach($config['db'] as $name => $settings)
			{
				$path = $config['path_classes'].strtolower($settings['type']).".php";
				include($path);
				$connection = new $settings['type']($settings);
				$this->connections[$name] = $connection;
			}
			
			//$this->db_default = $config['db_default'];
			//$this->db_logs = $config['db_logs'];
		}
		
		public function __destruct()
		{
		}
		
		/**
		 * @param unknown_type $name
		 * @return MySQL
		 */
		public function getConnection($name = "default")
		{
			if(!isset($this->connections[$name])) $name = "default";
			return isset($this->connections[$name]) ? $this->connections[$name] : null;
		}
		
		public function addLog($type)
		{
			$data = array();
			$data['date'] = getSystemDate();
			$data['url'] = getCurrentURL();
			$data['get'] = print_r($_GET, true);
			$data['post'] = print_r($_POST, true);
			$data['session'] = print_r($_SESSION, true);
			$data['message'] = "Not yet setup";
			$data['parameters'] = "Not yet setup";
			$data['type'] = $type;
			$data['ipaddress'] = getIPAddress();
			$data['browser'] = $_SERVER['HTTP_USER_AGENT'];
			
			foreach($data as $key => $value)
				print("<p><strong>{$key}: </strong>{$value}</p>");
		}
		
		private static $application = null;
		
		public static function CreateApplication($config)
		{
			if(!Application::$application)
				Application::$application = new Application($config);
		}
		
		/**
		 * @return Application
		 */
		public static function GetApplication()
		{
			return Application::$application;
		}
	}
}