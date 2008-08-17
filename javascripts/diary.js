function diary_date_selector_change()
{
	var new_offset = this.value * 22;
	$('#diary_date_scroller').animate({scrollTop: new_offset}, 250);
}

function diary_enable()
{
	if(document.getElementById('diary_date_selector'))
	{
		document.getElementById('diary_date_selector').onchange = diary_date_selector_change;
	}
}

womAdd('diary_enable()');