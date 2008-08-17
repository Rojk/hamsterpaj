<?php

function open_search_fetch($options = null)
{
	$query = 'SELECT * FROM open_search_boxes';
	if(isset($options['id']))
	{
		 $query .= ' WHERE id = '.$options['id'];
	}
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	while($data = mysql_fetch_assoc($result))
	{
		$res[] = $data;
	}
	$res['num_rows'] = mysql_num_rows($result);
	return $res;
}
function open_search_make_box($id)
{
	$box = open_search_fetch(array('id'=>$id));
	if($box['num_rows'] == 1)
	{
		$box = $box[0];
		$out .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$out .= '  <OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">'."\n";
		$out .= "\t".'<ShortName>'.$box['ShortName'].'</ShortName>'."\n";
		$out .= "\t".'<Description>'.$box['Description'].'</Description>'."\n";
		$out .= "\t".'<Tags>'.$box['Tags'].'</Tags>'."\n";
		$out .= "\t".'<Contact>waldemar.axdorph@gmail.com</Contact>'."\n";
		$out .= "\t".'<Url type="text/html" method="get" template="http://hamsterpaj.net/open_search/?q={searchTerms}&amp;type='.$box['id'].'&amp;search=true"></Url>'."\n";
		$out .= "\t".'<LongName>'.$box['LongName'].'</LongName>'."\n";
		$out .= "\t".'<Image height="16" width="16" type="image/vnd.microsoft.icon">http://images.hamsterpaj.net/favicon.ico</Image>'."\n";
		$out .= "\t".'<Image height="16" width="16" type="image/png">http://images.hamsterpaj.net/favicon.png</Image>'."\n";
		$out .= "\t".'<Query role="example" searchTerms="random" />'."\n";
		$out .= "\t".'<Developer>Waldemar Axdorph</Developer>'."\n";
		$out .= "\t".'<SyndicationRight>open</SyndicationRight>'."\n";
		$out .= "\t".'<AdultContent>false</AdultContent>'."\n";
		$out .= "\t".'<Language>se-sv</Language>'."\n";
		$out .= "\t".'<OutputEncoding>UTF-8</OutputEncoding>'."\n";
		$out .= "\t".'<InputEncoding>UTF-8</InputEncoding>'."\n";
		$out .= '  </OpenSearchDescription>'."\n";
	
		//add to database too for statistics or something
		$user_id = (login_checklogin() ? $_SESSION['login']['id'] : 0);
		$ip = (login_checklogin() ? $_SESSION['ip'] : $_SERVER['REMOTE_ADDR']);
		$query = 'INSERT INTO open_search_stats_boxes(type, user_id, ip, timestamp) VALUES('.$box['id'].', '.$user_id.', "'.$ip.'", '.time().')';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		return $out;
	}
	else
	{
		return 'Den typen finns inte';
	}
}

function open_search_boxes_list()
{
	$boxes = open_search_fetch();
	$out = '';
	if($boxes['num_rows'] > 0)
	{
		$out = '<ul class="open_search_edit">'."\n";

		for($i=0;$i<(count($boxes)-1);$i++)
		{
			$out .= "\t".'<li>';
			$out .= rounded_corners_top(array('color' => 'blue_deluxe'), true);
			$out .= '<br style="clear:both" />';
			$out .= '<h3><span class="box_link" id="link_'.$boxes[$i]['id'].'"><img src="'.IMAGE_URL.'plus.gif" alt="Expandera/Kollapsa" id="image_'.$boxes[$i]['id'].'" /></span> <a href="javascript:window.external.AddSearchProvider(\'http://www.hamsterpaj.net/open_search/?type='.$boxes[$i]['id'].'\');">'.$boxes[$i]['name'].'</a></h3>';
			$out .= '<div class="open_search_box_info" id="box_'.$boxes[$i]['id'].'">'."\n";
			$out .= '<p>'.$boxes[$i]['Description'].'</p>'."\n";
			$out .= 'Tillagd: '.fix_time($boxes[$i]['timestamp']).' <a href="javascript:window.external.AddSearchProvider(\'http://www.hamsterpaj.net/open_search/?type='.$boxes[$i]['id'].'\');">Lägg till bland dina sökmotorer</a>';
			$out .= '</div>';
			$out .= '<br style="clear:both" />';
			$out .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
			$out .= '</li>'."\n";
		}
		$out .= '</ul>'."\n";
	}
	else
	{
		$out .= 'Det finns inga söklådor!'."\n";
	}

	return $out;
}

