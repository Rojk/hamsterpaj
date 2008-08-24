$(document).ready(function(){
var global_photo_id;
var change_photo = true;
	function updateView(photo_id, initial)
	{
	if(photo_id != 'undefined')
		{
		$('#img_full').fadeOut(400, 
		function()
		{
		
			$('#loading').html('<div>Laddar...<img src="http://images.hamsterpaj.net/loading_icons/ajax-loader3.gif" alt="Loading" /></div>');
			//remove the space in the begging that photos_fast_load.php?action=get_photo_(left|right) adds. Where the space comes from is unknown
			if(photo_id.substr(0,1) == ' ')
				photo_id = photo_id.substr(1);

			$.ajax({
				type: 'GET',
				url: '/ajax_gateways/photos_fast_load.php',
				data: 'action=get_full_photo&id='+photo_id,
				complete: function(text)
				{
		     		$('#loading').html('');
		     		$('#img_full').html(text.responseText);
			     	$('#img_full').fadeIn(400);
					global_photo_id = photo_id;
					
					$('#comment_input_text').focus(
					function()
					{
						change_photo = false;
					});

					$('#comment_input_text').blur(
					function()
					{
						change_photo = true;
					});
					
					$('#tha_image').click(
						function()
						{
		  					$.get('/ajax_gateways/photos_fast_load.php', {action: 'get_photo_right', 'id': global_photo_id}, 
		  					function(data)
			  				{
								if(data != "")
								{
	  								updateView(data, true);
			  						global_photo_id = data;
									$('ul.photos_list_mini li').css('border', '4px solid #ffffff');
									$('a#updateviewid_' + data).parent().css('border', '4px solid #ff9c11');
		  						}
  							});	
						}
					);


   				}
			});
		});
	}
	else
	{
		alert('Om bilden inte visas nu så behöver du INTE skapa en tråd om det. Skriv istället till wally_91 och ta med följande.\n\n*Webbläsare:\n*Version:\n*OS:\n*Skärmdump:\n*Länk:\n\nDå kan vi lösa det enklare.\n\nTack!');
	}
		
	//end of function
	}
	
	function get_category_photos(photo_id, keys_are_used)
	{
	if(photo_id != 'undefined')
	{
		$('#loading').html('<div>Laddar...<img src="http://images.hamsterpaj.net/loading_icons/ajax-loader3.gif" alt="Loading" /></div>');
	
		$('#img_category').fadeOut(400, function()
		{
			$.ajax({
					type: 'GET',
					url: '/ajax_gateways/photos_fast_load.php',
					data: 'action=get_category_photos&id='+photo_id,
					complete: function(text)
					{
			     		$('#img_category').html(text.responseText);
			     		$('#loading').html('');
				     	$('#img_category').fadeIn(400);

						if(keys_are_used)
						{
								$('ul.photos_list_mini li').css('border', '4px solid #ffffff');
								$('a#updateviewid_' + photo_id).parent().css('border', '4px solid #ff9c11');
								var photoid_new = photo_id;
								updateView(photoid_new, false);
								global_photo_id = photoid_new;
						}

			    			$('ul.photos_list_mini li a').click(function()
							{
								$('ul.photos_list_mini li').css('border', '4px solid #ffffff');
								$(this).parent().css('border', '4px solid #ff9c11');
								var photoid_new = $(this).attr('id').substr(13);
								updateView(photoid_new, false);
								global_photo_id = photoid_new;
							});
   					}
			});
		});
	}
	else
	{
		alert('Om bilden inte visas nu så behöver du INTE skapa en tråd om det. Skriv istället till wally_91 och ta med följande.\n\n*Webbläsare:\n*Version:\n*OS:\n*Skärmdump:\n*Länk:\n\nDå kan vi lösa det enklare.\n\nTack!');
	}
	//end of function
	}
	
	$(window).keydown(function(event){
	  	switch (event.keyCode)
	  	{
  		case 37:
  			//left arrow key
  			if(change_photo)
  			{
	  			$.get('/ajax_gateways/photos_fast_load.php', {action: 'get_photo_left', 'id': global_photo_id}, 
  				function(data)
  				{
					if(data != "")
					{
	  					updateView(data, true);
				  		global_photo_id = data;
						$('ul.photos_list_mini li').css('border', '4px solid #ffffff');
						$('a#updateviewid_' + data).parent().css('border', '4px solid #ff9c11');
	  				}
	  			});
	  		}
  		  	break;
		case 39:
			//right arrow key
			if(change_photo)
			{
  				$.get('/ajax_gateways/photos_fast_load.php', {action: 'get_photo_right', 'id': global_photo_id}, 
	  			function(data)
  				{
					if(data != "")
					{
	  					updateView(data, true);
			  			global_photo_id = data;
						$('ul.photos_list_mini li').css('border', '4px solid #ffffff');
						$('a#updateviewid_' + data).parent().css('border', '4px solid #ff9c11');
	  				}
  				});
  			}
			break;
  		}
	});
	var start_photoid = $('.updateviewid').attr('id').split('_')[0];
	get_category_photos(start_photoid);
	updateView(start_photoid, true);

});

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
		preview_images[i].src = 'http://images.hamsterpaj.net/photos/mini/' + Math.floor(photos[i].id / 5000) + '/' + photos[i].id + '.jpg';
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