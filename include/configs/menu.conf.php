<?php
	$menu['hamsterpaj'] = array('label' => 'Hamsterpaj', 'url' => '/', 'index_label' => 'Förstasidan');
		$menu['hamsterpaj']['children']['nytt'] = array('label' => 'Senaste nytt', 'url' => '/hamsterpaj/nytt.php');
		$menu['hamsterpaj']['children']['utvecklarblogg'] = array('label' => 'Utvecklarblogg', 'url' => '/hamsterpaj/utvecklarblogg.php');
		$menu['hamsterpaj']['children']['om_hamsterpaj'] = array('label' => 'Om Hamsterpaj', 'url' => '/hamsterpaj/about.php');
		$menu['hamsterpaj']['children']['crew'] = array('label' => 'Vi som gör sidan', 'url' => '/hamsterpaj/crew.php');
		$menu['hamsterpaj']['children']['pedofilpolicy'] = array('label' => 'Pedofilpolicy', 'url' => '/hamsterpaj/pedofilpolicy.php');
		$menu['hamsterpaj']['children']['foerslag'] = array('label' => 'Förslag', 'url' => '/hamsterpaj/suggestions.php');
		$menu['hamsterpaj']['children']['rules_and_policies'] = array('label' => 'Regler och policies', 'url' => '/hamsterpaj/rules_and_policies.php');
		$menu['hamsterpaj']['children']['tillbakablickar'] = array('label' => 'Tillbakablickar', 'url' => '/hamsterpaj/tillbakablickar.php');

	$menu['game'] = array('label' => 'game', 'url' => '');
	$menu['image'] = array('label' => 'image', 'url' => '');
	$menu['clip'] = array('label' => 'clip', 'url' => '');
	$menu['flash'] = array('label' => 'flash', 'url' => '');

	$menu['forum'] = array('label' => 'Forum', 'url' => '/diskussionsforum/', 'index_label' => 'Översikt');
		if(login_checklogin())
		{
			$menu['forum']['children']['notices'] = array('label' => 'Dina notiser (' . $_SESSION['forum']['new_notices'] . ')', 'url' => '/diskussionsforum/notiser.php');
			$menu['forum']['children']['user_threads'] = array('label' => 'Dina trådar', 'url' => '/diskussionsforum/dina_traadar.php');
		}
		$menu['forum']['children']['new_threads'] = array('label' => 'Nya trådar', 'url' => '/diskussionsforum/nya_traadar.php');
		$menu['forum']['children']['search'] = array('label' => 'Sök', 'url' => '/diskussionsforum/soek.php');
		$menu['forum']['children']['rules'] = array('label' => 'Regler', 'url' => '/hamsterpaj/rules_and_policies.php');
	
	$menu['sex_sense'] = array('label' => 'Sex &amp; sinne', 'url' => '/sex_och_sinne/', 'index_label' => 'Start');
		$menu['sex_sense']['children']['latest'] = array('label' => 'Senaste frågorna', 'url' => '/sex_och_sinne/senaste_fraagorna.html');
		$menu['sex_sense']['children']['view_category'] = array('label' => 'Kategorier', 'url' => '/sex_och_sinne/kategorier.html');
		$menu['sex_sense']['children']['search'] = array('label' => 'Sök', 'url' => '/sex_och_sinne/soek.php');
		$menu['sex_sense']['children']['sexpretterna'] = array('label' => 'Sexperterna', 'url' => '/sex_och_sinne/sexperterna.php');
		if(login_checklogin())
		{
			$menu['sex_sense']['children']['question'] = array('label' => 'Ställ en fråga', 'url' => '/sex_och_sinne/ny_fraaga.html');
		}
		$menu['sex_sense']['children']['new_questions'] = array('label' => 'Admin', 'url' => '/sex_och_sinne/admin.php', 'is_privilegied' => 'sex_sense_admin');

	$menu['mattan'] = array('label' => 'Under mattan', 'url' => '/mattan/');
		$menu['mattan']['children']['alfabetet_paa_tid'] = array('label' => 'Alfabetet på tid', 'url' => '/mattan/alfabetet_paa_tid.php');
		$menu['mattan']['children']['ascii_art'] = array('label' => 'ASCII-art', 'url' => '/mattan/ascii_art.php');
		$menu['mattan']['children']['bakgrundsbilder'] = array('label' => 'Bakgrundsbilder', 'url' => '/mattan/bakgrundsbilder.php');
		$menu['mattan']['children']['ditt_namn'] = array('label' => 'Ditt namn', 'url' => '/mattan/ditt_namn.php');
		$menu['mattan']['children']['personnummer'] = array('label' => 'Falskt personnummer', 'url' => '/mattan/falskt_personnummer.php');
		$menu['mattan']['children']['foerkortningar'] = array('label' => 'Förkortningslistan', 'url' => '/mattan/foerkortningar.php');
