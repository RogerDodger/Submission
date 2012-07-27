<?php

/** 
 * Class for storing and retrieving data as a single plaintext file, separated
 * by tabs and newlines. 
 *
 * @author Cameron Thornton <cthor@cpan.org>
 * @copyright Copyright (c) 2012, Cameron Thornton
 */
class PlainStore {
	private $store;
	
	/**
	 * Load a new store at the given filename
	 * 
	 * @param str $file
	 */
	public function __construct($file) {
		if(!file_exists($file)) {
			if(!is_writable(dirname($file))) 
				throw new Exception("<".dirname($file)."> is not writable");
			else file_put_contents($file, '');
		} else {
			if(!is_writable($file)) {
				throw new Exception("<$file> is not writable");
			}
		}
		$this->store = $file;
	}
	
	/** 
	 * Read the store
	 * 
	 * @return mixed two-dimensional array containing the store's data
	 */
	public function read() {
		$store = file_get_contents($this->store);
		$rows = preg_split("#\\n#", $store, 0, PREG_SPLIT_NO_EMPTY);
		
		$data = array();
		foreach($rows as $row)
			$data[] = preg_split("#\\t#", $row);
		return $data;
	}
	
	/**
	 * Write a row to the store as tab-separated data
	 * 
	 * @param mixed $entry row to be entered, containing no tabs nor newlines
	 * 
	 * @return bool true on success
	 */
	public function write($entry) {
		if(preg_match("#[\\n\\r\\t]#", join("", $entry)))
			return false;
		file_put_contents($this->store, join("\t", $entry)."\n", FILE_APPEND);
		return true;
	}
	
	/**
	 * Delete a row from the store
	 * 
	 * @param int $index The index of the row
	 * 
	 * @return mixed two-dimensional array of store data after deletion
	 */
	public function delete($index) {
		$info = $this->read();
		
		$new = $dat = array();
		foreach($info as $key => $row) {
			if($key != $index) {
				$new[] = $row;
				$dat[] = join("\t", $row)."\n";
			}
		}
		
		//Write the new data
		file_put_contents($this->store, $dat);
		
		return $new;
	}
	
	/**
	 * Select a column from the store
	 * 
	 * @param int $index The index of the desired column
	 * 
	 * @return mixed array of the chosen column
	 */
	public function column($index) {
		$info = $this->read();
		$column = array();
		foreach($info as $row)
			if(isset($row[$index]))
				$column[] = $row[$index];
		return $column;
	}
	
}

?>
