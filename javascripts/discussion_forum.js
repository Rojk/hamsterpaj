function forum_reply()
{
	var post_id = this.id.substr(11);
	var author_username = document.getElementById('post_' + post_id + '_author_username').value;
	//document.getElementById('forum_post_form_content').value += '\n[svar:' + author_username + ':' + post_id + ']\n\n[/svar]';
	hp.discussion_forum.bbcode.insert({
																			textfield: 'forum_post_form_content',
																			start_tag: '\n[svar:' + author_username + ':' + post_id + ']\n\n',
																			end_tag:   '\n[/svar]\n'
																		});

	//window.location = '#post_form';
	window.scrollTo(0, document.getElementById('forum_post_form_content').offsetTop);
	document.getElementById('forum_post_form_content').focus();
	forum_help_load('reply');
}

function forum_remove()
{
	var removal_comment = prompt('Varför togs posten bort?\n(Visas för alla användare)', 'Här MÅSTE du lämna en beskrivning om varför posten togs bort!');
	if(!removal_comment || removal_comment == 'Här MÅSTE du lämna en beskrivning om varför posten togs bort!')
	{
		alert('Åtgärd avbruten.\nDu måste skriva ett meddelande och tryck OK.');
		return false;
	}
	else
	{
		removal_comment = encodeURIComponent(removal_comment);
	}
	
	document.getElementById('forum_post_' + this.value).className = 'forum_post_removed';
	document.getElementById('forum_post_' + this.value).innerHTML = 'Borttaget ';
	xmlhttp_ping('/ajax_gateways/discussion_forum.php?action=remove_post&post_id=' + this.value + '&removal_comment=' + removal_comment);
}

function forum_unremove()
{
	alert('.ted es annuk tta röf nadis mo addal etsåm ud nem ,negi alla röf un snys teggälnI');
	xmlhttp_ping('/ajax_gateways/discussion_forum.php?action=unremove_post&post_id=' + this.value);
}

function forum_direct_link()
{	
	var loader = hp.give_me_an_AJAX();
	var post_id = this.id.substring(25);
	var load_into = document.getElementById('forum_direct_link_input_' + post_id);
	
	// this.style.display = 'none'; below, because off (yes, you have probably guessed at this point) IE!
	load_into.style.display = 'inline';
	load_into.value = 'Laddar...';
	
	loader.open('GET', '/ajax_gateways/discussion_forum.php?action=direct_link_fetch&post_id=' + post_id, true);
	
	loader.onreadystatechange = function()
	{
		if(loader.readyState == 4 && loader.status == 200)
		{
			load_into.value = loader.responseText;
			load_into.onfocus = function()
			{
				this.select();
			}
			load_into.focus();
		}
	}
	
	loader.send(null);
	
	this.style.display = 'none';
}

function forum_thread_rename_click()
{
	var new_title = encodeURIComponent(document.getElementById('forum_thread_rename_input_' + this.value).value);
	alert('Då byter vi namn på tråden. Vänta några sekunder innan du gör något annat, så att våra AJAX hinner ladda.');

	xmlhttp_ping('/ajax_gateways/discussion_forum.php?action=rename_post&post_id=' + this.value + '&new_title=' + new_title);
}


function forum_form_control_click()
{
	var control_handle = this.id.substr(11, this.id.length-19);
	
	switch(control_handle)
	{
		case 'bold':
			hp.discussion_forum.bbcode.insert({
																					textfield: 'forum_post_form_content',
																					start_tag: '[b]',
																					end_tag:   '[/b]'
																				});
			break;
		case 'italic':
			hp.discussion_forum.bbcode.insert({
																					textfield: 'forum_post_form_content',
																					start_tag: '[i]',
																					end_tag:   '[/i]'
																				});
			break;
		case 'spoiler':
			hp.discussion_forum.bbcode.insert({
																					textfield: 'forum_post_form_content',
																					start_tag: '[spoiler]',
																					end_tag: '[/spoiler]'
																				});
			break;
		case 'code':
			var highlight_language = prompt('Om du vill ha "syntax highlighting" på din kod måste du skriva vilket språk du använder, idag finns det stöd för: php, javascript, html, css, asp, C#\nLäs mer i hjälprutan till höger!', '');
			if(highlight_language && highlight_language.length > 0)
			{
				hp.discussion_forum.bbcode.insert({
																						textfield: 'forum_post_form_content',
																						start_tag: '[code:' + highlight_language + ']',
																						end_tag:   '[/code]'
																					});			
			}
			else
			{
				hp.discussion_forum.bbcode.insert({
																						textfield: 'forum_post_form_content',
																						start_tag: '[code]',
																						end_tag:   '[/code]'
																					});
			}
			break;
		case 'image':
			var image_url = prompt('Ange adressen till bilden, det är väldigt viktigt att du får med http:// i början. Om du har bilden på din dator så måste du först ladda upp den till en plats på Internet innan du kan visa den i forumet.', '');
			if(image_url)
			{
				hp.discussion_forum.bbcode.insert({
																						textfield: 'forum_post_form_content',
																						start_tag: '[img]' + image_url + '[/img]'
																					});
			}
			break;
		case 'poll':
			var poll_id = prompt('Här fyller du i ID-numret på din undersökning, du hittar numret där du gör undersökningar.\nHar du inte gjort någon undersökning än? Klicka på länken i den gula hjälp-rutan!', '');
			if(poll_id)
			{
				hp.discussion_forum.bbcode.insert({
																						textfield: 'forum_post_form_content',
																						start_tag: '[poll:' + poll_id + ']'
																					});
			}
			break;
		case 'preview':
			var post_data = escape(document.getElementById('forum_post_form_content').value);
			loadFragmentInToElementByPOST('/diskussionsforum/post_preview.php', 'forum_preview_area', 'content=' + post_data)
			document.getElementById('forum_preview_area').style.display = 'block';
			window.location = '#forum_preview';
			break;
	}
	forum_help_load(control_handle);
	return false;
}

