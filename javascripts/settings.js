function settings_theme_preview()
{
	var theme_id = this.id.substr(8);
	document.getElementById('theme_preview').className = 'profile_' + theme_id;/*style.backgroundImage = 'url("http://images.hamsterpaj.net/profile_themes/' + theme_id + '/profile_head_bg.png")';*/
}


function enable_settings_theme_previews()
{
	var previews = getElementsByClassName(document, 'img', 'theme_preview');
	for(var i = 0; i < previews.length; i++)
	{
		previews[i].onclick = settings_theme_preview;
	}
}


womAdd('enable_settings_theme_previews()');