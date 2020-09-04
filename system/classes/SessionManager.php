<?php

class SessionManager {
	
   
	var $life_time;

	protected $connection;

	public function __construct() {
		
		$this->connection = Application::GetApplication()->getConnection('accounts');
		
		// Read the maxlifetime setting from PHP
		$this->life_time = get_cfg_var("session.gc_maxlifetime");

		// Register this object as the session handler
		session_set_save_handler(
		array( &$this, "open" ),
		array( &$this, "close" ),
		array( &$this, "read" ),
		array( &$this, "write"),
		array( &$this, "destroy"),
		array( &$this, "gc" )
		);
	}

	function open() {
	
		return true;

	}

	function close() {
		return true;
	}

	function read( $id ) {
		
		// Set empty result
		$data = "";
		// Fetch session data from the selected database
		$time = time();
		$newid = $this->connection->prepareValue($id);
		
		$sql = "SELECT `session_data` FROM `acc_sessions` WHERE `session_serial` = '".$newid."' AND `expires` > '".$time."'";

		$result = $this->connection->query($sql);
		
		if($result->rowCount > 0) {
			$data = $result->rows[0]->session_data;
		}
		
		return $data;
	}

	function write( $id, $data ) {
		// Build query
		
		$time = time() + $this->life_time;
				
		$newid = $this->connection->prepareValue($id);
		
		
		
		$sql = "SELECT session_id FROM acc_sessions WHERE session_serial = '".$newid."'";
		$record = $this->connection->querySingleRow($sql);
	
		if($record){
			$sessiondata = array(
					'session_id'=> $record->session_id,
					'session_data' =>  $data,
					'expires' => $time
			);
			$this->connection->update('acc_sessions', $sessiondata);
		}
		else{
			$sessiondata = array(
					'session_serial' => $newid,
					'session_data' =>  $data,
					'expires' => $time
			);
			$this->connection->insert('acc_sessions', $sessiondata);
		}

		return true;

	}

	function destroy( $id ) {

		// Build query
		$newid = $this->connection->prepareValue($id);
		$sql = "DELETE FROM `acc_sessions` WHERE `session_serial` = '".$newid."'";

		$this->connection->query($sql);

		return true;

	}

	function gc() {

		// Garbage Collection
		// Build DELETE query.  Delete all records who have passed
		// the expiration time
		$sql = "DELETE FROM `acc_sessions` WHERE `expires` < UNIX_TIMESTAMP()";

		$this->connection->query($sql);
		// Always return TRUE
		return true;
		 
	}
}