//		$menu['mattan']['children']['hamburgare'] = array('label' => 'Gissa Hamburgaren', 'url' => '/hamburgare/test.php');
		$menu['mattan']['children']['hamsterpajfabriken'] = array('label' => 'Hamsterpajfabriken', 'url' => '/mattan/hamsterpajfabriken.php');
		$menu['mattan']['children']['gissa_laaten'] = array('label' => 'Gissa Låten', 'url' => '/mattan/gissa_laaten.php');
		$menu['mattan']['children']['gratis_musik'] = array('label' => 'Gratis musik', 'url' => '/mattan/gratis_musik.php');
		$menu['mattan']['children']['koerkort'] = array('label' => 'Körkortsfrågor', 'url' => '/mattan/koerkort.php');
		$menu['mattan']['children']['ladda_ner_program'] = array('label' => 'Ladda ner program', 'url' => '/mattan/ladda_ner_program.php');
		$menu['mattan']['children']['pornalizer'] = array('label' => 'Pornalizer', 'url' => '/mattan/pornalizer.php');
		$menu['mattan']['children']['promoe'] = array('label' => 'Promoe', 'url' => '/mattan/promoe.php');
		$menu['mattan']['children']['snyggve'] = array('label' => 'Snyggve', 'url' => '/mattan/snyggve.php');
		$menu['mattan']['children']['collage_illusion'] = array('label' => 'Kollageapparaten', 'url' => '/mattan/collage_illusion.php');
		$menu['mattan']['children']['fruit_vernissage'] = array('label' => 'Fruktvernissage', 'url' => 'http://www.hamsterpaj.net/artiklar/?action=show&id=95');


	$menu['traeffa'] = array('label' => 'Träffa', 'url' => '/traffa/');
		$menu['traeffa']['children']['soek'] = array('label' => 'Sök', 'url' => '/traffa/search.php');
		$menu['traeffa']['children']['klotterplank'] = array('label' => 'Klotterplank', 'url' => '/traffa/klotterplanket.php');
		$menu['traeffa']['children']['galleriet'] = array('label' => 'Galleriet', 'url' => '/traffa/gallery.php');
		$menu['traeffa']['children']['grupper'] = array('label' => 'Grupper', 'url' => '/traffa/groups.php');
		$menu['traeffa']['children']['digga'] = array('label' => 'Digga', 'url' => '/traffa/digga.php');		
		$menu['traeffa']['children']['besoeksloggen'] = array('label' => 'Besöksloggen', 'url' => '/traffa/my_visitors.php');
		$menu['traeffa']['children']['age_guess'] = array('label' => 'Gissa Åldern', 'url' => '/traffa/age_guess.php');
		$menu['traeffa']['children']['gamla_klotterplanket'] = array('label' => 'Gamla klotterplanket', 'url' => '/traffa/klotterplank.php');
		$menu['traeffa']['children']['tester'] = array('label' => 'Tester', 'url' => '/tests/index.php');
		$menu['traeffa']['children']['new_photos'] = array('label' => 'Nya foton', 'url' => '/traffa/new_photos.php');

