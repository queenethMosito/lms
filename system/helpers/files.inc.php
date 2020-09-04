<?php
if(!defined("HELPER_FILES"))
{
	define("HELPER_FILES", 1);
	
	class Files_Helper
	{
		/**
		 * Checks and if needs to, creates the required folder. Will create the
		 * sub folder in the form 'folder00000' to split files into sub folders as there
		 * appears to be a limit on the server onto how many files can be in a folder.
		 * 
		 * This will allow us 10k * 10k = 100 000 000 files before we run out of space
		 * 
		 * Will also set the owner and file permissions correctly
		 * 
		 * @param string $root The root folder
		 * @param int $id The if of the inteded target, so we can create to correct sub folder
		 * @return string The full path with sub folder. Includes the trailing slash
		 */
		public function SelectFolderForId($root, $id, $id_folder = false)
		{
			// Prepare the sub folder
			$folder = floor($id / 10000);
			while(strlen($folder) < 5) $folder = "0".$folder;
			$folder = "folder".$folder;
			
			// Prepare the path
			if(substr($root, -1) != "/") $root .= "/";
			$path = $root.$folder."/";
			
			// If the id folder is set to true, then add it
			if($id_folder)
			{
				$path .= "{$id}/";
			}
			
			// Create the folder if needed
			if(!file_exists($path))
			{
				@mkdir($path, 0755, true);
				@chmod($path, 0755);
				@chown($path, "apache");
			}
			
			return $path;
		}
		
		public function CreateFolderForId($root, $id, $id_folder = false)
		{
			return $this->SelectFolderForId($root, $id, $id_folder);
		}
		
		/**
		 * Moves an uploaded folder to the destination. Simply extends the function move_uploaded_file
		 * by adding actions such as chmod and chown all in one function
		 * 
		 * @param string $source
		 * @param string $destination
		 * @return bool Teh result from the move_uploaded_file function
		 */
		public function MoveUploadedFile($source, $destination)
		{
			$result = @move_uploaded_file($source, $destination);
			
			@chmod($destination, 0644);
			@chown($destination, "apache");
			
			return $result;
		}
	}
}