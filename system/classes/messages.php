<?php
if(!defined("__MESSAGES_PHP__"))
{
	define("__MESSAGES_PHP__", 1);
	
	class Messages extends Base 
	{
		/**
		 * @var MySQL
		 */
		protected $connection;
		protected $user_id;
		
		public function __construct($connection)
		{
			parent::__construct();
			$this->connection = $connection;
	    	$this->user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
		}
		
		public function sendMessage($subject, $to, $message)
		{
			$date = getSystemDate();
			
			// Create a new message thread
			$data = array();
			$data['subject'] = $subject;
			$data['from'] = $this->user_id;
			$data['to'] = $to;
			$data['read_from'] = 1;
			$data['read_to'] = 0;
			$data['locked'] = 1;
			$data['reply'] = 0;
			$data['last_date'] = $date;
			$data['deleted_from'] = 0;
			$data['deleted_to'] = 0;
			
			$thread_id = $this->connection->insert("message_threads", $data);
			
			// Create the message
			$data = array();
			$data['thread_id'] = $thread_id;
			$data['date'] = $date;
			$data['user_id'] = $this->user_id;
			$data['message'] = $message;
			
			$message_id = $this->connection->insert("messages", $data);
			
			return array("thread_id" => $thread_id, "message_id" => $message_id);
		}
		
		public function replyToMessage($thread_id, $message)
		{
			$date = getSystemDate();
			$thread = $this->connection->selectSingle("message_threads", $thread_id);
			if(!$thread) return false;
			
			// Update the message thread
			$data = array();
			$data['id'] = $thread->id;
			if($thread->from == $this->user_id)
			{
				$data['read_to'] = 0;
				$data['deleted_to'] = 0;
			}
			else
			{
				$data['read_from'] = 0;
				$data['deleted_from'] = 0;
				$data['reply'] = 1;				
			}
			$data['last_date'] = $date;
			$this->connection->update("message_threads", $data);
			
			// Create the message
			$data = array();
			$data['thread_id'] = $thread_id;
			$data['date'] = $date;
			$data['user_id'] = $this->user_id;
			$data['message'] = $message;
			
			$message_id = $this->connection->insert("messages", $data);
			
			return $message_id;
		}
		
		public function getUnreadCount()
		{
			if($this->user_id == 0) return 0;
	    	$sql = "
	    		SELECT COUNT(*) AS `count` FROM `message_threads`
	    		WHERE (`from` = {$this->user_id} AND `read_from` = 0 AND `deleted_from` = 0) 
	    		OR (`to` = {$this->user_id} AND `read_to` = 0 AND `deleted_to` = 0) 
	    	";
	    	$result = Application::GetApplication()->getConnection()->query($sql);
	    	return $result->rows[0]->count;
		}
		
		public function discard_message($message_id)
		{
			// Get the current message
			$message = $this->connection->selectSingle("messages", $message_id);
			if(!$message) return;
			
			// Get the thread id
			$thread = $this->connection->selectSingle("message_threads", $message->thread_id);
			if(!$thread) return;
			
			// If the last date is greater than the current message's date dont delete
			if($thread->last_date > $message->date) return;
			
			// Delete the message
			if($thread->to == $this->user_id) $sql_set = "`deleted_to` = 1";
			else $sql_set = "`deleted_from` = 1";
			
			$sql = "UPDATE `message_threads` SET {$sql_set} WHERE `id` = {$thread->id}";
			$this->connection->query($sql);
		}
	}
}
?>