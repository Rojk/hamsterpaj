var grid_width = 40;var grid_height = 25;var flood_filling_color = '#ffffff'; // Helping the flood_fill recursion to keep track of which color to replace var painting_mode = 'pen'; //pen or flood_fillfunction get_hex_color(color_string){	if(color_string.indexOf('rgb') == -1)	{		return color_string;	}	else	{		return rgbval2hex(color_string);	}}function rgbval2hex(rgbval){	var comma_separated = rgbval.substring(4, rgbval.length-1);	var exploded = comma_separated.split(',');	var hexval = '#';	for(j = 0; j < 3; j++)	{		if((exploded[j]*1).toString(16).length == 1)		{			hexval += '0' + (exploded[j]*1).toString(16);		}		else		{			hexval += (exploded[j]*1).toString(16);		}	}	return hexval;}/* Define the color codes and their number. Use two arrays to avoid searching thru on earray thounsand times... */var colors_id2hex = Array();colors_id2hex['0'] = '#ffffff';colors_id2hex['1'] = '#aaaaaa';colors_id2hex['2'] = '#888888';colors_id2hex['3'] = '#444444';colors_id2hex['4'] = '#000000';colors_id2hex['5'] = '#ffbfbf';colors_id2hex['6'] = '#ff8080';colors_id2hex['7'] = '#ff4040';colors_id2hex['8'] = '#d90000';colors_id2hex['9'] = '#800000';colors_id2hex['a'] = '#ffe1bf';colors_id2hex['b'] = '#ffc480';colors_id2hex['c'] = '#ffa640';colors_id2hex['d'] = '#d97400';colors_id2hex['e'] = '#804400';colors_id2hex['f'] = '#ffffbf';colors_id2hex['g'] = '#ffff80';colors_id2hex['h'] = '#ffff40';colors_id2hex['i'] = '#d9d900';colors_id2hex['j'] = '#808000';colors_id2hex['k'] = '#e1ffbf';colors_id2hex['l'] = '#c4ff80';colors_id2hex['m'] = '#a6ff40';colors_id2hex['n'] = '#74d900';colors_id2hex['o'] = '#448000';colors_id2hex['p'] = '#bfffbf';colors_id2hex['q'] = '#80ff80';colors_id2hex['r'] = '#40ff40';colors_id2hex['s'] = '#00d900';colors_id2hex['t'] = '#008000';colors_id2hex['u'] = '#bfffe1';colors_id2hex['v'] = '#80ffc4';colors_id2hex['w'] = '#40ffa6';colors_id2hex['x'] = '#00d974';colors_id2hex['y'] = '#008044';colors_id2hex['z'] = '#bfffff';colors_id2hex['A'] = '#80ffff';colors_id2hex['B'] = '#40ffff';colors_id2hex['C'] = '#00d9d9';colors_id2hex['D'] = '#008080';colors_id2hex['E'] = '#bfe1ff';colors_id2hex['F'] = '#80c4ff';colors_id2hex['G'] = '#40a6ff';colors_id2hex['H'] = '#0074d9';colors_id2hex['I'] = '#004480';colors_id2hex['J'] = '#bfbfff';colors_id2hex['K'] = '#8080ff';colors_id2hex['L'] = '#4040ff';colors_id2hex['M'] = '#0000d9';colors_id2hex['N'] = '#000080';colors_id2hex['O'] = '#e1bfff';colors_id2hex['P'] = '#c480ff';colors_id2hex['Q'] = '#a640ff';colors_id2hex['R'] = '#7400d9';colors_id2hex['S'] = '#440080';colors_id2hex['T'] = '#ffbfff';colors_id2hex['U'] = '#ff80ff';colors_id2hex['V'] = '#ff40ff';colors_id2hex['W'] = '#d900d9';colors_id2hex['X'] = '#800080';colors_id2hex['Y'] = '#ffbfe1';colors_id2hex['Z'] = '#ff80c4';colors_id2hex['_'] = '#ff40a6';colors_id2hex['!'] = '#d90074';colors_id2hex['?'] = '#800044';var colors_hex2id = Array();colors_hex2id['#ffffff'] = '0';colors_hex2id['#aaaaaa'] = '1';colors_hex2id['#888888'] = '2';colors_hex2id['#444444'] = '3';colors_hex2id['#000000'] = '4';colors_hex2id['#ffbfbf'] = '5';colors_hex2id['#ff8080'] = '6';colors_hex2id['#ff4040'] = '7';colors_hex2id['#d90000'] = '8';colors_hex2id['#800000'] = '9';colors_hex2id['#ffe1bf'] = 'a';colors_hex2id['#ffc480'] = 'b';colors_hex2id['#ffa640'] = 'c';colors_hex2id['#d97400'] = 'd';colors_hex2id['#804400'] = 'e';colors_hex2id['#ffffbf'] = 'f';colors_hex2id['#ffff80'] = 'g';colors_hex2id['#ffff40'] = 'h';colors_hex2id['#d9d900'] = 'i';colors_hex2id['#808000'] = 'j';colors_hex2id['#e1ffbf'] = 'k';colors_hex2id['#c4ff80'] = 'l';colors_hex2id['#a6ff40'] = 'm';colors_hex2id['#74d900'] = 'n';colors_hex2id['#448000'] = 'o';colors_hex2id['#bfffbf'] = 'p';colors_hex2id['#80ff80'] = 'q';colors_hex2id['#40ff40'] = 'r';colors_hex2id['#00d900'] = 's';colors_hex2id['#008000'] = 't';colors_hex2id['#bfffe1'] = 'u';colors_hex2id['#80ffc4'] = 'v';colors_hex2id['#40ffa6'] = 'w';colors_hex2id['#00d974'] = 'x';colors_hex2id['#008044'] = 'y';colors_hex2id['#bfffff'] = 'z';colors_hex2id['#80ffff'] = 'A';colors_hex2id['#40ffff'] = 'B';colors_hex2id['#00d9d9'] = 'C';colors_hex2id['#008080'] = 'D';colors_hex2id['#bfe1ff'] = 'E';colors_hex2id['#80c4ff'] = 'F';colors_hex2id['#40a6ff'] = 'G';colors_hex2id['#0074d9'] = 'H';colors_hex2id['#004480'] = 'I';colors_hex2id['#bfbfff'] = 'J';colors_hex2id['#8080ff'] = 'K';colors_hex2id['#4040ff'] = 'L';colors_hex2id['#0000d9'] = 'M';colors_hex2id['#000080'] = 'N';colors_hex2id['#e1bfff'] = 'O';colors_hex2id['#c480ff'] = 'P';colors_hex2id['#a640ff'] = 'Q';colors_hex2id['#7400d9'] = 'R';colors_hex2id['#440080'] = 'S';colors_hex2id['#ffbfff'] = 'T';colors_hex2id['#ff80ff'] = 'U';colors_hex2id['#ff40ff'] = 'V';colors_hex2id['#d900d9'] = 'W';colors_hex2id['#800080'] = 'X';colors_hex2id['#ffbfe1'] = 'Y';colors_hex2id['#ff80c4'] = 'Z';colors_hex2id['#ff40a6'] = '_';colors_hex2id['#d90074'] = '!';colors_hex2id['#800044'] = '?';/* 	This code is copyright 2004 by Gavin Kistner, gavin@refinery.com	It is covered under the license viewable at http://phrogz.net/JS/_ReuseLicense.txt	Reuse or modification is free provided you abide by the terms of that license.	(Including the first two lines above in your source code mostly satisfies the conditions.)	Find the CSS rule by its selector text, in any styleshet	e.g. get_css_class( 'a.selected' ).style.fontWeight='bold';*/function get_css_class(selText){	for (var j=document.styleSheets.length-1;j>=0;j--){		var ss = document.styleSheets[j];		var cache = ss.cssRulesCache = ss.cssRulesCache || {};			//screw case sensitivity; IE and Mozilla don't agree on case for elements themselves		selText=selText.toLowerCase();			if (cache[selText]) return cache[selText];		var rules = ss.cssRules || ss.rules;		for (var i=0,len=rules.length;i<len;i++)
		{			var rule=rules[i];
			var ruleSelText = rule.selectorText;						// Clean up Safari's weirdness			ruleSelText = ruleSelText.replace(/\.(\w+)\[CLASS~="\1"\]/g,'.$1').replace(/\[ID"([^"]+)"\]/g,'#$1');						// Clean up IEMac's weirdness			ruleSelText = ruleSelText.replace(/\*([.#])/g,'$1');				//screw case sensitivity; IE and Mozilla don't agree on case for elements themselves			ruleSelText = ruleSelText.toLowerCase();			if (ruleSelText==selText) return (cache[selText]=rules[i]);		}	}	return null;}/* Mouse status handling */var mouse_status = 'up';function set_mouse_down(){	mouse_status = 'down';}function set_mouse_up(){	mouse_status = 'up';}document.onmousedown = set_mouse_down;document.onmouseup = set_mouse_up;function paint_pixel_onclick(){	paint_pixel(this, 'true');}function paint_pixel(pixel_id, override_mouse_status){	if(mouse_status == 'down')	{		if(painting_mode == 'pen')		{			this.style.background = active_color;		}		else		{			flood_filling_color = get_hex_color(this.style.backgroundColor);			flood_fill(this.id);		}	}	if(override_mouse_status == 'true')	{		if(painting_mode == 'pen')		{			pixel_id.style.background = active_color;		}		else		{			flood_filling_color = get_hex_color(pixel_id.style.backgroundColor);			flood_fill(pixel_id.id);		}	}}/* Color handling */var active_color = '#000000';function set_active_color(){
	if(this.id.length > 10)
	{		active_color = '#' + this.id.substr(13);
	}
}function reload_promoe_paintboard(img_id){	pixels = '';	pixels = Array();	imagestring = loaded_promoes[img_id]['imagestring'];	document.getElementById('promoe_edit_button').onclick = function(){		window.location = '/annat/promoe_editor.php?edit=' + img_id;	}	document.getElementById('promoe_profile_description').innerHTML = loaded_promoes[img_id]['description'];	create_promoe_paintboard();}var pixels =  Array();function create_promoe_paintboard(){	document.getElementById('promoe_paintboard').innerHTML = '';	for(var i = 0; i < (grid_width*grid_height); i++)	{		pixels[i] = document.createElement('div');		pixels[i].id = 'pixel_' + i;		pixels[i].className = 'pixel';		if(imagestring.substr(i, 1).length == 1)		{			pixels[i].style.background = colors_id2hex[imagestring.substr(i, 1)];		}		else		{			pixels[i].style.background = '#FFFFFF';		}		pixels[i].onmouseover = paint_pixel;		pixels[i].onmousedown = paint_pixel_onclick;		document.getElementById('promoe_paintboard').appendChild(pixels[i]);	}}function flood_fill(pixel_id){	document.getElementById(pixel_id).style.background = active_color;	/* Walk upwards */	if((pixel_id.substr(6)*1) - grid_width >= 0)	{		if(get_hex_color(document.getElementById('pixel_' + (pixel_id.substr(6) - 40)).style.backgroundColor) == flood_filling_color)		{			flood_fill('pixel_' + (pixel_id.substr(6) - 40));		}	}	/* Walk to the left */	if((pixel_id.substr(6)*1)%grid_width > 0)	{		if(get_hex_color(document.getElementById('pixel_' + (pixel_id.substr(6) - 1)).style.backgroundColor) == flood_filling_color)		{			flood_fill('pixel_' + (pixel_id.substr(6) - 1));		}	}	/* Walk to the right */	if((pixel_id.substr(6)*1)%grid_width != 39)	{		if(get_hex_color(document.getElementById('pixel_' + (pixel_id.substr(6)*1 + 1)).style.backgroundColor) == flood_filling_color)		{			flood_fill('pixel_' + (pixel_id.substr(6)*1 + 1));		}	}	/* Walk down */	if((pixel_id.substr(6)*1) + grid_width < grid_width*grid_height)	{		if(get_hex_color(document.getElementById('pixel_' + (pixel_id.substr(6)*1 + grid_width)).style.backgroundColor) == flood_filling_color)		{			flood_fill('pixel_' + (pixel_id.substr(6)*1 + grid_width));		}	}}function set_grid(status){
	var pixels = getElementsByClassName(document, 'div', 'pixel');

	if(status == 'on')	{		for(i = 0; i < pixels.length; i++)
		{
			pixels[i].style.width = '10px';
			pixels[i].style.height = '10px';
			pixels[i].style.borderLeft = 'none';	
			pixels[i].style.borderBottom = 'none';	
		}	
	}	else	{
		for(i = 0; i < pixels.length; i++)
		{
			pixels[i].style.width = '9px';
			pixels[i].style.height = '9px';
			pixels[i].style.borderLeft = '1px solid #565656';	
			pixels[i].style.borderBottom = '1px solid #565656';	
		}	
	}}function get_image_string(){	var image_string = '';	var color_code = '';	for(var i = 0; i < pixels.length; i++)	{		color_code = get_hex_color(pixels[i].style.backgroundColor);		image_string += colors_hex2id[color_code];	}	return image_string;}function set_painting_mode(mode){
	alert('Setting painting mode: ' + mode);	if(mode == 'pen')	{		painting_mode = 'pen';	}	else	{		painting_mode = 'flood_fill';	}}

