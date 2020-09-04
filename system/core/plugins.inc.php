<?php
if(!defined("SYSTEM_CORE_PLUGINS"))
{
	define("SYSTEM_CORE_PLUGINS", 1);

	final class Plugins
	{
		private static $plugins = array();
		private static $hooks = array();
		
		private static function _LoadPlugins($path, $prefix)
		{
			foreach(glob($path."plugin.*.inc.php") as $filename)
			{
				
				$className = substr($filename, strrpos($filename, "/") + 1);
				$className = substr($className, 7, -8);
				$key = strtolower($prefix.$className);
				
				$className = "Plugin_".$prefix.str_replace("-", "_", $className);
				
				
				if(isset(self::$plugins[$key])) continue;
				include_once($filename);
				if(!class_exists($className)) continue;
				
				self::$plugins[$key] = $plugin = new $className();
				
				$plugin->setup($key);
				$plugin->init();
			}
		}
		
		public static function LoadSystemPlugins()
		{
			self::_LoadPlugins(SYSTEM_PATH."generic/plugins/", "system_");
		}
		
		public static function LoadApplicationPlugins()
		{
			self::_LoadPlugins(APPLICATION_PATH."plugins/", "app_");
		} 
		
		public static function RegisterHook($key, $hook, $callback)
		{
			if(!is_callable($callback)) 
			{
				return false;
			}
			$hook = trim(strtolower($hook));
			if(!isset(self::$hooks[$hook])) 
			{
				self::$hooks[$hook] = array();
			}
			self::$hooks[$hook][] = array("key" => $key, "callback" => $callback);
			
			return true;
		}
		
		public static function CallHook($hook, $args = array())
		{
			$hook = trim(strtolower($hook));
			if(!isset(self::$hooks[$hook])) 
			{
				return $args;
			}
			if(!is_array($args)) 
			{
				$args = array("arg" => $args);
			}
			$args['abort'] = false;
			foreach(self::$hooks[$hook] as $callable)
			{
				$ret = call_user_func($callable['callback'], $args);
				if($ret && is_array($ret)) 
				{
					// $args = $ret + $args
					$args = array_merge($args, $ret);
				}
				if($args['abort']) break;
			}
			return $args;
		}
	}
	
	abstract class Plugin extends Base
	{
		protected $key;
		
		public function __construct()
		{
			parent::__construct();	
// 			$this->AutoLoad(); // From the controller_components parent class
		}
		
		public function setup($key)
		{
			$this->key = $key;
		}
		
		public function getKey()
		{
			return $this->key;
		}
		
		protected function registerHook($hook, $callback)
		{
			return Plugins::RegisterHook($this->key, $hook, $callback);
		}
		
		abstract function init();
	}
	
	/**
	 * List of hooks throughout the system and what is passed through and what can be expected to be passed back
	 * 
	 *******************************************************************
	 * System Hooks - Used by all applications
	 *******************************************************************
	 *
	 * system:init - Called after base classes have been loaded
	 * 
	 *******************************************************************
	 * Application Hooks - Used by MPC/WOW
	 *******************************************************************
	 *
	 *
	 *
	 *******************************************************************
	 * Module Hooks - Used by MPC/WOW
	 *******************************************************************
	 */
}
?>