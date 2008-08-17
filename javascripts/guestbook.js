var gb_entry_cache = Array();

function gb_unprivate(entry_id)
{
	xmlhttp_ping('/ajax_gateways/guestbook.php?action=unprivate&entry_id=' + entry_id);
	document.getElementById('gb_private_' + entry_id + '_label').innerHTML = '';	
	document.getElementById('gb_private_' + entry_id).style.display = 'inline';
	document.getElementById('gb_unprivate_' + entry_id).style.display = 'none';
}

function gb_private(entry_id)
{
	xmlhttp_ping('/ajax_gateways/guestbook.php?action=private&entry_id=' + entry_id);
	document.getElementById('gb_private_' + entry_id + '_label').innerHTML = 'Detta inlägg är privat';	
	document.getElementById('gb_private_' + entry_id).style.display = 'none';
	document.getElementById('gb_unprivate_' + entry_id).style.display = 'inline';
}

function gb_answer(username, user_id, entry_id)
{
	window.open('/traffa/gb-reply.php?action=reply&username=' + username + '&userid=' + user_id + '&answereid=' + entry_id, '', 'location=no, status=no, resizable=no, width=250, height=200');
}

function gb_remove(entry_id, username, date_time)
{
	xmlhttp_ping('/ajax_gateways/guestbook.php?action=remove&entry_id=' + entry_id);
	gb_entry_cache[entry_id] = document.getElementById('gb_entry_' + entry_id + '_content').innerHTML;
	$('#gb_entry_' + entry_id + '_content').slideUp(300);
	document.getElementById('gb_entry_' + entry_id + '_content').innerHTML = 'Tog bort inlägg #' + entry_id + ' skrivet av ' + username + ' ' + date_time + '&nbsp;&nbsp;&nbsp;&nbsp;<a style="cursor: pointer;" onclick="gb_recreate(' + entry_id + ')">[Ångra]</a>';
	$('#gb_entry_' + entry_id + '_content').slideDown(100);
}

function gb_recreate(entry_id)
{
	xmlhttp_ping('/ajax_gateways/guestbook.php?action=recreate&entry_id=' + entry_id);
	$('#gb_entry_' + entry_id + '_content').slideUp(100);
	document.getElementById('gb_entry_' + entry_id + '_content').innerHTML = gb_entry_cache[entry_id];
	$('#gb_entry_' + entry_id + '_content').slideDown(300);
}

function gb_history(recipient, sender)
{
	window.location = '/traffa/guestbook.php?action=history&view=' + recipient + '&remote=' + sender;
}

function gb_goto(user)
{
	window.location = '/traffa/guestbook.php?view=' + user;
}

function gb_block_user(sender)
{
	if(confirm('Vill du verkligen blockera användaren? En blockerad användare kan inte skicka meddelanden till dig.'))
	{
		window.location = '/traffa/userblocks.php?action=block&username=' + sender;
	}
}