function open_search_list_head()
{
	$boxes = open_search_fetch();
	for($i=0;$i<count($boxes)-1;$i++)
	{
		$out .= '<link rel="search"
           type="application/opensearchdescription+xml" 
           href="http://www.hamsterpaj.net/open_search/?type='.$boxes[$i]['id'].'"
           title="'.$boxes[$i]['ShortName'].'" />';
	}
	return $out;

}
function open_search_execute($query, $id)
{	
	$box = open_search_fetch(array('id'=>$id));
	if($box['num_rows'] == 1)
	{
		$box = $box[0];
		$location = str_replace('{Search}', $query, $box['link']);
		$user_id = (login_checklogin() ? $_SESSION['login']['id'] : 0);
		$ip = (login_checklogin() ? $_SESSION['ip'] : $_SERVER['REMOTE_ADDR']);
		$query = 'INSERT INTO open_search_stats_query(query, user_id, ip, type, timestamp) VALUES("'.$query.'", '.$user_id.', "'.$ip.'", '.$box['id'].', '.time().')';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		header('Location: '.$location);
		exit;
	}
	else
	{
		die('De finns ingen länk till den typen ('.$id.')'); 
	}
	
}

function open_search_menu_list($action='home')
{
	$menu = array();
	$menu[0]['href'] = '?action=home';
	$menu[0]['label'] = 'Start';
	$menu[1]['href'] = '?action=add_search_box';
	$menu[1]['label'] = 'Lägg till söklåda';
	$menu[2]['href'] = '?action=edit_search_boxes';
	$menu[2]['label'] = 'Redigera söklådor';
	$menu[3]['href'] = '?action=view_stats';
	$menu[3]['label'] = 'Statistik';
	$menu[5]['href'] = '?action=help';
	$menu[5]['label'] = 'Hjälp';
	
	foreach($menu as $key=>$menu_item)
	{
		if($menu_item['href'] == '?action='.$action)
			$menu[$key]['current'] = TRUE;
	}
	$rounded_corners_tabs_options['tabs'] = $menu;
	$out = rounded_corners_tabs_top($rounded_corners_tabs_options, true); 
	return $out;
}

function open_search_stats_query_fetch($options = null)
{
	$query = 'SELECT os.query, COUNT(*) AS num_searches, osb.name FROM open_search_stats_query AS os, open_search_boxes AS osb WHERE os.type = osb.id GROUP BY os.query ORDER BY num_searches DESC ';
	
	if(isset($options['limit']) && !isset($options['no_limit']))
	{
		if($options['limit'] >= 0)
		{
			$query .= ' LIMIT '.intval($options['limit']);
		}
		else
		{
			$query .= ' LIMIT 5';
		}
	}
			
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	if(mysql_num_rows($result) > 0)
	{
		$out .= '<ol>'."\n";
		while($data = mysql_fetch_assoc($result))
		{
			$out .= "\t".'<li><strong>'.$data['query'].'</strong> ('.$data['num_searches'].') - '.$data['name'].'</li>'."\n";
		}
		$out .= '</ol>';
	}
	else
	{
		$out .= 'Inga sökningar!';
	}
	return $out;

}
function open_search_stats_query_list_mini()
{
	$out = '';
	$out .= '<h2>Ministatistik över sökningar</h2>'."\n";
	$out .= open_search_stats_query_fetch(array('limit'=>5));
	return $out;
}

function open_search_stats_query_list($options = null)
{
	$out = '<h2>Statistik över sökningar</h2>'."\n";
	$options['no_limit'] = true;
	$out .= open_search_stats_query_fetch($options);
	return $out;
}

function open_search_stats_boxes_fetch()
{
	$query = 'SELECT box.name, COUNT(*) AS num_users FROM open_search_boxes AS box, open_search_stats_boxes AS stat_box WHERE stat_box.type = box.id GROUP BY stat_box.type ORDER BY num_users DESC';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

	$out = '<ol>'."\n";
	if(mysql_num_rows($result) > 0)
	{
		while($data = mysql_fetch_assoc($result))
		{
			$out .= "\t".'<li><strong>'.$data['name'].'</strong> används av '.$data['num_users'].' st</li>'."\n";
		}
	}
	else
	{
		$out .= "\t".'<li>Inga använder några söklådor!</li>'."\n";
	}
	$out .= '</ol>';
	return $out;
}
function open_search_stats_boxes_list()
{
	$out = '<h2>Mest valda söklådor</h2>';
	$out .= open_search_stats_boxes_fetch();
	return $out;
}

