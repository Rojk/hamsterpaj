<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'open_source.lib.php');
  /* OPEN_SOURCE */

	$file_structure = array(
		'open_source' => array(
			'readfile.php',
			'index.php',
			'standard.php'
		),
		
		'include' => array(
			'libraries' => array(
				'discussion_forum.lib.php',
				'photos.lib.php',
				'profile.lib.php'
			),
			
			'ui-functions.php',
			'shared-functions.php'
		),
		
		'hamsterpaj' => array(
			'nytt.php'
		),
		
		'diskussionsforum' => array(
			'index.php'
		),
		
		'traffa' => array(
			'profile.php',
			'photos.php'
		),
		
		'index.php'
	);
	
  $open_source_config['open_source_menu_path'] = 'start';
  $open_source_config['title'] = 'Open source-startsidan på Hamsterpaj.net';
	open_source_top($open_source_config);
  
  echo '<h2>Open Source?</h2>' . "\n";
  
  echo '<h3>Vad är open source?</h3>' . "\n";
	echo '<p>Vad open source är kan du läsa mer om på <a href="http://opensource.org/docs/osd">opensource.org</a>, eller om du vill ha svenska kan du läsa mer på <a href="http://sv.wikipedia.org/wiki/%C3%96ppen_k%C3%A4llkod">Wikipedia</a>.</p>' . "\n";

	echo '<br />' . "\n";
	
  echo '<h3>Varför släpper Hamsterpaj sin källkod?</h3>' . "\n";
  echo '<p>Vi har idag ett härke med kod, olika standarder, olika versioner och säkert tre forum som ligger "bakom varandra". Inloggningen är tung och väldigt mycket lappat och lagat över åren.</p>' . "\n";
	echo '<p>Idag är det bara jag som jobbar heltid, även om jag fortfarande mest ser sajten som ett hobbyprojekt. Det är jag och Joel som knackar koden, Alexander orkar aldrig göra något.';
	echo ' <a href="http://www.hamsterpaj.net/diskussionsforum/hamsterpaj/open_source/sida_1.php">Läs mer...</a></p>' . "\n";
  
  echo '<br />' . "\n";
  
  echo '<h2>Koda själv</h2>' . "\n";
  echo '<p>' . "\n";
  echo '<a href="http://www.hamsterpaj.net/open_source/site_2008_04_16.zip">'
     . '<img src="http://images.hamsterpaj.net/open_source/download_hamsterpaj.png" alt="En Hamsterpaj på 2.3MB" />'
     . '</a>' . "\n";
  echo '</p>' . "\n";
  
  echo '<br />' . "\n";
  
  echo '<h3>Släppta koder</h3>' . "\n";
	echo '<div id="open_source_file_structure">' . "\n";
	echo '<h3>/storage/www/www.hamsterpaj.net/data/</h3>' . "\n";
	echo open_source_get_file_tree($file_structure);
	echo '</div>' . "\n";

	echo '<br />' . "\n";

	echo '<h3>Databas</h3>' . "\n";
	echo '<p>' . "\n";
	echo '<a href="http://217.118.208.249/phpmyadmin/">http://217.118.208.249/phpmyadmin/</a><br />' . "\n";
	echo 'Användare: hp_structure<br />' . "\n";
	echo 'Inget lösenord krävs' . "\n";
	echo '</p>' . "\n";
	
	echo '<br />' . "\n";
	
	echo '<h3>När du är klar</h3>' . "\n";
	echo '<p>' . "\n";
	echo 'När du pillat färdigt lägger du upp koden i <a href="/diskussionsforum/hamsterpaj/open_source/">Open Source-forumet</a>.' . "\n";
	echo '</p>' . "\n";

	open_source_bottom($open_source_config);
?>