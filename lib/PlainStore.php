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
	 * Create a new store object from given file
	 * 
	 * @param str $file
	 * 
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
	 * 
	 */
	public function read() {
		$store = file_get_contents($this->store);
		$rows = preg_split("#\\n#", $store, 0, PREG_SPLIT_NO_EMPTY);
		$data = array();
		foreach($rows as $row)
			$data[] = preg_split("#\\t#", $row, 0, PREG_SPLIT_NO_EMPTY);
		return $data;
	}
	
	/**
	 * Write a two-dimensional array to the store as tab-separated data
	 * 
	 * @param mixed $twoDimensionalArray containing no tabs nor newlines
	 * @return bool $success
	 * 
	 */
	public function write($data) {
		foreach($data as $row)
			if(preg_match("#[\\n\\r\\t]#", join("", $row)))
				throw new Exception("Unpermitted control character(s) in data");
		$rows = array_map(function($row) {
			return join("\t", $row)."\n";
		}, $data);
		file_put_contents($this->store, $rows);
		return true;
	}
	
	/**
	 * Select a column from a two-dimensional array
	 * 
	 * @param mixed $twoDimensionalArray
	 * @param int $index The index of the desired column; defaults to 0
	 * 
	 * @return Array of the chosen column
	 * 
	 */
	public static function column($twoDimensionalArray, $index = 0) {
		$column = array();
		foreach($twoDimensionalArray as $row)
			if(isset($row[$index]))
				$column[] = $row[$index];
		return $column;
	}
}

?>