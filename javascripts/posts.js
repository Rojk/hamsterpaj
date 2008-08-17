function fix_post_form()
{
	if(document.getElementById('discussion_radio_continue')) //todo! endast om "may_split", bör finnas ett snyggare sätt att testa detta
	{
/*		document.getElementById('discussion_radio_continue').onclick = discussion_split_select;
		document.getElementById('discussion_radio_split').onclick = discussion_split_select;
		document.getElementById('discussion_radio_off_topic').onclick = discussion_split_select;
		document.getElementById('discussion_radio_new').onclick = discussion_split_select;
*/
		
		document.getElementById('discussion_split_select').onchange = discussion_split_select;
		document.getElementById('new_discussion').style.display = 'none';

	}
}

function discussion_split_select()
{
	switch(this.value)
	{
		case 'continue_discussion':
			document.getElementById('new_discussion').style.display = 'none';		
			if(document.getElementById('quality_warning'))
			{
				document.getElementById('quality_warning').style.display = 'block';
			}
		break;
		case 'new_discussion':
		case 'discussion_split':
			document.getElementById('new_discussion').style.display = 'block';
			if(document.getElementById('quality_warning'))
			{
				document.getElementById('quality_warning').style.display = 'block';
			}
		break;		
	}
	if(this.value == 'new_discussion')
	{
		document.getElementById('post_form').action = 'ny_diskussion.php';
	}
	else
	{
		document.getElementById('post_form').action = 'ny_post.php';
	}
	document.getElementById('preview_button').disabled = '';
}

function posts_answer_click()
{
	var post_data = this.id.substr(19);
	var post_id = post_data.substr(0, post_data.indexOf('_'));
	var post_author = post_data.substr(post_data.indexOf('_')+1);

//	document.getElementById('post_form_content').value += '[svar:' + post_author + '=' + post_id + ']' + "\n\n" + '[/svar]' + "\n";
	tinyMCE.setContent(tinyMCE.getContent() + '<br />' + '[svar:' + post_author + '=' + post_id + ']' + '<br /><br />' + '[/svar]' + "\n");
	window.location = '#post_form';
}

function posts_quote_click()
{
	var post_data = this.id.substr(18);
	var post_id = post_data.substr(0, post_data.indexOf('_'));
	var post_author = post_data.substr(post_data.indexOf('_')+1);

	if(window.getSelection())
	{
		userSelection = window.getSelection();
//		user_selection = document.getElementById('debug_input').value;
		//alert(post_user_selection);
		//userSelection = post_user_selection;
//		alert(userSelection);
		userSelection = String(userSelection);
	}

	if(userSelection.length < 1)
	{
		alert('Du måste markera ett stycke text för att kunna citera det. Den här funktionen fungerar inte i Internet Explorer, eftersom Microsoft inte pratar det språk som man har bestämt att ska gälla på Internet.');
	}
	else
	{
//		document.getElementById('post_form_content').value += '[citat:' + post_author + '=' + post_id + ']' + "\n" + userSelection + "\n" + '[/citat]' + "\n";
		tinyMCE.setContent(tinyMCE.getContent() + '<br />' + '[citat:' + post_author + '=' + post_id + ']' + '<br />' + userSelection + '<br />' + '[/citat]' + "\n");
		window.location = '#post_form';
	}
}

function post_delete_click()
{
	var post_data = this.id.substr(19);
	var post_id = post_data.substr(0, post_data.indexOf('_'));
	var post_author = post_data.substr(post_data.indexOf('_')+1);
	
	xmlhttp_ping('/forum/admin.php?action=remove_post&post_id=' + post_id);
	
	document.getElementById('post_' + post_id).className = 'removed_post';
	document.getElementById('post_' + post_id).innerHTML = '<h2>Inlägget har tagits bort <button class="button_120" id="comment_delete_post_button_' + post_id + '">Lämna kommentar</button></h2>';
	document.getElementById('comment_delete_post_button_' + post_id).onclick = post_delete_comment;
}

function post_delete_comment()
{
	var post_id = this.id.substr(27);
	window.open('/ajax_gateways/forum.php?action=post_delete_comment&post_id=' + 
					post_id, null, "height=300,width=600,status=yes,toolbar=no,menubar=no,location=no");
}

