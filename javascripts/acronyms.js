function expand_contract_acronym()
{
	if(this.className == 'expanded')
	{
		this.className = 'contracted';
	}
	else
	{
		this.className = 'expanded';
	}
}

function make_acronyms_clickable()
{
try
{
	var items = document.getElementById('acronyms').getElementsByTagName('li');
	for(var i = 0; i < items.length; i++)
	{
		items[i].onclick = expand_contract_acronym;
	}
	var buttons = document.getElementById('acronyms').getElementsByTagName('input');
	for(var i = 0; i < buttons.length; i++)
	{
		if(buttons[i].id.substr(0, 8) == 'btn_edit')
		{
			buttons[i].onclick = function()
			{
				window.location = '?edit=' + this.id.substring(9);
			}
		}
		else if(buttons[i].id.substr(0, 10) == 'btn_delete')
		{
			buttons[i].onclick = function()
			{
				window.location = '?delete=' + this.id.substring(11);
			}
		}
		else if(buttons[i].id.substr(0, 10) == 'btn_report')
		{
			buttons[i].onclick = function()
			{
				window.location = '?report=' + this.id.substring(11);
			}
		}
		else if(buttons[i].id.substr(0, 10) == 'btn_verify')
		{
			buttons[i].onclick = function()
			{
				window.location = '?action=verify&id=' + this.id.substring(11);
			}
		}
	}
}
catch(err) {
	alert(err.description);
}

womAdd("make_acronyms_clickable()");
womOn();
}