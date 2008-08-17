/* This changes the category dropdown into a text input when "create new" i selected */
function photo_upload_album_create_select()
{
	if(this.value == 'text_input')
	{
		var new_entry = prompt('För att lägga till en ny kategori, skriv in namnet på kateorin här och tryck OK.\nOm du inte vill lägga till någon ny kategori, tryck "Avbryt"');
		this.options[this.options.length] = new Option(new_entry, new_entry);
		this.selectedIndex = (this.options.length - 1);
		
		/* Add this category to all other selects */
		var dropdowns = getElementsByClassName(document, 'select', 'photo_upload_album_select');
		if(dropdowns.length > 0)
		{
			for(var i = 0; i < dropdowns.length; i++)
			{
				dropdowns[i].options[dropdowns[i].options.length] = new Option(new_entry, new_entry);
			}
		}	
	}
}

function enable_photo_upload_album_create_select()
{
	var dropdowns = getElementsByClassName(document, 'select', 'photo_upload_album_select');
	if(dropdowns.length > 0)
	{
		for(var i = 0; i < dropdowns.length; i++)
		{
			dropdowns[i].onchange = photo_upload_album_create_select;
		}
	}
}

womAdd('enable_photo_upload_album_create_select()');