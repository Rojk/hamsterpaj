<?php    
	require('../include/core/common.php');
	$ui_options['admtoma_category'] = 'chat';
	$ui_options['current_menu'] = 'chat';
	$ui_options['current_submenu'] = 'Chatten';
	$ui_options['stylesheets'][] = 'chat.css';
	ui_top($ui_options);

	$url = 'http://ved.hamsterpaj.net/chatt/index.php?';
	if(login_checklogin())
	{
		$url .= 'nick=';
		
		/* Please comment this if you understand the following lines */
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
	
	$webcam_link = '<a href="javascript: void(0);" onclick="window.open(\'' . $url . '&amp;chan=webcam' . '\',\'' . rand() . '\',\'location=no, width=640, height=478\')">';
	$traffa_link = '<a href="javascript: void(0);" onclick="window.open(\'' . $url . '&amp;chan=' . urlencode('träffa') . '\',\'' . rand() . '\',\'toolbar=no, width=640, height=478\')">';
	$chat_link = '<a href="javascript: void(0);" onclick="window.open(\'' . $url . '&amp;chan=chat' . '\',\'' . rand() . '\',\'toolbar=no, width=640, height=478\')">';
	$radio_link = '<a href="javascript: void(0);" onclick="window.open(\'' . $url . '&amp;chan=hamsterradio' . '\',\'' . rand() . '\',\'toolbar=no, width=640, height=478\')">';
	$moget_link = '<a href="javascript: void(0);" onclick="window.open(\'' . $url . '&amp;chan=moget' . '\',\'' . rand() . '\',\'toolbar=no, width=640, height=478\')">';
	$trivia_link = '<a href="javascript: void(0);" onclick="window.open(\'' . $url . '&amp;chan=trivia' . '\',\'' . rand() . '\',\'toolbar=no, width=640, height=478\')">';
	$webdesign_link = '<a href="javascript: void(0);" onclick="window.open(\'' . $url . '&amp;chan=webdesign' . '\',\'' . rand() . '\',\'toolbar=no, width=640, height=478\')">';

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
	<?php echo $webcam_link; ?>
		<h3 class="channel">#Webcam</h3>
		<h3 class="client_count">(150 chattare)</h3>
		<p>
			Jobbiga tolvåriga pojkar som vill visa snoppen i webcam och försöker lura
			tjejer att klä av sig i webcam.
		</p>
	
		<ul>
			<li>Nästan ingen övervakning</li>
			<li>Tjat om webcam och sex hela tiden</li>
		</ul>
	</a>
</div>


<div class="chat_channel">
	<?php echo $moget_link; ?>
		<h3 class="channel">#Moget</h3>
		<h3 class="client_count">(150 chattare)</h3>
		<p>
			IRC som det var förr, där folk vet hur man beter sig och den som känner OP får voice
		</p>
	
		<ul>
			<li>Inget <em>"Nån som vill chatta?"</em></li>
			<li>Inga mp3-script</li>
		</ul>
	</a>
</div>

<div class="chat_channel">
	<?php echo $radio_link; ?>
		<h3 class="channel">#Hamsterradio</h3>
		<h3 class="client_count">(150 chattare)</h3>
		<p>
			Webbradions chattkanal - prata med andra lyssnare här!
		</p>
	
		<ul>
			<li>Önska låtar och hälsa under [Hamsterradio] istället för på chatten!</li>
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

<?php
	ui_bottom();
?>
