<?php

/**
 * Class for returning the word count of strings or files.
 *
 * @author Cameron Thornton
 * @copyright Copyright (c) 2012, Cameron Thornton
 */
class WordCount {

	function __construct() {
		throw new Exception("WordCount should not create an object");
	}
	
	/**
	 * Counts the words of a file
	 * 
	 * @param str $filename 
	 * @return str The wordcount
	 */
	public static function file($filename) {
		if(!file_exists($filename) || !is_readable($filename))
			return false;
		return self::_count(file_get_contents($filename));
	}
	
	/**
	 * Counts the words of a string
	 * 
	 * @param type $string 
	 * @return str The wordcount
	 */
	public static function string($string) {
		return self::_count($string);
	}
	
	private static function _count($string) {
		return str_word_count($string);
	}
}

?>
