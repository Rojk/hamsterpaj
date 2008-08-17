<?php    
	require('../include/core/common.php');
	$ui_options['admtoma_category'] = 'chat';
	$ui_options['menu_path'] = array('chatt');
	$ui_options['stylesheets'][] = 'chat.css';
	ui_top($ui_options);

	$url = 'http://ved.hamsterpaj.net/chatt/index.php?';
	if(login_checklogin())
	{
		$url .= 'nick=';
		/* Please comment this if you understand the following lines */
		/* Ask Joel, he can explain it. /Joel */
		if (!preg_match("/^[A-Za-z]$/i",substr($_SESSION['login']['username'],0,1)))
		{
			$url.= substr($_SESSION['login']['username'],1,strlen($_SESSION['login']['username']));
		}
		else
		{
			$url .= $_SESSION['login']['username'];
		}
		$url .= '&amp;realname=';
		$ageArray = date_get_age($_SESSION['userinfo']['birthday']); 
		$url .= urlencode($ageArray . ';');
		$url .= urlencode($_SESSION['userinfo']['gender'] . ';');
		$url .= urlencode($_SESSION['userinfo']['location'] . ';');
		$url .= urlencode($_SESSION['login']['id'] . ';');
		$url .= urlencode($_SESSION['userinfo']['image'] . ';');
	}
	else
	{
		$url .= 'guest';
	}
	
	$fjortis_link = '<a href="javascript: void(0);" onclick="window.open(\'' . $url . '&amp;chan=fjortis' . '\',\'' . rand() . '\',\'location=no, width=640, height=478\')">';
	$traffa_link = '<a href="javascript: void(0);" onclick="window.open(\'' . $url . '&amp;chan=' . urlencode('träffa') . '\',\'' . rand() . '\',\'toolbar=no, width=640, height=478\')">';
	$kuddhornan_link = '<a href="javascript: void(0);" onclick="window.open(\'' . $url . '&amp;chan=' . urlencode('kuddhörnan') . '\',\'' . rand() . '\',\'toolbar=no, width=640, height=478\')">';
	$trivia_link = '<a href="javascript: void(0);" onclick="window.open(\'' . $url . '&amp;chan=trivia' . '\',\'' . rand() . '\',\'toolbar=no, width=640, height=478\')">';
	$webdesign_link = '<a href="javascript: void(0);" onclick="window.open(\'' . $url . '&amp;chan=webdesign' . '\',\'' . rand() . '\',\'toolbar=no, width=640, height=478\')">';
	$musik_link = '<a href="javascript: void(0);" onclick="window.open(\'' . $url . '&amp;chan=musik' . '\',\'' . rand() . '\',\'toolbar=no, width=640, height=478\')">';

	$chattare = file_get_contents('clients.txt');
	$chattare = explode("\n", $chattare);
?>
<h1>Chatten på hamsterpaj.net</h1>

<a href="http://www.java.com/" target="_blank">
	<div class="java_needed">
		<h2>Du måste ha Java på din dator för att chatten ska fungera!</h2>
		<p>
			Om chatten inte fungerar kan du behöva installera Java på din dator. Klicka här för att gå
			till Java.com, välj sedan <em>"Download now"</em>
		</p>
	</div>
</a>

<div class="chat_channel">
	<?php echo $traffa_link; ?>
		<h3 class="channel">#Träffa</h3>
		<h3 class="client_count">(150 chattare)</h3>
		<p>
			Öppen för alla som vill hitta någon ny människa att prata med
		</p>
	
		<ul>
			<li>Inget tjat om webcam</li>
			<li>Inget <em>"Skriv 123 om du vill chatta"</em></li>
			<li>Lyd om någon med % eller @ före namnet säger till dig</li>	
		</ul>
	</a>
</div>

<div class="chat_channel">
	<?php echo $fjortis_link; ?>
		<h3 class="channel">#Fjortis</h3>
		<h3 class="client_count">(150 chattare)</h3>
		<p>
			<p>Kanal öppen för våra yngre medlemmar.</p>
			<p>HÄR kan du komma med "...tryck 123 om du vill chatta" och sådant.</p>
			<p>Öppet för det mesta - utom sexsnack.</p>
			<p>Inga cam-förfrågningar!</p>
		</p>
	</a>
</div>

<div class="chat_channel">
	<?php echo $kuddhornan_link; ?>
		<h3 class="channel">#Kuddhörnan</h3>
		<h3 class="client_count">(150 chattare)</h3>
		<p>
			<p>Lite mysigare - lite gosigare.<br />Kuddkrig är fullt tillåtna. ;)</p>
			<p>Här kan man kanske hitta någon att mysa med? =)</p>
		</p>
	</a>
</div>

<div class="chat_channel">
	<?php echo $musik_link; ?>
		<h3 class="channel">#Musik</h3>
		<h3 class="client_count">(150 chattare)</h3>
		<p>
			Musikkanalen här på Hamsterpaj!
		</p>
	
		<ul>
			<li>MP3-script tillåtna</li>
		</ul>
	</a>
</div>


<div class="chat_channel">
	<?php echo $webdesign_link; ?>
		<h3 class="channel">#Webdesign</h3>
		<h3 class="client_count">(150 chattare)</h3>
		<p>
			Hjälpkanal för webbutveckling; CSS, (X)HTML, MySQL, PHP
		</p>
	
		<ul>
			<li>Du ska kunna grundläggade HTML för att få vara här</li>
			<li>Länka till dina problem</li>
			<li>Klistra inte in kod i kanalen</li>
		</ul>
	</a>
</div>


<div class="chat_channel">
	<?php echo $trivia_link; ?>
		<h3 class="channel">#Trivia</h3>
		<h3 class="client_count">(150 chattare)</h3>
		<p>
			Frågesport på chatten. Tävla mot andra och se vem som fortast kommer på rätt svar i frågor
			om allt mellan himmel och jord.
		</p>
	
		<ul>
			<li>Skriv <strong>!ask</strong> för att starta en ny omgång</li>
		</ul>
	</a>
</div>

<br style="clear: both;" />

<h1>Chatta med mIRC</h1>
<p>Om du vill chatta med mIRC så kan du titta bland <a href="/annat/nedladdningar.php">Ladda ner program</a>, där finns en länk till mIRC. För att komma igång med programmet, läs vår <a href="/artiklar/?action=show&id=48">mIRC-guide</a></p>

<dl>
	<dt>Server</dt>
	<dd>irc.hamsterpaj.net</dd>
</dl>

<?php
	ui_bottom();
?>
