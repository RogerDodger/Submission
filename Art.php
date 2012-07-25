<?php 
	include 'lib/config.php';
	include 'lib/PlainStore.php';
	
	//Retrieve password cookie if exists, or set one if it doesn't.
	$alphanum = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	if(!isset($_COOKIE['password'])) {
		$password = '';
		for($i = 0; $i < 10; $i++) $password .= $alphanum[rand(0, strlen($alphanum)-1)];
		setcookie('password', $password, time()+60*60*24*365);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Submission – /art/</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link href="css/main.css" rel="stylesheet" type="text/css" />
<script src="js/password_fill.js" type="text/javascript"></script>

<?php
	$store = new PlainStore(ART_INFO_FILE);
	
	if(!is_dir(ART_STORE_DIR) || !is_writable(ART_STORE_DIR))
		trigger_error('"'.ART_STORE_DIR.'" is not writable or is not a directory', E_USER_ERROR);
	
	$info = $store->read();
	/* info[] = array(
	 *    [0] => handle,
	 *    [1] => title,
	 *    [2] => password,
	 *    [3] => artist,
	 *    [4] => website,
	 * );
	 */
	
	$open  = new DateTime(ART_SUBS_OPEN);
	$close = new DateTime(ART_SUBS_CLOSE);
	
?>

</head>

<body>

<div id="main">

<h1>/art/-/fic/ Event</h1>

<h2>/art/ Submissions</h2>

<p>
	Submissions open: <strong><?php echo $open->format(RSS_UTC) ?></strong>.
	<br />
	Submissions close: <strong><?php echo $close->format(RSS_UTC) ?></strong>.
	<br />
<?php if(ART_SUBS_ALLOWED): ?>
	Submissions are <strong class="valid">open</strong>.
<?php else: ?>
	Submissions are <strong class="invalid">closed</strong>.
<?php endif; ?>
</p>

<h2>Guidelines</h2>

<ul>
	<li>Submitted works must be your own.</li>
	<li>Images must be in either <tt>.png</tt>, <tt>.jpg</tt>, or <tt>.gif</tt> format.</li>
	<li>Images must be smaller than 4MB.</li>
</ul>

<?php if(ART_SUBS_ALLOWED): ?>

<form action="<?php echo ROOT_DIR."/ArtFormHandler.php" ?>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="submit" value="true"/>
	<fieldset>
		<legend>Submit Art</legend>
		<p>Fields with <span class="required">*</span> are required.</p>
		<table>
			<tr>
				<td>Title<span class="required">*<span></td>
				<td>
					<input name="title" type="text" maxlength="<?php echo $MAXLEN['title'] ?>"/>
					<input type="submit" value="Submit"/>
				</td>
			</tr>
			<tr>
				<td>Artist</td>
				<td><input name="artist" type="text" maxlength="<?php echo $MAXLEN['artist'] ?>"/></td>
			</tr>
			<tr>
				<td>Website</td>
				<td><input name="website" type="text" maxlength="<?php echo $MAXLEN['website'] ?>"/></td>
			</tr>
			<tr>
				<td>Image<span class="required">*</span></td>
				<td><input name="image" type="file"/></td>
			</tr>
			<tr>
				<td>Password<span class="required">*</span></td>
				<td>
					<input name="password" type="password" maxlength="<?php echo 
			$MAXLEN['password'] ?>"/>
					(for submission deletion)
				</td>
			</tr>
		</table>
	</fieldset>
</form>

<p>
	The “Title” field is for the name of your work. Works must have a title so 
	that they may be referenced by it.
</p>
<p>
	The “Password” field is there so you can delete your submission (e.g., in 
	case you want to resubmit it). A persistent password cookie is automatically 
	generated for you when you first load the page, so you shouldn’t really have
	to think about this.
</p>
<p>
	The “Artist” and “Website” fields are optional. The “Website” field is there 
	in case you’d like a link to your dA (or similar) beside your work when
	it is presented.
</p>

<?php endif; ?>

<h2>Received Submissions</h2>

<form action="<?php echo ROOT_DIR."/ArtFormHandler.php" ?>" method="post">
	<table class="slim">
		<tr>
			<th>Title</th>
			<th>Filesize</th>
			<th>MIME Type</th>
			<th>Submission Date</th>
			<th></th>
		</tr>
<?php
		foreach($info as $item) {
			echo "\t\t<tr>\n";
			echo "\t\t\t<td>".(strlen($item[1]) > 32 ? substr($item[1],0,29)."&hellip;" : $item[1])."</td>\n";
			echo "\t\t\t<td>".round(filesize(ART_STORE_DIR.DIRECTORY_SEPARATOR.$item[0])/1024, 2)." KB</td>\n";
			echo "\t\t\t<td>".preg_replace("/;.+/", "", $Mime->file(ART_STORE_DIR.DIRECTORY_SEPARATOR.$item[0]))."</td>\n";
			echo "\t\t\t<td>".date('d M Y H:i:s', filemtime(ART_STORE_DIR.DIRECTORY_SEPARATOR.$item[0]))."</td>\n";
			echo "\t\t\t<td><input type=\"checkbox\" name=\"delete[]\" value=\"$item[0]\"/></td>\n";
			echo "\t\t</tr>\n";
		}
?>
	</table>
	<p>
		Password:
		<input name="password" type="password" maxlength="<?php echo 
			$MAXLEN['password'] ?>"/>
		<input type="submit" value="Delete selected items"/>
	</p>
</form>

<?php include 'lib/footer.php' ?>

</div>

</body>

</html>