<?php
/**
* Hamsterpaj Markup Language
* Version 1.0
* Author: Heggan
* Contact: Heggan@hamsterpaj.net
*
*
*/

function parseUserLink($string)
{

	return preg_replace('#&lt;(användare).*?[^&gt;]*&gt;(.[a-zA-Z0-9_-]*?)&lt;/(användare)&gt;#is','<a href="/traffa/quicksearch.php?username=$2">$2</a>',$string);
}


function parseCenter($string)
{
	$string = str_replace("&lt;centrera&gt;", '<center>', $string);
	$string = str_replace("&lt;/centrera&gt;", "</center>", $string);
	return $string;
}

function parseUp($string)
{
	$string = str_replace("&lt;upphöjt&gt;", "<sup>", $string);
	$string = str_replace("&lt;/upphöjt&gt;", "</sup>", $string);
	return $string;
}

function parseBold($string)
{
	$string = str_replace("&lt;fet&gt;", "<b>", $string);
	$string = str_replace("&lt;/fet&gt;", "</b>", $string);
	return $string;
}
function parseUnderline($string)
{
	$string = str_replace("&lt;understruket&gt;", "<u>", $string);
	$string = str_replace("&lt;/understruket&gt;", "</u>", $string);
	return $string;
}
function parseItalic($string)
{
	$string = str_replace("&lt;kursiv&gt;", "<i>", $string);
	$string = str_replace("&lt;/kursiv&gt;", "</i>", $string);
	return $string;
}
function html2specialchars($str){
	$trans_table = array_flip(get_html_translation_table(HTML_ENTITIES));
	return strtr($str, $trans_table);
}


function parseStorlek($string)
{

	if (preg_match_all('#&lt;(färg|storlek|stil).*?[^&gt;]*&gt;.*?&lt;/(färg|storlek|stil)&gt;#is', $string, $out)) {
		$c = count($out[0]);
		for($i = 0; $i < $c; $i++)
		{
			if (preg_match('#storlek=(stor)#is', $out[0][$i]))
			{
				$pxvalue = 'font-size: 25px; margin: 2px; ';
			}
			else if (preg_match('#storlek=(mellan)#is', $out[0][$i]))
			{
				$pxvalue = 'font-size: 12px; ';
			}
			else if (preg_match('#storlek=(liten)#is', $out[0][$i]))
			{
				$pxvalue = 'font-size: 9px; ';
			}
			else
			{
				$pxvalue = '';
			}
			if (preg_match('#färg=(\w[a-zA-Z0-9]*)#is', $out[0][$i], $colorvalue))
			{
				$temp = &$colorvalue[1];
				//echo $temp;
				$colorcode = 'color:' . $temp . ';';
			}
			else
			{
				$colorcode = '';
			}
			if (preg_match('#stil=(\w[0-9]*)#is', $out[0][$i], $fontvalue))
			{
				$temp = &$fontvalue[1];
				if ($temp == 1) {
					$fontcode = 'font-family: comic sans ms;';
				}
				else if ($temp == 2) {
					$fontcode = 'font-family: trebuchet ms, verdana, arial, helvetica, sans-serif;';
				}
				else if ($temp == 3) {
					$fontcode = 'font-family: arial, helvetica, sans-serif;';
				}
				else {
					$fontcode = '';
				}
			}

			$temp = $out[0][$i];
			$out[0][$i] = preg_replace('#&lt;(färg|storlek|stil).*?[^&gt;]*&gt;(.*?)&lt;/(färg|storlek|stil)&gt;#is','<span style="' . $pxvalue . ' ' . $colorcode . ' ' . $fontcode . '">$0</span>', $out[0][$i]);
			$out[0][$i] = preg_replace('#&lt;(färg|storlek|stil).*?[^&gt;]*&gt;(.*?)&lt;/(färg|storlek|stil)&gt;#is','$2',$out[0][$i]);
			$string = str_replace($temp, $out[0][$i], $string);
		}
	}
	return $string;
}


