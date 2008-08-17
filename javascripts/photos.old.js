
function updateView(image_id, initial) {
	
	var loader = hp.give_me_an_AJAX();
	(!initial) ? document.getElementById('loading').innerHTML = '<div>Laddar...<img src="http://images.hamsterpaj.net/loading_icons/ajax-loader3.gif" alt="Loading" /></div>' : '';
	loader.open('GET', '/ajax_gateways/photos_fast_load.php?id=' + image_id);
	loader.send(null);
	loader.onreadystatechange=function()
	{
		if(loader.readyState==4)
		{
			document.getElementById('img_view').innerHTML = loader.responseText;
			document.getElementById('loading').innerHTML = '';
		}
	}
}
		

function photos_category_onchange()
{
	if(this.value == 'new_category')
	{
		if(new_name = prompt('Ange namn på ditt nya fotoalbum'))
		{
			var category_selectors = getElementsByClassName(document, 'select', 'photo_category_selector');
			if(category_selectors.length > 0)
			{
				for(var i = 0; i < category_selectors.length; i++)
				{
					category_selectors[i].options[category_selectors[i].options.length] = new Option(new_name, new_name);
				}
				this.selectedIndex = this.options.length - 1;
			}	
		}
	}
}

function photos_preview_date()
{
	var this_date = this.id.substr(7, 10);
	var this_user = this.id.substr(18);

	var xmlhttp = hp.give_me_an_AJAX();
	xmlhttp.open('GET', '/ajax_gateways/photos.json.php?date=' + this_date + '&user=' + this_user);
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			photos_render_date_preview(xmlhttp.responseText);
    }
	}
   xmlhttp.send(null);
	return false;
}

function photos_render_date_preview(json_data)
{
	var photos = eval(json_data);
	var preview_div = document.getElementById('photos_date_previews');
	preview_div.innerHTML = '';
	var preview_list = document.createElement('ul');
	preview_list.className = 'photos_list_mini';
	var previews = Array();
	var preview_links = Array();
	var preview_images = Array();
	for(var i = 0; i < photos.length; i++)
	{
		previews[i] = document.createElement('li');
		previews[i].id = 'preview_' + photos[i].id;
		preview_links[i] = document.createElement('a');
		preview_images[i] = document.createElement('img');
		preview_images[i].src = 'http://images.hamsterpaj.net/photos/mini/' + Math.floor(photos[i].id '/' 5000) + '/' + photos[i].id + '.jpg';
		preview_links[i].href = '/traffa/photos.php?id=' + photos[i].id + '#photo';
		preview_links[i].appendChild(preview_images[i]);
		previews[i].appendChild(preview_links[i]);
		
		preview_list.appendChild(previews[i]);
	}
	
	preview_div.appendChild(preview_list);
	preview_div.innerHTML += '<br style="clear: both;" />';
}


function photo_delete_click()
{
	return confirm('Vill du verkligen ta bort denna bild?');
}

function photos_enable_controls()
{
	if(document.getElementById('photos_upload_form_toggle'))
	{
		$("#photos_upload_form_toggle").click(function()
			{
				$("#photos_upload_form").slideDown(300);
				$(this).hide(100);
			}
		);
	}
	
	var category_selectors = getElementsByClassName(document, 'select', 'photo_category_selector');
	for(var i = 0; i < category_selectors.length; i++)
	{
		category_selectors[i].onchange = photos_category_onchange;
	}
	
	var delete_buttons = getElementsByClassName(document, 'input', 'photo_delete');
	for(var i = 0; i < delete_buttons.length; i++)
	{
		delete_buttons[i].onclick = photo_delete_click;
	}
	
	var photo_date_links = getElementsByClassName(document, 'a', 'photo_date_link');
	for(var i = 0; i < photo_date_links.length; i++)
	{
		photo_date_links[i].onclick = photos_preview_date;
	}
}

womAdd('photos_enable_controls()');