function forum_help_load(help_handle)
{
	if(forum_help_texts[help_handle])
	{
		document.getElementById('forum_form_help_content').innerHTML = forum_help_texts[help_handle];
	}
}

function forum_user_ro()
{
	window.open('/admin/user_management.php?username=' + this.value);
}

function forum_spoiler_view()
{
	for(var i = 0; i < this.parentNode.parentNode.childNodes.length; i++)
	{
		if(this.parentNode.parentNode.childNodes[i].className == 'spoiler_content')
		{
			this.parentNode.parentNode.childNodes[i].style.display = 'block';
		}
	}
	this.parentNode.style.display = 'none';
}

function forum_thread_vote()
{
	if(this.id == 'forum_thread_vote_plus')
	{
		document.getElementById('thread_score').innerHTML = Number(document.getElementById('thread_score').innerHTML) + 1;
		xmlhttp_ping('/ajax_gateways/discussion_forum.php?action=vote&vote=positive&thread_id=' + document.getElementById('thread_id').value);
	}
	if(this.id == 'forum_thread_vote_minus')
	{
		document.getElementById('thread_score').innerHTML = Number(document.getElementById('thread_score').innerHTML) - 1;
		xmlhttp_ping('/ajax_gateways/discussion_forum.php?action=vote&vote=negative&thread_id=' + document.getElementById('thread_id').value);
	}
	
	document.getElementById('forum_thread_vote_plus').style.cursor = 'default';
	document.getElementById('forum_thread_vote_minus').style.cursor = 'default';

	document.getElementById('forum_thread_vote_plus').onclick = '';
	document.getElementById('forum_thread_vote_minus').onclick = '';

	document.getElementById('forum_thread_vote_plus').src = 'http://images.hamsterpaj.net/discussion_forum/thread_voting_plus_grey.png';
	document.getElementById('forum_thread_vote_minus').src = 'http://images.hamsterpaj.net/discussion_forum/thread_voting_minus_grey.png';
}

function forum_edit()
{
	var post_id = this.id.substr(18);
	window.open('/diskussionsforum/post_edit.php?post_id=' + post_id, 'forum_edit_window', 'status=0, toolbar=0, width=650, height=600');
}

function toggle_thread_subscription()
{
	if(this.checked)
	{
		xmlhttp_ping('/ajax_gateways/discussion_forum.php?action=add_thread_subscription&thread_id=' + document.getElementById('thread_id').value);
		alert('Tråden ligger nu i din bevakningslista!\nDu hittar alla dina bevakade trådar genom att trycka på ikonen för forumnotiser som du hittar brevid hamsterpaj-loggan!');
	}
	else
	{
		xmlhttp_ping('/ajax_gateways/discussion_forum.php?action=remove_thread_subscription&thread_id=' + document.getElementById('thread_id').value);
		alert('Nu bevakar du inte längre tråden!');
	}
}

function forum_submit_timeout(submit_button_id)
{
	if(document.getElementById(submit_button_id))
	{
		var submit_button = document.getElementById(submit_button_id);
		var timeout = submit_button.value.substr(7);
		if(timeout < 2)
		{
			submit_button.value = 'Spara';
			submit_button.disabled = false;
		}
		else
		{
			submit_button.value = 'Vänta: ' + (timeout-1);
			submit_button.disabled = true;
			setTimeout('forum_submit_timeout("' + submit_button_id + '")', 1000);
		}
	}
}