function post_junk_click()
{
	var post_data = this.id.substr(17);
	var post_id = post_data.substr(0, post_data.indexOf('_'));
	var post_author = post_data.substr(post_data.indexOf('_')+1);
	
	xmlhttp_ping('/forum/admin.php?action=post_junk&post_id=' + post_id);
	
	document.getElementById('post_content_' + post_id).style.background = '#f6ced1';
//	document.getElementById('post_' + post_id).innerHTML = '<span style="background: #f6ced1;">' + document.getElementById('post_' + post_id).innerHTML + '</span>';
//	window.open('/ajax_gateways/forum.php?action=post_delete_comment&post_id=' + 
//					post_id, null, "height=300,width=600,status=yes,toolbar=no,menubar=no,location=no");

//	loadFragmentInToElementByPOST('/forum/admin.php', 'post_content_' + post_id, "action=post_junk" + 
//									 "&post_id=" + post_id);
}

function post_censor_click()
{
	var post_data = this.id.substr(19);
	var post_id = post_data.substr(0, post_data.indexOf('_'));
	var post_author = post_data.substr(post_data.indexOf('_')+1);
	
	
	if(window.getSelection)
	{
		var userSelection = window.getSelection();
		var str;
		if((String(userSelection)).length < 5)
		{
			alert('Du måste markera minst 10 tecken för att kunna censurera.');
		}
		else
		{
			contentObj = document.getElementById('post_content_' + post_id);
			content = String(contentObj.innerHTML);
			var selection = window.getSelection();
			var range = selection.getRangeAt(0);
			start = range.startOffset;
			end = range.endOffset;
			len = end - start;
			//det har va fräckt men behövs inte längre då innehållet laddas om nyparsat nedan
			if (range != null) {
				var span=$E({tag:'span',
										 className:'censored',
										 id:''
											});
				span.appendChild(range.cloneContents());
				range.surroundContents(span);
				str = span.innerHTML;
				alert('/forum/admin.php' + '  ' + 'post_content_' + post_id + "  action=post_censor" + 
											 "   &post_id=" + post_id +
											 "   &censored=" + str);
				loadFragmentInToElementByPOST('/forum/admin.php', 'post_content_' + post_id, "action=post_censor" + 
											 "&post_id=" + post_id +
											 "&censored=" + str);
			}
		}
	}
}

function post_addition_click()
{
	var post_data = this.id.substr(21);
	var post_id = post_data.substr(0, post_data.indexOf('_'));
	var div = document.getElementById('post_addition_' + post_id);
	div.style.display = 'block';
	document.getElementById('post_addition_submit_' + post_id).onclick = post_addition_submit_click;
}

function post_edit_click()
{
	var post_data = this.id.substr(17);
	var post_id = post_data.substr(0, post_data.indexOf('_'));
	window.open('/ajax_gateways/forum.php?action=post_edit&post_id=' + 
					post_id, null, "height=300,width=600,status=yes,toolbar=no,menubar=no,location=no");
}

function post_comment_click()
{
	var post_data = this.id.substr(20);
	var post_id = post_data.substr(0, post_data.indexOf('_'));
	window.open('/ajax_gateways/forum.php?action=post_comment&post_id=' + 
					post_id, null, "height=300,width=600,status=yes,toolbar=no,menubar=no,location=no");
}

function post_link_click()
{
	var post_data = this.id.substr(17);
	var post_id = post_data.substr(0, post_data.indexOf('_'));
	var div = document.getElementById('post_link_' + post_id);
	if(div.style.display == 'none')
	{
		div.style.display = 'block';
	}
	else
	{
		div.style.display = 'none';
	}
}

function post_addition_submit_click()
{
	var post_data = this.id.substr(21);
	var post_id = post_data.substr(0);
	var addition = document.getElementById('post_addition_content_' + post_id).value;
	loadFragmentInToElementByPOST('/forum/admin.php', 'post_content_' + post_id, "action=post_addition" + "&post_id=" + post_id + "&addition=" + addition);
	var div = document.getElementById('post_addition_' + post_id);
	div.style.display = 'none';
}

function post_reference_click()
{
	var post_recursive_id = this.id.substr(15);
	var post_referred_id = post_recursive_id.substr(0, post_recursive_id.indexOf('-'));
	if(document.getElementById('post_referred_' + post_recursive_id).style.display == 'block')
	{
		document.getElementById('post_referred_' + post_recursive_id).style.display = 'none';
	}
	else
	{
		loadFragmentInToElementByPOST('/forum/post-ajaxgateway.php', 'post_referred_' + post_recursive_id, "action=post_load_referred" +
																																							"&post_id=" + post_referred_id +
																																							"&post_recursive_id=" + post_recursive_id);
		document.getElementById('post_referred_' + post_recursive_id).style.display = 'block';
	}
	post_enable_references();
}

