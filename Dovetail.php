<?php
class dovetail{
	/*(list:array)*/ function select_files($begin, $end="NOW", $root=NULL){

	}
	/*(json:array)*/ function open_file($file){
		if(!file_exists($file)){ return FALSE; }
		return json_decode(file_get_contents($file), TRUE);
	}

	/*(json:array)*/ function execute($list=array(), $method=array()){
		$data = array();
		foreach($list as $file){
			$json = dovetail::open_file($file);
			foreach($method as $handler){
				/*fix*/ if(!isset($data[$handler]) || !is_array($data[$handler])){ $data[$handler] = array(); }
				$data[$handler] = $handler($json, &$data[$handler]);
			}
		}
		return $data;
	}
		
	
	/***************************************************
	 * Data Storage
	 ***************************************************/
	var $src = NULL;
	var $data = array();
	function save($filename=NULL){
		if($filename == NULL && $this->src != NULL){ $filename = $this->src; }
	}
	function open($filename=NULL){
		if($filename == NULL && $this->src != NULL){ $filename = $this->src; }
		
		$this->data = $data;
		return $data;
	}
	
	/***************************************************
	 * Data Collection
	 ***************************************************/
	var $directories = array();
	var $files = array(/* hash=>filename */);
	var $allowed_extensions = array('json','hermes');
	function open_directory($directory, $auto_open=FALSE, $open_subdirectories=FALSE){
		if(!file_exists($directory) || !is_dir($directory)){ return FALSE; }
		if(!in_array($directory, $this->directories)){ $this->directories[] = $directory; }
		
		$files = array();
		$list = scandir($directory);
		foreach($list as $i=>$f){
			if(preg_match("#^[\.]+$#", $f)){
				/*ignore*/
			}
			elseif(preg_match("#[^\.]+[\.]((".implode('|', $this->allowed_extensions)."))$#i", $f)){
				$files[] = $directory.DIRECTORY_SEPERATOR.$f;
				if($auto_open !== FALSE){ self::open_file($directory.DIRECTORY_SEPERATOR.$f); }
			}
			elseif($open_subdirectories !== FALSE && is_dir($directory.DIRECTORY_SEPERATOR.$f)){
				$files = array_merge($files, self::open_directory($directory.DIRECTORY_SEPERATOR.$f, $auto_open, $open_subdirectories) );
			}
		}
		return $files;
	}
	function dc_open_file($filename){
		$this->files[self::hash_file($filename)] = $filename;
	}
	function hash_file($filename){
		/*development*/ return md5($filename);
	}
	function allowed_extension($ext=NULL, $action=NULL){
		if($ext == NULL){ return /*(array)*/ $this->allowed_extensions; }
		else{
			switch($action){
				case NULL: //check
					return in_array(strtolower($ext), $this->allowed_extensions);
					break;
				case TRUE: //add
					$this->allowed_extensions[] = strtolower($ext); return TRUE;
					break;
				case FALSE: //remove
					break;
			}
		}
	}
	
	/***************************************************
	 * Entry acces
	 ***************************************************/
	private $_entry = array();
	function entry(){
		return $this->_entry;
	}
	function register_entry($entry){
		$this->_entry = $entry;
	}
	 
	/***************************************************
	 * Registered labels
	 ***************************************************/
	var $register = array();
	function get_timestamp($entry=FALSE){
		if($entry === FALSE){ $entry = $this->entry(); }
	}
	function get_identity($entry=FALSE){
		if($entry === FALSE){ $entry = $this->entry(); }
	}
	
	/***************************************************
	 * Data Storage
	 ***************************************************/
	function get($variable, $entry=FALSE){
		if($entry === FALSE){ $entry = $this->entry(); }
		if(isset($entry[$variable])){
			return $entry[$variable];
		}
		else{ return FALSE; }
	}
}

// $dove = dovetail::execute( dovetail::select_files(), array('Peiling:method') );
// $results = Peiling::result($dovetail);

if(isset($_GET['debug'])){
	print '<pre class="debug dovetail">';
	$dove = new Dovetail;
	print_r($dove);
	print '<pre>';
}
?>