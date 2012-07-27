<?php

/*
 * Takes POST data from Art.php and performs appropriate logic
 */
	include 'lib/PlainStore.php';
	include 'lib/config.php';
	$error = array();
	
	$store = new PlainStore(ART_INFO_FILE);
	$info = $store->read();

	if(!is_dir(ART_STORE_DIR)) {
		if(file_exists(ART_STORE_DIR)) {
			throw new Exception('<'.ART_STORE_DIR.'> is a file');
		} else {
			if(!is_writable(dirname(ART_STORE_DIR))) {
				throw new Exception('<'.dirname(ART_STORE_DIR).'> is not writable');
			} else {
				mkdir(ART_STORE_DIR);
				file_put_contents(ART_STORE_DIR.DIRECTORY_SEPARATOR.'.htaccess',
						"deny from all\n");
			}
		}
	}
	
	if(isset($_POST['submit'])) {
		if(ART_SUBS_ALLOWED) {
			$required = array('title','password');
			foreach($required as $item)
				if(!isset($_POST[$item]) || $_POST[$item] == '')
					$error[] = "<strong>".ucfirst($item)."</strong> is a required field.";
			
			if(isset($_POST['password']) && preg_match("#[^a-zA-Z0-9]#", $_POST['password']))
				$error[] = "<strong>Password</strong> must be alphanumeric.";
			
			
			if(isset($_FILES)) {
				if($_FILES['image']['name'] == '') {
					$error[] = "<strong>Image</strong> is a required field.";
				} elseif($_FILES['image']['error'] > 0) {
					$error[] = "<strong>Image</strong> too large or upload error.";
				} else {
					$type = preg_replace("#;.+#", "", $Mime->file($_FILES['image']['tmp_name']));
					if(!isset($ART_TYPES[$type]))
						$error[] = "<strong>Filetype</strong> '$type' not permitted.";
					else $ext = $ART_TYPES[$type];
				}
			} else { $error[] = "<strong>Image</strong> is a required field."; }
			
			foreach(array_keys($MAXLEN) as $item) {
				if(isset($_POST[$item]) && strlen($_POST[$item]) > $MAXLEN[$item])
					$error[] = "<strong>$item</strong> must be fewer than <em>".
						$MAXLEN[$item]."</em> charaters.";
				if(isset($_POST[$item]) && preg_match("#\\n|\\r|\\t#", $_POST[$item]))
					$error[] = "Text fields may not have newlines nor tabs.";
			}
			
			if(isset($_POST['website']) && $_POST['website'] != '' && 
				!filter_var($_POST['website'], FILTER_VALIDATE_URL))
					$error[] = "<strong>Website</strong> '".$_POST['website'].
					"' is not a valid URL.";

			if(isset($_POST['title']) && $_POST['title'] != '' && 
				in_array($_POST['title'], $store->column(1), true))
					$error[] = "<strong>Title</strong> '".$_POST['title'].
					"' already exists.";

			
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
				
				$entry = array($handle);
				foreach(array('title','password','artist','website') as $field)
					if(isset($_POST[$field])) 
						$entry[] = htmlentities($_POST[$field]);
				
				move_uploaded_file($_FILES['image']['tmp_name'], ART_STORE_DIR.DIRECTORY_SEPARATOR.$handle);
				$store->write($entry);
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
				unlink(ART_STORE_DIR.DIRECTORY_SEPARATOR.$info[$index][0]);
				$info = $store->delete($index);
			} else {
				$error[] = "Passwords do not match.";
			}
		}
	}
	
	if($error == array()) header("Location: Art.php");
	
	//Display error page if errors exist
	$referer = ROOT_DIR."/Art.php";
	include 'lib/error.php';
?>