function open_search_addbox_draw_input($input)
{
	$out = '';
	$out .= '<form action="/ajax_gateways/open_search.php?action='.$input['action'].'" id="'.$input['formname'].'" class="open_search_form" method="post">'."\n";
	$out .= '<table>'."\n";

	$out .= '<tr>'."\n";
	$out .= '<td><label for="name_form"><a href="?action=help&amp;what=Namn" class="open_search_help" id="Namn'.($input['id'] != null ? '_'.$input['id'] : '').'" title="Läs om Namn">Namn</a></label></td><td><input id="name_form" tabindex="1" value="'.$input['name'].'" type="text" maxlength="40" name="name" /></td>'."\n";
	$out .= '</tr>'."\n";

	$out .= '<tr>'."\n";
	$out .= '<td><label for="ShortName_form"><a href="?action=help&amp;what=ShortName" class="open_search_help" id="ShortName'.($input['id'] != null ? '_'.$input['id'] : '').'" title="Läs om ShortName">ShortName</a></label></td><td><input id="ShortName_form" value="'.$input['ShortName'].'" maxlength="16" tabindex="2" type="text" name="ShortName" /></td>'."\n";
	$out .= '</tr>'."\n";

	$out .= '<tr>'."\n";
	$out .= '<td><label for="Description_form"><a href="?action=help&amp;what=Description" class="open_search_help" id="Description'.($input['id'] != null ? '_'.$input['id'] : '').'" title="Läs om Description">Description</a></label></td><td><input id="Description_form" value="'.$input['Description'].'" maxlength="1024" tabindex="3" type="text" name="Description" /></td>'."\n";
	$out .= '</tr>'."\n";

	$out .= '<tr>'."\n";
	$out .= '<td><label for="tags_form"><a href="?action=help&amp;what=Tags" class="open_search_help" id="Tags'.($input['id'] != null ? '_'.$input['id'] : '').'" title="Läs om Tags">Tags</a></label></td><td><input id="tags_form" tabindex="4" type="text" maxlength="255" value="'.$input['Tags'].'" name="Tags" /></td>'."\n";
	$out .= '</tr>'."\n";

	$out .= '<tr>'."\n";
	$out .= '<td><label for="LongName_form"><a href="?action=help&amp;what=LongName" class="open_search_help" id="LongName'.($input['id'] != null ? '_'.$input['id'] : '').'" title="Läs om LongName">LongName</a></label></td><td><input id="LongName_form" value="'.$input['LongName'].'" maxlength="48" tabindex="5" type="text" name="LongName" /></td>'."\n";
	$out .= '</tr>'."\n";
	
	$out .= '<tr>'."\n";
	$out .= '<td><label for="Link_form"><a href="?action=help&amp;what=Link" class="open_search_help" id="Link'.($input['id'] != null ? '_'.$input['id'] : '').'" title="Läs om Link">Link</a></label></td><td><input id="Link_form" tabindex="5" value="'.$input['link'].'" maxlength="150" type="text" name="Link" /></td>'."\n";
	$out .= '</tr>'."\n";

	$out .= '<tr>'."\n";
	$out .= '<td><input type="submit" tabindex="6" value="'.$input['submit_value'].'" /></td>'."\n";
	$out .= '</tr>'."\n";

	$out .= '</table>';
	$out .= '</form>'."\n";
	$out .= '<div class="help_box" id="help_box'.($input['formname'] == 'edit' ? '_'.$input['id'] : '').'" style="width: 300px;">'."\n";
	$out .= '<h2>Hjälp</h2>'."\n";
	$out .= '<div id="content" style="margin-top: 8px;">Klicka på länkarna för att få reda på mer om varje fält</div>'."\n";
	$out .= '</div>'."\n";
	$out .= '<br style="clear:both;" />'."\n";
	return $out;
}