function promoe_init()
{
	if(document.getElementById('promoe_paintboard'))
	{
		create_promoe_paintboard();	
	}

	var color_swatches = getElementsByClassName(document, 'div', 'promoe_color');
	for(var i = 0; i < color_swatches.length; i++)
	{
		color_swatches[i].onclick = set_active_color;
	}
	
	document.getElementById('promoe_drawing_mode_pen').onclick = function()
	{
		painting_mode = 'pen';
	}
	
	document.getElementById('promoe_drawing_mode_flood_fill').onclick = function()
	{
		painting_mode = 'flood_fill';
	}

	document.getElementById('promoe_grid_control').onclick = function()
	{
		if(this.value == 'Visa rutnätet')
		{
			this.value = 'Dölj rutnätet';
			set_grid('off');
		}
		else
		{
			this.value = 'Visa rutnätet';	
			set_grid('on');
		}
	}
	
	document.getElementById('promoe_restart_button').onclick = function()
	{
		if(confirm('Vill du verkligen börja rita en ny bild? Om du inte har sparat din bild kommer den försvinna!'))
		{
			window.location = '?create';	
		}
	}
	
	document.getElementById('promoe_preview_button').onclick = function()
	{
		document.getElementById('promoe_preview').innerHTML = '<img src="/annat/promoe_png.php?imagestring=' + get_image_string() + '&rand=' + Math.random();
	}
	
	document.getElementById('promoe_save_button').onclick = function()
	{
		var img_name;
		if(img_name = prompt('Välj ett namn på din bild:'))
		{
			window.location = '?save&name=' + img_name + '&imagestring=' + get_image_string() + '&parent=' + promoe_parent;
		}
	}
	
	if(document.getElementById('promoe_hype_button'))
	{
		document.getElementById('promoe_hype_button').onclick = function()
		{
			xmlhttp_ping('/annat/promoe_hype.php?id=' + promoe_id);
			this.value = 'Tack för din hype';
			this.disabled = 'disabled';
		}	
	}

}


womAdd('promoe_init()');