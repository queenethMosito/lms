<?php
if(!defined('CLASS_MYSQL_PHP')) {
	define('CLASS_MYSQL_PHP', 1);

	class MySQLResult {
		public $rows;
		public $rowCount;
		public $rowsAffected;
		public $info;
		public $sql;
	}

	class MySQL {
		const CLOSED = 0;
		const OPEN = 1;

		const TYPE_OBJ = 0;
		const TYPE_OBJECT = 0;
		const TYPE_CLASS = 1;
		const TYPE_ASSOC = 2;
		const TYPE_ARRAY = 3;

		protected $resource;

		protected $server;
		protected $username;
		protected $password;
		protected $database;
		protected $lastSQL;

		protected $state;

		public function __construct($config) {
			if(!is_array($config)) {
				return $this->handleDebugError('Invalid parameter type', 'Parameter config must be of type array');
			}

			$this->server = $config['server'];
			$this->username = $config['username'];
			$this->password = $config['password'];
			$this->database = isset($config['database']) ? $config['database'] : null;

			$this->resource = null;
			$this->state = MySQL::CLOSED;

			return true;
		}

		public function __destruct() {
			$this->close();
		}

		public function open($database = null) {
			if($this->state == MySQL::OPEN) {
				$result = $this->close();
				if(!$result) {
					return false;
				}
			}

			$this->resource = mysqli_connect($this->server, $this->username, $this->password);

			if(!$this->resource) {
				return $this->handleDebugError('Error creating MySQL connection');
			}

			if($database) $this->database = $database;
			if($this->database) {
				$result = mysqli_select_db($this->resource, $this->database);
				if(!$result) {
					return $this->handleDebugError('Error selecting database ['.$this->database.']');
				}
			}

			$this->state = MySQL::OPEN;
			mysqli_set_charset( $this->resource, 'utf8');

			return true;
		}

		public function selectDatabase($database) {
			if($this->state == self::CLOSED) {
				if(!$this->open()) {
					return false;
				}
			}
			$this->database = $database;
			if($this->database) {
				$result =  mysqli_select_db($this->resource, $this->database);
				if(!$result) {
					return $this->handleDebugError('Error selecting database ['.$this->database.']');
				}
			}
			return true;
		}

		public function close() {
			if($this->state == MySQL::CLOSED) {
				return true;
			}
			if(mysqli_close($this->resource)) {
				$this->state = MySQL::CLOSED;
				$this->resource = null;
				return true;
			}
		}

		public function getPrimaryField($tableName) {
			if($this->state != MySQL::OPEN) {
				if(!$this->open()) {
					return false;
				}
			}

			$tableName2 = $this->_formatTableName($tableName);
			$sql = 'SHOW KEYS FROM `'.$tableName2.'`';
			$this->lastSQL = $sql;
			$result = mysqli_query($this->resource, $sql);

			if(!$result) {
				return $this->handleDebugError('Error selecting primary field from table ['.$tableName.']');
			}

			$row = mysqli_fetch_assoc($result);
			if(!$row || !isset($row['Column_name'])) {
				return $this->handleDebugError('Error selecting primary field from table ['.$tableName.']', 'Either the table does not exist or the table has no primary key');
			}

			return $row['Column_name'];
		}

		public function getFields($tableName) {
			if($this->state != MySQL::OPEN) {
				if(!$this->open()) return false;
			}

			$tableName2 = $this->_formatTableName($tableName);
			$sql = 'SHOW COLUMNS FROM `'.$tableName2.'`';
			$this->lastSQL = $sql;
			$result = mysqli_query($this->resource, $sql);

			if(!$result) 	{
				return $this->handleDebugError('Error getting fields from table ['.$tableName.']');
			}

			$fields = array();
			while($row = mysqli_fetch_object($result)) {
				$fields[$row->Field] = $row;
			}

			return $fields;
		}

		public function selectSingle($tableName, $id, $type = self::TYPE_OBJECT, $className = null) {
			$key = $this->getPrimaryField($tableName);
			if(!$key) {
				return false;
			}
			$id = $this->prepareValue($id);

			$tableName2 = $this->_formatTableName($tableName);
			$sql = 'SELECT * FROM `'.$tableName2.'` WHERE `'.$key.'` = '.$id;
			$this->lastSQL = $sql;
			$result = mysqli_query( $this->resource, $sql);

			if(!$result) return $this->handleDebugError('Error fetching record from ['.$tableName.'] with id ['.$id.']');

			if($type == self::TYPE_ARRAY) {
				$row = mysqli_fetch_row($result);
			}
			elseif($type == self::TYPE_ASSOC) {
				$row = mysqli_fetch_assoc($result);
			}
			elseif($type == self::TYPE_CLASS && $className) {
				$row = mysqli_fetch_object($result, $className);
			}
			else {
				$row = mysqli_fetch_object($result);
			}

			if(!$row) {
				return null;
			}
			return $row;
		}

		public function select($tableName, $type = self::TYPE_OBJECT, $className = null) {
			$key = $this->getPrimaryField($tableName);

			$sql = 'SELECT * FROM `'.$tableName.'`';

			$result = mysqli_query($this->resource, $sql);
			$this->lastSQL = $sql;

			if(!$result) {
				return $this->handleDebugError('Error fetching records from ['.$tableName.']');
			}

			$list = array();

			if($type == self::TYPE_ARRAY) {
				while($row = mysqli_fetch_row($result)) {
					$list[] = $row;
				}
			}
			elseif($type == self::TYPE_ASSOC) {
				while($row = mysqli_fetch_assoc($result)) {
					$key ? $list[$row[$key]] = $row : $list[] = $row;
				}
			}
			elseif($type == self::TYPE_CLASS && $className) {
				while($row = mysqli_fetch_object($result, $className)) {
					$list[] = $row;
				}
			}
			else {
				while($row = mysqli_fetch_object($result)) {
					$key ? $list[$row->$key] = $row : $list[] = $row;
				}
			}

			return $list;
		}

		/**
		 * @param string $sql
		 * @return MySQLResult
		 */
		public function query($sql, $type = self::TYPE_OBJECT, $className = null) {
			if($this->state != MySQL::OPEN) {
				if(!$this->open()) return false;
			}

			$result = mysqli_query( $this->resource, $sql);
			$this->lastSQL = $sql;

			if(!$result) {
				return $this->handleDebugError('Error executing query');
			}

			$object = new MySQLResult();
			$object->info = mysqli_info($this->resource);
			$object->sql = $sql;
			$object->rowCount = @mysqli_num_rows($result);
			$object->rowsAffected = mysqli_affected_rows($this->resource);
			$object->rows = array();
			if($object->rowCount > 0) {
				if($type == self::TYPE_ARRAY) {
					while($row = mysqli_fetch_row($result)) {
						$object->rows[] = $row;
					}
				}
				elseif($type == self::TYPE_ASSOC) {
					while($row = mysqli_fetch_assoc($result)) {
						$object->rows[] = $row;
					}
				}
				elseif($type == self::TYPE_CLASS && $className) {
					while($row = mysqli_fetch_object($result, $className)) {
						$object->rows[] = $row;
					}
				}
				else {
					while($row = mysqli_fetch_object($result)) {
						$object->rows[] = $row;
					}
				}
			}
			@mysqli_free_result($result);

			return $object;
		}

		public function querySingleRow($sql, $type = self::TYPE_OBJECT, $className = null) {
			$result = $this->query($sql, $type, $className);
			return $result->rowCount > 0 ? $result->rows[0] : null;
		}

		public function queryScaler($sql) {
			$row = $this->querySingleRow($sql, self::TYPE_ARRAY);
			return $row ? $row[0] : null;
		}

		public function insert($tableName, $data) {
			$fields = $this->getFields($tableName);
			$primaryKey = $this->getPrimaryField($tableName);
			unset($fields[$primaryKey]);

			$data['date_created'] = gmdate('Y-m-d H:i:s');
			$data['date_modified'] = gmdate('Y-m-d H:i:s');

			$list = array();
			$values = array();

			foreach($data as $key => $value) {
				if(isset($fields[$key])) {
					$list[] = '`'.$key.'`';
					$values[] = '"'.$this->prepareValue($value).'"';
				}
			}

			$tableName2 = $this->_formatTableName($tableName);
			$sql = 'INSERT INTO `'.$tableName2.'` ('.implode(', ', $list).') VALUES ('.implode(', ', $values).')';
			$this->lastSQL = $sql;

			$result = mysqli_query($this->resource, $sql);

			if(!$result) {
				return $this->handleDebugError('Error inserting record');
			}

			$insertID =  mysqli_insert_id($this->resource);

			return $insertID;
		}

		public function update($tableName, $data) {
			$fields = $this->getFields($tableName);
			$primaryKey = $this->getPrimaryField($tableName);

			if(!$primaryKey) {
				return $this->handleDebugError('Error updating record', 'Cannot update a record without a primary key');
			}

			unset($fields[$primaryKey]);
			$data['date_modified'] = gmdate('Y-m-d H:i:s');

			if(!isset($data[$primaryKey])) {
				return $this->handleDebugError('Error updating record', 'The primary key must be part of the data array');
			}

			$list = array();

			foreach($data as $key => $value) {
				if(isset($fields[$key])) {
					$list[] = '`'.$key.'` = "'.$this->prepareValue($value).'"';
				}
			}

			$id = $this->prepareValue($data[$primaryKey]);
			$tableName2 = $this->_formatTableName($tableName);
			$sql = 'UPDATE `'.$tableName2.'` SET '.implode(', ', $list).' WHERE `'.$primaryKey.'` = '.$id;
			$this->lastSQL = $sql;

			$result = mysqli_query($this->resource, $sql);

			if(!$result) {
				return $this->handleDebugError('Error updating record');
			}

			return true;
		}

		public function delete($tableName, $id) {
			if(is_array($id)) {
				$count = 0;
				foreach($id as $value) {
					$count += $this->delete($tableName, $value);
				}
				return $count;
			}

			$id = $this->prepareValue($id);
			$key = $this->getPrimaryField($tableName);

			if(!$key) {
				return $this->handleDebugError('Error deleting record from ['.$tableName.'] with id ['.$id.']', 'Could not select the primary key from the table');
			}

			$tableName2 = $this->_formatTableName($tableName);
			$sql = 'DELETE FROM `'.$tableName2.'` WHERE `'.$key.'` = '.$id;
			$this->lastSQL = $sql;
			$result = mysqli_query($this->resource, $sql);

			if(!$result) {
				return $this->handleDebugError('Error delting record with id ['.$id.']');
			}

			return mysqli_affected_rows($this->resource);
		}

		public function lastSQLStatement() {
			return $this->lastSQL;
		}

		protected function handleDebugError($title, $message = null) {
			if(!$message) {
				$message = 'Error: ('.mysqli_errno($this->resource).'): '.mysqli_error($this->resource);
			}
			throw new Exception($title.'. '.$message);
		}

		public function prepareValue($value) {
			if($this->state != MySQL::OPEN) {
				if(!$this->open()) {
					return false;
				}
			}

	        if(get_magic_quotes_gpc()) {
	        	$value = stripslashes($value);
	        }

			$value = mysqli_real_escape_string($this->resource, $value);

			return $value;
		}

		public function lockTables($tables) {
			if($this->state != MySQL::OPEN) {
				if(!$this->open()) return false;
			}
			$data = array();
			if(is_array($tables)) {
				$data = $tables;
			}
			else {
				$data[] = $tables;
			}

			$prepped = array();
			foreach($data as $table) {
				$prepped[] = '`'.$table.'` WRITE';
			}
			$sql = 'LOCK TABLES '.implode(', ', $prepped);
			$this->lastSQL = $sql;

			$result = mysqli_query($this->resource, $sql);

			if(!$result) {
				return $this->handleDebugError('Error locking tables', $sql);
			}

			return true;
		}

		public function unlockTables() {
			if($this->state != MySQL::OPEN) {
				if(!$this->open()) {
					return false;
				}
			}

			$result = mysqli_query($this->resource, 'UNLOCK TABLES');
			$this->lastSQL =  'UNLOCK TABLES';

			if(!$result) {
				return $this->handleDebugError('Error unlocking tables');
			}

			return true;
		}

		protected function _formatTableName($tableName) {
			return str_replace('.', '`.`', $tableName);
		}
	}
}
