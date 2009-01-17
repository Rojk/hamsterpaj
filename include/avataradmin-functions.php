<?php
function refuse_image($userid, $validator) 
	{
		if($userid == 17505 || $userid == 573633 || $userid == 625747 || $userid == 68767)
		{
			die('Man kan inte ta bort denna bild...');
			exit;
		}

		global $hp_path;
		$query = 'UPDATE userinfo SET image = "3", image_validator = "' . $validator . '" ';
		$query.= ' WHERE userid = "' . $userid . '" LIMIT 1';
		mysql_query($query) or die();
		if(unlink(PATHS_IMAGES . 'users/full/' . $userid . '.jpg') && unlink(PATHS_IMAGES . 'users/thumb/' . $userid . '.jpg'))
		{
			guestbook_insert(array(
				'sender' => 2348,
				'recipient' => $userid,
				'is_private' => 1,
				'message' => 'OBS! Detta meddelande har skickats automatiskt. Det är ingen idé att svara på meddelandet, kontakta någon ordningsvakt eller fråga i forumet.
Din bild har nekats, acceptera det.

Välj en ny bild som följer vår policy:

1) Bilden föreställer dig och ansiktet syns tydligt

2) Det är bara du på bilden

3) Ingen alkohol, ingen porr och inga nazistiska symboler

4) Inget som bryter mot Svensk lag, är upphovsrättskyddat eller är kränkande för någon person förekommer

5) Det är en skarp och ljus bild på dig

6) Bilden är inte taggad från någon annan sida ex. snyggast

7) Du har inte angett rätt ålder/kön så att det överensstämmer med personen på bilden'
			));
    } 
		else 
		{
        	     echo '<script language="javascript">alert("Ett fel uppstod när ' . $userid . '.jpg skulle tas bort!");</script>';
		}
		admin_report_event($_SESSION['login']['username'], 'Refused avatar', $userid);


		log_admin_event('avatar validated', 'denied', $validator, $userid, 0); //image id not available here
		admin_action_count($_SESSION['login']['id'], 'avatar_denied');

	}

?>
