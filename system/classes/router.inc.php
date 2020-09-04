<?php
	/**
	 * Class information to go here
	 */
	if(!defined("_CLASS_ROUTER_INC_PHP_"))
	{
		define("_CLASS_ROUTER_INC_PHP_", 1);
		
		class RouterResult
		{
			public $path;
			public $script;
			public $class;
			public $method;
			public $params;
			
			public function __construct($path, $script, $class, $method, $params)
			{
				$this->path = $path;
				$this->script = $script;
				$this->class = $class;
				$this->method = $method;
				$this->params = $params;
			}
			
			public function getFullPath()
			{
				return $this->path . $this->script.".php";
			}
		}
		
		class Router
		{
			public function execute($basePath, $defaultPath, $defaultScript, $defaultMethod, $urlPath, $classNameFormat)
			{
				if(substr($basePath, -1) != "/") $basePath .= "/";
				if(substr($defaultPath, -1) != "/") $defaultPath .= "/";
				
				$urlPath = trim($urlPath);
				if(strlen($urlPath) == 0)
				{
					return array(new RouterResult($basePath.$defaultPath, $defaultScript, str_replace("?", $defaultScript, $classNameFormat), $defaultMethod, array()));
				}
				
				$data = array();
				$parts = explode("/", $urlPath);
				$count = sizeof($parts);
				
				// F S M P
				$array = array();
				array_push($array, "A");
				array_push($array, "B");
				array_push($array, "C");
				array_push($array, "D");
				
				while(sizeof($array) > 0)
				{
					$s = array_pop($array);
					if(strlen($s) == $count)
					{
						$path = "";
						$script = "";
						$method = "";
						$params = array();
						
						for($i = 0; $i < strlen($s); $i++)
						{
							if($s[$i] == "A") $path .= $parts[$i] . "/";
							if($s[$i] == "B") $script = $parts[$i];
							if($s[$i] == "C") $method = $parts[$i];
							if($s[$i] == "D") $params[] = $parts[$i];
						}
						
						if(!$path || $path == "" || $path == "/") $path = $defaultPath;
						if(!$script) $script = $defaultScript;
						if(!$method) $method = $defaultMethod;
						
						$className = str_replace("?", $script, $classNameFormat);
						
						$router = new RouterResult($basePath.$path, $script, $className, $method, $params);
						
						// Check if the file exists
						if(!file_exists($basePath.$path.$script.".php")) continue;
						
						// Check class exist
						include($basePath.$path.$script.".php");
						if(!class_exists($className)) continue;
						
						// Check method
						if(!is_callable(array($className, $method))) continue;
						
						$data[$s] = $router;
					}
					else
					{
						switch($s[0])
						{
							case "A":
								array_push($array, "A".$s);
								break;
							case "B":
								array_push($array, "A".$s);
								break;
							case "C":
								array_push($array, "A".$s);
								array_push($array, "B".$s);
								break;
							case "D":
								array_push($array, "A".$s);
								array_push($array, "B".$s);
								array_push($array, "C".$s);
								array_push($array, "D".$s);
								break;
						}
					}
				}
				
				if(sizeof($data) == 0)
				{
					return array(new RouterResult($basePath.$defaultPath, $defaultScript, str_replace("?", $defaultScript, $classNameFormat), $defaultMethod, array()));
				}
				
				ksort($data);
				return array_values($data);
			}
		}
	}
?>