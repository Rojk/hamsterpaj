<?php
	require('../include/core/common.php');
	require_once(PATHS_INCLUDE  . 'libraries/entertain.lib.php');
	require_once(PATHS_INCLUDE  . 'libraries/rank.lib.php');
	require_once(PATHS_INCLUDE  . 'libraries/photos.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/fp_modules.lib.php');

	$ui_options['javascripts'][] = 'fp_common_modules.js';

	$ui_options['stylesheets'][] = 'fp_common_modules.css';
	
	$ui_options['title'] = 'Startsidan på Hamsterpaj';
	$ui_options['menu_path'] = array('hamsterpaj');

	$ui_options['custom_logo'] = 'http://images.hamsterpaj.net/piraja/hp_piraja_logo.png';

	event_log_log('piraja');

	$five_config = array(
				// HULTSFRED
				'hfred' => array(	'image_1' => 'http://images.hamsterpaj.net/five_errors/hfred1.png',
							'image_2' => 'http://images.hamsterpaj.net/five_errors/hfred2.png',
							'preview' => 'http://images.hamsterpaj.net/five_errors/hfred_preview.png',
							'width' => 380,
							'height' => 262,
							'message_title' => 'Bra tjockis!',
							'message_text' => 'Du hittade alla fel p&aring; %seconds% sekunder! Johan letar fortfarande efter elden i t&auml;ltet...',
							'correctcircles' => array( 	array('x' => 197, 'y' => 55, 'r' => 20),
											array('x' => 172, 'y' => 121, 'r' => 20),
											array('x' => 25, 'y' => 113, 'r' => 30),
											array('x' => 69, 'y' => 120, 'r' => 20),
											array('x' => 336, 'y' => 14, 'r' => 16))),

				// BUSH
				'bush' => array(	'image_1' => 'http://images.hamsterpaj.net/five_errors/bush1.png',
							'image_2' => 'http://images.hamsterpaj.net/five_errors/bush2.png',
							'preview' => 'http://images.hamsterpaj.net/five_errors/bush_preview.png',
							'width' => 380,
							'height' => 297,
							'message_title' => 'Du &auml;r inte s&aring; korkad som han ser ut!',
							'message_text' => 'P&aring; %seconds% sekunder har Bush inte hittat det kalkonen hittade direkt...',
							'correctcircles' => array( 	array('x' => 369, 'y' => 280, 'r' => 23),
											array('x' => 77, 'y' => 199, 'r' => 14),
											array('x' => 257, 'y' => 48, 'r' => 43),
											array('x' => 74, 'y' => 67, 'r' => 12),
											array('x' => 213, 'y' => 107, 'r' => 12))),

			);


	function five_errors($five_errors)
	{
		$o .= '<div id="five_errors_img' . $five_errors['divid'] . '" style="cursor: pointer; position: relative; background: url(\'' . $five_errors['image'] . '\'); color: white; width: ' . $five_errors['width'] . 'px; height: ' . $five_errors['height'] . 'px;">' . "\n";
		foreach($five_errors['correctcircles'] as $circleindex => $circle)
		{
			$size = $circle['r']*2;
			$x = $circle['x'] - $size/2;
			$y = $circle['y'] - $size/2;
			$o .= '<img id="five_errors_img' . $five_errors['divid']. '_circle' . $circleindex. '" src="http://images.hamsterpaj.net/five_errors/error_circle.gif" style="display: none; position: absolute; width: ' . $size . 'px; height: ' . $size . 'px; left: ' . $x . 'px; top: ' . $y . 'px;" />'; 
		}
$o .= '</div>' . "\n";
		return $o;
	}

	if (isset($_GET['fffid']) && isset($five_config[$_GET['fffid']]))
	{
		$five_errors = $five_config[$_GET['fffid']];

		$five_errors['image'] = $five_errors['image_1'];
		$five_errors['divid'] = '1';

		$xxl = '<div style="background: #565656; padding: 23px;">' . "\n";
		$xxl .= '<div style="padding: 12px; margin-right: 23px; background: white; float: left;">' . "\n";
		$xxl .= five_errors($five_errors);
		$xxl .= '</div>' . "\n";

		$five_errors['image'] = $five_errors['image_2'];
		$five_errors['divid'] = '2';
		$xxl .= '<div style="padding: 12px; background: white; float: left;">' . "\n";
		$xxl .= five_errors($five_errors);
		$xxl .= '</div>' . "\n";

		$xxl .= '<br style="clear: both;" /></div>' . "\n";

		$ui_options['xxl'] = $xxl;
	}
	ui_top($ui_options);

	if (!isset($_GET['fffid']) || !isset($five_config[$_GET['fffid']]))
	{
		echo '<h1 id="five_errors_h1">Pilla inte p&aring; query-stringen klantarsle!</h1>' . "\n";
		echo '<p id="five_errors_p">Sluta leka hakker och klicka ist&auml;llet p&aring; bilderna nedan, precis som alla andra...</p>';
	} else {
		echo '<h1 id="five_errors_h1">Finn Fem Fel</h1>' . "\n";
		echo '<p id="five_errors_p">Pirajan har vart framme och kluddat i bilden till h&ouml;ger, hittar du det han &auml;ndrat?</p>'; //Pirajan har vart framme och kluddat i bilden till höger, hittar du det han&auml;ndratit?</p>' . "\n";
	}

	echo '<h2>Fler bilder</h2>' . "\n";
	echo '<div style="background: #565656; padding: 23px;">' . "\n";
	foreach($five_config as $imgname => $imgconfig)
	{
		echo '<div style="width: 100px; height: 80px; padding: 12px; margin-right: 23px; background: white; float: left;">' . "\n";
		echo '<a href="?fffid=' . $imgname . '"><img src="' . $imgconfig['preview'] . '" alt="' . $imgname . '"/></a>';
		echo '</div>' . "\n";	
	}
	echo '<br style="clear: both;" /></div>' . "\n";

	echo '
