<?php
$contest_tags['contest_types'] = array('korsord', 'ett-kryss-två', 'slogan', 'motivering', 'klurigt', 'webbspel', 'skicka-in-ett-tips', 'skicka-in-en-bild');
$contest_tags['price_types'] = array('pengar', 'lotter', 'IT-prylar', 'resor', 'biljetter', 'mp3-spelare', 'mobiltelefon', 'platt-tv', 'presentkort');

function contests_fetch($options)
{
	$options['order-by'] = (isset($options['order-by'])) ? $options['order-by'] : 'c.id';
	$options['order-direction'] = (isset($options['order-direction'])) ? $options['order-direction'] : 'DESC';
	$options['limit'] = (isset($options['limit'])) ? $options['limit'] : 20;
	
	if(isset($options['tags']))
	{
		$options['tags'] = (is_array($options['tags'])) ? $options['tags'] : array($options['tags']);	
	}
	
	$query = 'SELECT c.*, GROUP_CONCAT(tf.label) AS tag_labels, GROUP_CONCAT(tf.handle) AS tag_handles';
	$query .= ' FROM contests AS c, object_tags AS otf, tags AS tf';
	$query .= (isset($options['tags'])) ? ', object_tags AS ots, tags AS ts' : '';
	$query .= ' WHERE otf.reference_id = c.id AND tf.id = otf.tag_id AND otf.object_type = "contest" AND c.time_end > "' . (strtotime(date('Y-m-d'))-1) . '"';
	if(isset($options['tags']))
	{
		$query .= 'AND ots.object_id = tag_id AND ts.tag = ots.tag_id AND ts.label IN("' . implode('", "', $options['tags']) . '")';
	}
	$query .= ' GROUP BY c.id ORDER BY ' . $options['order-by'] . ' ' . $options['order-direction'];
	$query .= ' LIMIT ' . $options['limit'];

	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	while($contest = mysql_fetch_assoc($result))
	{
		unset($contest['tags']);
		$labels= explode(',', $contest['tag_labels']);
		$handles = explode(',', $contest['tag_handles']);
		for($i = 0; $i < count($labels); $i++)
		{
			$contest['tags'][$handles[$i]] = strtolower($labels[$i]);
		}
		$contests[] = $contest;
	}
	
	return $contests;
}

function contests_list($contests)
{
	global $contest_tags;
	
	$counter = 0;
	foreach($contests AS $contest)
	{
		echo '<!-- Contest #' . $contest['id'] . ' -->' . "\n";
		echo '<div class="contest">' . "\n";
		echo '<div class="contest_freetext">' . "\n";
		if($contest['user_confirmed'] == 1)
		{
			echo '<img src="http://images.hamsterpaj.net/contests/user_confirmed.png" />' . "\n";
		}
		echo '<h1><a href="/taevlingar/out.php?id=' . $contest['id'] . '" target="_blank">' . $contest['title'] . '</a></h1>' . "\n";
		echo '<p>' . nl2br($contest['description']) . '</p>' . "\n";
		echo '</div>' . "\n";
		
		echo '<div class="contest_tags">' . "\n";
		echo '<h3>Tävlingstyp</h3>' . "\n";
		$first = true;
		foreach($contest['tags'] AS $handle => $label)
		{
			if(in_array($label, $contest_tags['contest_types']))
			{
				if(!$first)
				{
					echo ', ';	
				}
				echo '<a href="/taevlingar/' . $handle . '.html">' . $label . '</a>';	
				$first = false;
			}
		}
		$first = true;
		echo '<h3>Vinsttyp</h3>' . "\n";
		foreach($contest['tags'] AS $handle => $label)
		{
			if(in_array($label, $contest_tags['price_types']))
			{
				if(!$first)
				{
					echo ', ';	
				}
				echo '<a href="/taevlingar/' . $handle . '.html">' . $label . '</a>';	
				$first = false;
			}
		}
		echo '</div>' . "\n";
		echo '<div class="contest_footer">' . "\n";
		echo '<span class="clicks">' . $contest['clicks'] . ' klick</span>' . "\n";
		$time_left = ceil(($contest['time_end'] - time())/86400);
		if($time_left == 1)
		{
			$time_label = 'Sista dagen idag';	
		}
		else
		{
			$time_label = $time_left . ' dagar kvar';	
		}
		
		$value = ($contest['value'] == 0) ? 'okänt' : cute_number($contest['value']) . ':-';
		$value = ($contest['prize_value_cirka'] == 1) ? 'Ca ' . $value : $value;
		echo '<span class="time_left">' . (ceil(($contest['time_end']-time())/86400)) . ' dagar kvar</span>' . "\n";
		echo '<span class="value">Värde: ' . $value . '</span>' . "\n";
		echo '<span class="mark_as_done">markera som avklarad</span>' . "\n";
		echo '</div>' . "\n";
		echo '</div>' . "\n\n\n";
		
		if($counter == 2)
		{
?>
<script type="text/javascript"><!--google_ad_client = "pub-9064365008649147";google_alternate_color = "FFFFFF";google_ad_width = 468;google_ad_height = 60;google_ad_format = "468x60_as";google_ad_type = "text";//2007-10-03: Tävlingsflikengoogle_ad_channel = "6075720464";google_color_border = "EEEEEE";google_color_bg = "FFFFFF";google_color_link = "000000";google_color_text = "000000";google_color_url = "333333";google_ui_features = "rc:6";//--></script><script type="text/javascript"  src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
<?php	
		}
		
		$counter++;
	}
}

