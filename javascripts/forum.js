
function enable_filter_low_quality_click()
{
	if(document.getElementById('filter_low_quality'))
	{
		document.getElementById('filter_low_quality').onchange = filter_low_quality_click;
	}
}

function filter_low_quality_click()
{
	if(this.checked == true)
	{
		document.getElementById('low_quality_level_select').style.display = 'block';
	}
	else
	{
		document.getElementById('low_quality_level_select').style.display = 'none';
	}
}


function posts_low_quality_click()
{
	var post_id = this.id.substr(19);
	
	if(document.getElementById('post_' + post_id).style.display == 'block')
	{
		document.getElementById('post_' + post_id).style.display = 'none'
	}
	else
	{
		document.getElementById('post_' + post_id).style.display = 'block'
	}
}

function enable_low_quality_post_click()
{
	var quote_links = getElementsByClassName(document, 'h2', 'low_quality_header');
	for(var i = 0; i < quote_links.length; i++)
	{
		quote_links[i].onclick = posts_low_quality_click;
	}
}

function posts_form_control_select()
{
	for(var i = 0; i < this.options.length; i++)
	{
		if(document.getElementById('control_item_' + this.options[i].value))
		{
			document.getElementById('control_item_' + this.options[i].value).style.display = 'none';
		}
	}
	
	document.getElementById('control_item_' + this.value).style.display = 'block';
}


function enable_posts_form_control_select()
{
	if(document.getElementById('content_control_select'))
	{
		document.getElementById('content_control_select').onchange = posts_form_control_select;
	}
}

/*
function discussion_expand_control_mouseover()
{
	var discussion_id = this.id.substr(15);
	document.getElementById('discussion_extra_' + discussion_id).style.display = 'block'
	this.src = 'http://images.hamsterpaj.net/buttons/green_arrow_up_circle.png';
}

function enable_discussion_expand_control_mouseover()
{
	var buttons = getElementsByClassName(document, 'img', 'expand_control');
	for(var i = 0; i < buttons.length; i++)
	{
		buttons[i].onmouseover = discussion_expand_control_mouseover;
	}
}

function discussion_list_item_mouseout()
{
	var discussion_id = this.id.substr(11);
	document.getElementById('discussion_extra_' + discussion_id).style.display = 'none';
}


function enable_discussion_list_item_mouseout()
{
	var divs = enannanvariantav_getElementsByClassName('discussion', 'div', document);
	for(var i = 0; i < divs.length; i++)
	{
		divs[i].onmouseout = discussion_list_item_mouseout;
	}
}
*/


function enable_discussion_watch_click()
{
	if(document.getElementById('forum_discussion_watch'))
	{
		document.getElementById('forum_discussion_watch').onclick = discussion_watch_click;
	}
}

function discussion_watch_click()
{
	var discussion_id = document.getElementById('discussion_id').value;
	if(document.getElementById('forum_discussion_watch').checked == true)
	{
		alert('Du har nu en bevakning på den här diskussionen.');// + "\n" + discussion_id);
		xmlhttp_ping('/forum/admin.php?action=discussion_watch&discussion_id=' + discussion_id);
	}
	else
	{
		alert('Du har nu tagit bort din bevakning på den här diskussionen.');// + "\n" + discussion_id);
		xmlhttp_ping('/forum/admin.php?action=discussion_watch_remove&discussion_ids=' + discussion_id);
	}
}	

function discussions_submit_selections_click(action)
{
	var discussions = getElementsByClassName(document, 'input', 'chkbox_remove');
	var discussion_ids = '';
	for(var i = 0; i < discussions.length; i++)
	{
		if(discussions[i].checked == true)
		{
			discussion_id = discussions[i].id.substr(24);
			if(discussion_ids == '')
			{
				discussion_ids = discussion_id;
			}
			else
			{
				discussion_ids = discussion_ids + ',' + discussion_id;
			}
			var div = document.getElementById('discussion_' + discussion_id);
			div.style.display = 'none';
		}
	}
	xmlhttp_ping('/forum/admin.php/?action=' + action + '&discussion_ids=' + discussion_ids);
}

function forum_favourite_category_check()
{
	if(this.checked)
	{
		xmlhttp_ping('/ajax_gateways/forum.php?action=add_favourite&category=' + this.value);
	}
	else
	{
		xmlhttp_ping('/ajax_gateways/forum.php?action=remove_favourite&category=' + this.value);
	}	
}

function enable_forum_favourite_category_check()
{
	if(document.getElementById('favourite_category_check'))
	{
		document.getElementById('favourite_category_check').onchange = forum_favourite_category_check;
	}
}

function enable_discussion_rubbish_click()
{
	if(document.getElementById('discussion_rubbish_button'))
	{
		document.getElementById('discussion_rubbish_button').onclick = discussion_rubbish_click;
	}
}

function discussion_rubbish_click()
{
	document.getElementById('discussion_admin_message').innerHTML = 'Diskussionen är nu klassad som skräp.';
	document.getElementById('discussion_admin_tag_edit_input').value = 'Skräp';
	xmlhttp_ping('/forum/admin.php?action=discussion_rubbish&discussion_id=' + this.value);
}

function forum_discussion_tutorial_button_click()
{
	document.getElementById('forum_discussion_tutorial').style.display = 'none';
	document.getElementById('forum_post_form').style.display = 'block';
	
}

