// If hp, "The Hamsterpaj Namespace", wasn't defined before then define it!
if(typeof(hp) == 'undefined'){ var hp = new Object(); }

hp.user_lists = {
	init: function()
	{
		this.insert.init();
	},
	
	insert:
	{
		item_count: 0,
		
		init: function()
		{
			document.getElementById('user_lists_insert_add_item').onclick = function()
			{
				hp.user_lists.insert.add_item();
			}
			
			// Add one item first...
			this.add_item();
		},
	
		add_item: function()
		{
			var item_list = document.getElementById('user_lists_insert_items');
			var item_id = this.item_count++;
			var new_item = document.createElement('li');
			
			new_item.id = 'user_lists_insert_item_' + item_id;
			new_item.className = 'checked';
			new_item.innerHTML =  '<input type="text" name="item_text_' + item_id + '" value="" /> <input type="button" id="user_lists_insert_item_check_' + item_id + '" value="Kryssa ur" class="button_120" />';
			new_item.innerHTML += '<input type="hidden" name="item_checked_' + item_id + '" id="user_lists_insert_item_checked_' + item_id + '" value="checked" />';
			
			item_list.appendChild(new_item);
			
			document.getElementById('user_lists_insert_item_check_' + item_id).onclick = function()
			{
				hp.user_lists.insert.check_item(this.id.substring(29));
			}
		},
		
		check_item: function(item_id)
		{
			var checker_object = document.getElementById('user_lists_insert_item_checked_' + item_id);
			checker_object.value = (checker_object.value == 'checked') ? 'unchecked' : 'checked';
			
			document.getElementById('user_lists_insert_item_' + item_id).className = checker_object.value;
			
			document.getElementById('user_lists_insert_item_check_' + item_id).value = (checker_object.value == 'unchecked') ? 'Kryssa i': 'Kryssa ur';
		}
	}	
}

womAdd('hp.user_lists.init()');