function contests_create($contest)
{
	$query = 'INSERT INTO contests(time_created, time_end, title, description, value, url, prize_value_cirka) ';
	$query .= 'VALUES("' .$contest['time_created'] . '", "' . $contest['time_end'] . '", "' . $contest['title'] . '", "' . $contest['description'];
	$query .= '", "' . $contest['value'] . '", "' . $contest['url'] . '", "' . $contest['prize_value_cirka'] . '")';
	
	mysql_query($query) or die(report_sql_error($query));
	
	$contest_id = mysql_insert_id();
		
	$options['item_id'] = $contest_id;
	$options['object_type'] = 'contest';
	$options['tag_label'] = $contest['tags'];
	tag_set_wrap($options);
	
	preint_r($options);
	
	return $contest_id;
}

function contests_form()
{
	global $contest_tags;
	
	echo '<div class="contests_form">' . "\n";
	echo '<form action="/taevlingar/admin.php?action=create" method="post">' . "\n";
	echo '<h3>Tävlingsrubrik</h3>' . "\n";
	echo '<input type="text" name="title" value="' . $contest['title'] . '" class="textbox" />' . "\n";
	echo '<h3>Beskrvining</h3>' . "\n";
	echo '<textarea name="description" class="textbox">' . htmlspecialchars($contest['description']) . '</textarea>' . "\n";
	echo '<h3>Vinstens värde</h3>' . "\n";
	echo '<input type="text" name="value" value="' . $contest['value'] . '" class="textbox" />' . "\n";
	echo '<input type="checkbox" name="prize_value_cirka" id="check_prize_value_cirka" />' . "\n";
	echo '<label for="check_prie_value_cirka">Värdet är ungefärligt</label>' . "\n";
	echo '<h3>Slutdatum</h3>' . "\n";
	echo '<input type="text" name="time_end" value="' . $contest['time_end'] . '" class="textbox" />' . "\n";
	echo '<h3>Webbadress</h3>' . "\n";
	echo '<input type="text" name="url" value="' . $contest['url'] . '" class="textbox" />' . "\n";
	echo '<h3>Tävlingstyp(er)</h3>' . "\n";
	foreach($contest_tags['contest_types'] AS $type_tag)
	{
		echo '<input type="checkbox" name="' . $type_tag . '" value="tag" id="tag_' . $type_tag . '" />' . "\n";
		echo '<label for="tag_' . $type_tag . '">' . $type_tag . '</label><br />' . "\n";
	}
	echo '<h3>Vinsttyp(er)</h3>' . "\n";
	foreach($contest_tags['price_types'] AS $type_tag)
	{
		echo '<input type="checkbox" name="' . $type_tag . '" value="tag" id="tag_' . $type_tag . '" />' . "\n";
		echo '<label for="tag_' . $type_tag . '">' . $type_tag . '</label><br />' . "\n";
	}
	
	echo '<h3>Release</h3>' . "\n";
	echo '<input type="text" name="release" value="' . date('Y-m-d H:i', schedule_release_get(array('type' => 'contest'))) . '" class="textbox" />' . "\n";
	
	echo '<input type="submit" value="Spara" />' . "\n";
	echo '</form>' . "\n";
	echo '</div>' . "\n";
}

function contests_update()
{
	
}

function contest_remove()
{
	
}

?>