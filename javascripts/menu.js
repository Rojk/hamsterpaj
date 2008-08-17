var double_click = false;

function menu_enable()
{
	var menu_group_labels = getElementsByClassName(document, '*', 'menu_title');
	
	for(var i = 0; i < menu_group_labels.length; i++)
	{
		menu_group_labels[i].onclick = menu_title_click;
		menu_group_labels[i].ondblclick = menu_title_dblclick;
	}
}

function menu_expand_contract(element_id)
{
	if(!double_click)
	{
		var menu_handle = element_id.substr(11);
		if(document.getElementById('menu_div_' + menu_handle))
		{
			if(document.getElementById('menu_div_' + menu_handle).className == 'menu_active')
			{
				$('#menu_div_' + menu_handle + ' .menu_content').hide(150);
				document.getElementById('menu_div_' + menu_handle).className = 'menu';
			}
			else
			{
				var active_menus = getElementsByClassName(document, 'div', 'menu_active');
				for(var i = 0; i < active_menus.length; i++)
				{
					if(active_menus[i].id != 'menu_div_' + menu_handle)
					{
						$('#' + active_menus[i].id + ' .menu_content').hide(150);
						document.getElementById(active_menus[i].id).className = 'menu';
					}
				}
				
				document.getElementById('menu_div_' + menu_handle).className = 'menu_active';
				$('#menu_div_' + menu_handle + ' .menu_content').hide(0);
				$('#menu_div_' + menu_handle + ' .menu_content').show(150);
			}
		}
	}
}

function menu_title_click()
{
	setTimeout('menu_expand_contract("' + this.id + '")', 150);
	setTimeout('double_click = false', 200);
	return false;
}

function menu_title_dblclick()
{
	double_click = true;
	if(this.href)
	{
		window.location = this.href;
	}
}

//womAdd('menu_enable()');