<?php

/*
 * Sends a confirmation e-mail to indicated address
 */
	require_once('config.php');
	if(file_exists(TMP_MAIL_LIST_FILE)) {
		$to = file_get_contents(TMP_MAIL_LIST_FILE);
		unlink(TMP_MAIL_LIST_FILE);
	}
	
?>
