<?php
	include '../include/core/common.php';
	$ui_options['title'] = 'Nyregistrerade användare - Hamsterpaj.net';
	
	if (!is_privilegied('remove_user')) {
		die('ARRGH! AGH! AAAARH ARGHAAAA *GACK*<br /><a href="/">Hjälp, jag vill hem D:</a>');
	}
	
	$one_week_ago = time() - 60 * 60 * 24 * 7;
	
	$query = 'SELECT l.id, l.username, u.last_warning, l.regtimestamp FROM login l, userinfo u WHERE l.id = u.userid AND l.regtimestamp > ' . $one_week_ago . ' ORDER BY l.regtimestamp DESC';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	$out = '<div style="font: 12px monospace;">
	<table style="width: 638px;">
	<tr>
		<th>Registrerad</th>
		<th>Användarnamn</th>
		<th>Varnad?</th>
	</tr>
		' . "\n";
	while ($data = mysql_fetch_assoc($result)) {
		$out .= '<tr style="background: #FAFAFA;">' . "\n";
		$out .= '<td>' . date("Y.m.d H:i", $data['regtimestamp']) . '</td>' . "\n";
		$out .= '<td><a href="/traffa/user_facts.php?user_id=' . $data['id'] . '">' . $data['username'] . '</a></td>' . "\n";
		$out .= '<td>';
		if ($data['last_warning'] > 0) {
			$out .= '<a title="' . fix_time($data['last_warning']) . '" style="color: red;" href="/admin/warnings.php?action=viewhistory&user_id=' . $data['id'] . '">Varnad</a>';
		}
		else {
			$out .= '<span style="color: #999;">Neup.</span>';
		}
		$out .= '</td>' . "\n";
		$out .= '</tr>' . "\n";
	}
	$out .= '</table>' . "\n";
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
		