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

	$five_errors['image_1'] = 'http://images.hamsterpaj.net/five_errors/hultsfred1.png';
	$five_errors['image_2'] = 'http://images.hamsterpaj.net/five_errors/hultsfred2.png';
	$five_errors['width'] = 380;
	$five_errors['height'] = 262;
	$five_errors['rows'] = 4;
	$five_errors['cols'] = 6;
	$five_errors['correct'] = array(8, 15, 30, 31, 34, 60);
	
	function five_errors($five_errors)
	{
		$o .= '<table style="background: url(\'' . $five_errors['image'] . '\'); color: white; width: ' . $five_errors['width'] . 'px; height: ' . $five_errors['height'] . 'px;">' . "\n";
		for($i = 0; $i < $five_errors['rows']; $i++)
		{
			$o .= '<tr>' . "\n";
			for($j = 0; $j < $five_errors['cols']; $j++)
			{
				$onclick = (in_array(($i*$five_errors['cols']+$j), $five_errors['correct'])) ? ' onclick="this.style.border = \'2px solid white\';"' : '';
				$label = (FALSE) ? ($i*$five_errors['cols']+$j) : '';
				$o .= '<td' . $onclick . ' style="border: 2px solid transparent;">' . $label . '</td>' . "\n";
			}			
			$o .= '</tr>' . "\n";
		}
		$o .= '</table>' . "\n";
		return $o;
	}

	$five_errors['image'] = $five_errors['image_1'];

	$xxl = '<div style="background: #565656; padding: 23px;">' . "\n";
	$xxl .= '<div style="padding: 12px; margin-right: 23px; background: white; float: left;">' . "\n";
	$xxl .= five_errors($five_errors);
	$xxl .= '</div>' . "\n";

	$five_errors['image'] = $five_errors['image_2'];
	$xxl .= '<div style="padding: 12px; background: white; float: left;">' . "\n";
	$xxl .= five_errors($five_errors);
	$xxl .= '</div>' . "\n";
	
	$xxl .= '<br style="clear: both;" /></div>' . "\n";

	$ui_options['xxl'] = $xxl;

	ui_top($ui_options);

	echo '<h1>Finn Fem Fel</h1>' . "\n";
	echo '<p>Pirajan har vart framme och kluddat i bilden till höger, hittar du det han målat dit?</p>' . "\n";
	echo '<h2>Fler bilder</h2>' . "\n";

	ui_bottom();
	?>
