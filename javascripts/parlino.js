function parlino_compose_keydown()
{
	document.getElementById('parlino_sms_counter').innerHTML = 254 - this.value.length;
}

function enable_parlino_char_count()
{
	if(document.getElementById('parlino_sms_compose'))
	{
		document.getElementById('parlino_sms_compose').onkeydown = parlino_compose_keydown;
	}
}

womAdd('enable_parlino_char_count()');