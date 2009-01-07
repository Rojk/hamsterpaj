// If hp, "The Hamsterpaj Namespace", wasn't defined before then define it!
if(typeof(hp) == 'undefined'){ var hp=new Object(); }

hp.synchronize = {
	synchronize_interval: 40,
	
	synchronized_objects: new Array(),
	init: function()
	{
		setInterval('hp.synchronize.do_synchronization()', this.synchronize_interval * 1000);
	},
	
	add: function(params)
	{
		this.synchronized_objects[params.handle] = params;
	},
	
	do_synchronization: function()
	{
		var loader = hp.give_me_an_AJAX();
		loader.onreadystatechange = function()
		{
			if(loader.readyState == 4 && loader.status == 200)
			{
				hp.synchronize.parse_response(eval('(' + loader.responseText + ')'));
			}
		}
		
		var fetch = '';
		for(var handle in this.synchronized_objects)
		{
			fetch += ((fetch == '') ? '' : ',') + handle;
		} 
		
		loader.open('GET', '/ajax_gateways/synchronize.json.php?fetch=' + fetch, true);
		loader.send(null);
	},
	
	parse_response: function(json_data)
	{
		for(var obj in json_data)
		{
			for(var handle in json_data[obj])
			{
				if(typeof(this.synchronized_objects[handle]) != 'undefined')
				{
					this.synchronized_objects[handle].json_data = json_data[obj][handle];
					this.synchronized_objects[handle].on_response();
				}
			}
		}
	}
};

womAdd('hp.synchronize.init()');