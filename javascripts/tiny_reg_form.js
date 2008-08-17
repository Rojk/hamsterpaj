function tiny_reg_form_show(header, bread_text)
{
	document.getElementById('hamsterpaj_website').style.display = 'none';	
	document.getElementById('tiny_reg_form').style.display = 'block';
	
	if(header.length > 1)
	{
		document.getElementById('tiny_reg_form_header').innerHTML = header;
	}
	if(bread_text.length > 1)
	{
		document.getElementById('tiny_reg_form_bread_text').innerHTML = bread_text;
	}
	xmlhttp_ping('/tiny_register.php?action=tiny_reg_form_show');
}

function tiny_reg_form_hide()
{
	document.getElementById('hamsterpaj_website').style.display = 'block';
	document.getElementById('tiny_reg_form').style.display = 'none';
}

function tiny_reg_form_submit()
{
	if(document.getElementById('tiny_reg_form_password').value != document.getElementById('tiny_reg_form_password_verify').value)
	{
		alert('Hey, du måste skriva samma lösenord i båda lösenordsfälten!');	
		return false;
	}
	
	loadFragmentInToElement('/tiny_register.php?username=' + document.getElementById('tiny_reg_form_username').value + '&password=' + document.getElementById('tiny_reg_form_password').value, 'tiny_reg_form_status');
}

function tiny_reg_form_enable()
{
	if(document.getElementById('tiny_reg_form_close'))
	{
		document.getElementById('tiny_reg_form_close').onclick = tiny_reg_form_hide;
	}
	if(document.getElementById('tiny_reg_form_username_check'))
	{
		document.getElementById('tiny_reg_form_username_check').onclick = function()
		{
			loadFragmentInToElement('/username_check.php?username=' + document.getElementById('tiny_reg_form_username').value, 'tiny_reg_form_username_status');
		}
	}
	if(document.getElementById('tiny_reg_form_submit'))
	{
		document.getElementById('tiny_reg_form_submit').onclick = tiny_reg_form_submit;
	}
}

womAdd('tiny_reg_form_enable()');