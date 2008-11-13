<?php
	if(login_checklogin() && date_get_age($_SESSION['userinfo']['birthday']) <= 13)
	{
		$output .= rounded_corners_top(array('color' => 'orange_deluxe'), true);
		$output .= '<img style="float: left; padding: 5px 5px 5px 0;" src="http://images.hamsterpaj.net/13skylt.png" />' . "\n";
		$output .= '<h1 style="margin: 0 0 3px 0; font-size: 16px;">Hamsterpaj är ingen barnsida, är du under 13 så använd www.lunarstorm.se</h1>' . "\n";
		$output .= '<p style="margin: 0 0 0 0;">Vi som gör Hamsterpaj tycker att medlemmar under 13 år ställer till en massa problem. Om du inte har fyllt 13 borde du läsa vår <a href="http://www.hamsterpaj.net/artiklar/?action=show&id=24">ålderspolicy</a> och fundera på om Hamsterpaj är rätt ställe för dig. Annars rekommenderar vi Lunarstorm, där kan man få häftiga statuspoäng!</p>' . "\n";
		$output .= '<div style="clear:both;"></div>' . "\n";
		$output .= rounded_corners_bottom(array('color' => 'orange_deluxe'), true);
	}
?>