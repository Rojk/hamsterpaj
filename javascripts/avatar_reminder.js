var reminder_element;
function avatar_reminder(openBox, username, gender, hp_url)
{
	if(openBox)
	{
		var oposite_gender = (gender == 'm' ? 'tjejer' : (gender == 'f' ? 'killar' : 'tjejer/killar'));
		var avatar_reminder_text = "Hörru tjockis, vem är du egentligen?<br />Vi vet att du heter " + username + " här på Hamsterpaj, men vi har ingen<br />aning om hur du ser ut. Tänk på alla " + oposite_gender + " du missar för att du inte<br />har någon visningsbild. ;)<br /><br /><a href=\"" + hp_url + "avatar-settings.php\">Ladda upp en bild &raquo;</a>";
		
		if(!document.getElementById('remind_box'))
		{
			reminder_element = document.createElement('div');
			reminder_element.id = 'reminder_box';
			reminder_element.innerHTML = '<div id="reminder_box_panel"><a href="javascript:void(0);" onClick="avatar_reminder(false);">X</a></div><div id="reminder_box_content"><img src="http://images.hamsterpaj.net/avatar_unknown.gif" width="150" height="200" style="float: left;" /><p>' + avatar_reminder_text + '</p></div>';
			document.body.appendChild(reminder_element);
		}
	}
	else
	{
		document.body.removeChild(reminder_element);
	}
}