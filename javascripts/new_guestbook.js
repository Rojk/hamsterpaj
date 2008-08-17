var entry_cache = Array();

function gb_reply_submit()
{
	var entry_id = this.id.substr(7);
	var message = document.getElementById('gb_reply_text_' + entry_id).value;
	var recipient = document.getElementById('gb_reply_recipient_' + entry_id).value;

	$('#gb_reply_form_' + entry_id).hide('150');
	document.getElementById('gb_reply_text_' + entry_id).value = '';
	
	xmlhttp_post_ping('/ajax_gateways/guestbook.json.php?new_entry', 'message=' + escape(message) + '&recipient=' + recipient + '&action=insert&reply-to=' + entry_id);
	document.getElementById('unanswered_label_' + entry_id).style.display = 'none';
	
	return false;
}

function gb_block_click()
{
	return confirm('Vill du verkligen blockera den här användaren från att skriva till dig igen?');
}

function gb_undelete_click()
{
	var entry_id = this.id.substr(16);
	$(this).hide(250).show(250);
	this.innerHTML = entry_cache[entry_id];
	$('#entry_' + entry_id + '_photo').show(250);
	xmlhttp_ping('/ajax_gateways/guestbook.json.php?action=undelete&entry_id=' + entry_id);
	this.onclick = '';
	this.style.cursor = 'default';
	return false;
}

function gb_private_click()
{
	var entry_id = this.id.substr(16);

	this.style.display = 'none';
	document.getElementById('unprivate_control_' + entry_id).style.display = 'inline';
	document.getElementById('private_label_' + entry_id).style.display = 'inline';
	xmlhttp_ping('/ajax_gateways/guestbook.json.php?action=private&entry_id=' + entry_id);
	
	return false;
}

function gb_unprivate_click()
{
	var entry_id = this.id.substr(18);

	this.style.display = 'none';
	document.getElementById('private_control_' + entry_id).style.display = 'inline';
	document.getElementById('private_label_' + entry_id).style.display = 'none';
	
	xmlhttp_ping('/ajax_gateways/guestbook.json.php?action=unprivate&entry_id=' + entry_id);
	return false;	
}

function gb_delete_click()
{
	var entry_id = this.id.substr(15);
	xmlhttp_ping('/ajax_gateways/guestbook.json.php?action=delete&entry_id=' + entry_id);
	$('#guestbook_entry_' + entry_id).hide(250).show(250);
	$('#entry_' + entry_id + '_photo').hide(250);
	
	entry_cache[entry_id] = document.getElementById('guestbook_entry_' + entry_id).innerHTML;

	document.getElementById('guestbook_entry_' + entry_id).innerHTML = '[Ångra]';
	setTimeout("document.getElementById('guestbook_entry_" + entry_id + "').onclick = gb_undelete_click", 15);
	document.getElementById('guestbook_entry_' + entry_id).style.cursor = 'pointer';
	
	return false;
}

function gb_reply_click()
{
	var entry_id = this.id.substr(14);
	$('#gb_reply_form_' + entry_id).toggle(150);
	document.getElementById('gb_reply_form_' + entry_id + '_message').focus();
	return false;
}

function gb_form_submit()
{

	var message = this.elements['message'].value;
	var recipient = this.elements['recipient'].value;
	if(this.elements['private'].checked == true)
	{
		var gb_private = 1;
	}
	else
	{
		var gb_private = 0;
	}

	if(message.length < 1)
	{
		hp.notices.infobubble.draw({ duration: 1.5, msg: 'Men tjockis, något får du allt skriva!' });
		return false;
	}

	xmlhttp_post_ping('/ajax_gateways/guestbook.json.php?new_entry', 'message=' + escape(message) + '&recipient=' + recipient + '&action=insert&private=' + gb_private);
	hp.notices.infobubble.draw({ duration: 0.5, msg: 'Sparat!' });
	this.innerHTML += '<p>Inlägget sparat!</p>' + "\n";
	return false;
}

function gb_zero_unread()
{
		if(confirm('Anledningen till din(a) spöknotiser är att du har olästa inlägg på en sida långt bak. Dessa kommer nu att markeras som "lästa", även om du inte läst dem.\n\nVill du fortsätta ändå?'))
		{
			xmlhttp_ping('/ajax_gateways/guestbook.json.php?action=zero_unread');
			alert('Klart, vänta några sekunder innan du gör någonting annat.\n\nHa en bra dag!');
		}
		
		return false;
}

function gb_enable_controls()
{
	var reply_links = getElementsByClassName(document, 'a', 'gb_reply_control');
	for(var i = 0; i < reply_links.length; i++)
	{
		reply_links[i].onclick = gb_reply_click;
	}
	
	var block_links = getElementsByClassName(document, 'a', 'gb_block_control');
	for(var i = 0; i < block_links.length; i++)
	{
		block_links[i].onclick = gb_block_click;
	}
	
	var delete_links = getElementsByClassName(document, 'a', 'gb_delete_control');
	for(var i = 0; i < delete_links.length; i++)
	{
		delete_links[i].onclick = gb_delete_click;
	}

	var private_links = getElementsByClassName(document, 'a', 'gb_private_control');
	for(var i = 0; i < private_links.length; i++)
	{
		private_links[i].onclick = gb_private_click;
	}
	
	var unprivate_links = getElementsByClassName(document, 'a', 'gb_unprivate_control');
	for(var i = 0; i < unprivate_links.length; i++)
	{
		unprivate_links[i].onclick = gb_unprivate_click;
	}

	var reply_submits = getElementsByClassName(document, 'input', 'gb_reply_submit');
	for(var i = 0; i < reply_submits.length; i++)
	{
		reply_submits[i].onclick = gb_reply_submit;
	}

	var gb_forms = getElementsByClassName(document, 'form', 'gb_form');
	for(var i = 0; i < gb_forms.length; i++)
	{
		gb_forms[i].onsubmit = gb_form_submit;
	}
	
	try
	{
		document.getElementById('guestbook_zero_unread').onclick = gb_zero_unread;
	}
	catch(E){ }
	
	$('.gb_form textarea, .guestbook_entries .gb_form textarea').bind('click', function()
	{
		if($(this).height() != 100)
		{
			$(this).animate({
				height: '100px'
			}, 800);
		}
	});
}

womAdd('gb_enable_controls()');