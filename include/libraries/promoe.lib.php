<?php
function promoe_thumbs_list($heading, $promoes)
{
	$return = '<fieldset class="promoe_thumbs">' . "\n";
	$return .= '<legend>' . $heading . '</legend>' . "\n";
	
	foreach($promoes AS $promoe)
	{
		$return .= '<div>' . "\n";
		$return .= '<span class="time">' . date('Y-m-d H:i', $promoe['date']) . '</span>' . "\n";
		$return .= '<a href="/annat/promoe.php?view=' . $promoe['id'] . '">' . "\n";
		if(isset($promoe['imagestring']))
		{
			$return .= '<img src="/annat/promoe_png.php?imagestring=' . $promoe['imagestring'] . '" />' . "\n";	
		}
		else
		{
			$return .= '<img src="/annat/promoe_png.php?id=' . $promoe['id'] . '" />' . "\n";	
		}
		$return .= '</a>' . "\n";
		$return .= '<a href="/traffa/profile.php?id=' . $promoe['author_id'] . '">' . $promoe['author_username'] . '</a>' . "\n";
		$return .= '</div>' . "\n\n";
	}
	$return .= '</fieldset>' . "\n";

	return $return;
}

function promoe_paintboard($promoe = null)
{
	if(isset($promoe) && login_checklogin())
	{
		$query = 'SELECT user FROM promoe_hypes WHERE user = "' . $_SESSION['login']['id'] . '" AND promoe = "' . $promoe['id'] . '" LIMIT 1';
		$result = mysql_query($query);
		if(mysql_num_rows($result) == 0)
		{
			echo '<input type="button" class="button" id="promoe_hype_button" value="Hypa" />' . "\n";
		}
		else
		{
			echo '<span class="promoe_already_hyped">Du har redan hypat denna Promoe</span>' . "\n";
		}
	}
	
	$heading = (isset($promoe)) ? $promoe['description'] . ' ritad av <a href="/traffa/profile.php?id=' . $promoe['author_id'] . '">' . $promoe['author_username'] . '</a>' : 'Rita en ny Promoe';

	echo '<h1>' . $heading . '</h1>' . "\n";
?>
	<div class="promoe_editor">
	<div class="left">
		<div id="promoe_paintboard">
			<h2>Bilden laddas</h2>
			<p>
				Det kan ta några sekunder att ladda bilden, den laddas inte förrens resten av sidan har laddat klart.
			</p>
		</div>
		
		<div class="promoe_save_restart">
			<input type="button" value="Rita en ny bild" id="promoe_restart_button" />
			<input type="button" value="Spara bilden" id="promoe_save_button" />
			<input type="button" value="Förhandsgranska" id="promoe_preview_button" />
		</div>
	</div>
	
	<div class="right">
		<div class="promoe_colors">
			<h3>Färgväljare</h3>
<?php
	include(PATHS_INCLUDE . 'configs/promoe.conf.php');

	foreach($promoe_colors AS $color)
	{
		echo '<div id="promoe_color_' . substr($color, 1) . '" style="background: ' . $color . '" class="promoe_color">&nbsp;</div>' . "\n";
	}
	
?>
		</div>
		
		<div class="promoe_drawing_mode">
			<div class="promoe_drawing_mode_pen">
				<input type="radio" value="Pennan" id="promoe_drawing_mode_pen" checked="checked" name="promoe_painting_mode" />
				<label for="promoe_drawing_mode_pen">Pennan</label>
			</div>
						
			<input type="radio" value="flood_fill" id="promoe_drawing_mode_flood_fill" name="promoe_painting_mode" />
			<label for="promoe_drawing_mode_flood_fill">Färghinken</label>
		</div>
		
		<div class="promoe_grid_control">
			<input type="button" value="Visa rutnätet" id="promoe_grid_control" />
		</div>
		
	</div>
</div>

<div id="promoe_preview">
</div>

<?php
echo '<script>' . "\n";
echo 'var imagestring = \'' . $promoe['imagestring'] . '\';' . "\n";
$parent = ($promoe['parent'] > 0) ? $promoe['parent'] : $promoe['id'];
echo 'var promoe_parent = \'' . $parent . '\';' . "\n";
echo 'var promoe_id = \'' . $promoe['id'] . '\'' . "\n";
echo '</script>' . "\n";

}

?>