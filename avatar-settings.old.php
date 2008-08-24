<?php

	require('/storage/www/standard.php');
	$ui_options['menu_path'] = array('installningar', 'byt_visningsbild');

	if($_SESSION['login']['id'] == 804445)
	{
		ui_top($ui_options);
		echo '<h1>Det är tyvärr inte möjligt att byta bild!</h1>' . "\n";
		echo '<p>Då användare med snoppnäsor inte stöds av vårat system går det tyvärr inte att byta bild för detta konto. För ytterliggare information, kontakta Lunarworks AB: 0340-64 11 00</p>' . "\n";
		ui_bottom();
		exit;
	}

	include($hp_includepath . 'md5image-functions.php');
	include(PATHS_INCLUDE . 'copy_protection/exif.php');

	if(!isset($_SESSION['userid']))
	{
		jscript_alert('Du måste vara inloggad för att komma åt denna sidan!aaaaa');
		jscript_location('/');
		die();
	}


	/*
	***********************************************
	Avatar-Settings
	Här är filen som laddar upp en bild, tillåter
	användaren att beskära, förhandsgranska och spara
	sin bild.

	************************************************
	/*
	- - - - - - - - - - - - - -
	GLOBALA VARIABLER
	- - - - - - - - - - - - - -
	*/
	$img_tmp_path = $hp_path . 'tmp/avatars/'; 	// Temporär sökväg till avatars
	$img_path_thumb =  PATHS_IMAGES . 'users/thumb/';		// Avatarsökvägen
	$img_path_full =  PATHS_IMAGES . 'users/full/';

	$img_tmp_orginal_filename = $img_tmp_path . $_SESSION['userid'] . '_orginal.jpg';
	$img_tmp_crop_filename = $img_tmp_path . $_SESSION['userid'] . '_crop.jpg';
	$img_tmp_full_filename = $img_tmp_path . $_SESSION['userid'] . '_full.jpg';
	$img_tmp_thumb_filename = $img_tmp_path . $_SESSION['userid'] . '_thumb.jpg';

	$img_tmp_orginal_url = $hp_url . 'tmp/avatars/' . $_SESSION['userid'] . '_orginal.jpg?' . rand();
	$img_tmp_full_url = $hp_url . 'tmp/avatars/' . $_SESSION['userid'] . '_full.jpg?' . rand();
	$img_tmp_thumb_url = $hp_url . 'tmp/avatars/' . $_SESSION['userid'] . '_thumb.jpg?' . rand();

	$img_orginal_width = 442; 	// Efter en bild laddas upp så skalas dom om till en angiven width.
	$img_full_width = 320; 		// Fullstorleksbildens bredd
	$img_full_height = 427; 	// Fullstorleksbildens höjd
	$img_thumb_width = 75; 		// Thumbnailens bredd
	$img_thumb_height = 100; 	// Thumbnailens höjd

	/*
	- - - - - - - - - - - - - -
	FUNKTIONER
	- - - - - - - - - - - - - -
	*/
	// En enkelk funktion som skapar orginalbilden som kommer sen användas till att beskära på
	function makeorginaljpg($image,$filename) {
		global $img_orginal_width;


		if(is_file($image))
		{
			system('convert -scale ' . $img_orginal_width . ' ' . $image . ' ' . $filename, $retval_full);

			$return = TRUE;
			unlink($image);
		}
		else
		{
			$return = FALSE;
		}
		return $return;
	}

	/*
	- - - - - - - - - - - - - -
	POST-ACTIONS
	- - - - - - - - - - - - - -
	*/

	// Uppladdning
	if($_POST && $_GET['action'] == 'upload')
	{

		if($_SESSION['userinfo']['image_ban_expire'] > time()) {
			jscript_alert('Du har blivit avstängd från bilduppladdningen fram till ' . date('Y-m-d H:i', $_SESSION['userinfo']['image_ban_expire']));
			jscript_go_back();
			die('Du har blivit avstängd från bilduppladdningen fram till ' . date('Y-m-d H:i', $_SESSION['userinfo']['image_ban_expire']));
			exit;
		}

		$filnamn = strtolower($_FILES['userimage']['name']);

		if(($_FILES['userimage']['type'] == 'image/png' && substr($filnamn, -4) == '.png') ||
			($_FILES['userimage']['type'] == 'image/x-png' && substr($filnamn, -4) == '.png') ||
			($_FILES['userimage']['type'] == 'image/gif' && substr($filnamn, -4) == '.gif') ||
			($_FILES['userimage']['type'] == 'image/jpeg' && (substr($filnamn, -4) == '.jpg' || substr($filnamn, -5) == '.jpeg')) ||
			($_FILES['userimage']['type'] == 'image/pjpeg' && substr($filnamn, -4) == '.jpg') ||
			($_FILES['userimage']['type'] == 'image/bmp' && substr($filnamn, -4) == '.bmp'))
		{
			$tempfile = $_FILES['userimage']['tmp_name'];

			if(makeorginaljpg($tempfile, $img_tmp_orginal_filename) == TRUE )
			{
				header('Location:' . $_SERVER['PHP_SELF'] . '?step=2' . (isset($_POST['registerproccess']) ? '&registerproccess=1' : ''));
			}
			else
			{
				  jscript_alert('Det blev nåt fel vid uppladdning av bilden.');
				  jscript_go_back();
				  die('Det blev nåt fel vid uppladdning av bilden.');
			}

		}
		else
		{
			  jscript_alert("Du har laddat upp en bild i fel filformat. Tillåtna filformat är JPG, PNG och GIF. <br /><br />Typ: " . $_FILES['userimage']['type'] . "<br />Din fil: " . $filnamn);
			  jscript_go_back();
			  die('Du har laddat upp en bild i fel filformat. Det som är tillåtett är JPG/PNG/GIF.');
		}
	}

	// Beskärningen
	if($_POST && $_GET['action'] == 'crop')
	{
			$randomnr = rand();
			$img_crop_height = max(intval($_POST['y2'] - $_POST['y1']), 0);
			$img_crop_width = max(intval($img_crop_height*0.75), 0);

			$img_crop_top = intval($_POST['y1']);
			$img_crop_left = intval($_POST['x1']);


			if(!is_numeric($img_crop_height) || !is_numeric($img_crop_width) || !is_numeric($img_crop_top) || !is_numeric($img_crop_left))
			{
			  die('Sluta hacka dårå!');
			}

				if (is_file($img_tmp_orginal_filename))
				{
					system('convert -crop ' . $img_crop_width . 'x' . $img_crop_height . '+' . $img_crop_left . '+' . $img_crop_top . '-100 ' . $img_tmp_orginal_filename . ' ' . $img_tmp_crop_filename, $retval_full);
				}
				if (is_file($img_tmp_crop_filename))
				{
					system('convert -resize ' . $img_full_width . 'x' . $img_full_height . '! ' . $img_tmp_crop_filename . ' ' . $img_tmp_full_filename, $retval_full);
					system('convert -resize ' . $img_thumb_width . 'x' . $img_thumb_height . '! ' . $img_tmp_crop_filename . ' ' . $img_tmp_thumb_filename, $retval_full);
				}

			header('Location:' . $_SERVER['PHP_SELF'] . '?step=3' . (isset($_GET['registerproccess']) ? '&registerproccess=1' : ''));

	}

	// Spara
	if($_POST && $_GET['action'] == 'save')
	{
		$img_full_filename = $img_path_full . $_SESSION['userid']  . '.jpg';
		$img_thumb_filename = $img_path_thumb . $_SESSION['userid']  . '.jpg';

		rename($img_tmp_full_filename, $img_full_filename);
		rename($img_tmp_thumb_filename, $img_thumb_filename);
		unlink($img_tmp_orginal_filename);
		unlink($img_tmp_crop_filename );

		/* Write copy protection tags */

		write_copy_protection($img_full_filename, 'Copyrighted Work');
		write_copy_protection($img_thumb_filename, 'Copyrighted Work');

		$newdata['userinfo']['image'] = 1;
		login_save_user_data($_SESSION['userid'], $newdata);
		$_SESSION['userinfo']['image'] = 1;

		/*$message_bar = '<a href="/traffa/profile.php?id=' . $_SESSION['login']['id'] . '">' . $_SESSION['login']['username'] . '</a> laddade nyss upp en ny visningsbild.';
		$file_handle = fopen(PATHS_NCLUDE . 'message_bar_current.txt', 'w');
		fwrite($file_handle, $message_bar);
		fclose($file_handle);*/

		jscript_alert('Din bild är nu sparad och lagd på förhandsgranskning\\nFör att din nya bild ska visas kan du behöva trycka på F5. Om din gamla bild fortsätter visas är detta helt normalt, det kan ta ett par dagar innan din dator "glömt av" den gamla bilden och hämtat den nya!');

		if (isset($_POST['registerproccess']))
		{
			jscript_location('/register.php?nextstep=3&bild=1');
		}
		else
		{
			jscript_location($hp_url . '/installningar/generalsettings.php');
		}
	}

	// Ta bort
	if($_GET['action'] == 'delete')
	{
		$img_full_filename = $img_path_full . $_SESSION['userid']  . '.jpg';
		$img_thumb_filename = $img_path_thumb . $_SESSION['userid']  . '.jpg';

		$newdata['userinfo']['image'] = 0;
		login_save_user_data($_SESSION['userid'], $newdata);
		$_SESSION['userinfo']['image'] = 0;

		if(is_file($img_full_filename)) {
			unlink($img_full_filename);
		}

		if(is_file($img_thumb_filename )) {
			unlink($img_thumb_filename );
		}
		jscript_alert('Din bild är nu borttagen!');
		jscript_location($hp_url . 'avatar-settings.php');
	}


	/*
	- - - - - - - - - - - - - -
	SID-KODEN
	- - - - - - - - - - - - - -
	SID-koden, den underbara SID-koden. Undra just vad det betyder...

	Besserwisserjoel noterar: Session ID
	*/
	if($_GET['step'] == '')
	{
		if (isset($_GET['registerproccess']))
		{
			$out .= '<div class="pink_faded_div">';
			$out .= '<h2>Grattis!</h2>';
			$out .= 'Ditt användarnamn var ledigt, och du är nu medlem på Hamsterpaj!<br />';
			$out .= 'Vi kommer nu guida dig igenom tre steg där du fyller i lite frivillig information om dig själv!<br /><br />';
			$out .= '</div>';
		}
		$out .= '
<h1 style="margin: 0px; ">Ladda upp ditt foto (steg 1)</h1>
Ladda upp ditt foto här, det måste vara en bild av filtyperna JPG, GIF eller PNG.<br /><br />
<form name="uploadimg" action="/avatar-settings.php?action=upload" method="post" enctype="multipart/form-data" style="margin: 0px;">
<input name="userimage" type="file" /><br /><br />
<h3>Alla bilder granskas av riktiga människor, innan bilden skickas till granskning måste du intyga följande:</h3>
<ul style="padding: 0px; list-style-type: none;">
	<li>
		<input type="checkbox" id="check_1" />
		<label for="check_1">Det är bara jag på bilden, om någon annan är med så syns de bara i bakgrunden</label>
	</li>
	<li>
		<input type="checkbox" id="check_2" />
		<label for="check_2">Mitt ansikte syns på bilden</label>
	</li>
	<li>
		<input type="checkbox" id="check_3" />
		<label for="check_3">Det finns varken porr eller nazistiska symboler med på bilden och det syns ingen alkohol i förgrunden</label>
	</li>
	<li>
		<input type="checkbox" id="check_trap" />
		<label for="check_3">Folk som inte läser reglerna är jobbiga, kryssa inte i denna ruta. Annars laddas bilden inte upp.</label>
	</li>
	<li>
		<input type="checkbox" id="check_4" />
		<label for="check_4">Bilden är inte märkt av snyggast.se eller någon annan community</label>
	</li>
	<li>
		<input type="checkbox" id="check_5" />
		<label for="check_5">Jag har angivit min riktiga ålder eller ingen ålder alls, bilden föreställer mig så som jag ser ut idag</label>
	</li>
</ul>
<p>
	Om du trots allt försöker ladda upp en bild på någon seriefigur, en kändis, djur eller på någon
	annan än dig själv riskerar du avstängning får Hamsterpaj. Utan varning.
</p>
<script>
	function verify_checkboxes()
	{
		for(var i = 1; i <= 5; i++)
		{
			if(document.getElementById(\'check_\' + i).checked == false)
			{
				alert(\'Du måste ladda upp en bild som följer reglerna och intyga detta genom att kryssa i alla kryssrutor!\');
				return false;
			}
		}

		if(document.getElementById(\'check_trap\').checked == true)
		{
			alert(\'Men fy på dig, du måste läsa reglerna igen ordentligt!\');
			return false;
		}
	}
</script>
<input name="submit" type="submit" value="Ladda upp" onclick="return verify_checkboxes();" class="button_80" />';

if(isset($_GET['registerproccess']))
{
	$out .= '<input type="hidden" name="registerproccess" value="1" />
	<br /><br />
	<input type="button" class="button" value="Nej tack, jag vill inte ladda upp en bild &raquo;"	onclick="location.href=\'/register.php?nextstep=3\'">';
}
$out .= '</form>';
		$img_full_filename = $img_path_full . $_SESSION['userid']  . '.jpg';
		if (is_file($img_full_filename))
		{
			$out .= '<h2 style="margin: 0px; ">Din nuvarande bild:</h2>';
			$out .= insert_avatar($_SESSION['login']['id'], 'style="border: 1px solid #333333;"');
			$out .= '<br /><b><a href="' . $_SERVER['PHP_SELF'] . '?action=delete">» Ta bort bilden</a></b>';
		}

	}

	/*
	BESKÄRNING
	*/
	elseif($_GET['step'] == '2')
	{
		if (!is_file($img_tmp_orginal_filename))
		{
			jscript_alert('Någonting blev fel vid uppladdningen av bilden, försök igen!');
			jscript_go_back();
			die();
		}
		$copy_data = read_copy_protection($img_tmp_orginal_filename);
		if ($copy_data['copyright'] == 1 && $_SESSION['login']['id'] != $copy_data['userid'])
		{
			jscript_alert('Den gubben gick inte');
			jscript_go_back();
			die();
		}

		$img_height = intval(exec('identify ' . $img_tmp_orginal_filename. ' | cut -f3 -d" " | cut -f2 -d"x" | cut -f1 -d"+"'));
		$flash_height = $img_height + 70;

		//$crop_width = $img_height * 0.75;
		$crop_width = intval($img_height * 0.75);

		$out .= '<h1 style="margin: 0px; ">Skala och besk&auml;r ditt foto (steg 2)</h1>';
		$out .= 'Här kan du beskära och klippa ut valt område från ditt foto. Alla foton kommer klippas ut med ration 3:4.<br /><br />';
		$out .= '» Vill du ladda upp en annan bild så gå tillbaka till <a href="avatar-settings.php';
		if (isset($_GET['registerproccess']))
		{
			$out .= '?registerproccess=1';
		}
		$out .= '">uppladdningen</a>.<br/>';

			$swfurl = 'cropper.swf?cropheight=' . $img_height . '&cropwidth=' . $crop_width . '&imageFile=' . $img_tmp_orginal_url . '&postFile=avatar-settings.php?action=crop';
			if (isset($_GET['registerproccess']))
			{
				$swfurl.= '%26registerproccess=1';
			}
			$swfurl.= '&imageWidth=442&imageHeight=' . $img_height . '&cropText=Klar&hiddenFields=&imageQueryString=&target=_self';

			$out .= '<object type="application/x-shockwave-flash" data="' . $swfurl . '" width="460" height="' . $flash_height . '">';
			$out .= '<param name="movie" value="' . $swfurl . '" />';
			$out .= '</object>';

	}

	elseif($_GET['step'] == '3')
	{
		$out .= '<h1 style="margin: 0px; ">Förhandsgranska och spara (steg 3)</h1>';
		$out .= 'Du har nu skalat och beskärt ditt foto. Om du tycker ditt foto är okej så tryck på Spara så kommer ditt foto sparas och läggas in för granskning.<br /><br />';
		$out .= '» Vill du ladda upp en annan bild så gå tillbaka till <a href="avatar-settings.php';
		$out .= (isset($_GET['registerproccess']) ? '?registerproccess=1' : '');
		$out .= '">uppladdningen</a>.<br/>';
		$out .= '» Eller vill du skala om bilderna gå till <a href="avatar-settings.php?step=2';
		$out .= (isset($_GET['registerproccess']) ? '&registerproccess=1' : '');
		$out .= '">beskärningen</a>.';


		$out .= '<div id="preview">';
		$out .= '<br /><b>Fullstorlek:</b><br/> <img src="' . $img_tmp_full_url . '">';
		$out .= '<br /><b>Thumbnail:</b><br /> <img src="' . $img_tmp_thumb_url. '">';
		$out .= '</div>';
		$out .= '<p><form name="saveimg" action="avatar-settings.php?action=save" method="post" enctype="multipart/form-data" style="margin: 0px;">';
		$out .= '<input name="submit" type="submit" value="Spara" class="button" /><br />';
		if (isset($_GET['registerproccess']))
		{
			$out .= '<input type="hidden" name="registerproccess" value="1" />';
		}
		$out .= '</form></p>';
	}

	ui_top($ui_options);
	echo rounded_corners($out);
	ui_bottom();
?>
