function enable_game_vote_click()
{
	if(document.getElementById('game_vote'))
	{
		document.getElementById('game_vote').onclick = game_vote_click;
	}
}

function game_vote_click()
{
	var game_handle = document.getElementById('game_handle').value;
	document.getElementById('game_vote_div').innerHTML = '<h5>Hypat!</h5>';
	xmlhttp_ping('/spel/vote.' + game_handle + '.php');
}

function game_fetch_link_button_click()
{
	loadFragmentInToElementByPOST('/spel/upload_game.php', 'game_preview',  
								 "fetch_link=" + document.getElementById('game_fetch_link_input').value);
	return false;
}

function game_fetch_link_button_click_enable()
{
	if(document.getElementById('game_fetch_link_button'))
	{
		document.getElementById('game_fetch_link_button').onclick = game_fetch_link_button_click;
	}
}

function game_browse_preference_button_click()
{
//	xmlhttp_ping('/spel/played.' + this.id.substr(7) + '.php');
//	window.location.reload();
	document.game_search_options_form.submit();
}

function game_browse_preference_button_click_enable()
{
	if(document.getElementById('played_yes'))
	{
		document.getElementById('played_yes').onclick = game_browse_preference_button_click;
	}
	if(document.getElementById('played_no'))
	{
		document.getElementById('played_no').onclick = game_browse_preference_button_click;
	}
	if(document.getElementById('played_ignore'))
	{
		document.getElementById('played_ignore').onclick = game_browse_preference_button_click;
	}
}

function open_fullscreen_window(target_url)
{
	alert('För att få riktig fullskärm trycker du på F11 på ditt tangentbord, när du vill tillbaks till fönsterläge håller du inne ALT-knappen och trycker på F4!');
	document.getElementById('game').innerHTML = '';
	var sc_width = screen.width;
	var sc_height = screen.height;
	window.open(target_url, 'fullscreen_window', 'width=' + sc_width + ', height=' + sc_height + ', toolbar=no, location=no');
}

function game_challenge_button_click_enable()
{
	if(document.getElementById('game_challenge_button'))
	{
		document.getElementById('game_challenge_button').onclick = game_challenge_button_click;
	}
}

function game_challenge_button_click()
{
	document.getElementById('game_challenge').style.display = 'block';
	document.getElementById('game_challenge_result').style.display = 'none';
	window.location = '#challenge';
}

function game_challenge_hp_submit_click_enable()
{
	if(document.getElementById('game_challenge_hp_submit'))
	{
		document.getElementById('game_challenge_hp_submit').onclick = game_challenge_hp_submit_click;
	}
}

function game_challenge_mail_submit_click_enable()
{
	if(document.getElementById('game_challenge_mail_submit'))
	{
		document.getElementById('game_challenge_mail_submit').onclick = game_challenge_mail_submit_click;
	}
}

function game_challenge_hp_submit_click()
{
	//hämta och kolla username, message och game_handle (hidden input)
	
	//Här skickas meddelandet och en bekräftelse fås tillbaka och visas i diven istället för formuläret
	loadFragmentInToElementByPOST('/spel/challenge.php', 'game_challenge_result',
									"action=challenge_hp"
									+ "&username_1=" + document.getElementById('game_challenge_username_1').value
									+ "&username_2=" + document.getElementById('game_challenge_username_2').value
									+ "&username_3=" + document.getElementById('game_challenge_username_3').value
									+ "&message=" + document.getElementById('game_challenge_hp_message').value
									+ "&game_handle=" + document.getElementById('game_handle').value
									+ "&game_title=" + document.getElementById('game_title').value
									);
	document.getElementById('game_challenge').style.display = 'none';
	document.getElementById('game_challenge_result').style.display = 'block';
}

function game_challenge_mail_submit_click()
{
	//hämta och kolla username, message och game_handle (hidden input)
	
	//Här skickas meddelandet och en bekräftelse fås tillbaka och visas i diven istället för formuläret
	loadFragmentInToElementByPOST('/spel/challenge.php', 'game_challenge_result',
									"action=challenge_mail"
									+ "&email_1=" + document.getElementById('game_challenge_email_1').value
									+ "&email_2=" + document.getElementById('game_challenge_email_2').value
									+ "&email_3=" + document.getElementById('game_challenge_email_3').value
									+ "&message=" + document.getElementById('game_challenge_mail_message').value
									+ "&sender=" + document.getElementById('challenge_sender_name').value
									+ "&game_handle=" + document.getElementById('game_handle').value
									+ "&game_title=" + document.getElementById('game_title').value
									+ "&security_code=" + document.getElementById('security_code').value
									);
	document.getElementById('game_challenge').style.display = 'none';
	document.getElementById('game_challenge_result').style.display = 'block';
}

function game_comment_submit_click_enable()
{
	if(document.getElementById('comment_submit_button'))
	{
		document.getElementById('comment_submit_button').onclick = game_comment_submit_click;
	}
	return false;
}

function game_comment_submit_click()
{
	//skicka med ny kommentar till laddning av ny kommentarslistning
	loadFragmentInToElementByPOST('/spel/comment.php', 'game_comments_list',
									"action=comment"
									+ "&comment=" + escape(document.getElementById('game_comment_textarea').value)
									+ "&game_handle=" + document.getElementById('game_handle').value);
}

function games_comment_delete(post_id)
{
	loadFragmentInToElementByPOST('/spel/comment_delete.php', 'game_comment_' + post_id,
									"action=comment_delete"
									+ "&comment_id=" + post_id);
	document.getElementById('game_comment_' + post_id).style.display = 'none';
}

womAdd('game_browse_preference_button_click_enable()');
womAdd('game_fetch_link_button_click_enable()');
womAdd('enable_game_vote_click()');
womAdd('game_challenge_button_click_enable()');
womAdd('game_challenge_hp_submit_click_enable()');
womAdd('game_challenge_mail_submit_click_enable()');
womAdd('game_comment_submit_click_enable()');