function toggle_category_subscription()
{
	if(this.checked)
	{
		xmlhttp_ping('/ajax_gateways/discussion_forum.php?action=add_category_subscription&category_id=' + this.value);
		hp.notices.infobubble.draw({ duration: 3, msg: "Nya trådar i den här kategorin kommer dyka på notis-sidan!" });
	}
	else
	{
		xmlhttp_ping('/ajax_gateways/discussion_forum.php?action=remove_category_subscription&category_id=' + this.value);
		hp.notices.infobubble.draw({ duration: 3, msg: "Prenumerationen har avslutats" });
	}
}

function forum_form_submit()
{
	this.disabled = 'true';
	this.value = 'Vänta...';
	
	document.getElementById('forum_post_form').submit();
	
	return false;
}

function forum_toggle_gb_form()
{
	$('#forum_comment_' + this.value).toggle(250);
}

function forum_enable_controls()
{
	var reply_buttons = getElementsByClassName(document, 'button', 'forum_reply_button');
	for(var i = 0; i < reply_buttons.length; i++)
	{
		reply_buttons[i].onclick = forum_reply;
	}

	var edit_buttons = getElementsByClassName(document, 'button', 'forum_edit_button');
	for(var i = 0; i < edit_buttons.length; i++)
	{
		edit_buttons[i].onclick = forum_edit;
	}

	var remove_buttons = getElementsByClassName(document, 'button', 'forum_remove_button');
	for(var i = 0; i < remove_buttons.length; i++)
	{
		remove_buttons[i].onclick = forum_remove;
	}

	var unremove_buttons = getElementsByClassName(document, 'button', 'forum_unremove_button');
	for(var i = 0; i < unremove_buttons.length; i++)
	{
		unremove_buttons[i].onclick = forum_unremove;
	}
	
	var direct_link_buttons = getElementsByClassName(document, 'button', 'forum_direct_link_button');
	for(var i = 0; i < direct_link_buttons.length; i++)
	{
		direct_link_buttons[i].onclick = forum_direct_link;
	}

	var guestbook_buttons = getElementsByClassName(document, 'button', 'forum_comment_button');
	for(var i = 0; i < guestbook_buttons.length; i++)
	{
		guestbook_buttons[i].onclick = forum_toggle_gb_form;
	}

	var ro_buttons = getElementsByClassName(document, 'button', 'forum_user_ro');
	for(var i = 0; i < ro_buttons.length; i++)
	{
		ro_buttons[i].onclick = forum_user_ro;
	}

	var spoiler_buttons = getElementsByClassName(document, 'button', 'spoiler_control');
	for(var i = 0; i < spoiler_buttons.length; i++)
	{
		spoiler_buttons[i].onclick = forum_spoiler_view;
	}
	
	var thread_subscription_control = getElementsByClassName(document, 'input', 'thread_subscription_control');
	if(thread_subscription_control.length == 1)
	{
		thread_subscription_control[0].onchange = toggle_thread_subscription;
	}
	
	var category_subscription_controls = getElementsByClassName(document, 'input', 'category_subscribtion_control');
	for(var i = 0; i < category_subscription_controls.length; i++)
	{
		category_subscription_controls[i].onchange = toggle_category_subscription;
	}
	
	var form_submit_buttons = getElementsByClassName(document, 'input', 'forum_form_submit');
	for(var i = 0; i < form_submit_buttons.length; i++)
	{
		if(form_submit_buttons[i].value.substr(0, 5) == 'Vänta')
		{
			forum_submit_timeout(form_submit_buttons[i].id);
		}
		form_submit_buttons[i].onclick = forum_form_submit;
	}
	
	if(document.getElementById('forum_thread_vote_plus'))
	{
		document.getElementById('forum_thread_vote_plus').onclick = forum_thread_vote;
		document.getElementById('forum_thread_vote_minus').onclick = forum_thread_vote;
	}

	var control_handles = Array('bold', 'italic', 'spoiler', 'image', 'code', 'survey', 'poll', 'preview');

	for(var i = 0; i < control_handles.length; i++)
	{
		if(document.getElementById('forum_form_' + control_handles[i] + '_control'))
		{
			document.getElementById('forum_form_' + control_handles[i] + '_control').onclick = forum_form_control_click;
		}
	}
	
	if(document.getElementById('reply_mode_child_discussion'))
	{
		document.getElementById('reply_mode_child_discussion').onclick = function()
		{
			forum_help_load('child_thread');
		}
	}
	
	if(document.getElementById('forum_post_form_help_selector'))
	{
		document.getElementById('forum_post_form_help_selector').onchange = function()
		{
			forum_help_load(this.value);
		}
	}
	
	var sticky_controls = getElementsByClassName(document, 'button', 'forum_sticky_control');
	for(var i = 0; i < sticky_controls.length; i++)
	{
		sticky_controls[i].onclick = sticky_control_click;
	}
	
	var thread_lock_controls = getElementsByClassName(document, 'button', 'forum_thread_lock_control');
	for(var i = 0; i < thread_lock_controls.length; i++)
	{
		thread_lock_controls[i].onclick = thread_lock_control_click;
	}
	
	var forum_thread_rename_buttons = getElementsByClassName(document, 'button', 'forum_thread_rename_button');
	for(var i = 0; i < forum_thread_rename_buttons.length; i++)
	{
		forum_thread_rename_buttons[i].onclick = forum_thread_rename_click;
	}
}

