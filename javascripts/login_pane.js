// If hp, "The Hamsterpaj Namespace", wasn't defined before then define it!
if(typeof(hp) == "undefined"){ var hp=new Object(); }

hp.login_pane = {
	init: function(){
		if('debug' == 'debug'){
			hp.login_pane.new_request();
		}
		setInterval("hp.login_pane.new_request()", 20000);
	},

	create_ajax_object: function(){
		try{
			return new XMLHttpRequest();
		}catch(e){
			try{
				return new ActiveXObject("Msxml2.XMLHTTP");
			}catch(e){
				try{
					return new ActiveXObject("Microsoft.XMLHTTP");
				}catch(e){
					return false;
				}
			}
		}
	},

	new_request: function(){
		var loader = this.create_ajax_object();
		loader.onreadystatechange=function(){ hp.login_pane.handle_response(loader); };
		loader.open("GET", "/ajax_gateways/update-events.json.php?destroy_cache="+Math.random(), true);
		loader.send(null);
	},

	handle_response: function(loader){
		/* All data loaded and no errormessages from (the?) serverside. */
		if(loader.readyState == 4 && loader.status == 200){
			/* Call parse_response with a 'JSON-object': */
			this.parse_response(eval('(' + loader.responseText + ')'));
		}
	},

	parse_response: function(json_response){
		if(json_response.logged_on)
		{
			
			/* PM */
			if(json_response.new_messages == 0)
			{
				document.getElementById("login_pane_messages_label").innerHTML = "Meddelanden";
			}
			else
			{
				document.getElementById("login_pane_messages_label").innerHTML = (json_response.new_messages == 1) ? 'Ett nytt' : json_response.new_messages+' nya';
			}
	
			/* Guestbook */
			if(json_response.new_guestbook_posts == 0)
			{
				document.getElementById("login_pane_guestbook_label").innerHTML = "Gästbok";
			}
			else
			{
				document.getElementById("login_pane_guestbook_label").innerHTML = (json_response.new_guestbook_posts == 1) ? 'Ett nytt' : json_response.new_messages+' nya';
			}
			
			/* Forum */		
	
	
			/* Groups */


		}
		else
		{
			//User was logged off...
		}
	}
}

womAdd("hp.login_pane.init()");