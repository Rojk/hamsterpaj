<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/photoblog.lib.php');
	
	$ui_options['stylesheets'][] = 'photoblog.css.php';
	
	$uri_parts = explode('/', $_SERVER['REQUEST_URI']);
	
	$out .= '<ul>
	<li>
		<a href="/fotoblogg/">Min dagbok</a>
	</li>
	<li>
		<a href="/fotoblogg/Lef/">Lefs dagbok</a>
	</li>
	<li>
		<a href="/fotoblogg/ladda_upp/">Ladda upp</a>
	</li>
	<li>
		<a href="/fotoblogg/instaellningar/">Inställningar</a>
	</li>
	</ul>
	
	' . "\n";
	
	switch ($uri_parts[2])
	{
		case 'instaellningar':
			$out .= '<h2>INSTÄLLNINGAR</h2>' . "\n";
		break;
		
		case 'ladda_upp':
			/*	
				########################################################
					Page title
				########################################################
			*/
			$out .= '<h1>Välkommen att ladda upp bilder i din fotoblogg</h1>' . "\n";
			
			/*	
				########################################################
					Upload form
				########################################################
			*/
			$out .= '<object width="100" height="40">
							 <param name="movie" value="../upload.swf">
							 <embed src="../upload.swf" width="100" height="40"></embed>
								</object>' . "\n";  
								
			/*	
				########################################################
					Uploaded photos setting
				########################################################
			*/
			$out .= '<div class="photoblog_photo_properties" id="#">' . "\n";
				$out .= '<div class="properties">' . "\n";
					$out .= '<p>Datepicker - save - set today | Select album - Create album</p>';
					$out .= '<p>WYSIWYG-editor tinymce</p>';
					$out .= '<p>Save</p>';
				$out .= '</div>' . "\n";
				$out .= '<div class="float">' . "\n";
					$out .= '<div class="thumbnail_wrapper">' . "\n";
						$out .= '<img src="http://images.hamsterpaj.net/photos/thumb/8/42818.jpg" class="thumbnail" />' . "\n";
					$out .= '</div>' . "\n";
					$out .= '<div class="rotate">' . "\n";
						$out .= '<img src="" class="rotate_left" />' . "\n";
						$out .= '<img src="" class="rotate_right" />' . "\n";
					$out .= '</div>' . "\n";
				$out .= '</div>' . "\n";
			$out .= '</div>' . "\n";
								
			/*	
				########################################################
					How to upload photos
				########################################################
			*/			   
    	$options['type'] = 'notification';
    	$options['title'] = '';
    	$options['message'] = '<p>Du kan ladda upp flera bilder samtidigt genom att markera flera när du väljer bilder.<br /> Men tänk på att det tar en del tid att ladda upp bilderna, och du bör kanske inte ladda upp så många i taget.</p><p>Fungerar inte bilduppladdningen? Klicka <a href="#">här</a> för att använda en enklare version av uppladdningen</a>';
    	$out .= ui_server_message($options);   
    	
    	/*	
				########################################################
					Rules of what to upload
				########################################################
			*/
			$options['type'] = 'warning';
			$options['title'] = 'Att tänka på innan du laddar upp bilder';
			$options['message'] = '<h3>Du förlorar kontrollen över bilder du laddar upp!</h3>' . "\n";
			$options['message'] .= '<p>Bilder som en gång laddats upp till Internet kan kopieras och skickas vidare i all evighet. Det gäller på Hamsterpaj såväl som på alla andra webbsajter.</p>' . "\n";
			$options['message'] .= '<h3>Är du en blond tjej med stora tuttar eller kille med brunt hår och slingor? </h3>' . "\n";
			$options['message'] .= '<p>Den där blyge typen med fula glasögon och som luktade äckligt i din klass i mellanstadiet kommer förr eller senare stjäla din bild för att ragga på Lunarstorm, Hamsterpaj, PlayAhead och andra communities. När det händer så kontaktar du en ordningsvakt så löser vi det!</p>' . "\n";
			$options['message'] .= '<h3>Hamsterpaj är ingen porrsajt, Goatse är äckligt och hitlerhälsningar olagliga</h3>' . "\n";
			$options['message'] .= '<p>Snälla låt bli porr och goatse här, tänk på att barn besöker den här sajten!</p>' . "\n";
			$options['message'] .= '<em>Brottsbalkens sextonde kapitel, paragraf åtta</em><br />' . "\n";
			$options['message'] .= '<p>8 § Den som i uttalande eller i annat meddelande som sprids hotar eller uttrycker missaktning för folkgrupp eller annan sådan grupp av personer med anspelning på ras, hudfärg, nationellt eller etniskt ursprung, trosbekännelse eller sexuell läggning, döms för hets mot folkgrupp till fängelse i högst två år eller om brottet är ringa, till böter.</p>' . "\n";
			$out .= ui_server_message($options);
		break;
			
		default:
			$out .= 'Välkommen till ' . "\n";
			$out .= preg_match('/s$/', $uri_parts[2]) ? $uri_parts[2] : $uri_parts[2] . 's';
			$out .= ' fotoblogg!';
		break;
	}
	$out .= '<br /><br />' . preint_r($uri_parts);
	/*
	/fotoblogg/user/
	/fotoblogg/instaellningar
	/fotoblogg/ladda_upp
	/fotoblogg <- sin egen
	*/
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
