var parlino_characters_left = 160;

function enable_parlino_text_counter()
{
	if(document.getElementById('characters_left') && document.getElementById('parlino_content_input'))
	{
		document.getElementById('parlino_content_input').onkeyup = parlino_text_counter;
		parlino_characters_left = document.getElementById('characters_left').innerHTML;
	}
}

function parlino_text_counter()
{
	var strlen = document.getElementById('parlino_content_input').value.length;
	
	document.getElementById('characters_left').innerHTML = parlino_characters_left - strlen;
}


womAdd('enable_parlino_text_counter()');