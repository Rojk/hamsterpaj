<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'photos.lib.php');
	require(PATHS_LIBRARIES . 'comments.lib.php');
	require(PATHS_LIBRARIES . 'userblock.lib.php');


	if (userblock_checkblock($GET['id']))
	{
		ui_top();
		echo '<p class="error">IXΘYΣ! Du har blivit blockad, var snel hest så slipper du sånt ;)<br /><em>Visste du förresten att IXΘYΣ betyder Fisk på grekiska?</em></p>';
		ui_bottom();
		exit;
	}
	
	if(isset($_GET['action']))
	{
		switch($_GET['action'])
		{
			case 'get_category_photos':
				$out .= '<a id="photo"></a>' . "\n";
				$photos = photos_fetch(array('id' => $_GET['id']));
				
					if(count($photos) > 0)
					{
						$user_id = $photos[0]['user'];
						$categoryphotos = photos_fetch(array('category' => $photos[0]['category']));
						$out .= photos_browse($categoryphotos, true);
					}
					else
					{
						$out .= '<h1>Bilden du söker finns inte!</h1>' . "\n";
						$out .= '<p>Den bild du försöker ladda verkar inte finnas kvar på Hamsterpaj, kanske har den blivit borttagen?</p>' . "\n";
					}
					$out .= '<div style="clear: both;"></div>' . "\n";
				break;
			case 'get_full_photo':
				$out .= '<a id="photo"></a>' . "\n";
				$photos = photos_fetch(array('id' => $_GET['id']));
					if(count($photos) > 0)
					{
						$user_id = $photos[0]['user'];
						$categoryphotos = photos_fetch(array('category' => $photos[0]['category']));
						$out .= photos_display($photos, true);
					}
					else
					{
						$out .= '<h1>Bilden du söker finns inte!</h1>' . "\n";
						$out .= '<p>Den bild du försöker ladda verkar inte finnas kvar på Hamsterpaj, kanske har den blivit borttagen?</p>' . "\n";
					}
					$out .= '<div style="clear: both;"></div>' . "\n";
				break;
			case 'get_photo_left':
				$left_id = photos_fetch_next_id(array('id' => $_GET['id'], 'direction' => 'left'));
				$out .= $left_id['id'];
				break;
			case 'get_photo_right':
				$right_id = photos_fetch_next_id(array('id' => $_GET['id'], 'direction' => 'right'));
				$out .= $right_id['id'];
				break;
			default:
				$out .= 'Ingen action';
				break;
		}
	}

	echo $out;
?>