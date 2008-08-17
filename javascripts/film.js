function film_fetch_link_button_click()
{
	loadFragmentInToElementByPOST('/film/admin/film_upload.php', 'film_preview',  
								 "fetch_link=" + document.getElementById('film_fetch_link_input').value);
	return false;
}

function film_fetch_link_button_click_enable()
{
	if(document.getElementById('film_fetch_link_button'))
	{
		document.getElementById('film_fetch_link_button').onclick = film_fetch_link_button_click;
	}
}


function open_fullscreen_window(target_url)
{
	alert('För att få riktig fullskärm trycker du på F11 på ditt tangentbord, när du vill tillbaks till fönsterläge håller du inne ALT-knappen och trycker på F4!');
	document.getElementById('film').innerHTML = '';
	var sc_width = screen.width;
	var sc_height = screen.height;
	window.open(target_url, 'fullscreen_window', 'width=' + sc_width + ', height=' + sc_height + ', toolbar=no, location=no');
}

womAdd('film_fetch_link_button_click_enable()');
