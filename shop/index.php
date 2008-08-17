<?php
	/* OPEN_SOURCE */
	
	require('../include/core/common.php');
	require(PATHS_INCLUDE  . 'libraries/photos.lib.php');
	$ui_options['stylesheets'][] = 'shop.css';
	$ui_options['javascripts'][] = 'shop.js';
	$ui_options['title'] = 'Startsidan på Hamsterpaj - Sveriges ungdomssida!';
	$ui_options['menu_path'] = array('hamsterpaj');
	
	function shop_rounded_corners_top($options)
	{
		$options['color'] = isset($options['color']) ? $options['color'] : 'dark';
		
		$output .= '<div class="shop_rounded_corners_' . $options['color'] . '">' . "\n";
			$output .= '<div>' . "\n";
				$output .= '<div>' . "\n";
					$output .= '<div>' . "\n";
		return $output;
	}
	
	function shop_rounded_corners_bottom()
	{
						$output .= '</div>' . "\n";
					$output .= '</div>' . "\n";
				$output .= '</div>' . "\n";
			$output .= '</div>' . "\n";
		return $output;
	}
	
	$options['color'] = 'dark';
	$out .= shop_rounded_corners_top($options);
	$out .= 'Hej på dig!' . "\n";
	$out .= shop_rounded_corners_bottom();
	
	$options['color'] = 'bright';
	$out .= shop_rounded_corners_top($options);
	$out .= 'Hej på dig med!' . "\n";
	$out .= shop_rounded_corners_bottom();
	
	$out .= '<div id="boys">' . "\n";
	$options['color'] = 'bright';
	$out .= shop_rounded_corners_top($options);
		$options['color'] = 'dark';
		$out .= shop_rounded_corners_top($options);
			$out .= 'Killar' . "\n";
		$out .= shop_rounded_corners_bottom();
	$out .= shop_rounded_corners_bottom();
	$out .= '</div>' . "\n";
	
	$out .= '<div id="girls">' . "\n";
		$options['color'] = 'bright';
		$out .= shop_rounded_corners_top($options);
			$options['color'] = 'dark';
			$out .= shop_rounded_corners_top($options);
				$out .= 'Tjejer' . "\n";
			$out .= shop_rounded_corners_bottom();
		$out .= shop_rounded_corners_bottom();
	$out .= '</div>' . "\n";

	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
