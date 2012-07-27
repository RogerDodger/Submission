<?php

/*
 * Takes POST data from Fic.php and performs appropriate logic
 */
	include 'lib/PlainStore.php';
	include 'lib/config.php';
	include 'lib/WordCount.php';
	$error = array();
	
	$store = new PlainStore(FIC_INFO_FILE);
	$info = $store->read();

	if(!is_dir(FIC_STORE_DIR)) {
		if(file_exists(FIC_STORE_DIR)) {
			throw new Exception('<'.FIC_STORE_DIR.'> is a file');
		} else {
			if(!is_writable(dirname(FIC_STORE_DIR))) {
				throw new Exception('<'.dirname(FIC_STORE_DIR).'> is not writable');
			} else {
				mkdir(FIC_STORE_DIR);
				file_put_contents(FIC_STORE_DIR.DIRECTORY_SEPARATOR.'.htaccess',
						"deny from all\n");
			}
		}
	}
	
	if(isset($_POST['submit'])) {
		if(FIC_SUBS_ALLOWED) {
			$required = array('title','art_title','email','password');
			foreach($required as $item)
				if(!isset($_POST[$item]) || $_POST[$item] == '')
					$error[] = "<strong>".ucfirst(str_replace('_', ' ', $item)).
						"</strong> is a required field.";
			
			if(isset($_POST['password']) && preg_match("#[^a-zA-Z0-9]#", $_POST['password']))
				$error[] = "<strong>Password</strong> must be alphanumeric.";
			
			if(isset($_FILES)) {
				if($_FILES['story']['name'] == '') {
					$error[] = "<strong>Story</strong> is a required field.";
				} elseif($_FILES['story']['error'] > 0) {
					$error[] = "<strong>Story</strong> too large or upload error.";
				} else {
					$type = preg_replace("#;.+#", "", $Mime->file($_FILES['story']['tmp_name']));
					if(!isset($FIC_TYPES[$type]))
						$error[] = "<strong>Filetype</strong> '$type' is not permitted.";
					elseif(WordCount::file($_FILES['story']['tmp_name']) < MIN_WORD_COUNT)
						$error[] = "<strong>Story</strong> is fewer than ".
							MIN_WORD_COUNT." words.";
					else $ext = $FIC_TYPES[$type];
				}
			} else { $error[] = "<strong>Story</strong> is a required field."; }
			
			foreach(array_keys($MAXLEN) as $item) {
				if(isset($_POST[$item]) && strlen($_POST[$item]) > $MAXLEN[$item])
					$error[] = "<strong>$item</strong> must be fewer than <em>".
						$MAXLEN[$item]."</em> charaters.";
				if(isset($_POST[$item]) && preg_match("#\\n|\\r|\\t#", $_POST[$item]))
					$error[] = "Text fields may not have newlines nor tabs.";
			}
			
			if(isset($_POST['email']) && $_POST['email'] != '' && 
					!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
				$error[] = "<strong>Email</strong> '".$_POST['email'].
					"' is not a valid e-mail address.";
			
			/*
			 * Check if art_title is the name of a submitted artwork.
			 * Shouldn't happen with browser requests, so no point in having
			 * a descriptive error.
			 */
			$art = new PlainStore(ART_INFO_FILE);
			if(isset($_POST['art_title']) && $_POST['art_title'] != '' && 
				!in_array(htmlentities($_POST['art_title']), $art->column(1)))
					$error[] = 1;
						
			/*
			 * Valid data? Process submission.
			 */
			if($error == array()) {
				//Create new unique handle
				$primary_keys = $store->column(0);
				$alphanum = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				do {
					$handle = '';
					for($i = 0; $i < 5; $i++) $handle .= $alphanum[rand(0, strlen($alphanum)-1)];
				} while(in_array($handle, $primary_keys));
				$handle .= $ext;
				
				//Set cookie in case user defined one, then hash the password
				setcookie('password',$_POST['password'],time()+60*60*24*365);
				$_POST['password'] = hash(HASH_TYPE, $_POST['password']);
				$_POST['wordcount'] = WordCount::file($_FILES['story']['tmp_name']);
				
				
				$fields = array('title','password','art_title',
				                'email','wordcount','artist');
				
				$entry = array($handle);
				foreach($fields as $field)
					if(isset($_POST[$field]))
						$entry = htmlentities($_POST[$field]);
				
				move_uploaded_file($_FILES['story']['tmp_name'], FIC_STORE_DIR.DIRECTORY_SEPARATOR.$handle);
				$store->write($entry);
				
				/*
				 * Notify given e-mail address of successful submission
				 */
				mail($_POST['email'], MAIL_SUBJECT, MAIL_MESSAGE, MAIL_HEADERS, MAIL_PARAMS);
			}
		} else { 
			$error[] = "<strong>Submissions are closed.</strong>"; 
		}
	} 
	
	if(isset($_POST['password']) && isset($_POST['delete'])) {
		foreach($_POST['delete'] as $handle) {
			$index = array_search($handle, $store->column(0), true);
			if($index === false) {
				$error[] = "<strong>$handle</strong> is an invlid handle.";
			} elseif($info[$index][2] == hash(HASH_TYPE, $_POST['password'])) {
				unlink(FIC_STORE_DIR.DIRECTORY_SEPARATOR.$info[$index][0]);
				$info = $store->delete($index);
			} else {
				$error[] = "Passwords do not match.";
			}
		}
	}
	
	if($error == array()) header("Location: Fic.php");
	
	//Display error page if errors exist
	$referer = ROOT_DIR."/Fic.php";
	include 'lib/error.php';
?>
