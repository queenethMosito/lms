<?php
class Classes_Model extends Model {
	public function __construct() {
		parent::__construct('classes');
	}

	public function allClasses(){
		$sql = "SELECT *
                FROM classes c
                WHERE c.status_id=1 order by c.class_name ASC";
		$results = $this->connection->query($sql)->rows;
		return $results;
	}
	public function getClass($class_id){
	    $sql = "SELECT *
                FROM classes c
                WHERE c.status_id=1 and class_id=".$class_id;
	    $results = $this->connection->querySingleRow($sql);
	    return $results;
	}
	public function updateClasses($classes){
	    if(isset($classes['class_id']) && $classes['class_id'] != null && trim($classes['class_id']) != ''){
	        $classes['class_start_time'] = date("H:i:s",strtotime($classes['class_start_date']));
	        $classes['class_end_time'] = date("H:i:s",strtotime($classes['class_end_date']));
	        $classes['class_start_date'] = date("Y-m-d",strtotime($classes['class_start_date']));
	        $classes['class_end_date'] = date("Y-m-d",strtotime($classes['class_end_date']));
	        $this->connection->update("classes", $classes);
	    }
	    else{
	        $classes['class_start_time'] = date("H:i:s",strtotime($classes['class_start_date']));
	        $classes['class_end_time'] = date("H:i:s",strtotime($classes['class_end_date']));
	        $classes['class_start_date'] = date("Y-m-d",strtotime($classes['class_start_date']));
	        $classes['class_end_date'] = date("Y-m-d",strtotime($classes['class_end_date']));
	        $classes['class_description']=str_replace("'","`",$classes['class_description']);
	        $this->connection->insert("classes", $classes);
	    }
	}
	public function deleteClasses($class_id)
	{
	    $sql = "UPDATE classes SET status_id = 0 WHERE class_id = ". $class_id;
	    return $this->connection->query($sql);
	}
	

}