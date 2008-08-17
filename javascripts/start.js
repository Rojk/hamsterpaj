function enable_fp_spotlight()
{
	var thumbnails = getElementsByClassName(document, 'img', 'fp_user_list_thumb');
	for(var i = 0; i < thumbnails.length; i++)
	{
		thumbnails[i].onclick = fp_spotlight_scroll;
	}
}

function fp_spotlight_scroll()
{
	var new_offset = this.id.substr(14) * 310;
	$('#fp_spotlight').animate({scrollLeft: new_offset}, 250);
}

womAdd('enable_fp_spotlight()');