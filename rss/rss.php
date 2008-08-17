<?php
require('../include/core/common.php');
/*
	No params
*/
function rss_now()
{
	return date("r");
}

/*
	@ param: int 'uid' - the userid OR username of the user.
*/
function rss_get_userinfo($param)
{
$out = array();

	if(isset($param['uid']))
	{
		if(is_numeric($param['uid']))
		{
			$where = 'l.id = '.$param['uid'].' AND is_removed = 0 LIMIT 1';
			$out['numeric'] = true;
		}
		else
		{
			$where = 'l.username = "'.$param['uid'].'" AND is_removed = 0 LIMIT 1';
			$out['numeric'] = false;
		}
	
		$query = 'SELECT l.id, l.username, u.gbrss FROM login AS l LEFT JOIN userinfo AS u ON u.userid = l.id WHERE '.$where;
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

		if(mysql_num_rows($result) != 1)
		{
			$err['errors'][] = 'Hittade inte användaren!';
		}
		else
		{
			$data = mysql_fetch_assoc($result);
			$out['username'] = $data['username'];
			$out['uid'] = $data['id'];
			$out['gbrss'] = ($data['gbrss'] == 1 ? true : false);
			$out['link'] = ($out['numeric'] ? $out['uid'] : $out['username']);
		}
	}
	else
	{
		$err['errors'][] = 'UID finns inte!';
	}

	return(array('errors'=>$err['errors'], 'username'=>$out['username'], 'uid'=>$out['uid'], 'gbrss'=>$out['gbrss'], 'link'=>$out['link'], 'numeric'=>$out['numeric']));
}

/*
	@ param: string 'username' - the username
	@ param: string/int 'link' - the username or uid depending on the url
*/
function rss_create_top($param)
{
	return '<?xml version="1.0" encoding="utf-8"?>
	<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<atom:link href="http://www.hamsterpaj.net/rss/'.$param['link'].'" rel="self" type="application/rss+xml" />
		<title>'.rtrim($param['username'], 's').'s gästbok - Hamsterpaj.net RSS</title>
		<link>http://www.hamsterpaj.net/rss/'.$param['link'].'</link>
		<description>De senaste gästboks inläggen skriva till '.$param['username'].'</description>
		<language>se-sv</language>
		<pubDate>'.rss_now().'</pubDate>
		<lastBuildDate>'.rss_now().'</lastBuildDate>
    	<docs>http://blogs.law.harvard.edu/tech/rss</docs>
';
}

/*
	No params
*/
function rss_create_bottom()
{
	return '
	</channel>
</rss>';
}

/*
	@ param: int 'uid' - the user's userid.
	@ param: int 'limit' - how many posts to display
	
*/
function rss_create_items($param)
{ 
	$query = 'SELECT l.username AS sender_name, gb.message, gb.timestamp AS sent, gb.id
FROM traffa_guestbooks AS gb, login AS l
WHERE gb.recipient = '.$param['uid'].' AND gb.is_private = 0 AND gb.deleted = 0 AND l.id = gb.sender AND gb.sender != 2348
ORDER BY gb.timestamp DESC
LIMIT '.$param['limit'];
	$res = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

	if(mysql_num_rows($res) > 0)
	{
		while($data = mysql_fetch_assoc($res))
		{
			$data['sent'] = date('r', $data['sent']);
			$out .= "\t".'<item>'."\n";
			$out .= "\t\t".'<title><![CDATA['.(strlen($data['message']) > 37 ? substr($data['message'], 0, 40).'...' : $data['message']).']]></title>'."\n"; //title
			$out .= "\t\t".'<link>http://www.hamsterpaj.net/traffa/guestbook.php?view='.$param['uid'].'#guestbook_entry_'.$data['id'].'</link>'."\n";//link
			$out .= "\t\t".'<description><![CDATA['.(strlen($data['message']) > 150 ? substr($data['message'], 0, 147).'...' : $data['message']).']]></description>'."\n";//description
			$out .= "\t\t".'<author>'.$data['sender_name'].'</author>'."\n";//author
			$out .= "\t\t".'<pubDate>'.$data['sent'].'</pubDate>'."\n";//date when sent
			$out .= '<guid>http://www.hamsterpaj.net/traffa/guestbook.php?view='.$param['uid'].'#guestbook_entry_'.$data['id'].'</guid>';
			$out .= "\t".'</item>'."\n\n";
		}
	}
	
	return $out;
}

$userinfo = rss_get_userinfo(array('uid'=>$_GET['uid']));
if(empty($userinfo['errors']))
{
	if($userinfo['gbrss'])
	{
		if(strstr($_SERVER['HTTP_USER_AGENT'], 'Mozilla') !== false)
		{
			header('Content-Type: text/xml');
		}
		else
		{
			header('Content-Type: application/rss+xml');
		}
		echo rss_create_top(array('username'=>$userinfo['username'], 'link'=>$userinfo['link']));
		echo rss_create_items(array('uid'=>$userinfo['uid'], 'limit'=>15));
		echo rss_create_bottom();			
	}
	else
	{
		echo '<p>Användaren vill inte att du ska snoka i sin gästbok. Kolla in <a href="/rss/Johan">Johans</a> istället.</p>';
	}
}
else
{
	echo '<ul>';
	foreach($userinfo['errors'] as $error)
	{
		echo '<li>'.$error.'</li>';
	}
	echo '</ul>';
}
?>
