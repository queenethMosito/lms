<?php	
/**
 * Core abstract class for all models in the system to extend from. Provides simple database
 * methods such as Select, Insert, Delete and Update. Makes use of the MySQL class
 * @package core
 */
abstract class Model extends Base {
	/**
	 * @var MySQL
	 */
	protected $connection;
	
	/**
	 * @var string
	 */
	protected $tableName = "";
	
	/**
	 * Constructs the model object. Will fethc the default connection object
	 * from the application singleton
	 * @param string $tableName
	 */
	public function __construct($tableName = '') {
		parent::__construct();
		$this->connection = Application::GetApplication()->getConnection();
		$this->tableName = $tableName;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Base::__destruct()
	 */
	public function __destruct() {
		parent::__destruct();
	}
	
	/**
	 * Pass through a new connection for the model to use
	 * @param MySQL $connection The new connection to be used by the model
	 */
	public function setConnection($connection) {
		$this->connection = $connection;
	}
	
	/**
	 * Selects a single element from the table that this model is representing
	 * @param int $id
	 * @return Object
	 */
	public function selectSingle($id) {
		$id = (int) $id;
		return $this->connection->selectSingle($this->tableName, $id);
	}
	
	/**
	 * Method to select all records from the table. Optional order variable
	 * @param mixed $order
	 */
	public function selectAll($order = null) {
		if(!$order) {
			return $this->connection->select($this->tableName);	
		}
		if(!is_array($order)) {
			$order = array($order => "ASC");	
		}
		$sql = "SELECT * FROM `{$this->tableName}`";
		$orderList = array();
		foreach($order as $key => $value) {
			if(is_int($key)) $orderList[] = "{$value} ASC";
			else $orderList[] = "{$key} {$value}";
		}
		$sql .= ' ORDER BY '.implode(', ', $orderList);
		return $this->connection->query($sql)->rows;
	}
	
	/**
	 * Inserts a record into the table
	 * Automatically adds the date created and modified fields
	 * @param array $data
	 * @return int The insert id
	 */
	public function insert($data) {
		$data['date_created'] = $data['date_modified'] = gmdate('Y-m-d H:i:s');
		return $this->connection->insert($this->tableName, $data);
	}
	
	/**
	 * Updates a record in the table
	 * Automatically adds the date modified fields
	 * @param array $data Must contain the PRIMARY KEY as one of the fields
	 */
	public function update($data) {
		$data['date_modified'] = gmdate('Y-m-d H:i:s');
		return $this->connection->update($this->tableName, $data);
	}
	
	/**
	 * Deletes the record from the current table
	 * @param int $id The id of the record to delete
	 */
	public function delete($id) {
		$this->connection->delete($this->tableName, $id);
	}
	
	/**
	 * Locks the current table
	 */
	public function lock() {
		$this->connection->lockTables($this->tableName);
	}
	
	/**
	 * Unlocks the current table
	 */
	public function unlock() {
		$this->connection->unlockTables($this->tableName);
	}
}

/**
 * Generic model that will be instantiated if the specified model cannot be found.
 * It is constructed with the name of the table that the model refers to.
 * @package core
 * @see Model
 */
final class GenericModel extends Model {
	/**
	 * Simple constructs a base model that provides only simple functionlity on the given table name
	 * @param string $tableName
	 */
	public function __construct($tableName) {
		parent::__construct($tableName);
	}
}