/**
 * document.createElement convenience wrapper
 *
 * The data parameter is an object that must have the "tag" key, containing
 * a string with the tagname of the element to create.  It can optionally have
 * a "children" key which can be: a string, "data" object, or an array of "data"
 * objects to append to this element as children.  Any other key is taken as an
 * attribute to be applied to this tag.
 *
 * Available under an MIT license:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * @param {Object} data The data representing the element to create
 * @return {Element} The element created.
 */
function $E(data) {
    var el;
    if ('string'==typeof data) {
        el=document.createTextNode(data);
    } else {
        //create the element
        el=document.createElement(data.tag);
        delete(data.tag);

        //append the children
        if ('undefined'!=typeof data.children) {
            if ('string'==typeof data.children ||
                'undefined'==typeof data.children.length
            ) {
                //strings and single elements
                el.appendChild($E(data.children));
            } else {
                //arrays of elements
                for (var i=0, child=null; 'undefined'!=typeof (child=data.children[i]); i++) {
                    el.appendChild($E(child));
                }
            }
            delete(data.children);
        }

        //any other data is attributes
        for (attr in data) {
            el[attr]=data[attr];
        }
    }

    return el;
}


function post_enable_controls()
{
	var delete_buttons = getElementsByClassName(document, 'input', 'post_delete_button');
	for(var i = 0; i < delete_buttons.length; i++)
	{
		delete_buttons[i].onclick = post_delete_click;
	}
	var comment_buttons = getElementsByClassName(document, 'input', 'post_comment_button');
	for(var i = 0; i < delete_buttons.length; i++)
	{
		comment_buttons[i].onclick = post_comment_click;
	}
	var censor_buttons = getElementsByClassName(document, 'input', 'post_censor_button');
	for(var i = 0; i < censor_buttons.length; i++)
	{
		censor_buttons[i].onclick = post_censor_click;
	}
	var addition_buttons = getElementsByClassName(document, 'input', 'post_addition_button');
	for(var i = 0; i < addition_buttons.length; i++)
	{
		addition_buttons[i].onclick = post_addition_click;
	}
	var edit_buttons = getElementsByClassName(document, 'input', 'post_edit_button');
	for(var i = 0; i < edit_buttons.length; i++)
	{
		edit_buttons[i].onclick = post_edit_click;
	}
	var junk_buttons = getElementsByClassName(document, 'input', 'post_junk_button');
	for(var i = 0; i < junk_buttons.length; i++)
	{
		junk_buttons[i].onclick = post_junk_click;
	}
	var answer_links = getElementsByClassName(document, 'input', 'post_answer_button');
	for(var i = 0; i < answer_links.length; i++)
	{
		answer_links[i].onclick = posts_answer_click;
	}
	var quote_buttons = getElementsByClassName(document, 'input', 'post_quote_button');
	for(var i = 0; i < quote_buttons.length; i++)
	{
		quote_buttons[i].onclick = posts_quote_click;
	}
	var link_buttons = getElementsByClassName(document, 'input', 'post_link_button');
	for(var i = 0; i < link_buttons.length; i++)
	{
		link_buttons[i].onclick = post_link_click;
	}
}

function post_enable_references()
{
	var post_references = getElementsByClassName(document, 'span', 'post_reference');
	for(var i = 0; i < post_references.length; i++)
	{		
		post_references[i].onclick = post_reference_click;
	}
}

function enable_preview_button()
{
	if(document.getElementById('preview_button'))
	{
		document.getElementById('preview_button').onclick = post_preview_click;
	}
}

function enable_submit_button()
{
	if(document.getElementById('submit_button'))
	{
		//document.getElementById('submit_button').onclick = post_submit_click;
	}
}

