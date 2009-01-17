<?php
	require('include/core/common.php');
	
	$query = 'UPDATE fp_modules SET clicks = clicks + 1 WHERE id = "' . $_GET['id'] . '" LIMIT 1';
	mysql_query($query);
	
	if (preg_match('eis#^(http://)(www\.)(hamsterpaj|pajen|hamsterpajiskolan)\.(net|se)#eis', $_SERVER['HTTP_REFERER']))
	{
		event_log_log('fp_module_click');
		header('Location: ' . base64_decode($_GET['url']));
	}
	else
	{
		$url = base64_decode(utf8_decode($_GET['url']));
		$out .= '<style>
			body {
				background: #6391B3;
				font: 14px Verdana, sans-serif;
				color: #222222;
			}
			#content a, #content a:visited {
				color: #000000;
				text-decoration: none;
				border-bottom: thin dotted black;
			}
			a img, a:visited img {
				border: 0;
			}
		</style>' . "\n";
		$out .= '<div style="text-align: center;">' . "\n";
		$out .= '<div style="width: 355px; margin: 0 auto 0 auto; background: #e3e3e3; border: thin solid #aaaaaa;">' . "\n";
		$out .= '<div id="header">' . "\n";
		$out .= '<a href="/"><img src="http://images.hamsterpaj.net/ui/ui_logo.png" alt="Hamsterpaj logo" /></a>' . "\n";
		$out .= '</div>' . "\n";
		$out .= '<div id="content" style="padding: 4px; padding-top: 0; text-align: left;">' . "\n";
		$out .= '<p>' . "\n";
		$out .= 'Är du säker på att du vill gå vidare till' . "\n";
		$out .= '<a href="' . $url . '">' . $url . '</a>' . "\n";
		$out .= '</p>' . "\n";
		$out .= '</div>' . "\n";
		$out .= '</div>' . "\n";
		$out .= '</div>' . "\n";
		echo $out;
	}
?>