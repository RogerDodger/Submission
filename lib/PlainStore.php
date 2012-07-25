<?php

/**
 * Class for storing and retrieving data as a single, tab-delimited plaintext
 * file.
 *
 * @author Cameron Thornton <cthor@cpan.org>
 */
class PlainStore {
	private $store;
	
	/*
	 * $store = new PlainStore(str $file);
	 * 
	 * creates a new store with the filename $file
	 *
	 * creates empty file $file if $file does not exist
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
	
	/*
	 * @method read([int $column = -1])
	 * Read the store
	 * 
	 * @return two-dimensional array of store data
	 */
	public function read() {
		$store = file_get_contents($this->store);
		$rows = preg_split("#\\n#", $store, 0, PREG_SPLIT_NO_EMPTY);
		$data = array();
		foreach($rows as $row)
			$data[] = preg_split("#\\t#", $row, 0, PREG_SPLIT_NO_EMPTY);
		return $data;
	}
	
	/*
	 * @method write()
	 * Write a two-dimensional array to the store as tab-separated data
	 * 
	 * @args two-dimensional array, elements containing no newlines nor tabs
	 * @return true if success; throws exception if failure;
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
	
	/*
	 * PlainStore::column($twoDimensionalArray, $index = 0);
	 * 
	 * @returns the $index'th column in a 2D array
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