function open_search_help_list($what)
{
	$out = '';
	switch($what)
	{
		case 'Namn':
			$out .= 'Skriv rubriken till söklådan.<br />Denna kommer inte visas i söklådan utan endast på hamsterpaj.<br /><br /><strong>Max 40 tecken</strong>'."\n";
			break;
		case 'ShortName':
			$out .= 'En kort titel som identifierar söklådan.<br /><br /><strong>Max 16 tecken</strong>'."\n";
			break;
		case 'Description':
			$out .= 'En förklaring av söklådan.<br /><br /><strong>Max 1024 tecken</strong>'."\n";
			break;
		case 'Tags':
			$out .= 'Contains a set of words that are used as keywords to identify and categorize this search content. Tags must be a single word and are delimited by the space character (\' \').<br /><br /><strong>Max 255 tecken</strong><br /><br />Jag vet inte riktigt varför detta behövs men jag tänkte att nyckelord kan vara bra för hp :P'."\n";		
			break;
		case 'LongName':
			$out .= 'En längre förklaring av söklådan.<br /><br /><strong>Max 48 tecken</strong>'."\n";			
			break;
		case 'Link':
			$out .= 'Länk till sökningen som <strong>MÅSTE</strong> innehålla {Search}. Det är den som är sökordet i sökningen. Länken <strong>måste</strong> utgå från <strong>rooten</strong>.<br /><br />Ex.<br />/foo/bar.php?id={Search}&amp;extra=true<br /><br />blir<br />/foo/bar.php?id=1337&amp;extra=true (om sökning är 1337). <br /><br /><strong>Max 150 tecken</strong>'."\n";
			break;
		default:
			$out .= '<ul>'."\n";
			$out .= "\t".'<li><a href="?action=help&what=Namn">Namn</a></li>'."\n";
			$out .= "\t".'<li><a href="?action=help&what=ShortName">ShortName</a></li>'."\n";
			$out .= "\t".'<li><a href="?action=help&what=Description">Description</a></li>'."\n";
			$out .= "\t".'<li><a href="?action=help&what=Tags">Tags</a></li>'."\n";
			$out .= "\t".'<li><a href="?action=help&what=LongName">LongName</a></li>'."\n";
			$out .= "\t".'<li><a href="?action=help&what=Link">Link</a></li>'."\n";
			$out .= '</ul>'."\n";
			break;
	}
	return $out;
}

function open_search_add_box_execute($input, $options = null)
{
	$query = 'INSERT INTO open_search_boxes(name, ShortName, Description, Tags, LongName, link, timestamp) VALUES("'.$input['name'].'", "'.$input['ShortName'].'", "'.$input['Description'].'", "'.$input['Tags'].'", "'.$input['LongName'].'", "'.$input['Link'].'", '.time().')';
	mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

	$content = 'Söklådan har blivit tillagd!'."\n";
	$content .= '<br /><br />'."\n";
	$content .= 'Länk: <input type="text" value="http://hamsterpaj.net/open_search/#search_box_'.mysql_insert_id().'" /> <a href="/open_search/#search_box_'.mysql_insert_id().'">Öppna</a>'."\n";
	if($options['json_encode'] == true)
	{
		return json_encode(array('h2'=>'Tillagt!', 'content'=>$content));
	}
	else
	{
		$out = '<h2>Tillagt!</h2>'."\n";
		$out .= '<p>'.$content.'</p>'."\n";
		return $out;
	}
}

function open_search_edit_box_execute($input, $options = null)
{
	$id = intval($input['id']);
	$query = 'UPDATE open_search_boxes SET name = "'.$input['name'].'", ShortName = "'.$input['ShortName'].'", Description = "'.$input['Description'].'", Tags = "'.$input['Tags'].'", LongName = "'.$input['LongName'].'", Link = "'.$input['Link'].'" WHERE id = '.$id;
	mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

	$content = 'Söklådan har blivit uppdaterad!'."\n";
	$content .= '<br /><br />'."\n";
	$content .= 'Länk: <input type="text" value="http://hamsterpaj.net/open_search/#search_box_'.$id.'" /> <a href="/open_search/#search_box_'.$id.'">Öppna</a>'."\n";
	if($options['json_encode'] == true)
	{
		return json_encode(array('h2'=>'Tillagt!', 'content'=>$content));
	}
	else
	{
		$out = '<h2>Tillagt!</h2>'."\n";
		$out .= '<p>'.$content.'</p>'."\n";
		return $out;
	}
}

function open_search_edit_list($options = null)
{
	$query = 'SELECT * FROM open_search_boxes ORDER BY `timestamp` DESC';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	$out = '';
	if(mysql_num_rows($result) > 0)
	{
		$out = '<ul class="open_search_edit">'."\n";

		while($data = mysql_fetch_assoc($result))
		{
			$out .= rounded_corners_top(array('color'=>'blue_deluxe'), true);
			$out .= '<span style="clear:both;">&nbsp;</span>'."\n";
			$out .= "\t".'<li><h3><a href="#" class="box_link" id="link_'.$data['id'].'"><img src="'.IMAGE_URL.'plus.gif" alt="Expandera/Kollapsa" id="image_'.$data['id'].'" /></a> '.$data['name'].'</h3>';
			$out .= '<div class="open_search_box_info" id="box_'.$data['id'].'">'."\n";
			
			$data['formname'] = 'edit';
			$data['action'] = 'edit';
			$data['submit_value'] = 'Uppdatera';
			$out .= open_search_addbox_draw_input($data);
			
			$out .= '</div>';
			$out .= '</li>'."\n";
			$out .= '<span style="clear:both;">&nbsp;</span>'."\n";
			$out .= rounded_corners_bottom(array('color'=>'blue_deluxe'), true);
			
		}
		$out .= '</ul>'."\n";
	}
	
	return $out;
}
?>