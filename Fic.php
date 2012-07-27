<?php 
	include 'lib/config.php';
	include 'lib/PlainStore.php';
	$title = '/fic/';
	include 'lib/header.php';
	$store = new PlainStore(FIC_INFO_FILE);
	
	$info = $store->read();
	/* info[] = array(
	 *    [0] => handle,
	 *    [1] => title,
	 *    [2] => password,
	 *    [3] => art_title,
	 *    [4] => email,
	 *    [5] => word_count,
	 *    [6] => artist,
	 * );
	 */
	
	$open  = new DateTime(FIC_SUBS_OPEN);
	$close = new DateTime(FIC_SUBS_CLOSE);
	
	$art = new PlainStore(ART_INFO_FILE);
	$art_titles = $art->column(1);
?>

<h1>/art/-/fic/ Event</h1>

<h2>/fic/ Submissions</h2>

<p>
	Submissions open: <strong><?php echo $open->format(RSS_UTC) ?></strong>.
	<br />
	Submissions close: <strong><?php echo $close->format(RSS_UTC) ?></strong>.
	<br />
<?php if(FIC_SUBS_ALLOWED): ?>
	Submissions are <strong class="valid">open</strong>.
<?php else: ?>
	Submissions are <strong class="invalid">closed</strong>.
<?php endif; ?>
</p>

<h2>Guidelines</h2>

<ul>
	<li>Submitted works must be your own.</li>
	<li>Submitted works must have a title.</li>
	<li>Submissions must indicate the title of the art associated with your story.</li>
	<li>Stories must be in <strong>plaintext</strong> format.
		<ul>
			<li>
				This can be done by using the “Save as... (.txt)” option
				in your word processor.
			</li>
			<li>Preferred encoding: UTF-8</li>
		</ul>
	</li>
	<li>Stories must be at least <?php echo MIN_WORD_COUNT ?> words.</li>
	<li>
		You must provide your email address so that you can receive further
		information about the preliminary-round judging.
	</li>
</ul>

<?php if(FIC_SUBS_ALLOWED): ?>

<form action="<?php echo ROOT_DIR."/FicFormHandler.php" ?>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="submit" value="true"/>
	<fieldset>
		<legend>Submit Story</legend>
		<p>Fields with <span class="required">*</span> are required.</p>
		<table>
			<tr>
				<td>Title<span class="required">*</span></td>
				<td>
					<input name="title" type="text" maxlength="<?php echo $MAXLEN['title'] ?>"/>
					<input type="submit" value="Submit"/>
				</td>
			</tr>
			<tr>
				<td>Art title<span class="required">*</span></td>
				<td>
					<select name="art_title">
						<optgroup label="Submitted Artworks">
							<option value=""></option>
<?php foreach($art_titles as $art_title) 
	echo "\t\t\t\t\t\t\t<option value=\"$art_title\">$art_title</option>\n" ?>
						</optgroup>
					</select>
				</td>
			</tr>
			<tr>
				<td>Email<span class="required">*</span></td>
				<td><input name="email" type="text" maxlength="<?php echo $MAXLEN['email'] ?>"/></td>
			</tr>
			<tr>
				<td>Author</td>
				<td><input name="author" type="text" maxlength="<?php echo $MAXLEN['artist'] ?>"/></td>
			</tr>
			<tr>
				<td>Story<span class="required">*</span></td>
				<td><input name="story" type="file"/></td>
			</tr>
			<tr>
				<td>Password<span class="required">*</span></td>
				<td>
					<input name="password" type="password" maxlength="<?php echo 
			$MAXLEN['password'] ?>" value="<?php echo $password ?>"/>
					(for submission deletion)
				</td>
			</tr>
		</table>
	</fieldset>
</form>

<p>
	The “Password” field is there so you can delete your submission (e.g., in 
	case you want to resubmit it). A persistent password cookie is automatically 
	generated for you when you first load the page, so you shouldn’t really have
	to think about this.
</p>
<p>
	You will receive an email at the given address on successful submission to
	let you know if you entered the right address. This is done because if you 
	typo’d your email, you wouldn’t be able to participate in the preliminary
	voting, and so your submission would end up disqualified (and that’d kinda 
	suck).
</p>	
<p>
	If you don’t receive a confirmation email, check your spam box, etc. to 
	check if it actually is there; then, if you still can’t find it, delete and 
	resubmit your story—making sure to enter the the right address.
</p>

<?php endif; ?>

<h2>Received Submissions</h2>

<form action="<?php echo ROOT_DIR."/FicFormHandler.php" ?>" method="post">
	<table class="slim">
		<tr>
			<th>Title</th>
			<th>Word Count</th>
			<th>Charset</th>
			<th>Submission Date</th>
			<th></th>
		</tr>
<?php
		foreach($info as $item) {
			echo "\t\t<tr>\n";
			echo "\t\t\t<td>".(strlen($item[1]) > 32 ? substr($item[1],0,29)."&hellip;" : $item[1])."</td>\n";
			echo "\t\t\t<td>".$item[5]."</td>\n";
			echo "\t\t\t<td>".preg_replace("/.+; charset=/", "", $Mime->file(FIC_STORE_DIR.DIRECTORY_SEPARATOR.$item[0]))."</td>\n";
			echo "\t\t\t<td>".date('d M Y H:i:s', filemtime(FIC_STORE_DIR.DIRECTORY_SEPARATOR.$item[0]))."</td>\n";
			echo "\t\t\t<td><input type=\"checkbox\" name=\"delete[]\" value=\"$item[0]\"/></td>\n";
			echo "\t\t</tr>\n";
		}
?>
	</table>
	<p>
		Password:
		<input name="password" type="password" maxlength="<?php echo 
			$MAXLEN['password'] ?>" value="<?php echo $password ?>"/>
		<input type="submit" value="Delete selected items"/>
	</p>
</form>

<?php include 'lib/footer.php' ?>