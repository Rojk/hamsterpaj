<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'poll.lib.php');
	header('Content-type: application/javascript');

	echo 'document.write(\'<script type="text/javascript" language="javascript" src="http://www.hamsterpaj.net/javascripts/womlib.js"></script>\');' . "\n\n";
	echo 'document.write(\'<script type="text/javascript" language="javascript" src="http://www.hamsterpaj.net/javascripts/xmlhttp.js"></script>\');' . "\n\n";
	echo 'document.write(\'<script type="text/javascript" language="javascript" src="http://www.hamsterpaj.net/javascripts/scripts.js"></script>\');' . "\n\n";
	echo 'document.write(\'<script type="text/javascript" language="javascript" src="http://www.hamsterpaj.net/javascripts/poll.js"></script>\');' . "\n\n";
	echo 'document.write(\'<link rel="stylesheet" type="text/css" href="http://www.hamsterpaj.net/stylesheets/poll.css" />\');' . "\n\n";

	$poll = poll_fetch(array('id' => $_GET['poll']));
	$poll_output = poll_render($poll[0]);
	$poll_output = htmlentities($poll_output, ENT_QUOTES, 'UTF-8');
	$healthy = array('&lt;', '&gt;', '&quot;', "\n");
	$yummy = array('<', '>', '\\"', '');
	$poll_output = str_replace($healthy, $yummy, $poll_output);
	echo 'document.write("' . $poll_output . '");' . "\n";
	//echo 'document.write("<a href=\\"http://www.hamsterpaj.net/poll/\\" target=\\"_blank\\">Gratis unders&ouml;kning</a> fr&aring;n Hamsterpaj");';
?>