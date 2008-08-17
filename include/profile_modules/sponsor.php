<?php
echo '<div style="border: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['background'] . '; padding: 3px;">' . "\n";

$sponsor_handl = fopen(PATHS_INCLUDE . 'pageviews/sponsor/' . date('Y-m-d') . '.txt', 'a');
fwrite($sponsor_handl, x);
fclose($sponsor_handl);

$img_code = '<img src="' . IMAGE_URL . 'images/sponsorads/dunkenad.jpg" style="border: 1px solid #3f657a; float: left; width: 500px; margin-right: 3px;" />';
if ($_SESSION['sponsor_click'] != 1)
{
	echo '<a href="/heggan/sponsor.php" target="_blank">' . $img_code . '</a>';
}
else
{
	echo $img_code;
}

?>
<div style="height: 100%">
	<?php
	echo 'Visas på <strong>';
	include (PATHS_INCLUDE . 'sponsor.txt');
	echo '</strong> profiler<br /><strong>' . cute_number(filesize(PATHS_INCLUDE . 'pageviews/sponsor/' . date('Y-m-d') . '.txt')) . '</strong> visningar idag<br />';
	echo '<strong>';
	readfile(PATHS_INCLUDE . 'pageviews/sponsor/' . date('Y-m-d') . '_clicks.txt');
	echo '</strong> klick idag.<br />';

	?>
	<br />Att synas i rutan kostar 250:- per dygn, exl. moms. Maila johan<script>document.write('@');</script>rodent.se för med information.
</div>
	<br style="clear: both;" />
</div>
