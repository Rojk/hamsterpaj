<?php    

require('../include/core/common.php');
$ui_options['current_menu'] = 'chat';
ui_top($ui_options);

        echo '<div id="contentPostbox" style="text-align: center;">';
        echo '<a href="' . $_SERVER['PHP_SELF'] . '?view=today">Idag</a>&nbsp;&nbsp;';
        echo '<a href="' . $_SERVER['PHP_SELF'] . '?view=week">Denna vecka</a>&nbsp;&nbsp;';
        echo '<a href="' . $_SERVER['PHP_SELF'] . '?view=total">Totalt</a>&nbsp;&nbsp;';
        echo '</div>';


	if($_GET['view'] == 'today'){
		echo '<p class="title">Visar statistik för idag</p>';
	}
	elseif($_GET['view'] == 'week'){
		echo '<p class="title">Visar statistik för senaste veckan</p>';
	}
	elseif($_GET['view'] == 'total'){
		echo '<p class="title">Visar total statistik</p>';
	} else{
		echo '<p class="title">Chattstatistik</p>';
	}
	if($_GET['view'] == 'today'){
		echo '<iframe src="http://ved.hamsterpaj.net/chattodaystats.html" style="border: none; width: 750px; height: 2750px;"></iframe>';
	}
	elseif($_GET['view'] == 'week'){
		echo '<iframe src="http://ved.hamsterpaj.net/chatweekstats.html" style="border: none; width: 750px; height: 2750px;"></iframe>';
	}
	elseif($_GET['view'] == 'total'){
		echo '<iframe src="http://ved.hamsterpaj.net/chattotalstats.html" style="border: none; width: 750px; height: 2900px;"></iframe>';
	}
	else{
		echo 'Här kan du se chattaktiviteten för #chat på irc.hamsterpaj.net. Statistiken har genererats av Hamstern.<bR>';
		echo 'Välj ett av menyalternativen för att visa statistik.';
	}

	ui_bottom();
?>