<script type="text/javascript">
	var fiveErrorsImg1 = document.getElementById(\'five_errors_img1\');
	var fiveErrorsImg2 = document.getElementById(\'five_errors_img2\');

	fiveErrorsImg1.onclick = five_errors_click;
	if (fiveErrorsImg1.captureEvents) fiveErrorsImg1.captureEvents(Event.CLICK);
	fiveErrorsImg2.onclick = five_errors_click;
	if (fiveErrorsImg2.captureEvents) fiveErrorsImg2.captureEvents(Event.CLICK);

	var five_errors_startdate = new Date();

	function five_errors_click(e)
	{
		var posx=0;
		var posy=0;
		var elm=this;

		var correctcircles=new Array(5);

';

		foreach($five_errors['correctcircles'] as $circleindex => $circle)
		{
			echo 'correctcircles[' . $circleindex . '] = new Object();' . "\n";
			echo 'correctcircles[' . $circleindex . '].x=' . $circle['x']. ';' . "\n";	
			echo 'correctcircles[' . $circleindex . '].y=' . $circle['y']. ';' . "\n";	
			echo 'correctcircles[' . $circleindex . '].r=' . $circle['r']. ';' . "\n";	
		}

echo '
		if (!e) var e = window.event;
	
		if (e.pageX||e.pageY)
		{
			posx=e.pageX;
			posy=e.pageY;
		} else if(e.clientX||e.clientY) {
			posx=e.clientX+document.body.scrollLeft;
			posy=e.clientY+document.body.scrollTop;
		}

		while(elm!=null)
		{
			posx -= elm.offsetLeft;
			posy -= elm.offsetTop;
			elm=elm.offsetParent;
		}

		var counter=0;
		for (var i = 0; i < correctcircles.length; i++)
		{
			var diff = Math.sqrt((posx - correctcircles[i].x)*(posx - correctcircles[i].x) + (posy - correctcircles[i].y)*(posy - correctcircles[i].y));
			var circleElm1 = document.getElementById(\'five_errors_img1_circle\'+i);
			var circleElm2 = document.getElementById(\'five_errors_img2_circle\'+i);

			if (diff < correctcircles[i].r)
			{
				circleElm1.style.display = \'block\';
				circleElm2.style.display = \'block\';
			}

			if (circleElm1.style.display == \'block\')
			{
				counter++;
			}
		}

		if (counter == 5)
		{
			var endtime = (new Date() - five_errors_startdate);
			elmh1 = document.getElementById(\'five_errors_h1\');
			elmp = document.getElementById(\'five_errors_p\');
		';

		echo 'elmh1.innerHTML = \'' . $five_errors['message_title'] . '\';' . "\n";
		echo 'var textmessage = \'' . $five_errors['message_text'] . '\';' . "\n";

		echo '
			elmp.innerHTML = textmessage.replace(\'%seconds%\', \'\'+Math.round(endtime/1000));
		}
	}
</script>
	';

ui_bottom();
	?>