function enable_forum_discussion_tutorial_button_click()
{
	if(document.getElementById('forum_discussion_tutorial_button'))
	{
		document.getElementById('forum_discussion_tutorial_button').onclick = forum_discussion_tutorial_button_click;
	}
}

function discussion_list_expander_click()
{
	if(document.getElementById('discussion_list').style.height != 'auto')
	{
		document.getElementById('discussion_list').style.height = 'auto';
		this.src = 'http://images.hamsterpaj.net/buttons/green_arrow_up_circle.png';
	}
	else
	{
		this.src = 'http://images.hamsterpaj.net/buttons/green_arrow_down_circle.png';
		document.getElementById('discussion_list').style.height = '';
	}
}

function enable_discussion_list_expander_click()
{
	if(document.getElementById('discussion_list_expander'))
	{
		document.getElementById('discussion_list_expander').onclick = discussion_list_expander_click;
	}
}

function forum_admin_edit_tags_click()
{
	var discussion_id = document.getElementById('discussion_id').value;
	var tags = document.getElementById('discussion_admin_tag_edit_input').value;
	var ping_url = '/forum/admin.php?action=update_tags&discussion_id=' + discussion_id + '&tags=' + escape(tags);
	xmlhttp_ping(ping_url);
	document.getElementById('discussion_admin_message').innerHTML = 'Taggarna har uppdaterats.';
}

function enable_forum_admin_edit_tags_click()
{
	if(document.getElementById('discussion_admin_tag_edit_submit'))
	{
		document.getElementById('discussion_admin_tag_edit_submit').onclick = forum_admin_edit_tags_click;
	}	
}

function forum_admin_discussion_rename_click()
{
	var discussion_id = document.getElementById('discussion_id').value;
	var new_title = document.getElementById('forum_admin_discussion_rename_input').value;
	var ping_url = '/forum/admin.php?action=discussion_rename&discussion_id=' + discussion_id + '&new_title=' + escape(new_title);
	xmlhttp_ping(ping_url);
	document.getElementById('discussion_admin_message').innerHTML = 'Diskussionen har ändrat namn';
	document.getElementById('discussion_head_header').innerHTML = new_title;
}

function enable_forum_admin_discussion_rename_click()
{
	if(document.getElementById('forum_admin_discussion_rename'))
	{
		document.getElementById('forum_admin_discussion_rename').onclick = forum_admin_discussion_rename_click;
	}	
}

function forum_admin_discussion_delete_click()
{
	var discussion_id = document.getElementById('discussion_id').value;
	var discussion_title = document.getElementById('discussion_title').value;
	var discussion_author = document.getElementById('discussion_author').value;
	var ping_url = '/forum/admin.php?action=discussion_delete&discussion_id=' + discussion_id;
	xmlhttp_ping(ping_url);
	document.getElementById('discussion_admin_message').innerHTML = 'Diskussionen har tagits bort.';
	window.open('/ajax_gateways/forum.php?action=discussion_delete_comment&discussion_title=' + 
				discussion_title + '&discussion_author=' + discussion_author, null, "height=300,width=600,status=yes,toolbar=no,menubar=no,location=no");
}

function enable_forum_admin_discussion_delete_click()
{
	if(document.getElementById('forum_admin_discussion_delete'))
	{
		document.getElementById('forum_admin_discussion_delete').onclick = forum_admin_discussion_delete_click;
	}	
}


function enable_discussion_head_tabs()
{
	if(document.getElementById('discussions_head_tabs'))
	{
		var child_nodes = document.getElementById('discussions_head_tabs').childNodes;
		for(var i = 0; i < child_nodes.length; i++)
		{
			if(child_nodes[i].tagName == 'DIV')
			{
				child_nodes[i].onclick = function()
				{
					var handles = Array('navigation', 'tags', 'administration');
					for(var i = 0; i < handles.length; i++)
					{
						if(document.getElementById('forum_info_pane_' + handles[i]))
						{
							document.getElementById('forum_info_pane_' + handles[i]).className = 'info_pane';
						}
						if(document.getElementById('forum_tab_' + handles[i]))
						{
							document.getElementById('forum_tab_' + handles[i]).className = '';
						}
					}
					var handle = this.id.substr(10);
					this.className = 'active';
					document.getElementById('forum_info_pane_' + handle).className = 'info_pane_visible';
				}
			}
		}
	}

}

function discussion_category_save_button_enable()
{
	if(document.getElementById('discussion_category_save_button'))
	{
		document.getElementById('discussion_category_save_button').onclick = discussion_category_save_button_click;
	}
}

function discussion_category_save_button_click()
{
	var discussion_id = document.getElementById('discussion_id').value;
	var category_id = document.getElementById('discussion_category_id').value;
	xmlhttp_ping('/ajax_gateways/forum.php?action=discussion_category_set&discussion_id=' + discussion_id + '&category_id=' + category_id);
}

womAdd('enable_discussion_rubbish_click()');
womAdd('enable_filter_low_quality_click()');
womAdd('enable_low_quality_post_click()');
womAdd('enable_posts_form_control_select()');
womAdd('enable_discussion_watch_click()');
womAdd('enable_forum_favourite_category_check()');
womAdd('enable_forum_discussion_tutorial_button_click()');
womAdd('enable_discussion_list_expander_click()');
womAdd('enable_forum_admin_edit_tags_click()');
womAdd('enable_forum_admin_discussion_rename_click()');
womAdd('enable_forum_admin_discussion_delete_click()');
womAdd('enable_discussion_head_tabs()');



