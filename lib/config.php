<?php
	date_default_timezone_set('UTC');
	define('RSS_UTC', 'D, d M Y H:i:s \U\T\C');
	define('RSS_SHORT',  'd M Y H:i:s');
	
	define('ROOT_DIR', '.');
	
	define('HASH_TYPE', 'md5');
	
	define('ART_SUBS_OPEN',  '2011-07-30 00:00:00');
	define('ART_SUBS_CLOSE', '2012-08-03 00:00:00');
	define('ART_INFO_FILE',  'GalleryInfo');
	define('ART_STORE_DIR',  'images');

	define('FIC_SUBS_OPEN',  '2011-08-03 04:00:00');
	define('FIC_SUBS_CLOSE', '2012-08-06 04:00:00');
	define('FIC_INFO_FILE',  'StoryInfo');
	define('FIC_STORE_DIR',  'stories');
	define('MIN_WORD_COUNT',  2500);
	
	$Now = new DateTime('now');
	define('ART_SUBS_ALLOWED', new DateTime(ART_SUBS_OPEN) < $Now && 
			$Now < new DateTime(ART_SUBS_CLOSE));
	define('FIC_SUBS_ALLOWED', new DateTime(FIC_SUBS_OPEN) < $Now && 
			$Now < new DateTime(FIC_SUBS_CLOSE));
	
	$MAXLEN = array(
		'title'     => 64,
		'art_title' => 64,
		'password'  => 10,
		'artist'    => 32,
		'email'     => 256,
		'website'   => 256,	
	);
	
	$ART_TYPES = array(
		'image/gif'  => '.gif',
		'image/jpeg' => '.jpg',
		'image/png'  => '.png',
	);
	
	$FIC_TYPES = array(
		'text/plain' => '.txt',
	);
	
	$Mime = new finfo(FILEINFO_MIME);
	
	define('ADMIN_NAME', '');
	
	$from = $_SERVER['SERVER_ADMIN'];
	if(ADMIN_NAME != '')
		$from = ADMIN_NAME." <$from>";
	
	define('MAIL_SUBJECT', "Write-off Submission Received");
	define('MAIL_MESSAGE',
		"This e-mail is informing you that your submission has been received.\n\n".
		"If you have received this e-mail, then all is well.\n\n".
		"If you haven't received this e-mail, please delete this e-mail.\n");
	define('MAIL_HEADERS',
		"From: $from" . "\r\n" .
		"X-Mailer: PHP/" . phpversion());
?>