<?php
	require('include/core/common.php');
	
	$query = 'UPDATE fp_modules SET clicks = clicks + 1 WHERE id = "' . $_GET['id'] . '" LIMIT 1';
	mysql_query($query);
	
	if (preg_match('#^(http://)(www\.)(hamsterpaj|pajen|hamsterpajiskolan)\.(net|se)/(index.php)#', $_SERVER['HTTP_REFERER']))
	{
		event_log_log('fp_module_click');
		header('Location: ' . base64_decode($_GET['url']));
	}
	else
	{
		$url = base64_decode(utf8_decode($_GET['url']));
		$out .= '
		<html>
			<head>
				<title>Varning! - Hamserpaj.net</title>
				<link rel="shortcut icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />
				<style>
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
					#explanation, #footer {
						font: 12px Verdana, sans-serif;
					}
				</style>
			</head>
			<body>
				<div style="text-align: center;">
					<div style="width: 355px; margin: 0 auto 0 auto; background: #e3e3e3; border: thin solid #aaaaaa;">
						<div id="header">
							<a href="/">
							<img src="http://images.hamsterpaj.net/ui/ui_logo.png" alt="Hamsterpaj logo" />
							</a>
						</div>
						<div id="content" style="padding: 4px; padding-top: 0; text-align: left;">
							<p>
							Är du säker på att du vill gå vidare till
							</p>
							<p>
							<a href="' . $url . '">' . $url . '</a>
							</p>
							<p id="explanation">
							 <em>- Får du upp den här sidan utan att ha klickat på några konstiga länkar?</em><br />
							 Ingen fara, du har helt enkelt inte HTTP_REFERER påslaget i din webbläsare.
							</p>
						</div>
						<div id="footer">
							<em>Referer: ';
							$out .= (strlen($_SERVER['HTTP_REFERER']) > 0) ? $_SERVER['HTTP_REFERER'] : '<tom>';
							$out .= '</em>
						</div>
					</div>
				</div>
			</body>
		</html>' . "\n";
		echo $out;
	}
?>