function sticky_control_click()
{
	if(this.innerHTML == 'Klistra')
	{
		xmlhttp_ping('/ajax_gateways/discussion_forum.php?action=setsticky&post_id=' + this.id.substring(15));
		this.innerHTML = 'Avklistra';
	}
	else
	{
		xmlhttp_ping('/ajax_gateways/discussion_forum.php?action=unsticky&post_id=' + this.id.substring(15));
		this.innerHTML = 'Klistra';
	}
}

function thread_lock_control_click()
{
	if(this.innerHTML == 'Lås tråd')
	{
		xmlhttp_ping('/ajax_gateways/discussion_forum.php?action=lock_thread&post_id=' + this.id.substring(20));
		this.innerHTML = 'Lås upp tråd';
	}
	else
	{
		xmlhttp_ping('/ajax_gateways/discussion_forum.php?action=unlock_thread&post_id=' + this.id.substring(20));
		this.innerHTML = 'Lås tråd';
	}
}

//womAdd('forum_enable_controls()');
$(document).ready(function(){
  forum_enable_controls();
});

womAddReal('forum_enable_post_thumbnails()');

function forum_enable_post_thumbnails()
{
	var imgs = getElementsByClassName(document, 'img', 'forum_post_image');
	for(var img = 0; img < imgs.length; img++)
	{
		var current_image = imgs[img];
		if($(current_image).parent().width() < $(current_image).width())
		{
			$(current_image).width(200);
			$(current_image).wrap('<a href="' + $(current_image).attr('src') + '" title="Se bilden i full storlek" target="_blank"></a>');
		}
	}
}

/* BBCode, textformatting and such things: */

// If hp, "The Hamsterpaj Namespace", wasn't defined before then define it!
if(typeof(hp) == 'undefined'){ var hp=new Object(); }

hp.discussion_forum = {
	bbcode: {
		insert: function(params){
			params.end_tag = (typeof(params.end_tag) == "undefined") ? "" : params.end_tag;
			var textarea = document.getElementById(params.textfield);
			textarea.focus();
			var text_to_parse = (document.selection) ? document.selection.createRange().text : textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);
			var parsed_text = params.start_tag + text_to_parse + params.end_tag;

			if(typeof(document.selection)!="undefined"){
				document.selection.createRange().text = parsed_text;
				
				//var move_cursor_to_range = textarea.createTextRange();
				//move_cursor_to_range.moveStart("character", -(params.end_tag.length));
				//move_cursor_to_range.moveEnd("character", 0);
				//move_cursor_to_range.select();

				/*var debug=textarea.createTextRange();
				debug.move("character", );
				debug.select();*/
			}
			else
			{
				var move_cursor_to = textarea.selectionStart + params.start_tag.length;
				var move_scrollbar_to = textarea.scrollTop; //Save scroll position...
				
				var replace  = textarea.value.substring(0, textarea.selectionStart);
				    replace += parsed_text;
				    replace += textarea.value.substring(textarea.selectionEnd);
				textarea.value = replace;
				
				textarea.scrollTop = move_scrollbar_to; //...and restore it.
				// Put the cursor in the textarea inside the tags:
				textarea.setSelectionRange(move_cursor_to, move_cursor_to);	
			}
		}
	}
}

$(function() {
	$('.remove_subscribtion_listed').click(function() {
		var thread_id = $(this).parent().parent().attr("id");
		$.ajax({
			url: '/ajax_gateways/discussion_forum.php',
			type: 'GET',
			data: 'thread_id=' + thread_id + '&action=remove_thread_subscription'			 
		});
		$('#' + thread_id).fadeOut('500');
		return false;
	});
	$('.remove_answer_notice_listed').click(function() {
		var post_id = $(this).attr("id");
		$.ajax({
			url: '/ajax_gateways/discussion_forum.php',
			type: 'GET',
			data: 'post_id=' + post_id + '&action=remove_answer_notice'			 
		});
		$(this).parent().parent().fadeOut('500');
		return false;
	});
});