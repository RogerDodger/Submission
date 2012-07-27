<?php
	//Grab password cookie, and set it if it doesn't exist.
	$alphanum = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	if(!isset($_COOKIE['password'])) {
		$password = '';
		for($i = 0; $i < 10; $i++) $password .= $alphanum[rand(0, strlen($alphanum)-1)];
		setcookie('password', $password, time()+60*60*24*365);
	} else {
		$password = $_COOKIE['password'];
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Submission<?php if(isset($title)) echo " – $title" ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link href="css/main.css" rel="stylesheet" type="text/css" />

</head>

<body>

<div id="main">