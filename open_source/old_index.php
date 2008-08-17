<?php
	/* OPEN_SOURCE */
	
	$files[] = 'index.php';
	$files[] = 'open_source/readfile.php';
	$files[] = 'open_source/index.php';
	$files[] = 'open_source/standard.php';
	$files[] = 'diskussionsforum/index.php';
	$files[] = 'hamsterpaj/nytt.php';
	$files[] = 'include/libraries/discussion_forum.lib.php';
	$files[] = 'include/libraries/photos.lib.php';
	$files[] = 'include/ui-functions.php';
	$files[] = 'include/shared-functions.php';
	$files[] = 'include/libraries/profile.lib.php';
	$files[] = 'traffa/profile.php';
	$files[] = 'traffa/photos.php';
		
	sort($files);
	
	echo '<ul>' . "\n";
	foreach($files AS $file)
	{
		echo '<li><a href="/open_source/readfile.php?file=' . $file . '">' . $file . '</a> <a href="/open_source/readfile.php?download&file=' . $file . '">[DL]</a></li>' . "\n";
	}
	echo '</ul>' . "\n";

?>

<h3>Databas</h3>
<a href="http://217.118.208.249/phpmyadmin/">http://217.118.208.249/phpmyadmin/</a><br />
Användare: hp_structure<br />
Inget lösenord krävs