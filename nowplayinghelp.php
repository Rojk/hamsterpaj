<?php
	require('include/core/common.php');
	
	$ui_options['current_menu'] = 'annat';
	$ui_options['title'] = 'Visa vad du spelar just nu';

	ui_top($ui_options);

	if(login_checklogin())
	{
?>

<h1>För att detta ska fungera måste du använda Winamp!</h1>

<ol>
	<li>Ladda hem <a href="http://www.winamp.com/plugins/details.php?id=138883">Now playing plugin</a></li>
	<li>Stäng av Winamp</li>
	<li>Dubbelklicka och kör installationsfilen</li>
	<li>Starta winamp</li>
	<li>Tryck <strong>Options</strong> -&gt; <strong>Preferences</strong></li>
	<li>Leta upp <strong>General purpose</strong> i listan till vänst, finns nästan längst ner i kategorin <strong>Plug-ins</strong></li>
	<li>Markera <strong>Now Playing plug-in v<em>x</em>.<em>x</em>  [gen_NowPlaying.dll]</strong></li>
	<li>Tryck <strong>Configure selected plug-in</strong></li>
	<li>Under fliken <strong>General Options</strong> bockar du i <strong>Enabled</strong></li>
	<li>Växla till fliken <strong>HTTP Post</strong>, där fyller du i följande:
		<ul>
			<li><strong>HTTP Post Enabled</strong></li>
			<li><strong>Url:</strong> http://www.hamsterpaj.net/musicpost.php</li>
<?php
			echo '<li><strong>Extra data:</strong> userid=' . $_SESSION['login']['id']. '&hash=' . md5($_SESSION['login']['id'] . 'gullejo') . '</li>' . "\n";
?>
		</ul>
	</li>
	<li>Välj <strong>Apply</strong> och sedan <strong>Close</strong></li>
</ol>
<?php
	}
	else
	{
		echo '<h1>Endast medlemmar kan aktivera now playing, man måste ju ha en presentation... ;)</h1>' . "\n";
	}
	ui_bottom();

?>


