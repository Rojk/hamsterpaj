function movie_compability_init()
{
	var radio_buttons = getElementsByClassName(document, 'input', 'movie_compability_input_scroll');

	for(var i = 0; i < radio_buttons.length; i++)
	{
		radio_buttons[i].onclick = movie_compability_smooth_scroll_down;
	}
	
	var movie_controls = getElementsByClassName(document, 'li', 'movie_not_seen_control');
	for(var i = 0; i < movie_controls.length; i++)
	{
		movie_controls[i].onclick = movie_compability_expand_unseen;
	}
}

function movie_compability_expand_unseen()
{
	if(document.getElementById('movie_not_seen_' + this.id.substr(23)).style.display == 'block')
	{
		document.getElementById('movie_not_seen_' + this.id.substr(23)).style.display = 'none';
	}
	else
	{
		document.getElementById('movie_not_seen_' + this.id.substr(23)).style.display = 'block';
	}
}

function movie_compability_smooth_scroll_down()
{
	setTimeout('window.scroll(window.scrollX, document.documentElement.scrollTop + 28)', 0);
	setTimeout('window.scroll(window.scrollX, document.documentElement.scrollTop + 23)', 50);
	setTimeout('window.scroll(window.scrollX, document.documentElement.scrollTop + 16)', 100);
	setTimeout('window.scroll(window.scrollX, document.documentElement.scrollTop + 10)', 150);
	setTimeout('window.scroll(window.scrollX, document.documentElement.scrollTop + 6)', 200);
	setTimeout('window.scroll(window.scrollX, document.documentElement.scrollTop + 3)', 250);
}

womAdd('movie_compability_init()');