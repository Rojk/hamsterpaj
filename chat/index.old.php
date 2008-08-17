<?php    


	require('../include/core/common.php');
	$ui_options['admtoma_category'] = 'chat';
	$ui_options['current_menu'] = 'chat';
	$ui_options['current_submenu'] = 'Chatten';
	ui_top($ui_options);

	$faq_category = 'chat';
	
	$chat_is_working=0; // 1 or 0

	$url = 'http://ved.hamsterpaj.net/chatt/index.php?';
	if(login_checklogin()){
		$url .= 'nick=';
		if (!preg_match("/^[A-Za-z]$/i",substr($_SESSION['login']['username'],0,1))) {
			$url.= substr($_SESSION['login']['username'],1,strlen($_SESSION['login']['username']));
		}
		else {
			$url .= $_SESSION['login']['username'];
		}
		$url .= '&realname=';
		$ageArray = date_get_age($_SESSION['userinfo']['birthday']); 
		$url .= urlencode($ageArray . ';');
		$url .= urlencode($_SESSION['userinfo']['gender'] . ';');
		$url .= urlencode($_SESSION['userinfo']['location'] . ';');
		$url .= urlencode($_SESSION['login']['id'] . ';');
		$url .= urlencode($_SESSION['userinfo']['image'] . ';');
		$url .= '&port=53';
	}
	else {
		$url .= 'guest';
	}
	$webcam_link = '<a href="javascript:;" onclick="window.open(\'' . $url . '&amp;chan=webcam' . '\',\'' . rand() . '\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=yes, copyhistory=no, width=640, height=478\')">';
	$traffa_link = '<a href="javascript:;" onclick="window.open(\'' . $url . '&amp;chan=träffa' . '\',\'' . rand() . '\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=yes, copyhistory=no, width=640, height=478\')">';
	$chat_link = '<a href="javascript:;" onclick="window.open(\'' . $url . '&amp;chan=chat' . '\',\'' . rand() . '\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=yes, copyhistory=no, width=640, height=478\')">';
	$radio_link = '<a href="javascript:;" onclick="window.open(\'' . $url . '&amp;chan=hamsterradio' . '\',\'' . rand() . '\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=yes, copyhistory=no, width=640, height=478\')">';
	$moget_link = '<a href="javascript:;" onclick="window.open(\'' . $url . '&amp;chan=moget' . '\',\'' . rand() . '\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=yes, copyhistory=no, width=640, height=478\')">';
	$trivia_link = '<a href="javascript:;" onclick="window.open(\'' . $url . '&amp;chan=trivia' . '\',\'' . rand() . '\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=yes, copyhistory=no, width=640, height=478\')">';
	$chattare = file_get_contents('clients.txt');
	$chattare = explode("\n", $chattare);
	if ($chat_is_working != '1') {
		/*echo '<div id="loading" style="position:absolute;visibility:visible;padding:3px;';
		echo 'border-style:solid;border-width:2px;border-color:black; left:90px; top:120px;';
		echo 'width:353px; height:71px; background-color:#C0C0C0;">';*/
		//echo '<p align="center"><font face="Verdana" size="4">Chatten är för närvarande ur funktion!</font></p>';
		//echo '<p align="center"><font face="Verdana" size="4">Läs driftstatus på förstasidan!</font></div>';
	}
?>
<h1>Chatten på hamsterpaj.net</h1>
	
<div class="pink_faded_div">
<h2>#<?php echo $webcam_link; ?>Webcam</a></h2>
Vi tröttnade på alla förbannade tolvåringar som vill visa snoppen i webcam och gjorde en egen
skitkanal åt dom. Vi tycker inte att någon ska visa sig naken i webcam, men här samlar vi
iallafall alla kåta killar som inte lyckas få något i verkligheten och därför tjatar på
tjejer på nätet.<br />
<b><?php echo $webcam_link; ?>Kliv in i #Webcam&gt;&gt;</a></b>
</div>
	
<div class="pink_faded_div">
<h2>#<?php echo $traffa_link; ?>Träffa</a> (<?php echo $chattare[0]; ?> chattare just nu) </h2>

Har du svårt att hålla dig till reglerna i de andra kanalerna?
<br /><br/>Här är kanalen för er som inte riktigt klarar av de andra kanalerna. <br />
Här är det mesta fritt och du är välkommen att stiga på när du vill, men några regler finns det allt. <br />
<br />
<strong>Regler:</strong></li>
<ul>
<li>Skriv inte med STORA bokstäver</li>
<li>Upprepa dig inte</li>
<li>Bryt inte mot Sveriges Rikes Lag</li>
<li>Klona inte dig själv</li>
</ul>
<br />
<b><?php echo $traffa_link; ?>Kliv in i #Träffa&gt;&gt;</a></b>
</div>

<div class="blue_faded_div">
<h2>#<?php echo $chat_link; ?>Chat</a> (<?php echo $chattare[1]; ?> chattare just nu) </h2>


Här är kanalen för dig som bara vill chatta, diskutera och samtala. Bara glid in i samtalet och fråga inte om någon vill chatta.
<br /><br />

<strong>Regler:</strong>
<ul>
<li>Skriv inte med STORA bokstäver</li>
<li>Upprepa dig inte</li>
<li>Ragga inte</li>
<li>Bryt inte mot Sveriges Rikes Lag</li>
<li>Fråga inte om någon vill chatta.</li>
<li>Klona inte dig själv</li>
</ul>
<br />