//	$menu['grupper'] = array('label' => 'Grupper', 'url' => '/grupper/', 'is_privilegied' => 'groups_superadmin');
		
 /* Chatt */
	$menu['chatt'] = array('label' => 'Chatt', 'url' => '/chat/', 'index_lable' => 'Börja chatta');
	$menu['chatt']['children']['statistik'] = array('label' => 'Statistik', 'url' => '/chat/stats.php');
	$menu['chatt']['children']['statistik']['children']['fjortis'] = array('label' => '#Fjortis', 'url' => '/chat/stats.php?chan=fjortis');
	$menu['chatt']['children']['statistik']['children']['traffa'] = array('label' => '#Träffa', 'url' => '/chat/stats.php?chan=traffa');
	$menu['chatt']['children']['statistik']['children']['moget'] = array('label' => '#Moget', 'url' => '/chat/stats.php?chan=moget');
	$menu['chatt']['children']['statistik']['children']['kuddhornan'] = array('label' => '#Kuddhörnan', 'url' => '/chat/stats.php?chan=kuddhornan');
	$menu['chatt']['children']['regler'] = array('label' => 'Regler', 'url' => '/chat/regler.php');
	$menu['chatt']['children']['op_instruktioner'] = array('label' => 'Instruktioner för OPs', 'url' => '/chat/op_instruktioner.php');

	
	$menu['artiklar'] = array('label' => 'Artiklar', 'url' => '/artiklar/');
	
		$menu['artiklar']['children']['search'] = array('label' => 'Visa alla', 'url' => '/artiklar/?action=list');
			$menu['artiklar']['children']['search']['children']['4'] = array('label' => 'Debatt', 'url' => '/artiklar/?action=list&category=4');
			$menu['artiklar']['children']['search']['children']['6'] = array('label' => 'Guider', 'url' => '/artiklar/?action=list&category=6');
			$menu['artiklar']['children']['search']['children']['1'] = array('label' => 'Hamsterpaj', 'url' => '/artiklar/?action=list&category=1');
			$menu['artiklar']['children']['search']['children']['5'] = array('label' => 'Intervjuer', 'url' => '/artiklar/?action=list&category=5');
			$menu['artiklar']['children']['search']['children']['2'] = array('label' => 'Kemi', 'url' => '/artiklar/?action=list&category=2');
			$menu['artiklar']['children']['search']['children']['9'] = array('label' => 'Nyheter', 'url' => '/artiklar/?action=list&category=9');
			$menu['artiklar']['children']['search']['children']['7'] = array('label' => 'Sex & Samlevnad', 'url' => '/artiklar/?action=list&category=7');
			$menu['artiklar']['children']['search']['children']['8'] = array('label' => 'Övrigt', 'url' => '/artiklar/?action=list&category=8');
		
		if(is_privilegied('articles_admin'))
		{
			$menu['artiklar']['children']['admin'] = array('label' => 'Admin', 'url' => '/artiklar/?action=admin');
		}
		
	$menu['piraja'] = array('label' => 'Piraja', 'url' => '/piraja/', 'index_label' => 'Piraja');
		$menu['piraja']['children']['allt'] = array('label' => 'Allt från Piraja', 'url' => '/piraja/');
		$menu['piraja']['children']['finn_fem_fel'] = array('label' => 'Finn fem fel', 'url' => '/piraja/five_errors.php?fffid=hfred');
		$menu['piraja']['children']['hyvlar'] = array('label' => 'Test av rakhyvlar', 'url' => '/piraja/hyvlar.php');
		$menu['piraja']['children']['haarsprej'] = array('label' => 'Test av hårsprej', 'url' => '/piraja/haarsprej.php');		
		$menu['piraja']['children']['prylar'] = array('label' => 'Prylar', 'url' => '/piraja/prylar.php');
		

