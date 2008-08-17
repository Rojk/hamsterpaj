// If hp, "The Hamsterpaj Namespace", wasn't defined before then define it!
if(typeof(hp) == 'undefined'){ var hp = new Object(); }

hp.music_library = {

	last_playlist: new Array(),

	init: function()
	{
		
	},
	
	fetch_artist_data: function(artist, preview_clip)
	{
		var loader = hp.give_me_an_AJAX();
		loader.onreadystatechange = function()
		{
			if(loader.readyState == 4 && loader.status == 200)
			{
				hp.music_library.artist_data_recieved(loader.responseText, preview_clip);
			}
		}
		loader.open('GET', '/mattan/gratis_musik.php?ajax=true&artist=' + encodeURIComponent(artist), true);
		loader.send(null);
	},
	
	artist_data_recieved: function(data, preview_clip)
	{
		var json_data = eval('(' + data + ')');
		
		$('#music_library_player').show('medium');
		
		var player_div = document.getElementById('music_library_player');
		/* IE-fix */ (navigator.appVersion.indexOf('MSIE')!=-1) ? player_div.style.position='absolute' : void(0);
		window.scrollTo(0, player_div.offsetTop);
		/* IE-fix */ (navigator.appVersion.indexOf('MSIE')!=-1) ? player_div.style.position='static' : void(0);
		
		document.getElementById('music_library_player_title').innerHTML = json_data.title;
		document.getElementById('music_library_player_text').innerHTML = json_data.text;
		document.getElementById('music_library_player_miscellaneous').innerHTML = json_data.miscellaneous;
		
		var playlist_div = document.getElementById('music_library_player_playlist');
		playlist_div.innerHTML = this.generate_playlist(json_data.playlist, preview_clip);
		
		for(var node = 0; node < playlist_div.childNodes[0].childNodes.length; node++)
		{
			var current_node = playlist_div.childNodes[0].childNodes[node];
			
			if(current_node.className == 'odd_selected' || current_node.className == 'even_selected')
			{
				playlist_div.scrollTop = (navigator.appVersion.indexOf('MSIE')!=-1) ? current_node.offsetTop : current_node.offsetTop - playlist_div.offsetTop;
			}
		}
		
		document.getElementById('music_library_player_player').innerHTML = this.generate_player(preview_clip);
	},
	
	generate_player: function(sound_file)
	{
		return '<embed width="160" height="20" flashvars="file=' + sound_file + '&width=160&height=20" allowfullscreen="true" quality="high" name="single" id="single" style="" src="/entertain/flvplayer.swf" type="application/x-shockwave-flash"/>';
	},
	
	swap_sound_file: function(sound_file)
	{
		var playlist_div = document.getElementById('music_library_player_playlist');
		var old_scroll = playlist_div.scrollTop;
		
		playlist_div.innerHTML = this.generate_playlist(this.last_playlist, sound_file);
		playlist_div.scrollTop = old_scroll;
		
		document.getElementById('music_library_player_player').innerHTML = this.generate_player(sound_file);
	},

	generate_playlist: function(playlist_array, current_track_url)
	{
		this.last_playlist = playlist_array;
		
		var output = '<ul>';
		for(var item = 0; item < playlist_array.length; item++)
		{
			var selected = (playlist_array[item].url == current_track_url) ? '_selected' : '';
			
			output += '<li class="' + ((item % 2) ? 'even' : 'odd' ) + selected + '">'
			       +  '<img onclick="hp.music_library.swap_sound_file(\'' + playlist_array[item].url + '\')"'
			       +  ' src="http://images.hamsterpaj.net/music_library/playlist_preview_icon_' + ((item % 2) ? 'even' : 'odd' ) + '.png" alt="Provlyssna" />'
			       +  '<a href="' + playlist_array[item].url + '">' + playlist_array[item].title + '</a>'
			       +  '</li>';
		}
		output += '</ul>';
		
		return output;
	}
}