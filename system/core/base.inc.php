<?php
abstract class Base {
	public function __construct() {
		// Do nothing
	}
	public function __destruct() {
		// Do nothing
	}
	protected function handleDebugError($title, $message) {
		print ("
			<div>
			<h3>{$title}</h3>
			<p>{$message}</p>
			</div>
		") ;
		return false;
	}

	/**
	 * Loads a model from the system with the given name.
	 * The model parameter
	 * can either be a string for single model load, or an array to load multiple
	 * models. The connectionName parameter tells which connection the model(s)
	 * should use
	 *
	 * @param array|string $model
	 *        	The name/s of the model/s to load
	 * @param string $connectionName
	 *        	The connection that the model/s should use
	 * @return object An instance of the model. If multiple models are loaded then return null
	 */
	protected function loadModel($model, $connectionName = 'default') {
		// Check if we are loading multiple models
		if (is_array ( $model )) {
			foreach ( $model as $modelName ) {
				$this->loadModel ( $modelName, $connectionName );
			}
			return null;
		}

		if (substr ( $model, - 1 ) == '/')
			$model = substr ( $model, 0, - 1 );
		$class = $model;
		if (strpos ( $class, '/' ) !== false)
			$class = substr ( $class, strpos ( $class, '/' ) + 1 );
		$class = str_replace ( '-', '_', $class );
		$path = strtolower ( $model ) . '.php';

		$relativePath = $path;
		$path = MODELS_PATH . $path;
		if (! file_exists ( $path )) {
			// Try in another application
			if (($pos = strpos ( $relativePath, '/' )) !== false) {
				$app = substr ( $relativePath, 0, $pos );
				$newPath = substr ( $relativePath, $pos + 1 );
				$path = SYSTEM_PATH . "applications/{$app}/models/{$newPath}";
			}

			// If still not, try in the global models directory
			if (! file_exists ( $path )) {
				$path = SYSTEM_PATH . "generic/models/" . $relativePath;
			}

			if (! file_exists ( $path )) {
				// Create a generic model here
				$obj = new GenericModel ( strtolower ( $class ) );

				$varName = "m" . $class;
				$this->{$varName} = $obj;

				return null;
			}
		}
		include_once ($path);

		// New - try name_Model first else the old way of nameModel
		$className = $class . '_Model';

		if (! class_exists ( $className ))
			return null;
		$obj = new $className ();
		$obj->setConnection ( Application::GetApplication ()->getConnection ( $connectionName ) );

		$varName = "m" . $class;
		$this->{$varName} = $obj;

		return $obj;
	}

	/**
	 * Loads a view from the views folder
	 * TODO: Make use of a template engine here
	 *
	 * @param string $path
	 *        	The view to load
	 * @param mixed $data
	 *        	An array of parameters to pass through to the view
	 * @param bool $return_as_content
	 *        	If true, return the content else send the content to the buffer
	 */
	public function loadView($path, $data = array(), $returnAsContent = true) {
		ob_start ();

		// If the path start with a / then use the absolute instead
		if (substr ( $path, 0, 1 ) == '/') {
			$fullPath = FRAMEWORK_PATH . substr ( $path, 1 ) . '.php';
		} else {
			$fullPath = VIEWS_PATH . $path . '.php';
		}

		if (! file_exists ( $fullPath )) {
			print "<p>Unable to load the view at <b>{$path}</b></p>";
		} else {
			if (is_array ( $data )) {
				foreach ( $data as $key__ => $value__ )
					$$key__ = $value__;
			}
			include ($fullPath);
		}

		$content = ob_get_clean ();

		if (! $returnAsContent) {
			echo $content;
			return true;
		} else {
			return $content;
		}
	}
}
