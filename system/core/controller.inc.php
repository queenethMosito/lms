<?php
abstract class Controller extends Base
{
	/**
	 * @var MySQL
	 * @deprecated
	 */
	protected $connection; // TODO: Take this out once all controllers are making use of models

	/**
	 * @var Input_Helper
	 */
	protected $hInput;

	/**
	 * @var Output_Helper
	 */
	protected $hOutput;

	/**
	 * @var User_Model
	 */
	protected $mUser;

	protected $js_list = array();
	protected $css_list = array();
	protected $meta = array('title' => '', 'description' => '', 'keywords' => '');
	protected $theme = null;

	public function indexAction() {
		throw new Exception('Index Action not defined in this controller', 0);
	}

	public function getTheme() {
		return $this->theme;
	}

	public function __construct() {
		parent::__construct();

		if($_SERVER['SERVER_NAME'] == 'developersj.co.za') {
			$this->forceSSL();
		}

		// TODO: Take this out once all controllers are making use of models
		$this->connection = Application::GetApplication()->getConnection();

		// Autoload these helpers
		$this->loadHelper(array('Input', 'Output'));

		// Autoload these models
		$this->loadModel(array('User'),'accounts');

		// Meta information
		$this->meta['title'] = 'Welcome';
	}

	public function __destruct() {
		parent::__destruct();
	}

	public function _css_list() {
		return $this->css_list;
	}

	public function _js_list() {
		return $this->js_list;
	}

	public function _meta_data() {
		return $this->meta;
	}

	protected function forceSSL() {
		global $config;
		if(!$config['ssl']) {
			return;
		}
		if($_SERVER['SERVER_NAME'] != 'developersj.co.za') {
			return;
		}

		if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") {
		    $url = "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		    header("Location: {$url}");
		    die();
		}
	}

	/**
	 * Loads a helper from the system with the given name. The helper parameter
	 * can either be a string for single helper load, or an array to load multiple
	 * helpers.
	 *
	 * @param array|string $helper The name/s of the helper/s to load
	 * @return object An instance of the helper. If multiple helpers are loaded then return null
	 */
	protected function loadHelper($helper) {
		if(is_array($helper)) {
			foreach($helper as $helperName) {
				$this->LoadHelper($helperName);
			}
			return null;
		}

		if(substr($helper, -1) == '/') $helper = substr($helper, 0, -1);
		$class = $helper;
		if(strpos($class, '/') !== false) $class = substr(strpos($class, '/') + 1);
		$class = str_replace('-', '_', $class);
		$path = strtolower($helper).'.inc.php';

		$path = SYSTEM_PATH.'helpers/'.$path;
		if(!file_exists($path)) return null;
		require_once $path;

		$className = "{$class}_Helper";
		if(!class_exists($className)) {
			return false;
		}

		$obj = new $className;

		$varName = 'h'.$class;
		$this->{$varName} = $obj;

		return $obj;
	}
}