/*
	$menu['taevlingar'] = array('label' => 'Tävlingar', 'url' => '/taevlingar/');
		$menu['taevlingar']['children']['dyra_vinster'] = array('label' => 'Dyra vinster', 'url' => '/taevlingar/dyra_vinster.php');
		$menu['taevlingar']['children']['sista_chansen'] = array('label' => 'Sista chansen', 'url' => '/taevlingar/lite_tid_kvar.php');
		$menu['taevlingar']['children']['populaera'] = array('label' => 'Populära', 'url' => '/taevlingar/populaera.php');
		$menu['taevlingar']['children']['opopulaera'] = array('label' => 'Opopulära', 'url' => '/taevlingar/opopulaera.php');
*/
 /* Inställningar */
 if(login_checklogin())
 {
	$menu['installningar'] = array('label' => 'Inställningar', 'url' => '/installningar/generalsettings.php', 'index_label' => 'Generella');
		$menu['installningar']['children']['byt_namn'] = array('label' => 'Byt namn', 'url' => '/installningar/changename.php');
		//$menu['installningar']['children']['byt_losen'] = array('label' => 'Byt lösenord', 'url' => '/installningar/renew_password.php');
		$menu['installningar']['children']['profil'] = array('label' => 'Presentation', 'url' => '/installningar/profilesettings.php');
		$menu['installningar']['children']['forum_installningar'] = array('label' => 'Foruminställningar', 'url' => '/installningar/forum_settings.php');
		$menu['installningar']['children']['byt_visningsbild'] = array('label' => '<strong>Byt visningsbild</strong>', 'url' => '/installningar/avatar-settings.php');
		$menu['installningar']['children']['blockera'] = array('label' => 'Blockera', 'url' => '/installningar/userblock.php');
		$menu['installningar']['children']['avregistrera'] = array('label' => 'Avregistrera', 'url' => '/installningar/unregister.php');
	}
 /* Administration */
  $admin_privilegies = array('warnings_admin', 'use_statistic_tools', 'schedule_admin', 'ip_ban_admin', 'register_suspend_admin', 'ov_admin', 'entertain_add', 'privilegies_admin', 'use_ghosting_tools', 'backgrounds_admin', 'music_guess_admin', 'avatar_admin', 'user_management_admin');
	$menu['admin'] = array('label' => 'A', 'url' => 'javascript:void(0)', 'is_privilegied' => $admin_privilegies, 'index_label' => 'Adminstart (ej klar)');
		$menu['admin']['children']['anvaendare'] = array('label' => 'Användare', 'url' => 'javascript:void(0)', 'is_privilegied' => array('avatar_admin', 'ip_ban_admin', 'forum_userlabel_admin', 'warnings_admin', 'user_management_admin', 'use_ghosting_tools'));
			$menu['admin']['children']['anvaendare']['children']['avatarer'] = array('label' => 'Visningsbilder', 'url' => '/admin/avatarer.php', 'is_privilegied' => 'avatar_admin');
			$menu['admin']['children']['anvaendare']['children']['ban'] = array('label' => 'IP-ban', 'url' => '/admin/ip_ban_admin.php', 'is_privilegied' => 'ip_ban_admin');
			$menu['admin']['children']['anvaendare']['children']['forumstatus'] = array('label' => 'Forumstatus', 'url' => '/admin/forum_userlabel.php', 'is_privilegied' => 'forum_userlabel_admin');
			$menu['admin']['children']['anvaendare']['children']['warnings'] = array('label' => 'Varningar', 'url' => '/admin/warnings.php');
			$menu['admin']['children']['anvaendare']['children']['user_management'] = array('label' => 'User management', 'url' => '/admin/user_management.php', 'is_privilegied' => 'user_management_admin');
			$menu['admin']['children']['anvaendare']['children']['pm_hack'] = array('label' => 'PM-hack', 'url' => '/admin/pm_hack.php', 'is_privilegied' => 'use_ghosting_tools');
			$menu['admin']['children']['anvaendare']['children']['guestbook_hack'] = array('label' => 'GB-hack', 'url' => '/admin/guestbook_hack.php', 'is_privilegied' => 'use_ghosting_tools');
			$menu['admin']['children']['anvaendare']['children']['user_ghost'] = array('label' => 'Ghosta', 'url' => '/admin/user_ghost.php', 'is_privilegied' => 'use_ghosting_tools');
			$menu['admin']['children']['anvaendare']['children']['user_logins'] = array('label' => 'User Logins', 'url' => '/admin/user_logins.php', 'is_privilegied' => 'ip_ban_admin');
			$menu['admin']['children']['anvaendare']['children']['newly_registered_users'] = array('label' => 'Nyregistrerade användare', 'url' => '/admin/newly_registered_users.php', 'is_privilegied' => 'remove_user');
			$menu['admin']['children']['anvaendare']['children']['user_flag'] = array('label' => 'Användarflaggor', 'url' => '/admin/user_flag.php', 'is_privilegied' => 'user_flag_admin');
		$menu['admin']['children']['crew-folk'] = array('label' => 'Crew-folk', 'url' => 'javascript:void(0)', 'is_privilegied' => $admin_privilegies);
			$menu['admin']['children']['crew-folk']['children']['admins'] = array('label' => 'Besättning', 'url' => '/admin/admins.php');
			$menu['admin']['children']['crew-folk']['children']['ov_reg'] = array('label' => 'OV Registrering', 'url' => '/admin/ov_reg.php', 'userlevel' => 3);
			$menu['admin']['children']['crew-folk']['children']['ov_watch'] = array('label' => 'OV Statistik', 'url' => '/admin/ov_watch.php', 'is_privilegied' => 'ov_admin');
			$menu['admin']['children']['crew-folk']['children']['ov_list'] = array('label' => 'OV Kontaktuppgifter', 'url' => '/admin/ov_list.php', 'userlevel' => 3);
			$menu['admin']['children']['crew-folk']['children']['log_view'] = array('label' => 'Administrativ logg', 'url' => '/admin/log_view.php', 'is_privilegied' => 'ov_admin');
			$menu['admin']['children']['crew-folk']['children']['mass_gb'] = array('label' => 'Mass-GB', 'url' => '/admin/mass_gb.php', 'is_privilegied' => 'igotgodmode');
			$menu['admin']['children']['crew-folk']['children']['privilegies_admin'] = array('label' => 'Privilegier', 'url' => '/admin/privilegies_admin.php', 'is_privilegied' => 'privilegies_admin');
	
	$site_admin_privilegies = array('fp_module_rearrange', 'schedule_admin', 'use_statistic_tools', 'register_suspend_admin', 'entertain_add', 'backgrounds_admin', 'music_guess_admin', 'open_search');
	$menu['site_admin'] = array('label' => 'SA', 'url' => 'javascript:void(0)', 'is_privilegied' => $site_admin_privilegies);
		$menu['site_admin']['children']['fp_admin'] = array('label' => 'Ordna startsidan', 'url' => '/admin/fp_module_list.php', 'is_privilegied' => 'fp_module_rearrange');
		$menu['site_admin']['children']['schemalagt'] = array('label' => 'Schemalagt', 'url' => '/admin/schemalagt.php', 'is_privilegied' => 'schedule_admin');
		$menu['site_admin']['children']['statistik'] = array('label' => 'Statistik', 'url' => '/admin/event_log.php', 'is_privilegied' => 'use_statistic_tools');
		$menu['site_admin']['children']['pageviews'] = array('label' => 'Sidvisningar', 'url' => '/admin/pageviews.php', 'is_privilegied' => 'use_statistic_tools');
		$menu['site_admin']['children']['registrering'] = array('label' => 'Stäng av registreringen', 'url' => '/admin/register_suspend.php', 'is_privilegied' => 'register_suspend_admin');
		$menu['site_admin']['children']['flash_update'] = array('label' => 'Flashupdate', 'url' => '/admin/flash_update.php', 'is_privilegied' => 'entertain_add');
		$menu['site_admin']['children']['wallpapers'] = array('label' => 'Bakgrundsbilder (nya)', 'url'  =>'/admin/wallpapers_admin.php', 'is_privilegied' => 'backgrounds_admin');
		$menu['site_admin']['children']['music_guess'] = array('label' => 'Gissa låten', 'url' => '/admin/music_guess.php', 'is_privilegied' => 'music_guess_admin');
		$menu['site_admin']['children']['open_search'] = array('label' => 'Open search', 'url' => '/admin/open_search.php', 'is_privilegied' => 'open_search');
		$menu['site_admin']['children']['dev'] = array('label' => 'Utveckling', 'url' => 'javascript:void(0)', 'is_privilegied' => array('use_debug_tools'));
			$menu['site_admin']['children']['dev']['children']['handy'] = array('label' => 'Handy <i>(riktig)</i>', 'url' => '/handy.php', 'is_privilegied' => 'use_debug_tools');
			$menu['site_admin']['children']['dev']['children']['visa_sessionsdata'] = array('label' => 'Visa sessionsdata', 'url' => '/admin/view_session.php', 'is_privilegied' => 'use_debug_tools');

