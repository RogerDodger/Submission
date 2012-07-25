<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Submission – Error</title>

		<link href="css/error.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<h1>Error</h1>
		<ul>
<?php foreach($error as $e) echo "\t\t\t<li>$e</li>\n"?>
		</ul>
<?php if(isset($referer)): ?>
		<p>
			<a href="<?php echo $referer ?>">Go back to whence ye came!</a>
		</p>
<?php endif; ?>
	</body>
</html>