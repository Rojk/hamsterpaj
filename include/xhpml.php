<?php

function profile_parse_presentation($string)
{
	$tag_names = array('fet', 'kursiv', 'understruken', 'rubrik', 'underrubrik', 'box', 'text', 'länk', 'upphöjd');
	foreach ($tag_names as $tag)
	{
		$nr_stop_tags = substr_count($string, '&lt;/' . $tag);
		$nr_start_tags = substr_count($string, '&lt;' . $tag);
		
		if ($nr_start_tags < $nr_stop_tags)
		{
			return htmlspecialchars('Det fattas ' . ($nr_start_tags - $nr_stop_tags) . ' <' . $tag . '>');
		}
		else if($nr_stop_tags < $nr_start_tags)
		{
			return htmlspecialchars('Det fattas ' . ($nr_start_tags - $nr_stop_tags) . ' </' . $tag . '>'); 
		}
	}
	$pattern = array('#\&lt;fet\&gt;(.*?)\&lt;/fet\&gt;#s',
	'#\&lt;kursiv\&gt;(.*?)\&lt;/kursiv\&gt;#s',
	'#\&lt;understruken\&gt;(.*?)\&lt;/understruken\&gt;#s',
	'#\&lt;rubrik\&gt;(.*?)\&lt;/rubrik\&gt;#s',
	'#\&lt;underrubrik\&gt;(.*?)\&lt;/underrubrik\&gt;#s',
	'#\&lt;box typ=([0-9]*?)\&gt;(.*?)#',
	'#\&lt;/box\&gt;(.*?)#',
	'#\&lt;text typ=([0-9]*?)\&gt;(.*?)#',
	'#\&lt;/text\&gt;(.*?)#',
	'#\&lt;länk användare=&quot;([a-zA-Z0-9_-]*?)&quot;\&gt;(.*?)#',
	//'#\&lt;länk webb=&quot;(.*?)&quot;\&gt;(.*?)#',
	'#\&lt;länk webb=&quot;(https?://[a-z0-9-]+(\.[a-z0-9-]+)+)&quot;\&gt;(.*?)#',
	'#\&lt;länk\&gt;(.*?)\&lt;/länk\&gt;#',
	'#\&lt;/länk\&gt;(.*?)#',
	'#\&lt;upphöjd\&gt;(.*?)\&lt;/upphöjd\&gt;#s',);

	$replace = array('<strong>$1</strong>',
	'<em>$1</em>',
	'<u>$1</u>',
	'<h5>$1</h5>',
	'<h6>$1</h6>',
	'<div class="traffa_freetext_box$1">$2',
	'</div>$1',
	'<span class="traffa_freetext_text$1">$2',
	'</span>$1',
	'<a href="/traffa/quicksearch.php?username=$1">$2',
	'<a href="$1">$3',
	'<a href="/traffa/quicksearch.php?username=$1">$1</a>',
	'</a>',
	'<sup>$1</sup>');

	$text = preg_replace($pattern, $replace, $string);

	return $text;
}
?>
