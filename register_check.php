<?php
	session_start();
	require('include/core/common.php');
	
	if(!isset($_GET['u'])){
		die();
	}
	
?>
<html>
<head>
<title>Användarkoll</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo '/stylesheets/ui_' . UI_CSS_VERSION . '.css';?>" rel="stylesheet" type="text/css">
</head>
<body>
<div id="main" style="width: 320px;">
<h2>Användarkoll</h2>
<?php
	$result = mysql_query('SELECT username FROM login WHERE username="' . $_GET['u'].'"') or die(mysql_error());
	if (mysql_num_rows($result) > 0)
	{
		echo 'Tyvärr, användarnamnet <b>' . mysql_result($result,0,0) . '</b> var upptaget.';
	}
	else
	{
		echo 'Användarnamnet <b>' . $_GET['u'] . '</b> är ledigt!';
	}
?>
</div>
</body>
</html>
