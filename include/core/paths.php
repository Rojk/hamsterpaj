<?php
        /*
                paths.php
                - - - - - - - - - -
                Den här filen innehåller strängar med path/URL till siten.
                Ändras enbart när siten flyttas till annan plats på servern.
        */
        
	if($_SERVER['SERVER_NAME'] == 'dev.hamsterpaj.net')
	{
		$hp_path = '/storage/www/dev.hamsterpaj.net/data/';
	}
	else
	{
	  $hp_path = '/storage/www/www.hamsterpaj.net/data/';
	}
  $hp_includepath = $hp_path . 'include/';

  if($_SERVER['SERVER_NAME'] == 'se1.hamsterpaj.net')
  {
    $hp_url = 'http://www.hamsterpaj.net/';
  }
  else
  {
          $hp_url = 'http://' . $_SERVER['SERVER_NAME'] . '/';
  }
?>
