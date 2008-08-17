/*
function amuse_view_item(item_id, row_id)
{
	alert('Item #' + item_id + ', row #' + row_id);
}
*/


function open_fullscreen_window(target_url)
{
	alert('För att få riktig fullskärm trycker du på F11 på ditt tangentbord, när du vill tillbaks till fönsterläge håller du inne ALT-knappen och trycker på F4!');
	var sc_width = screen.width;
	var sc_height = screen.height;
	window.open(target_url, 'fullscreen_window', 'width=' + sc_width + ', height=' + sc_height + ', toolbar=no, location=no');
}


	var amuse_open_item = '';
	function amuse_view_item(item_id, row_id)
	{
		if(document.getElementById('amuse_open_item'))
		{
			document.getElementById('amuse_open_item').parentNode.removeChild(document.getElementById('amuse_open_item'));
		}

		amuse_open_item = document.createElement('div');
		amuse_open_item.id = 'amuse_open_item';
		document.getElementById('amuse_row_' + row_id).appendChild(amuse_open_item);
		loadFragmentInToElement('ajax_gateway.php?action=display_item&item_id=' + item_id, 'amuse_open_item');
	}
	
	function amuse_close_item()
	{
		document.getElementById('amuse_open_item').parentNode.removeChild(document.getElementById('amuse_open_item'));
	}
	
	function amuse_show_control(control_id)
	{
		document.getElementById('amuse_control_panel').style.display = 'none';
		document.getElementById(control_id).style.display = 'block';
	}
	
	function amuse_hide_control(control_id)
	{
		document.getElementById(control_id).style.display = 'none';
		document.getElementById('amuse_control_panel').style.display = 'block';
	}
	
	function amuse_load_comments(item_id)
	{
		document.getElementById('amuse_comments').style.display = 'block';
		loadFragmentInToElement('ajax_gateway.php?action=load_comments&item_id=' + item_id, 'amuse_comments');
	}