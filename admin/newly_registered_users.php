<?php
	include '../include/core/common.php';
	$ui_options['title'] = 'Nyregistrerade användare - Hamsterpaj.net';
	$ui_options['javascripts'][] = 'jquery.tooltip.js';
	$ui_options['javascripts'][] = 'jquery.dimensions.js';
	$ui_options['javascripts'][] = 'newly_registered_users.js';
	$ui_options['stylesheets'][] = 'jquery.tooltip.css';
	
	
	if (!is_privilegied('remove_user')) {
		die('ARRGH! AGH! AAAARH ARGHAAAA *GACK*<br /><a href="/">Hjälp, jag vill hem D:</a>');
	}
	
	$one_week_ago = time() - 60 * 60 * 24 * 7;
	
	if (is_numeric($_GET['offset'])) {
		$offset = (int)$_GET['offset'];
	} else {
		$offset = 0;
	}
	
	$query = 'SELECT l.id, l.regtimestamp, l.username, u.last_warning, l.quality_level, l.quality_level_expire, t.guestbook_entries, u.forum_posts';
	$query .= ' FROM login l, userinfo u, traffa t';
	$query .= ' WHERE l.id = t.userid AND l.id = u.userid AND l.regtimestamp > ' . $one_week_ago;
	$query .= ' ORDER BY l.regtimestamp DESC';
	$query .= ' LIMIT ' . $offset . ', ' . ($offset + 99) . '';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	$out = '<div style="font: 12px monospace;">
	<style>
	#ui_content * {
		cell-padding: 0;
		cell-spacing: 0;
		
		margin: 0;
		padding: 0;
	}
	</style>';
	
	if (is_numeric($_GET['offset']) && $_GET['offset'] > 99) {
		$out .= '<a href="?offset=' . ($_GET['offset'] - 100) . '">Framåt</a>' . "\n";
	} 
	if (is_numeric($_GET['offset'])) {
		$out .= '<a href="?offset=' . ($_GET['offset'] + 100) . '">Bakåt</a>' . "\n";
	} else {
		$out .= '<a href="?offset=100">Bakåt</a>' . "\n";
	}
	$out .= '' . "\n";
	
	$out .= '<table id="newly_registered_users" style="width: 638px;">
	<tr>
		<th>Registrerad</th>
		<th>Användarnamn</th>
		<th>Varnad?</th>
		<th>Read only?</th>
	</tr>
		' . "\n";
	while ($data = mysql_fetch_assoc($result)) {
		$out .= '<tr style="background: #FAFAFA;">' . "\n";
		$out .= '<td>' . date("Y.m.d H:i", $data['regtimestamp']) . '</td>' . "\n";
		$out .= '<td><a class="user_info" id="' . $data['id'] . '" href="/traffa/user_facts.php?user_id=' . $data['id'] . '">' . $data['username'] . '</a></td>' . "\n";
		$out .= '<span id="user_info_' . $data['id'] . '" style="display: none;">Foruminlägg: ' . $data['forum_posts'] . '<br />Gästboksinlägg: ' . $data['guestbook_entries'] . '</span>' . "\n";
		$out .= '<td>';
		if ($data['last_warning'] > 0) {
			$out .= '<a class="user_warning" id ="' . $data['id'] . '" style="color: red;" href="/admin/warnings.php?action=viewhistory&user_id=' . $data['id'] . '">Varnad</a>';
			$out .= '<span id="user_warning_' . $data['id'] . '" style="display: none;">' . fix_time($data['last_warning']) . '</span>';
		}
		else {
			$out .= '<span style="color: #999;">Neup.</span>';
		}
		$out .= '<td>';
		if ($data['quality_level_expire'] > time()) {
			$out .= '<a style="color: red;" class="user_read_only" id="' . $data['id'] . '" href="/admin/user_management.php?username=' . $data['username'] . '">Jao</a>';
			$out .= '<span id="user_read_only_' . $data['id'] . '" style="display: none;">';
			$out .= 'Level ' . $data['quality_level'] . ', går ut ' . fix_time($data['quality_level_expire']);
			$out .= '</span>';
		}
		else {
			$out .= '<span style="color: #999;">Nepp! :D</span>';
		}
		$out .= '</td>' . "\n";
		$out .= '</td>' . "\n";
		$out .= '</tr>' . "\n";
	}
	$out .= '</table>' . "\n";
	$out .= '</div>' . "\n";
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
		