function parseBox($string)
{

	if (preg_match_all('#&lt;box.*?[^&gt;]*&gt;.*?&lt;/box&gt;#is', $string, $match))
	{
		$c = count($match[0]);
		for($i = 0; $i < $c; $i++)
		{

			if (preg_match('# bakgrund=(\w[a-zA-Z0-9]*)#is', $match[0][$i], $colorvalue))
			{
				$temp = &$colorvalue[1];
				if (is_numeric($temp))
				{
					if ($temp > 100)
					{
						$colorcode = 'background: url(/images/traffabgs/' . $temp . '.jpg);';
					}
					else if ($temp < 100)
					{
						$colorcode = 'background: url(/images/traffabgs/' . $temp . '.png);';
					}
				}
				else
				{
					$colorcode = 'background: ' . $temp . ';';
				}
			}
			else
			{
				$colorcode = '';
			}
			if (preg_match('# bredd=(\w[0-9]*)#is', $match[0][$i], $colorvalue2))
			{
				$temp = &$colorvalue2[1];
				if ($temp < 500 && $temp > 0) {
					$widthcode = 'width: ' . $temp . 'px;';
					} else if ($temp > 500) {
						$widthcode = 'width: 500px;';
						} else if ($temp < 0) {
							$widthcode = 'width: 0px;';
						}
					}
					else
					{
						$widthcode = '';
					}
					if (preg_match('#.*höjd=(\w[0-9]*)#is', $match[0][$i], $colorvalue3))
					{
						$temp = &$colorvalue3[1];
						if ($temp < 500 && $temp > 0) {
							$heightcode = 'height: ' . $temp . 'px;';
							} else if ($temp > 500) {
								$heightcode = 'height: 500px;';
								} else if ($temp < 0) {
									$heightcode = 'height: 0px;';
								}

							}
							else
							{
								$heightcode = '';
							}
							if (preg_match('# kant=((solid|prickig|sträckad))#is', $match[0][$i], $stylevalue))
							{
								$temp = &$stylevalue[1];
								if ($temp == 'solid') {
									$stylecode = 'solid';
									} else if ($temp == 'sträckad') {
										$stylecode = 'dashed';
										} else if ($temp == 'prickig') {
											$stylecode = 'dotted';
										}



									}
									else
									{
										$stylecode = 'solid';
									}

									if (preg_match('#kantfärg=(\w[a-zA-Z0-9]*)#is', $match[0][$i], $bordervalue))
									{
										$temp = &$bordervalue[1];
										$bordercode = $temp;

									}
									else
									{
										$bordercode = 'black';
									}

									if (preg_match('#kantbredd=(\w[0-9]*)#is', $match[0][$i], $borderwidthvalue))
									{
										$temp = &$borderwidthvalue[1];
										$borderwidthcode = $temp. 'px';
									}
									else
									{
										$borderwidthcode = '1px';
									}
									$temp = $match[0][$i];
									$match[0][$i] = preg_replace('#&lt;box.*?[^&gt;]*&gt;(.*?)&lt;/box&gt;#is','<div style="border: ' . $borderwidthcode . ' ' . $stylecode . ' ' . $bordercode .'; ' .  $colorcode . ' ' .$heightcode . ' ' . $widthcode .' padding: 2px;">$1</div>', $match[0][$i]);
									//$match[0][$i] = str_replace('&lt;/box&gt;', '</div>', $match[0][$i]);
									//echo $match[0][$i];
									$string = str_replace($temp, $match[0][$i], $string);

								}
							}
							return $string;

						}

						function translateColors($string)
						{
							$search = array('=röd', '=grön', '=blå', '=vit', '=svart', '=brun', '=lila', '=grå');
							$replace = array('=red', '=green', '=blue', '=white', '=black', '=brown', '=BlueViolet', '=grey');
							return str_replace($search, $replace, $string);
						}

						function parseAll($string)
						{
							$string = translateColors($string);
							$string = parseBold($string);
							$string = parseItalic($string);
							$string = parseUnderline($string);
							$string = parseCenter($string);
							$string = parseStorlek($string);
							$string = parseUp($string);
							$string = parseBox($string);
							$string = parseUserLink($string);
							return $string;
						}
						?>
