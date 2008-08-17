<style>
	@import url('/stylesheets/flags-customize.css');
</style>
<script type="text/javascript" language="javascript" src="/javascripts/flags-customize.js"></script>

<h1>Här kan du välja vilka flaggor du vill ha på din presentation</h1>

<p>
	Flaggor visar snabbt vad du tycker och vem du är. Du kan hitta andra som har samma flaggor
	som dig, och andra kan hitta dig med hjälp av flaggorna.
</p>
<div id="flags_customize">

<?php
	$categories['politics']['label'] = 'Politik';
	$categories['countries']['label'] = 'Länder';	
	$categories['lifestyle']['label'] = 'Livsstil';
	$categories['religion']['label'] = 'Religion';
	$categories['sports']['label'] = 'Sporter';
	
	$query = 'SELECT ufl.handle FROM user_flags_list AS ufl, user_flags AS uf WHERE uf.user = "' . $_SESSION['login']['id'] . '" AND ufl.id = uf.flag';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	while($data = mysql_fetch_assoc($result))
	{
		$user_flags[] = $data['handle'];
	}

	echo '<ul id="flags_customize_navigation">' . "\n";
	foreach($categories AS $handle => $category)
	{
		echo '	<li id="flags_nav_' . $handle . '" class="' . $handle . '">' . $category['label'] . '</li>' . "\n";
	}
	echo '</ul>' . "\n";
	

	
	foreach($categories AS $handle => $category)
	{
		$style = ($i == 0) ? ' style="display: block;"' : '';
		echo '<div id="flags_form_' . $handle . '"' . $style . ' class="flags_customize_category">' . "\n";
		echo '<form action="javascript: flags_customize_submit(\'flags_form_' . $handle . '\');">' . "\n";
		echo '<h2>' . $category['label'] . '</h2>' . "\n";

		$current_group = '';
		$query = 'SELECT handle, label, `group`, category FROM user_flags_list WHERE category = "' . $handle . '" ORDER BY `group`, label ASC';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		for($i = 0; $data = mysql_fetch_assoc($result); $i++)
		{
			if(strlen($current_group) > 0 && $data['group'] != $current_group)
			{
				$current_group = '';
				echo '	</ul>' . "\n";
				echo '</fieldset>' . "\n";
			}
			
			if(strlen($data['group']) > 0 && $data['group'] != $current_group)
			{
				$current_group = $data['group'];
				echo '<fieldset>' . "\n";
				echo '	<legend>' . $data['group'] . '</legend>' . "\n";
				echo '		<ul>' . "\n";
				echo '		<li><input type="radio" name="' . $data['group'] . '" value="none" />Ingen</li>' . "\n";
			}
			
			$checked = (in_array($data['handle'], $user_flags)) ? ' checked="checked"' : '';
			
			if(strlen($data['group']) > 0)
			{
				echo '		<li>' . "\n";
				echo '			<input type="radio" name="' . $data['group'] . '" value="' . $data['handle'] . '" id="input_' . $data['group'] . '_' . $data['handle'] . '"' . $checked . ' />' . "\n";
				echo '			<label for="input_' . $data['group'] . '_' . $data['handle'] . '">' . $data['label'] . '</label>' . "\n";
				echo '		</li>' . "\n";
			}
			else
			{
				echo '		<input type="checkbox" name="' . $data['handle'] . '" value="enable" id="input_' . $data['handle'] . '"' . $checked . ' />' . "\n";
				echo '		<label for="input_' . $data['handle'] . '">' . $data['label'] . '</label><br />' . "\n";
			}
		}
		echo '<input type="submit" value="Spara" />' . "\n";
		echo '</form>' . "\n";
		echo '</div>' . "\n";
	}

?>
	<br style="clear: both;" />
	<div id="flags_customize_message" style="color: red; font-weight: bold;"></div>
</div>