function post_preview_click()
{
	title_ok = true;
	if(document.getElementById('new_discussion').style.display == 'block')
	{
		title_ok = check_title_length();
	}
	if(title_ok)
	{
		var content = tinyMCE.getContent('post_form_content').replace(/\+/g, "&#43");
		if(content.length > 0)
		{
			enable_submit_button();
			document.getElementById('preview').style.display = 'block';
			document.getElementById('submit_button_div').style.display = 'block';
		
			content = content.replace(/\\/g, "&#92");
			content = escape(content);
			loadFragmentInToElementByPOST('/forum/preview.php', 'preview', "subject=" + document.getElementById('post_form_subject').value + "" +
									"&text=" + content +
									"&desired_quality=" + document.getElementById('desired_quality_value').value +
									"&quality_rank=" + document.getElementById('quality_rank_value').value);
		}
		else
		{
			document.getElementById('preview').style.display = 'none';
			document.getElementById('submit_button_div').style.display = 'none';
		}
	}
//	loadFragmentInToElementByPOST('/forum/preview.php', 'preview', "subject=" + document.getElementById('post_form_subject').value + "" +
//																																							 "&text=" + document.getElementById('post_form_content').value + "" );
	return false;
}

function post_submit_click()
{
	var submit = true;
	if(document.getElementById('new_discussion').style.display == 'block')
	{
		submit = check_title_length();
	}
	if(submit)
	{
		document.getElementById('submit_button').value = 'Var vänlig vänta.';
		document.getElementById('submit_button').disabled = 'disabled';
		document.getElementById('post_form').submit();
		return false;
	}
	return false;
}

function check_title_length()
{
	var subject_length = document.getElementById('post_form_subject').value.length;
	if(subject_length < 5 || subject_length > 60)
	{
		alert('När du skapar en ny diskussion måste du ange en rubrik.\n Tänk på att rubriken ska beskriva vad diskussionen handlar om. Din rubrik måste vara minst fem tecken lång, men får inte vara längre än 60 tecken.');
		return false;
	}
	else
	{
		return true;
	}
}

function show_misspelling(id)
{
	var elements = enannanvariantav_getElementsByClassName('spelling_suggestion');
	for(var i = 0; i < elements.length; i++)
	{
		if(elements[i].id == id)
		{
			elements[i].style.display = 'block';
		} else {
			elements[i].style.display = 'none';
		}
	}
}

//todo! Den här verkar fungera bättre än den som fanns men det kanske bara var att de används olika
//Eventuellt kan denna ersätta den befintliga eller ändras anropet ovan till att fungera
//med den befintliga.
function enannanvariantav_getElementsByClassName(className, tag, elm){
	var testClass = new RegExp("(^|\\s)" + className + "(\\s|$)");
	var tag = tag || "*";
	var elm = elm || document;
	var elements = (tag == "*" && elm.all)? elm.all : elm.getElementsByTagName(tag);
	var returnElements = [];
	var current;
	var length = elements.length;
	for(var i=0; i<length; i++){
		current = elements[i];
		if(testClass.test(current.className)){
			returnElements.push(current);
		}
	}
	return returnElements;
}

function forum_quality_show_all()
{
	var low_quality_posts = getElementsByClassName(document, 'div', 'low_quality_post');
	var post_id;
	for(var i = 0; i < low_quality_posts.length; i++)
	{
		post_id = low_quality_posts[i].id.substr(27);
		document.getElementById('post_' + post_id).style.display = 'block';
	}
}

function forum_quality_hide_all()
{
	var low_quality_posts = getElementsByClassName(document, 'div', 'low_quality_post');
	var post_id;
	for(var i = 0; i < low_quality_posts.length; i++)
	{
		post_id = low_quality_posts[i].id.substr(27);
		document.getElementById('post_' + post_id).style.display = 'none';
	}
}

function forum_quality_hide_show_all()
{
	if(this.innerHTML == 'Visa alla inlägg med låg kvalitet')
	{
		forum_quality_show_all()
		this.innerHTML = 'Dölj alla inlägg med låg kvalitet'
	}
	else
	{
		forum_quality_hide_all()
		this.innerHTML = 'Visa alla inlägg med låg kvalitet'
	}
}

function enable_quality_show_hide_all()
{
	if(document.getElementById('forum_quality_hide_show_all_control'))
	{
		document.getElementById('forum_quality_hide_show_all_control').onclick = forum_quality_hide_show_all;
	}
}

function posts_show_tag_div()
{
	document.getElementById('tag_div_button').style.display = 'none';
	document.getElementById('tag_div_normal').style.display = 'block';
	return false;
}

function forum_insert_smilie(smilie)
{
	tinyMCE.execInstanceCommand("post_form_content","mceInsertContent",false,smilie);
	// Inserts some HTML contents at the current selection
}

womAdd('enable_preview_button()');
womAdd('fix_post_form()');
womAdd('post_enable_controls()');
womAdd('post_enable_references()');
womAdd('enable_quality_show_hide_all()');
