function rank_input_part_mouse_over()
{
	if(document.getElementById('rank_input'))
	{
//		alert(this.id.substr(16));
		document.getElementById('rank_input').style.backgroundPosition = "0px -" + (this.id.substr(16) * 20) + "px";
	}
}

function rank_input_part_mouse_over_enable()
{
	var items = getElementsByClassName(document, 'div', 'rank_input_part');
	for(var i=0; i < items.length; i++)
	{
		items[i].onmouseover = rank_input_part_mouse_over;
	}
}

function rank_input_part_click()
{
	if(document.getElementById('rank_input'))
	{
		xmlhttp_ping('/ajax_gateways/rank.php?item_id=' + document.getElementById('rank_item_id').value
											+ '&item_type=' + document.getElementById('rank_item_type').value
											+ '&rank=' + (this.id.substr(16) / 2));
		document.getElementById('rank_previous').value = this.id.substr(16) / 2;
		rank_input_previous_reset();
		document.getElementById('rank_input_message').style.display = 'block';
	}
}

function rank_input_part_click_enable()
{
	var items = getElementsByClassName(document, 'div', 'rank_input_part');
	for(var i=0; i < items.length; i++)
	{
		items[i].onclick = rank_input_part_click;
	}
}

function rank_input_previous_reset()
{
	if(document.getElementById('rank_previous'))
	{
		document.getElementById('rank_input').style.backgroundPosition = "0px -" + (document.getElementById('rank_previous').value * 2 * 20) + "px";
	}
}

function rank_input_mouse_out_enable()
{
	if(document.getElementById('rank_input'))
	{
		document.getElementById('rank_input').onmouseout = rank_input_previous_reset;
	}
}

womAdd('rank_input_mouse_out_enable()');
womAdd('rank_input_part_mouse_over_enable()');
womAdd('rank_input_part_click_enable()');
womAdd('rank_input_previous_reset()');