require_once(PATHS_CONFIGS . 'entertain.conf.php');
// this is just to make sure that the entertain configs are available at this point
$handles = array('game', 'flash', 'clip', 'image');
foreach($handles as $handle)
{
	$type = $entertain_types[$handle];
	//First, a top level menu item for each type (= subdivision of the entertain system)
	$menu[$handle] = array('label' => $type['label_capitol'], 'url' => '/' . $type['url_handle'] . '/', 'index_label' => $type['label_capitol']);
	//Second, all the configured kinds of lists for each type
	foreach($type['views'] as $view)
	{
		$menu[$handle]['children'][$view] = array('label' => $entertain_lists[$view]['label'], 'url' => '/' . $type['url_handle'] . '/' . $entertain_lists[$view]['url_handle'] . '/');
		if($view == 'search')
		{
			foreach($entertain_type_categories[$handle] as $category_id)
			{
				$menu[$handle]['children']['search']['children'][$entertain_categories[$category_id]['handle']] = array('label' => $entertain_categories[$category_id]['title'], 'url' => '/' . $type['url_handle'] . '/' .$entertain_categories[$category_id]['handle'] . '/');
			}
			asort($menu[$handle]['children']['search']['children']);
		}
	} 
	
	// $privilegie_handle
		$privilegie_handle_array = array(1 => 'game', 2 => 'flash', 3 => 'clip', 4 => 'image');
		$privilegie_handle = array_search($handle, $privilegie_handle_array);
		$menu[$handle]['children']['admin'] = array('label' => 'Admin', 'url' => '/' . $type['url_handle'] . '/admin/', 'is_privilegied' => 'entertain_add');
}
	$menu['game']['children']['nord'] = array('label' => 'Nord', 'url' => '/entertain/nordframe.php');
?>
