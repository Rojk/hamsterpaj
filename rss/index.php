<?php
/*
TODO
add field to user's settings

*/
require('../include/core/common.php');
ui_top(array('title'=>'RSS - Hamsterpaj'));

echo '<h1>Hamsterpaj RSS</h1>'."\n";
echo '<br />'."\n";
echo '<h2>Gästböcker</h2>'."\n";
echo '<p>Du kan prenumerera på RSS-flöden från gästböcker. Skriv bara in användarnamnet eller användaridet.</p>'."\n";

echo '<form action="#" onsubmit="document.location.href = \'/rss/\'+document.getElementById(\'uid\').value;return false;">
Namn eller användarid: <input type="text" id="uid" />
<input type="submit" value="Få RSS-flöde!" />
</form>'."\n";
echo '<img src="'.IMAGE_URL.'rss-icon.jpg" alt="RSS-ikon" style="padding-left: 90px;" />';

ui_bottom();
?>
