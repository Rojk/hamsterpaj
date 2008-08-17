function enable_fun_images()
{
	var hype_buttons = getElementsByClassName(document, 'input', 'fun_images_hype_button');
	for(var i = 0; i < hype_buttons.length; i++)
	{
		hype_buttons[i].onclick = fun_images_hype;
	}
}

function fun_images_hype()
{
	var image_id = this.id.substr(22);

	xmlhttp_ping('/kul/roliga_bilder/hype_gateway.php?image=' + image_id);

	this.value = 'Tack för din hype!';
	this.disabled = true;
}

womAdd('enable_fun_images()');