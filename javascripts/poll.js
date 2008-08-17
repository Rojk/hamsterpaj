function poll_reload_chart(poll_id)
{
	document.getElementById('poll_' + poll_id + '_chart').src = 'http://www.hamsterpaj.net/dynamic_images/poll_chart.php?poll_id=' + poll_id;
	document.getElementById('poll_' + poll_id + '_chart').style.display = 'block';
	if(document.getElementById('poll_' + poll_id + '_no_votes'))
	{
		document.getElementById('poll_' + poll_id + '_no_votes').style.display = 'none';
	}
}

function poll_submit()
{
	var poll_id = this.id.substr(10);
	
	for(var i = 1; i <= 7; i++)
	{
		if(document.getElementById('poll_' + poll_id + '_input_' + i))
		{
			if(document.getElementById('poll_' + poll_id + '_input_' + i).checked)
			{
				var answer_id = i;
			}
			document.getElementById('poll_' + poll_id + '_input_' + i).disabled = true;
		}
	}
	document.getElementById('poll_' + poll_id + '_submit').style.display = 'none';
	document.getElementById('poll_' + poll_id + '_vote_count').innerHTML = Number(document.getElementById('poll_' + poll_id + '_vote_count').innerHTML) + 1;
	
	/* Cross-domain bla bla security bla bla trusted zone bla bla xmlhttp bla bla */
	/* Ping the voting-script using an image preloader, instead of xmlhttp_ping() */
	poll_gateway = new Image(); 
	poll_gateway.src = 'http://www.hamsterpaj.net/ajax_gateways/poll.php?action=vote&poll_id=' + poll_id + '&answer_id=' + answer_id;
//	xmlhttp_ping('http://www.hamsterpaj.net/ajax_gateways/poll.php?action=vote&poll_id=' + poll_id + '&answer_id=' + answer_id);

	setTimeout('poll_reload_chart(' + poll_id + ')', 250);

	return false;
}

function enable_polls()
{
	var poll_forms = getElementsByClassName(document, 'form', 'poll_form');
	for(var i = 0; i < poll_forms.length; i++)
	{
		poll_forms[i].onsubmit = poll_submit;
	}
}

womAdd('enable_polls()');