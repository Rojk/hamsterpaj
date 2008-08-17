// If hp, "The Hamsterpaj Namespace", wasn't defined before then define it!
if(typeof(hp) == 'undefined'){ var hp=new Object(); }

hp.ascii_art = {
	init: function()
	{
		var all_divs = document.getElementsByTagName('div');
		for(var div = 0; div < all_divs.length; div++)
		{
			if(typeof(all_divs[div].id) != 'undefined' && all_divs[div].id.substring(0, 15) == 'ascii_art_vote_')
			{
				all_divs[div].onmousemove = function(capture)
				{
					if(typeof(capture) != 'undefined')
					{
						var layer_x = capture.layerX;
					}
					else
					{
						this.style.position = 'absolute';
						var layer_x = hp.mouse.x - this.offsetLeft;
						this.style.position = 'static';
					}
					
					var display_surface = 75 - (15 * Math.floor((layer_x / 100) * 5));
					this.style.backgroundPosition = '0px ' + display_surface + 'px';
				}
				
				all_divs[div].onmouseout = function()
				{
					this.style.backgroundPosition = '0px ' + (15 * parseInt(this.id.substring(15).split('_')[1])) + 'px';
				}
				
				all_divs[div].onclick = function(capture)
				{
					// Clear all event handlers set for voting...
					this.onclick = function(){  }
					this.onmousemove = function(){  }
					this.onmouseout = function(){  }
					
					if(typeof(capture) != 'undefined')
					{
						var layer_x = capture.layerX;
					}
					else
					{
						this.style.position = 'absolute';
						var layer_x = hp.mouse.x - this.offsetLeft;
						this.style.position = 'static';
					}
					
					var vote_alternative = 1 + Math.floor((layer_x / 100) * 5);
					this.style.backgroundPosition = '0px ' + (75 - ((vote_alternative-1) * 15)) + 'px';
					hp.ascii_art.vote(this.id.substring(15).split('_')[0], vote_alternative);
				}
			}
		}
		
		var all_links = document.getElementsByTagName('A');
		for(var link = 0; link < all_links.length; link++)
		{
			if(typeof(all_links[link].id) != 'undefined' && all_links[link].id.substring(0, 27) == 'ascii_art_direct_link_show_')
			{
				all_links[link].onclick = function()
				{
					this.style.display = 'none';
					
					var direct_link_input = document.getElementById('ascii_art_direct_link_input_' + this.id.substring(27));
					direct_link_input.style.display = 'block';
					direct_link_input.onfocus = function()
					{
						this.select();
					}
					
					
					return false;
				}
			}
		}
	},
	voted_alternatives: new Array(),
	vote: function(ascii_art_id, vote_alt)
	{
		if(typeof(this.voted_alternatives[ascii_art_id]) == 'undefined')
		{
			xmlhttp_ping('/ajax_gateways/ascii_art.php?ascii_art_id=' + ascii_art_id + '&vote=' + vote_alt);
			this.voted_alternatives[ascii_art_id] = true;
		}
	}
}

womAdd('hp.ascii_art.init()');