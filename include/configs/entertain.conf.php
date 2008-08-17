<?php
/* For all the stuff below we use the following convention:
	handle			a handle in english used only in code and database
	url_handle		an url secure handle in swedish (or other language) used in urls
	label			a label in swedish (with initial capital if there is no label_capitol present)
	label_capitol	a label in swedish with initial capitol
	label_plural	a label in plural
*/

//	$entertain_lists = array('toplist', 'favorites', 
	
	/* Entertain har följande avdelningar
		handle		förklaring				typ av data/lagring
		-------		-----------				--------------------
		game		spel					fil i distribute
		clip		filmklipp				fil i distribute
		flash		flashfilmer				fil i distribute
		prank		busringningar			fil i distribute
		music		(gratis) musik			fil i distribute
		image		roliga bilder			fil på images
		background	bakgrundsbilder			filer (i olika upplösning) på images
		software	program					länk i beskrivningen

		'game', 'clip', 'flash', 'prank', 'music', 'image', 'background', 'software'
	*/

	define(ENTERTAIN_ADMIN_LEVEL, 4);

	$entertain_adtoma_categories['clip']			= 'amuse.movies';
	$entertain_adtoma_categories['game']			= 'amuse.games';
	$entertain_adtoma_categories['flash']			= 'amuse.flash';
	$entertain_adtoma_categories['images']			= 'amuse.images';

	$entertain_types['game']['title']				= 'ett onlinespel';
	$entertain_types['game']['label']				= 'onlinespel';
	$entertain_types['game']['label_plural']		= 'spel';
	$entertain_types['game']['label_capitol']		= 'Spel';
	$entertain_types['game']['handle']				= 'game';
	$entertain_types['game']['url_handle']			= 'onlinespel';
	$entertain_types['game']['views']				= array('toplist', 'favorites', 'search');
	$entertain_types['game']['default_list_style']			= 'thumbnails';

	$entertain_types['clip']['title']				= 'ett filmklipp';
	$entertain_types['clip']['label']				= 'filmklipp';
	$entertain_types['clip']['label_plural']		= 'filmklipp';
	$entertain_types['clip']['label_capitol']		= 'Filmklipp';
	$entertain_types['clip']['handle']				= 'clip';
	$entertain_types['clip']['url_handle']			= 'filmklipp';
	$entertain_types['clip']['views']				= array('toplist', 'favorites', 'search');
	$entertain_types['clip']['default_list_style']			= 'thumbnails';
	
	$entertain_types['flash']['title']				= 'en flashfilm';
	$entertain_types['flash']['label']				= 'flashfilm';
	$entertain_types['flash']['label_plural']		= 'flashfilmer';
	$entertain_types['flash']['label_capitol']		= 'Flashfilmer';
	$entertain_types['flash']['handle']				= 'flash';
	$entertain_types['flash']['url_handle']			= 'flashfilmer';
	$entertain_types['flash']['views']				= array('toplist', 'favorites', 'search');
	$entertain_types['flash']['default_list_style']			= 'thumbnails';
	
	$entertain_types['prank']['title']				= 'en busringning';
	$entertain_types['prank']['label']				= 'busringning';
	$entertain_types['prank']['label_plural']		= 'busringningar';
	$entertain_types['prank']['label_capitol']		= 'Busringningar';
	$entertain_types['prank']['handle']				= 'prank';
	$entertain_types['prank']['url_handle']			= 'busringning';
	$entertain_types['prank']['views']				= array('toplist', 'favorites', 'search');

	$entertain_types['music']['title']				= 'musik';
	$entertain_types['music']['label']				= 'musik';
	$entertain_types['music']['label_plural']		= 'musik';
	$entertain_types['music']['label_capitol']		= 'Musik';
	$entertain_types['music']['handle']				= 'music';
	$entertain_types['music']['url_handle']			= 'musik';
	$entertain_types['music']['views']				= array('toplist', 'favorites', 'search');

	$entertain_types['image']['title']				= 'en rolig bild';
	$entertain_types['image']['label']				= 'rolig bild';
	$entertain_types['image']['label_plural']		= 'roliga bilder';
	$entertain_types['image']['label_capitol']		= 'Roliga bilder';
	$entertain_types['image']['handle']				= 'image';
	$entertain_types['image']['url_handle']			= 'roliga_bilder';
	$entertain_types['image']['views']				= array('toplist', 'favorites', 'search');
	$entertain_types['image']['default_list_style']			= 'thumbnails';

	$entertain_types['background']['label']			= 'en bakgrundsbild';
	$entertain_types['background']['label']			= 'bakgrund';
	$entertain_types['background']['label_plural']	= 'bakgrunder';
	$entertain_types['background']['label_capitol'] = 'Bakgrunder';
	$entertain_types['background']['handle']		= 'background';
	$entertain_types['background']['url_handle']	= 'bakgrund';
	$entertain_types['background']['views']			= array('toplist', 'favorites', 'search');

	$entertain_types['software']['label']			= 'ett program';
	$entertain_types['software']['label']			= 'program';
	$entertain_types['software']['label_plural']	= 'program';
	$entertain_types['software']['label_capitol']	= 'Program';
	$entertain_types['software']['handle']			= 'software';
	$entertain_types['software']['url_handle']		= 'program';
	$entertain_types['software']['views']			= array('toplist', 'search');
	$entertain_types['software']['default_list_style']			= 'full';

	$entertain_lists['toplist']['handle']		= 'toplist';
	$entertain_lists['toplist']['url_handle']	= 'topplistan';
	$entertain_lists['toplist']['label']		= 'Topplistan';
	$entertain_list_handles['topplistan']		= 'toplist';
	$entertain_lists['favorites']['handle']		= 'favorites';
	$entertain_lists['favorites']['url_handle']	= 'favoriter';
	$entertain_lists['favorites']['label']		= 'Favoriter';
	$entertain_list_handles['favoriter']		= 'favorites';
	$entertain_lists['search']['handle']		= 'search';
	$entertain_lists['search']['url_handle']	= 'blaeddra';
	$entertain_lists['search']['label']			= 'Bläddra';
	$entertain_list_handles['blaeddra']			= 'search';
	
	$entertain_list_styles['titles']['items_per_page']		= null;
	$entertain_list_styles['thumbnails']['items_per_page']	= 40;
	$entertain_list_styles['half']['items_per_page']		= 30;
	$entertain_list_styles['full']['items_per_page']		= 20;

		$entertain_categories[12]['handle'] = 'biotrailers';
	$entertain_categories[12]['title'] = 'Biotrailers';
	$entertain_categories[0]['handle'] = 'vaelgjort';
	$entertain_categories[0]['title'] = 'Välgjort';
	$entertain_categories[1]['handle'] = 'musikvideos';
	$entertain_categories[1]['title'] = 'Musikvideos';
	$entertain_categories[2]['handle'] = 'foeljtetaanger';
	$entertain_categories[2]['title'] = 'Följtetånger';
	$entertain_categories[3]['handle'] = 'reklamfilmer';
	$entertain_categories[3]['title'] = 'Reklamfilmer';
	$entertain_categories[4]['handle'] = 'olyckor';
	$entertain_categories[4]['title'] = 'Olyckor';
	$entertain_categories[5]['handle'] = 'djur';
	$entertain_categories[5]['title'] = 'Djur';
	$entertain_categories[6]['handle'] = 'coola_grejor';
	$entertain_categories[6]['title'] = 'Coola grejor';
	$entertain_categories[7]['handle'] = 'hamsterpajs_egna';
	$entertain_categories[7]['title'] = 'Hamsterpajs egna';
	$entertain_categories[8]['handle'] = 'oeversaettningar';
	$entertain_categories[8]['title'] = 'Översättningar';
	$entertain_categories[9]['handle'] = 'osorterat';
	$entertain_categories[9]['title'] = 'Osorterat';
	$entertain_categories[10]['handle'] = 'slowmotion';
	$entertain_categories[10]['title'] = 'Slowmotion';
	$entertain_categories[11]['handle'] = 'snabbspolat';
	$entertain_categories[11]['title'] = 'Snabbspolat';

	$entertain_categories[13]['handle'] = 'dolda_kameran';
	$entertain_categories[13]['title'] = 'Dolda kameran';
	
	$entertain_categories[14]['handle'] = 'data_it';
	$entertain_categories[14]['title'] = 'Data/IT';
	$entertain_categories[15]['handle'] = 'djur';
	$entertain_categories[15]['title'] = 'Djur';
	$entertain_categories[16]['handle'] = 'tjockisar';
	$entertain_categories[16]['title'] = 'Tjockisar';
	$entertain_categories[17]['handle'] = 'amerikaner';
	$entertain_categories[17]['title'] = 'Amerikaner';
	$entertain_categories[18]['handle'] = 'kaendisar';
	$entertain_categories[18]['title'] = 'Kändisar';
	$entertain_categories[19]['handle'] = 'bush';
	$entertain_categories[19]['title'] = 'George Bush';
	$entertain_categories[20]['handle'] = 'tecknade_bilder';
	$entertain_categories[20]['title'] = 'Tecknade bilder';
	$entertain_categories[21]['handle'] = 'skyltar';
	$entertain_categories[21]['title'] = 'Skyltar';

	$entertain_categories[22]['title'] =  'Turspel';
	$entertain_categories[22]['handle'] = 'turspel';
	$entertain_categories[23]['title'] =  'Simpla spel';
	$entertain_categories[23]['handle'] = 'simpla_spel';
	$entertain_categories[24]['title'] =  'Skjutspel';
	$entertain_categories[24]['handle'] = 'skjutspel';
	$entertain_categories[25]['title'] =  'Sportspel';
	$entertain_categories[25]['handle'] = 'sportspel';
	$entertain_categories[26]['title'] =  'Strategispel';
	$entertain_categories[26]['handle'] = 'strategispel';
	$entertain_categories[27]['title'] =  'Problemlösningsspel';
	$entertain_categories[27]['handle'] = 'problemloesningsspel';
	$entertain_categories[28]['title'] =  'Tidspress';
	$entertain_categories[28]['handle'] = 'tidspress';
	$entertain_categories[29]['title'] =  'Racingspel';
	$entertain_categories[29]['handle'] = 'racingspel';
	$entertain_categories[30]['title'] =  'Multiplayerspel';
	$entertain_categories[30]['handle'] = 'multiplayerspel';
	$entertain_categories[31]['title'] =  'Plattformsspel';
	$entertain_categories[31]['handle'] = 'plattformsspel';
	$entertain_categories[32]['title'] =  'Tajmingspel';
	$entertain_categories[32]['handle'] = 'tajmingspel';
	$entertain_categories[33]['title'] =  'Crews favoritspel';
	$entertain_categories[33]['handle'] = 'crews_favoritspel';
	$entertain_categories[34]['title'] =  'Barnförbjudna spel';
	$entertain_categories[34]['handle'] = 'barnfoerbjudna_spel';
	$entertain_categories[35]['title'] =  'Pusselspel';
	$entertain_categories[35]['handle'] = 'pusselspel';
	$entertain_categories[36]['title'] =  'Klicka';
	$entertain_categories[36]['handle'] = 'klicka';
	$entertain_categories[37]['title'] =  'Klassiker';
	$entertain_categories[37]['handle'] = 'klassiker';
	$entertain_categories[38]['title'] =  'Precision';
	$entertain_categories[38]['handle'] = 'precision';
	$entertain_categories[39]['title'] =  'Fightingspel';
	$entertain_categories[39]['handle'] = 'fightingspel';
	$entertain_categories[40]['title'] =  'Labyrintspel';
	$entertain_categories[40]['handle'] = 'labyrintspel';
	$entertain_categories[41]['title'] =  'Flipperspel';
	$entertain_categories[41]['handle'] = 'flipperspel';
	$entertain_categories[42]['title'] =  'Rollspel';
	$entertain_categories[42]['handle'] = 'rollspel';
	$entertain_categories[43]['title'] =  'Highscore';
	$entertain_categories[43]['handle'] = 'highscore';
	
	$entertain_categories[44]['title'] =  'Webbläsare';
	$entertain_categories[44]['handle'] = 'webbläsare';
	$entertain_categories[45]['title'] =  'Chattprogram';
	$entertain_categories[45]['handle'] = 'chat';
	$entertain_categories[46]['title'] =  'Bildredigering';
	$entertain_categories[46]['handle'] = 'bildredigering';
	$entertain_categories[47]['title'] =  'Spel';
	$entertain_categories[47]['handle'] = 'spel';
	
	// More images...
	$entertain_categories[48]['title'] =  'Japaner';
	$entertain_categories[48]['handle'] = 'japaner';
	$entertain_categories[49]['title'] =  'Barn';
	$entertain_categories[49]['handle'] = 'barn';
	
	foreach($entertain_categories as $id => $category)
	{
		$entertain_category_ids[$category['handle']] = $id;
	}

	$entertain_type_categories['game'] = array(9,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43);
	$entertain_type_categories['clip'] = array(1, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13);
	$entertain_type_categories['image'] = array(14, 15, 16, 17, 18, 19, 20, 21, 9, 48, 49);
	$entertain_type_categories['flash'] = array(0, 1, 2, 9);
	$entertain_type_categories['software'] = array(44, 45, 47, 46, 9);
	
	$entertain_type_categories['prank'] = array(9);
	$entertain_type_categories['music'] = array(9);
	$entertain_type_categories['background'] = array(9);
?>