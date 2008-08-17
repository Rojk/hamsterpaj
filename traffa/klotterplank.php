<?php	

	/*
	Gästbok kodad av Schneaker 2004-08-13. Använder tabellen "guestbook". Målet var väl främst att bredda c0ke i snabbhet, tydlighet, lättlästhet och radantal.
	$_GET['sendAnswer'] = Ett svar skickas, variabeln innehåller ID-nummret på det inlägg som skall besvaras
	$_GET['answer'] = Visar dialogen för att besvara ett inlägg, variabeln håller inläggets ID-nummer
	$_GET['hide'] = Döljer ett inlägg, variabeln håller ID-nummret på inlägget som skall döljas.
	$_GET['edit'] = Visar dialogen för att redigera inlägg, variabeln håller ID-nummret på inlägget som skall redigeras.
	$_GET['ip'] = Visar alla inlägg från detta IP-nummer
	$_GET['userid'] = Visar alla inlägg från detta userid
	*/
	
//	header('location: /traffa/klotterplanket.php');


	$text_colors=array('black','red','orange','pink','green','blue','purple','brown');
	$entries='50';

	require('../include/core/common.php');
	$ui_options['menu_path'] = array('traeffa', 'gamla_klotterplanket');

	ui_top($ui_options);


	function makeQuery($mode, $criteria){
		$query = 'SELECT ';
		$query .= 'klotterplank.id, klotterplank.text, klotterplank.userid, ';
		$query .= 'klotterplank.timestamp, login.username, userinfo.birthday, userinfo.image, userinfo.gender, userinfo.zip_code, zip_codes.spot AS geo_location ';
		$query .= 'FROM ';
		$query .= 'klotterplank, login, userinfo, zip_codes ';
		$query .= 'WHERE userinfo.userid = klotterplank.userid AND login.id = klotterplank.userid AND zip_codes.zip_code = userinfo.zip_code ';
		if($mode == 'ip')
		{
			$query .= 'AND klotterplank.ip = "' . $criteria . '" ';
		}
		elseif($mode == 'userid'){
			$query .= 'AND klotterplank.userid = ' . $criteria . ' ';
		}
		$query .= 'ORDER BY klotterplank.id DESC ';
		global $entries;
		if($mode == 'normal'){
			$query .= 'LIMIT '.$entries;
		}
		return $query;
	}
	function viewPosts($mode = 'normal', $criteria = NULL){//Visar alla inlägg
		$query = makeQuery($mode, $criteria);
		$result = mysql_query($query) or die('Ett fel har upstått:<br/>' . mysql_error());
		$genderlabels['P'] = 'Pojke';
		$genderlabels['F'] = 'Flicka';

		while($data = mysql_fetch_assoc($result)){
			$userage = date_get_age($data['birthday']);
			if($data['gender'] == 'P')
			{
				$divbg = 'blue_faded_div';
			}
			elseif($data['gender'] == 'F')
			{
				$divbg = 'pink_faded_div';
			}
			else
			{
				$divbg = 'grey_faded_div';
			}

			if (isset($_SESSION['login']['username'])) {
				if (preg_match("/".$_SESSION['login']['username']."/i",$data['text'])) {
					$divbg = 'orange_faded_div';
				}
				elseif ($_SESSION['login']['username'] == $data['username']) {
					$divbg = 'green_faded_div';
				}
			}

			echo '<div class="'.$divbg.'" style="repeat-x; margin-top: 10px; border: 1px solid #CCCCCC;">' . "\n";
			echo '<table class="body" style="width: 100%;"><tr><td style="vertical-align: top; width: 75px;">' . "\n";
			if($data['image'] == 1 || $data['image'] == 2)
			{
				echo insert_avatar($data['userid']);
			}
			else
			{
				echo '<img src="/images/noimage.png" style="width: 75px; height: 75px; border: 1px solid #cccccc;" alt="Ingen visningsbild"/>' . "\n";
			}
			echo '</td><td style="vertical-align: top;">';
			echo fix_time($data['timestamp']) . ' (inlägg #' . $data['id'] . ') <a style="text-decoration:none;" href="javascript:#;" onclick="javascript:document.gbform.message.value=document.gbform.message.value+\''.$data['username'].' : \';document.gbform.message.focus();">[^]</a><br/>' . "\n";
			echo '<a href="' . $hp_url . '/traffa/profile.php?id=' . $data['userid'] . '">' . "\n";
			echo '<b>' . $data['username']  . '</b></a> ' . "\n";
			echo birthdaycake($data['birthday']) . ' ' . "\n";
			echo $genderlabels[$data['gender']];
			if($userage > 1)
			{
				echo ' ' . $userage . 'år' . "\n";
			}
			if(strlen($data['geo_location']) > 1)
			{
				echo ' från ' . htmlentities($data['geo_location']) . "\n";
			}
			echo '<br/>' . "\n";
			echo setsmilies($data['text']) . "\n";
			echo '</td></tr></table>' . "\n";
			echo '</div>' . "\n";
		}//while-satsen för att skriva ut inlägg
	}//Funktionsasvlutet

	function spamFilter($message,$ip,$nick) {//Returnerar TRUE om testet klarades, annars skriver funktionen ut felmeddelande
		$message = strtolower($message);
		if($_SESSION['login']['userlevel'] >= 5){ //Ingen spamcheck för userlevel 3+
			return TRUE;
		}
		if(strlen($message) < 2)
		{
			jscript_alert('Lite mer än sådär får du allt skriva...');
			return FALSE;
		}
		$content_check_retval = content_check($message);
		if($content_check_retval != 1)
		{
			jscript_alert($content_check_retval);
			return FALSE;
		}
		if(strlen($message) > 4000)
		{
			jscript_alert('Försök fatta dig lite kortare, det är trots allt ett klotterplank. Använd forumet om du vill diskutera!');
			return FALSE;
		}
		$query = 'SELECT COUNT(id) AS total FROM klotterplank WHERE userid = ' . $_SESSION['userid'] . ' AND timestamp > UNIX_TIMESTAMP() - 60';
		$result = mysql_query($query);
		$data = mysql_fetch_assoc($result);
		if($data['total'] > 0){
			jscript_alert('Max ett inlägg per minut, ge dig till tåls litegranna');
			return FALSE;
		}


		return TRUE;
	}
	function drawKlotterplankPostForm($message = NULL) {
		echo '<div class="grey_faded_div">' . "\n";
		echo '<h2>Klotterplanket - skriv nytt inlägg</h2>' . "\n";
		if ($_SESSION['klotterplank']['lastpost'] > time()-60) {

	echo '<script language="javascript">' . "\n";
	echo 'function fixtime(input){' . "\n";
		echo 'minutes = Math.round((input / 60) + 0.5) - 1;' . "\n";
		echo 'seconds = input - (minutes * 60);' . "\n";
		echo 'minutes = minutes + "";' . "\n";
		echo 'if(minutes.length < 2){' . "\n";
			echo 'minutes = "0" + minutes;' . "\n";
		echo '}' . "\n";
	echo 'seconds = seconds + "";' . "\n";
	echo 'if(seconds.length < 2){' . "\n";
		echo 'seconds = "0" + seconds;' . "\n";
	echo '}' . "\n";
	echo 'returnval = minutes + ":" + seconds;' . "\n";
	echo 'return returnval;' . "\n";
	echo '}' . "\n";

	echo 'function testtime(){' . "\n";
	echo 'if(input >= 0) {';
		echo 'document.forms.timeleft.counter.value = \'Tid kvar innan du kan posta igen: \' + fixtime(input);' . "\n";
		echo 'input = input - 1;' . "\n";
		echo 'setTimeout("testtime()", 1000);' . "\n";
	echo '}';
	echo 'else {';
		echo 'location.href="';
		echo $_SERVER['PHP_SELF']; 
		if (isset($_GET['reload'])) 
		{ 
			echo '?reload='.$_GET['reload']; 
		} 
		echo '";';
		echo '}' . "\n";
	echo '}' . "\n";
	echo '</script>' . "\n";

				echo '<form name="timeleft">' . "\n";
				echo '<input type="text" name="counter" class="subtitle" style="border: none; width: 500px;" disabled="true" />' . "\n";
				echo '</form>' . "\n";
				$timeleft = $_SESSION['klotterplank']['lastpost'] - time() + 60;
 				
 				echo '<script language="javascript">' . "\n";
 				echo 'var input = ' . $timeleft . ';' . "\n";
 				echo 'testtime();' . "\n";
 				echo '</script>' . "\n";
			}
			else {
				echo '<form name="gbform" action="' . $_SERVER['PHP_SELF'];
				if (isset($_GET['reload'])) {
					echo '?reload='.$_GET['reload'];
				}
				echo '" method="post">' . "\n";
				echo '<textarea tabindex="1" name="message" onkeypress="textCounter(this,5000);" class="textbox" style="width: 530px; height: 90px;">' . $message . '</textarea><br />' . "\n";

				echo '<input type="submit" value="Skicka" name="sendGB" class="button" style="width: 530px;" tabindex="2" />' . "\n";
				echo '<fieldset style="width: 500px;"><legend><b>Infoga smilies</b></legend>' . "\n";
				echo listSmilies('document.gbform.message');
				echo '</fieldset>' . "\n";
				echo '</form>' . "\n";
			}
		echo '</div>' . "\n";
	}
	function postToDatabase(){//skickar in ett vanligt GB-inlägg till databasen

		$message = wordwrap($_POST['message'], 59, "\n", 1);

		if ($_SESSION['login']['userlevel'] < 5) {
			$message = nl2br($message);
		}
		else {
			$message = nl2br($message);
		}

		$query = 'INSERT INTO klotterplank (userid, timestamp, text) VALUES ';
		$query .= '("' . $_SESSION['login']['id'] . '", UNIX_TIMESTAMP(), "' . $message . '")';
		mysql_query($query) or die('Det uppstod ett fel när inlägget skrevs till databasen. Försök igen senare<br/>' . mysql_error());
		global $entries;
		$query = 'DELETE FROM klotterplank WHERE id = ' . intval(mysql_insert_id() - $entries) . ' LIMIT 1';
		mysql_query($query) or die(report_sql_error($query));
		if ($_SESSION['login']['userlevel'] < 5) {
			$_SESSION['klotterplank']['lastpost'] = time();
		}
		else {
			$_SESSION['klotterplank']['lastpost'] = 1;
		}
		
		event_log_log('old_klotterplank_post');
	}

	/*
		HÄR SLUTAR FUNKTIONERNA OCH KODEN SOM KÖRS DIREKT BÖRJAR HÄR!
	*/
	
	rounded_corners_top(array('color' => 'orange'));
	echo '.<h1>Eyy, detta är gamla klotterplanket...</h1>...gå till <a href="http://www.hamsterpaj.net/traffa/klotterplanket.php">det nya klotterplanket</a> istället!';
	rounded_corners_bottom(array('color' => 'orange'));
	
	
	if (isset($_GET['reload']) && $_GET['reload']!="0") {
		if ($_GET['reload']!="10" && $_GET['reload']!="30" && $_GET['reload']!="60") { echo 'ogiltigt'; die; }
		echo '<script language="JavaScript" type="text/JavaScript">'."\n";
		echo '<!--//'."\n";
		echo 'function goReload() {'."\n";
		echo 'location.href="';
		echo $_SERVER['PHP_SELF'];
		echo '?reload='.$_GET['reload'];
		echo '&random='.rand(1000000,9999999);
		if (isset($_GET['stick']) or isset($_POST['stick'])) {
			echo '&stick=y';
		}
		echo '";'."\n";
		echo '}'."\n";
		echo 'setTimeout("goReload()", '.$_GET['reload'].'000)'."\n";
		echo '//--></script>';
	}
	echo '<form name="autoupdate" method="get" action="'.$_SERVER['PHP_SELF'].'"><select name="reload" onchange="autoupdate.submit();"><option value="0">-- Automatisk uppdatering --</option>';
  echo '<option value="10"';
    if (isset($_GET['reload']) && $_GET['reload']=="10") { echo ' selected'; }
  echo '>var 10:e sekund</option>';
	echo '<option value="30"';
    if (isset($_GET['reload']) && $_GET['reload']=="30") { echo ' selected'; }
	echo '>var 30:e sekund</option>';
	echo '<option value="60"';
		if (isset($_GET['reload']) && $_GET['reload']=="60") { echo ' selected'; }
	echo '>varje minut</option>';
	echo '<option value="0"';
    if (isset($_GET['reload']) && $_GET['reload']=="0") { echo ' selected'; }
	echo '>- Pausa -</option>';
	echo '</select>';
		if (isset($_POST['stick']) or isset($_GET['stick']))
		{
			echo '<input type="hidden" name="stick" value="yes">';
		}
		if (isset($_POST['color']) or isset($_GET['color']))
		{
			$this_color=@$_POST['color'].@$_GET['color'];
			echo '<input type="hidden" name="color" value="' . $this_color . '">';
		}
	echo '</form>';

	if(isset($_POST['sendGB']) && isset($_SESSION['login']['id'])){ //sendGB är namnet på [Skicka]-knappen
		if(spamFilter($_POST['message'],$_SERVER['REMOTE_ADDR'],$_POST['name'])){
			postToDatabase();
			drawKlotterplankPostForm();
		}
		else{
				drawKlotterplankPostForm($_POST['message']);
		}
	}
	elseif($_SESSION['login']['id'] > 0)
	{
		drawKlotterplankPostForm();
	}

	viewPosts('normal');

	ui_bottom();
?>
