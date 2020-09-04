<?php
if(!defined("_CLASS_STORAGE_MANAGER_"))
{
	define("_CLASS_STORAGE_MANAGER_", 1);
	
	class Storage_Manager
	{
		protected $app;
		protected $base_path;
		protected $currentFolder = 0;
		
		/**
		 * @var MySQL
		 */
		protected $connection;
		
		public function __construct($application = null)
		{
			global $config;
			$this->app = $application ? $application : $config['application'];
			$this->base_path = STORAGE_PATH.$this->app."/";
			$this->connection = Application::GetApplication()->getConnection("documents");
		}
		
		protected function checkName($name)
		{
			$name = trim($name);
			$name = str_replace(array("/", "\\", "=", "*", "&", "%", "`". "?"), "-", $name);
			if(!$name) $name = "No name";
			return $name;
		}
		
		public function validate($data, $user_id = "", $auto_create = true)
		{
			$ids = array();
			$folder_id = $this->currentFolder;
			
			foreach($data as $folder)
			{
				if(isset($folder['table']))
				{
					$sql = "
						SELECT * FROM `folders` 
						WHERE `parent_id` = {$folder_id} AND `application` = '{$this->app}'
						AND `table_name` = '{$folder['table']}' AND `table_id` = {$folder['id']}
					";
					$result = $this->connection->query($sql);
					if(!$result->rowCount) 
					{
						if(!$auto_create) return $ids;
						$id = $this->createFolder($user_id, $folder['name'], $folder_id);
						$folder_id = $id;
						$ids[] = $id;
						$update = array();
						$update['id'] = $id;
						$update['table_name'] = $folder['table'];
						$update['table_id'] = $folder['id'];
						$this->connection->update("folders", $update);
					}
					else 
					{
						$ids[] = $result->rows[0]->id;
						$folder_id = $result->rows[0]->id;
					}
				}
				elseif(isset($folder['name']))
				{
					$sql = "
						SELECT * FROM `folders` 
						WHERE `parent_id` = {$folder_id} AND `application` = '{$this->app}'
						AND `name` = '{$folder['name']}'
					";
					$result = $this->connection->query($sql);
					if(!$result->rowCount) 
					{
						if(!$auto_create) return $ids;
						$id = $this->createFolder($user_id, $folder['name'], $folder_id);
						$folder_id = $id;
						$ids[] = $id;
					}
					else 
					{
						$ids[] = $result->rows[0]->id;
						$folder_id = $result->rows[0]->id;
					}
				}
				else return $ids;
			}
			return $ids;
		}
		
		public function getFolder($folder_id)
		{
			return $this->connection->selectSingle("folders", $folder_id);
		}
		
		public function getFolders()
		{
			$sql = "SELECT * FROM `folders` WHERE `parent_id` = {$this->currentFolder} AND `application` = '{$this->app}' 
				AND `enabled` = 1 ORDER BY `name`";
			$result = $this->connection->query($sql);
			return $result->rows;	
		}
		
		public function getFiles()
		{
			$sql = "SELECT * FROM `documents` WHERE `folder_id` = {$this->currentFolder} AND `enabled` = 1 ORDER BY `name`";
			$result = $this->connection->query($sql);
			$files = array();
			foreach($result->rows as $row)
			{
				$sql = "SELECT * FROM `document_revisions` WHERE `document_id` = {$row->id} ORDER BY `revision` DESC LIMIT 1";
				$row->latest = $this->connection->query($sql)->rows[0];
				$files[] = $row;
			}
			return $files;
		}
		
		public function createFolder($user_id, $name, $parent_id = -1)
		{
			$name = $this->checkName($name);
			$date = getSystemDateGMT();
			$insert = array();
			$insert['date_created'] = $insert['date_modified'] = $date;
			$insert['user_id'] = $user_id;
			$insert['application'] = $this->app;
			$insert['parent_id'] = $parent_id == -1 ? $this->currentFolder : $parent_id;
			$insert['name'] = $name;
			
			return $this->connection->insert("folders", $insert);
		}
		
		public function replaceFile($id, $file, $user_id)
		{
			$date = getSystemDateGMT();
			$document = $this->connection->selectSingle("documents", $id);
			
			// Update the timestamp
			$this->connection->update("documents", array("id" => $id, "date_modified" => $date));
			
			// Caluclate revision
			$sql = "SELECT `revision` FROM `document_revisions` WHERE `document_id` = {$id} ORDER BY `revision` DESC LIMIT 1";
			$result = $this->connection->query($sql)->rows[0]->revision;
			
			// Create the new revision
			$rev = array();
			$rev['document_id'] = $id;
			$rev['date_created'] = $date;
			$rev['user_id'] = $user_id;
			$rev['mime'] = $file['type'];
			$rev['filename'] = $file['name'];
			$rev['extension'] = $ext = ($pos = strrpos($file['name'], ".")) !== false ? strtolower(substr($file['name'], $pos + 1)) : "";
			$rev['revision'] = $result + 1;
			$rev['size'] = $file['size'];
			$rev['hash'] = $hash = $this->generateHash();
			
			$rev_id = $this->connection->insert("document_revisions", $rev);
			
			// Move the file
			$target = STORAGE_PATH."documents/";
			for($i = 0; $i < 3; $i++) $target .= substr($hash, $i * 3, 3)."/";
			mkdir($target, 0777, true);
			$target .= substr($hash, 9, 3);
			$target .= $ext ? ".".$ext : "";
			move_uploaded_file($file['tmp_name'], $target);
			@unlink($file['tmp_name']); // Just in case
		}
		
		public function uploadFile($file, $name, $user_id, $table = array())
		{
			$date = getSystemDateGMT();
			
			// Check name
			$name = trim($name);
			if(!$name) 
			{
				$name = $file['name'];
				if(($pos = strrpos($name, ".")) !== false) $name = substr($name, 0, $pos);
			}
			$name = $this->checkName($name);
			
			// Create the document
			$doc = array();
			$doc['date_created'] = $doc['date_modified'] = $date;
			$doc['name'] = $name;
			$doc['user_id'] = $user_id;
			$doc['folder_id'] = $this->currentFolder;
			
			if($table)
			{
				$doc['table_name'] = $table['name'];
				$doc['table_id'] = $table['id'];
			}
			
			$doc_id = $this->connection->insert("documents", $doc);
			
			// Create the revision
			$rev = array();
			$rev['document_id'] = $doc_id;
			$rev['date_created'] = $date;
			$rev['user_id'] = $user_id;
			$rev['mime'] = $file['type'];
			$rev['filename'] = $file['name'];
			$rev['extension'] = $ext = ($pos = strrpos($file['name'], ".")) !== false ? strtolower(substr($file['name'], $pos + 1)) : "";
			$rev['revision'] = 1;
			$rev['size'] = $file['size'];
			$rev['hash'] = $hash = $this->generateHash();
			
			$rev_id = $this->connection->insert("document_revisions", $rev);
			
			// Move the file
			$target = STORAGE_PATH."documents/";
			for($i = 0; $i < 3; $i++) $target .= substr($hash, $i * 3, 3)."/";
			mkdir($target, 0777, true);
			$target .= substr($hash, 9, 3);
			$target .= $ext ? ".".$ext : "";
			copy($file['tmp_name'], $target);
			@unlink($file['tmp_name']); // Just in case
			
			return $doc_id;
		}
		
		protected function generateHash()
		{
			$letters = "abcdef0123456789";
			$hash = "";
			while(true)
			{
				for($i = 0; $i < 12; $i++) $hash .= $letters[rand(0, 15)];
				$sql = "SELECT COUNT(*) AS `count` FROM `document_revisions` WHERE `hash` = '{$hash}'";
				$result = $this->connection->query($sql);
				if($result->rows[0]->count == 0) return $hash;
			}
		}
		
		public function navigate($folder, $mode = self::MODE_FOLDER_ID)
		{
			switch($mode)
			{
				case self::MODE_FOLDER_ID:
					// TODO: Check
					$this->currentFolder = $folder;
					return true;
				case self::MODE_FOLDER_UP:
					if($this->currentFolder == 0) return true;
					$folder = $this->connection->selectSingle("folders", $this->currentFolder);
					$this->currentFolder = $folder->parent_id;
					return true;	
				case self::MODE_FOLDER_SUB_NAME:
					$folders = $this->getFolders();
					foreach($folders as $f)
					{
						if($f->name == $folder)
						{
							$this->currentFolder = $f->id;
							return true;
						}
					}
					return false;			
			}
			return false;
		}
		
		public function parentFolder()
		{
			$folder = $this->connection->selectSingle("folders", $this->currentFolder);
			if(!$folder || $folder->parent_id == 0) return null;
			$parent = $this->connection->selectSingle("folders", $folder->parent);
			return $parent;
		}
		
		public function currentFolder()
		{
			return $this->connection->selectSingle("folders", $this->currentFolder);
		}
		
		public function getFile($file_id)
		{
			$file = $this->connection->selectSingle("documents", $file_id);
			$sql = "SELECT * FROM `document_revisions` WHERE `document_id` = {$file_id} ORDER BY `revision` DESC";
			$file->revisions = $this->connection->query($sql)->rows;
			return $file;
		}
		
		/**
		 * Performs a hard delete of the file with all revisions
		 * CAUTION SHOULD BE EXERCISED WHEN CALLING THIS FUNCTION AS
		 * THERE NO UNDO FOR THIS. 
		 * 
		 * @see Storage_Manager::softDeleteFile for deleting with recovery
		 * @param int $file_id The id of the document to be removed
		 * @return bool The result of the operation
		 */
		public function deleteFile($file_id)
		{
			$file = $this->connection->selectSingle("documents", $file_id);
			if(!$file) return false;
			
			// Delete the revisions
			$sql = "SELECT * FROM `document_revisions` WHERE document_id = {$file->id}";
			$result = $this->connection->query($sql);
			foreach($result->rows as $rev)
			{
				$hash = $rev->hash;
				$target_path = STORAGE_PATH."documents/";
				for($i = 0; $i < 3; $i++) $target_path .= substr($hash, $i * 3, 3)."/";
				$target = $target_path.substr($hash, 9, 3);
				$target .= $rev->extension ? ".".$rev->extension : "";
				
				unlink($target);
				
				// Also unlink any thumbs
				$handle = opendir($target_path);
				$look = substr($hash, 9, 3)."-";
				while($doc = readdir($handle))
				{
					//print($doc."\n");
					if(substr($doc, 0, 4) == $look) unlink($target_path.$doc);
				}
				closedir($handle);
			}
			
			$this->connection->query("DELETE FROM document_revisions WHERE document_id = {$file->id}");
			
			// Delete the document
			$this->connection->delete("documents", $file->id);
			
			return true;
		}
		
		public function file_path($file_id)
		{
			$file = $this->connection->selectSingle("documents", $file_id);
			if(!$file) return false;
			
			$sql = "SELECT * FROM `document_revisions` WHERE `document_id` = {$file_id} ORDER BY `revision` DESC LIMIT 1";
			$rev = $this->connection->query($sql)->rows[0];
			if(!$rev) return;
			
			$hash = $rev->hash;
			$target = STORAGE_PATH."documents/";
			for($i = 0; $i < 3; $i++) $target .= substr($hash, $i * 3, 3)."/";
			$target .= substr($hash, 9, 3);
			$target .= $rev->extension ? ".".$rev->extension : "";
			
			return $target;
		}
		
		public function downloadFile($file_id)
		{
			$file = $this->connection->selectSingle("documents", $file_id);
			if(!$file) return false;
			
			$sql = "SELECT * FROM `document_revisions` WHERE `document_id` = {$file_id} ORDER BY `revision` DESC LIMIT 1";
			$rev = $this->connection->query($sql)->rows[0];
			if(!$rev) return;
			
			$hash = $rev->hash;
			$target = STORAGE_PATH."documents/";
			for($i = 0; $i < 3; $i++) $target .= substr($hash, $i * 3, 3)."/";
			$target .= substr($hash, 9, 3);
			$target .= $rev->extension ? ".".$rev->extension : "";
			
			header('Content-Type: '.$rev->mime);
			header("Content-Disposition: attachment;filename=\"{$file->name}.{$rev->extension}\"");
			header('Cache-Control: max-age=0');
			readfile($target);
			die();
		}
		
		public function hashToFilename($hash, $extension)
		{
			$target = STORAGE_PATH."documents/";
			for($i = 0; $i < 3; $i++) $target .= substr($hash, $i * 3, 3)."/";
			$target .= substr($hash, 9, 3);
			$target .= $extension ? ".".$extension : "";
			
			return $target;
		}
		
		public function downloadThumb($file_id, $width, $height = -1)
		{
			include(CLASS_PATH."util-images.php");
			$file = $this->connection->selectSingle("documents", $file_id);
			if(!$file) return false;
			
			$sql = "SELECT * FROM `document_revisions` WHERE `document_id` = {$file_id} ORDER BY `revision` DESC LIMIT 1";
			$rev = $this->connection->query($sql)->rows[0];
			if(!$rev) return;
			
			$mime = strtolower($rev->mime);
			if(!substr($mime, 0, 6) == "image/") throw new Exception("Not an image");
			$mode = 0;
			switch($mime)
			{
				case "image/jpg":
				case "image/jpeg":
				case "image/pjpeg":
					$mode = Util_Images::TYPE_JPG;
					break;
				case "image/png":
					$mode = Util_Images::TYPE_PNG;
					break;
				case "image/gif":
					$mode = Util_Images::TYPE_GIF;
					break;
			}
			if(!$mode) 
			{
				$this->downloadFile($file_id);
				return;
			}
			
			$extra = "-thumb-".($width < 0 ? "x" : $width)."-".($height < 0 ? "y" : $height);
			
			$hash = $rev->hash;
			$target = STORAGE_PATH."documents/";
			for($i = 0; $i < 3; $i++) $target .= substr($hash, $i * 3, 3)."/";
			$target .= substr($hash, 9, 3);
			$target_thumb = $target.$extra.($rev->extension ? ".".$rev->extension : ""); 
			$target .= $rev->extension ? ".".$rev->extension : "";
			
			if(!file_exists($target_thumb))
			{
				$class = new Util_Images();
				$class->loadImage($target, Util_Images::MODE_FILE);
				$class->resizeImage($width, $height);
				if($mode == Util_Images::TYPE_PNG)
					$class->saveImage($target_thumb, $mode, 8);
				else
					$class->saveImage($target_thumb, $mode);
			}
			
			header('Content-Type: '.$rev->mime);
			//header("Content-Disposition: attachment;filename=\"{$file->name}\"");
			header('Cache-Control: max-age=0');
			readfile($target_thumb);
			die();
		}
		
		public function getBreadcrumbs()
		{
			$crumbs = array();
			$current = $this->currentFolder();
			$crumbs[$this->currentFolder] = $current ? $current->name : "ROOT";
			while($current)
			{
				$current = $this->connection->selectSingle("folders", $current->parent_id);  
				if($current)
					$crumbs[$current->id] = $current->name;
				else
					$crumbs[0] = "ROOT";
			}
			return array_reverse($crumbs, true);
		}
		
		public function renameFolder($user_id, $folder_id, $new_name)
		{
			$update = array();
			$update['id'] = $folder_id;
			$update['name'] = $new_name;
			$update['date_modified'] = getSystemDateGMT();
			$this->connection->update("folders", $update);
		}
		
		public function renameFile($user_id, $file_id, $new_name)
		{
			$update = array();
			$update['id'] = $file_id;
			$update['name'] = $new_name;
			$update['date_modified'] = getSystemDateGMT();
			$this->connection->update("documents", $update);
		}
		
		public function softDeleteFile($user_id, $file_id)
		{
			$this->connection->update("documents", array("id" => $file_id, "enabled" => 0));
		}
		
		public function softDeleteFolder($user_id, $folder_id)
		{
			// Delete all sub folders
			$sql = "SELECT `id` FROM `folders` WHERE `parent_id` = {$folder_id}";
			$result = $this->connection->query($sql);
			foreach($result->rows as $row) $this->softDeleteFolder($user_id, $row->id);
			$sql = "UPDATE `documents` SET `enabled` = 0 WHERE `folder_id` = {$folder_id}";
			$this->connection->query($sql);
			$this->connection->update("folders", array("id" => $folder_id, "enabled" => 0));
		}
		
		public function findFilesForTable($table_name, $table_id = -1)
		{
			$where = $table_id == -1 ? "" : " AND `documents`.`table_id` = {$table_id} ";
			$sql = "
				SELECT `documents`.* 
				FROM `documents`
				LEFT JOIN `folders` ON `documents`.`folder_id` = `folders`.`id` 
				WHERE `documents`.`table_name` = '{$table_name}' AND `application` = '{$this->app}' {$where}
			";
			return $this->connection->query($sql)->rows;
		}
		
		public function findFoldersForTable($table_name, $table_id = -1)
		{
			$where = $table_id == -1 ? "" : " AND `table_id` = {$table_id} ";
			$sql = "
				SELECT * 
				FROM `folders`
				WHERE `table_name` = '{$table_name}' AND `application` = '{$this->app}' {$where}
			";
			return $this->connection->query($sql)->rows;
		}
		
		const MODE_FOLDER_ID = 1;
		const MODE_FOLDER_UP = 2;
		const MODE_FOLDER_SUB_NAME = 3;
	}
}
?>