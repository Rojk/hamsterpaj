<p>
Här är en snabbreferens med kortkommandon i Windows, allting fungerar i XP, men kan fungera lite till och från i andra versioner.
</p>
<?php
$shortcuts['cut']['keys'] = array('ctrl', 'x');
$shortcuts['cut']['text'] = 'Klipper ut det du markerat och sparar i klippbordet';
	
$shortcuts['copy']['keys'] = array('ctrl', 'c');
$shortcuts['copy']['text'] = 'Kopierar det du markerat och sparar i klippbordet';
	
$shortcuts['paste']['keys'] = array('ctrl', 'v');
$shortcuts['paste']['text'] = 'Klistrar in det som finns i klippbordet';
	
$shortcuts['save']['keys'] = array('ctrl', 's');
$shortcuts['save']['text'] = 'Sparar filen eller dokumenter du har öppet';
	
$shortcuts['select_all']['keys'] = array('ctrl', 'a');
$shortcuts['select_all']['text'] = 'Markerar allt (alla filer, all text, alla bilder...)';
	
$shortcuts['undo']['keys'] = array('ctrl', 'z');
$shortcuts['undo']['text'] = 'Ångrar senaste ändringen';
	
$shortcuts['find']['keys'] = array('ctrl', 'f');
$shortcuts['find']['text'] = 'Öppnar sökrutan';
	
$shortcuts['find_next']['keys'] = array('f3');
$shortcuts['find_next']['text'] = 'Visar nästa träff i din sökning';
	
$shortcuts['properties']['keys'] = array('alt', 'dubbelklick');
$shortcuts['properties']['comment'] = 'På fil eller mapp';
$shortcuts['properties']['text'] = 'Visar egenskaper för filen eller mappen';
	
$shortcuts['print']['keys'] = array('ctrl', 'p');
$shortcuts['print']['text'] = 'Öppnar dialogen "Skriv ut"';
	
$shortcuts['explorer']['keys'] = array('windows', 'e');
$shortcuts['explorer']['text'] = 'Öppnar utforskaren (windows explorer)';
	
$shortcuts['refresh']['keys'] = array('f5');
$shortcuts['refresh']['text'] = 'Uppdaterar innehållet i det fönster du har uppe';
	
$shortcuts['disable_autorun']['keys'] = array('shift');
$shortcuts['disable_autorun']['comment'] = 'Håll nere';
$shortcuts['disable_autorun']['text'] = 'Förhindrar "autorun" när du sätter i en CD-skiva';
	
$shortcuts['high_contrast']['keys'] = array('shift', 'alt', 'print screen');
$shortcuts['high_contrast']['comment'] = 'Välj OK i rutan som kommer upp';
$shortcuts['high_contrast']['text'] = 'Växlar till högkontrastläge (svart, vitt, äckliga färger och jättestor text). Samma för att gå tillbaks till vanligt läge.';
	
$shortcuts['switch_app']['keys'] = array('alt', 'tab');
$shortcuts['switch_app']['text'] = 'Bläddrar genom program som är igång';
	
$shortcuts['delete_perm']['keys'] = array('shift', 'delete');
$shortcuts['delete_perm']['comment'] = 'Markera en eller flera filer först';
$shortcuts['delete_perm']['text'] = 'Tar bort filen/filerna utan att flytta den till papperskorgen först';
	
$shortcuts['switch_app_reverse']['keys'] = array('alt', 'tab', 'shift');
$shortcuts['switch_app_reverse']['text'] = 'Bläddrar genom program som är igång, men stegar baklänges genom programmen';

$shortcuts['close']['keys'] = array('alt', 'f4');
$shortcuts['close']['text'] = 'Stänger programmet eller rutan du har igång just nu';

$shortcuts['sub_close']['keys'] = array('ctrl', 'f4');
$shortcuts['sub_close']['text'] = 'Stänger rutan eller filen du har uppe inuti ett program, utan att avsluta programmet';

$shortcuts['minimize_all']['keys'] = array('windows', 'm');
$shortcuts['minimize_all']['text'] = 'Minimerar alla fönster så att du ser skrivbordet';

$shortcuts['undo_minimize_all']['keys'] = array('shift', 'windows', 'm');
$shortcuts['undo_minimize_all']['text'] = 'Öppnar alla fönster som minimerats med windows + m';

$shortcuts['lock']['keys'] = array('windows', 'l');
$shortcuts['lock']['text'] = 'Låser datorn så att lösenord måste anges för att fortsätta arbeta';
	
$shortcuts['run']['keys'] = array('windows', 'r');
$shortcuts['run']['text'] = 'Öppnar rutan "kör"';
	
$shortcuts['next_field']['keys'] = array('tab');
$shortcuts['next_field']['text'] = 'Hoppar till nästa inmatningsfält eller knapp';

$shortcuts['previous_field']['keys'] = array('shift', 'tab');
$shortcuts['previous_field']['text'] = 'Hoppar till föregående inmatningsfält eller knapp';

sort($shortcuts);

echo '<ul style="list-style-type: none;padding-left: 0px;">' . "\n";
foreach($shortcuts AS $shortcut)
{
echo '<li style="clear: both; margin-bottom: 10px;">' . "\n";
echo '<img src="http://images.hamsterpaj.net/li_arrow.png" style="float: left;" />' . "\n";
echo '<h3 style="margin: 0px; padding: 0px;">';
$first_key = true;
foreach($shortcut['keys'] AS $key)
{
if($first_key == true)
{
$first_key = false;
}
else
{
echo ' + ';
}
echo '<strong class="key_' . $key . '">' . ucfirst($key) . '</strong>';
}
if(isset($shortcut['comment']))
{
echo ' (' . $shortcut['comment'] . ')';
}
echo '</h3>' . "\n";
echo '<p style="margin: 0px; margin-top: 2px; padding: 0px;">' . "\n";
echo $shortcut['text'] . "\n";
echo '</p>' . "\n";
}
echo '</ul>' . "\n";
?>