<?php

function rank_draw($rank, $options)
{
	$options['size'] = isset($options['size']) ? $options['size'] : 'small';
	$output = '';
	$output .= '<div class="film_rank">' . "\n";
	for($i = 0; $i < floor($rank); $i++)
	{
		$output .= '<img src="' . IMAGE_URL . 'common_icons/stars/' . $options['size'] . '_star_full.png" />' . "\n";
	}
	if(($rank - floor($rank)) > 0.25 && ($rank - floor($rank)) < 0.75)
	{
		$output .= '<img src="' . IMAGE_URL . 'common_icons/stars/' . $options['size'] . '_star_half.png" />' . "\n";			
		$i++;
	}
	for($i = $i; $i < 5; $i++)
	{
		$output .= '<img src="' . IMAGE_URL . 'common_icons/stars/' . $options['size'] . '_star_empty.png" />' . "\n";			
	}
	$output .= '</div>' . "\n";
	return $output;
}

function rank_input_draw($item_id, $item_type, $options)
{
	$previous = isset($options['previous']) ? $options['previous'] : 0;
	$output = '';
	$output = '<div class="rank_input_container">' . "\n";
	$output .= '<div id="rank_input" class="rank_input"' . (login_checklogin() ? '' : ' onclick="javascript: tiny_reg_form_show();"') . '>' . "\n";
	for($i=0; $i<=10; $i++)
	{
		$output .= '<div class="rank_input_part" id="rank_input_part_' . $i . '"></div>';
	}
	$output .= '<input type="hidden" id="rank_previous" value="' . $previous . '" />' . "\n";
	$output .= '<input type="hidden" id="rank_item_id" value="' . $item_id . '" />' . "\n";
	$output .= '<input type="hidden" id="rank_item_type" value="' . $item_type . '" />' . "\n";
	$output .= '</div>' . "\n";
	$output .= '<br style="clear: both;" /><div id="rank_input_message">Tack för din röst!</div>' . "\n";
	$output .= '</div>' . "\n";
	return $output;
}

?>