<b><?php echo $chat_link; ?>Kliv in i #Chat&gt;&gt;</a></b>
</div>

<div class="blue_faded_div">
<h2>#<?php echo $radio_link; ?>Hamsterradio</a> (?? chattare just nu) </h2>

Här kan du diskutera Hamsterpajs egen webbradio. Vill du önska låtar så gör du det inte här utan på www.hamsterpaj.net/radio.
<br />Där kan du också skicka in hälsningar och frågor till våra radiopratare.
<br/><br />
<strong>Regler:</strong>
<ul>
<li>Håll dig till att prata om radion</li>
<li>Skriv inte med STORA bokstäver</li>
<li>Upprepa dig inte</li>
<li>Bryt inte mot Sveriges Rikes Lag</li>
<li>Önska inte låtar här</li>
<li>Klona inte dig själv</li>
</ul>
<br/>
<b><?php echo $radio_link; ?>Kliv in i #Hamsterradio&gt;&gt;</a></b>
</div>


<?php
if(date_get_age($_SESSION['userinfo']['birthday']) > 14 || !isset($_SESSION['login']) || $_SESSION['userinfo']['birthday'] == '0000-00-00')
{
?>
<div class="orange_faded_div">
<h2>#<?php echo $moget_link; ?>moget</a>  - 15 års åldersgräns (<?php echo $chattare[2]; ?> chattare just nu) </h2>

Det är här veteranerna håller till och då gäller det att vara mogen. <br />
Nolltolerans och femtonårsgräns är vad som gäller i #moget, men bli inte avskräckt från att titta in. 
<br />Men bara om du är mogen såklart.<br/><br />

<strong>Regler:</strong>
<ul>
<li>Skriv inte med STORA bokstäver</li>
<li>Upprepa dig inte</li>
<li>Bryt inte mot Sveriges Rikes Lag</li>
<li>Fråga inte om någon vill chatta</li>
<li>Ragga inte</li>
<li>Svara med din rätta ålder när du blir tillfrågad</li>
<li>Klona inte dig själv</li>
</ul>
<br />
<b><?php echo $moget_link; ?>Kliv in i #moget&gt;&gt;</a></b>
</div>
<?php
}
?>

<div class="green_faded_div">
<h2>#<?php echo $trivia_link; ?>Trivia - Frågesport</a></h2>
Hamsterchattens egna frågesport. Kliv in och roa dig en stund med frågesporten du med
<br /><br />
<b>Regler:</b><ul>
<li>Håll dig till att svara på frågorna</li>
<li>Chattande hänvisas till andra kanaler</li>
<li>Spamma inte</li>
<li>Klona inte dig själv</li>
</ul>
<br />
<b><?php echo $trivia_link; ?>Kliv in i #trivia&gt;&gt;</a></b>
</div>


<div class="grey_faded_div">
<h2>Bra att veta</h2>
<b>Kommandon</b><br />
<i>!info Foo_Bar</i> - Visar lite information om Foo_Bar<br />
<i>/ignore Foo_Bar</i> - Blockerar Foo_Bar från att skriva till dig.<br />
<i>/me äter bullar</i> - Skriver "* Pelle äter kakor" i lila text om du heter Pelle.<br />
<i>/join #chat</i> - Gör att du hoppar in i #chat. På så sätt kan du vara i både #Träffa och #chat samtidigt.<br />
<i>/nick nyttnick</i> - Gör att du byter nick till nyttnick dvs /nick Pelle så heter du Pelle i chatten sen utan att behöva starta om chatten<br />
<br />
<b>Java</b><br />
För att kunna chatta direkt från www.hamsterpaj.net behöver man ett gratis och ofarligt insticksprogram kallat Java.<br />
<a href="/guider/java.php">Klicka här för att installera java</a>
<br /><br />
<b>IRC</b><br />
Våran chatt använder något som kallas för IRC - <i>Internet Relay Chat</i>. Om du är van att använda exempelvis mIRC eller irssi
så går det alldeles utmärkt att använda dessa istället för java-appleten.<br />
Skriv bara <i>/server irc.hamsterpaj.net</i> och sedan <i>/join #chat</i> i din IRC-klient.<br />
Om du redan chattar på t.ex. QuakeNet med mIRC så kan du ansluta till hamsterpaj.net utan att lämna QuakeNet, då skriver du:
<i>/server&nbsp;-m&nbsp;irc.hamsterpaj.net&nbsp;-j&nbsp;#chat</i>.
<br /><br />
<b>Operatörer</b><br />
Personer med % eller @ är operatörer och deras uppgift är att hålla ordning i chatten.<br />
De som ansvarar för att chatten fungerar heter Heggan och Johan, det är även de två som utser operatörer.
<br /><br />
<b>Hamstern</b><br />
Hamstern är en bot, en slags chatt-dator. Han är ingen människa utan ett datorprogram. Trots detta kan han göra mycket nytta<br />
Han slänger ut folk som upprepar sig eller skriver för mycket skit.<br />
Han kan även en del roliga kommandon, såsom:<br />
<i>!straffa Foo_Bar</i> - Belönar Foo_Bar med slumpmässigt straff.<br />
<i>!peak</i> - Visar kanalens peak, dvs hur många som max varit inne samtidigt.
</div>


<?php
	ui_bottom();
?>
