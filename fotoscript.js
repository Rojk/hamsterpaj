/*
	IE refuses to load an image inserted with object.innerHTML += if another string is added using object.innerHTML
	directly after. So, I made a delay of 50msec between each thumb that is added. That's the reason to 
	photoalbum_insert_thumb and photoalbum_insert_empty_thumb...
*/

var full_loading_image = new Image;
full_loading_image.src = 'http://images.hamsterpaj.net/images/photoalbum_full_loading.png';

function display_album(photos)
{
	document.getElementById('photoalbum_thumb_scroll').innerHTML = '';
	for(i = 0; i < photos.length && i < 8; i++)
	{
		if(photos[i] != 'false') /* CRAP! Array(45) will create an array with 45 keys, instead of one key with value 45. Therefore, I add another key with value false... */
		{
			setTimeout("photoalbum_insert_thumb(" + photos[i] + ")", i*50);
		}
	}
	for(i = i; i < 8; i++)
	{
		setTimeout("photoalbum_insert_empty_thumb()", i*50);
	}
}

function photoalbum_insert_thumb(id)
{
	document.getElementById('photoalbum_thumb_scroll').innerHTML += '<img src="http://images.hamsterpaj.net/images/photoalbums/images_' + Math.round(id/1000) + '/' + id + '_thumb.jpg" style="60px; height: 45px; cursor: pointer; margin: 5px; border: 1px solid #6b6b6b;" onclick="photoalbum_display_image(' + id + ');" />';
}

function photoalbum_insert_empty_thumb()
{
	document.getElementById('photoalbum_thumb_scroll').innerHTML += '<img src="http://images.hamsterpaj.net/images/photoalbum_no_image.png" style="margin: 5px; width: 60px; height: 45px; border: 1px solid #6b6b6b;" />';
}

function photoalbum_display_image(image)
{
	document.getElementById('photo_big').src = full_loading_image.src;
	document.getElementById('photo_comment_submit').disabled = true;
	document.getElementById('photo_big').src = 'http://images.hamsterpaj.net/images/photoalbums/images_' + Math.round(image/1000) + '/' + image + '_full.jpg';
	document.getElementById('photo_description').innerHTML = 'Laddar...';
	document.getElementById('photo_comments').innerHTML = 'Laddar...';
	document.getElementById('photoalbum_iframe').src = '/photoalbum/iframe.php?id=' + image;
	document.getElementById('photo_comment_id').value = image;
	document.getElementById('delete_photo_link').href = 'javascript: window.open("/traffa/admin_remove_photo.php?photo=' + image + '", "jihad", "width=500, height=400");';
	document.getElementById('photoalbum_direct_link').innerHTML = photoalbum_base_url + '&photo_id=' + image + '#photoalbum';
	document.getElementById('photoalbum_direct_link').innerHTML += '<br />http://images.hamsterpaj.net/images/photoalbums/images_' + Math.round(image/1000) + '/' + image + '_full.jpg';
}

function photoalbum_resize_full(size)
{
	var full = document.getElementById('photo_big');
	var height = full.height; //This is done because Firefox increases both the width AND the height property when told to increasethe width.
	var width = full.width;
	if(full.style.position == 'absolute' || size == 'normal')
	{
		full.style.width = 'auto';
		full.style.height = 'auto';
		full.style.position = 'static';
	}
	else
	{
		if(height > width)
		{
			var factor = (screen.availHeight - 100) / height;
		}
		else
		{
			var factor = (screen.availWidth - 20) / width;
		}
		full.style.height = (height * factor) + 'px';
		full.style.width = (width * factor) + 'px';
		full.style.position = 'absolute';
		full.style.top = '0px';
		full.style.left = '0px';
	}
}
