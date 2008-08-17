

function game_fetch_link_button_click()
{
	loadFragmentInToElementByPOST('/spel/upload_game.php', 'game_preview',  
								 "fetch_link=" + document.getElementById('game_fetch_link_input').value);
	return false;
}

function game_fetch_link_button_click_enable()
{
	document.getElementById('game_fetch_link_button').onclick = 'game_fetch_link_button_click()';
}

womAdd('game_fetch_link_button_click_enable()');
