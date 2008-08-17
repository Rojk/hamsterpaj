			function rojk_love_trigger(e)
			{
				var posX = e.pageX;
				var posY = e.pageY;
				//alert('Pow! ' + posX + ' ' + posY);
				var love_hole = document.createElement('img');
				love_hole.src = 'http://images.hamsterpaj.net/rojk/heart.gif';
				document.getElementById('love_div').appendChild(love_hole);
				love_hole.style.display = 'block';
				love_hole.style.position = 'absolute';
				
				love_hole.style.top = posY + 'px';
				love_hole.style.left = posX + 'px';
			}
			
			function rojk_shot()
			{
				alert('Love you!');
				window.location = 'http://www.hamsterpaj.net/traffa/photos.php?id=33090#photo';
			}
			
			function rojk_love()
			{
				alert('Send some loving!');
				document.body.style.cursor = 'url("http://images.hamsterpaj.net/steve/sniper.png"), crosshair';
				var overlay_div = document.createElement('div');
				overlay_div.id = 'love_div';
				overlay_div.style.position = 'absolute';
				overlay_div.style.width = '100%';
				overlay_div.style.height  = '5000px';
				overlay_div.style.margin = '-10px';
				overlay_div.onclick = rojk_love_trigger;
				document.body.appendChild(overlay_div);
				document.getElementById('rojk').style.display = 'none';
				document.getElementById('rojk_love').style.display = 'none';
				
				
				overlay_div.innerHTML = '<DIV ID="flying_rojk" STYLE="position:absolute; left: -500px; width:47; height:68;"><IMG SRC="http://images.hamsterpaj.net/rojk/rojk.gif" BORDER=0 onclick="rojk_shot();"></DIV>';
				
				flying_rojk = new Chip("flying_rojk",47,68);
				movechip("flying_rojk");
			}
			
function rojk_activate()
{
	if(document.getElementById('rojk'))
	{
	 	document.getElementById('rojk').onmousedown = rojk_mousedown;
	 	document.getElementById('rojk').onmouseup = rojk_mouseup;
	}
	if(document.getElementById('rojk_love'))
	{
	 	document.getElementById('rojk_love').onclick = rojk_love;		
	}
}

var rojk_long_click = false;
var rojk_mouse = false;

function rojk_mousedown()
{
	rojk_long_click = false;
	rojk_mouse = true;

}


function rojk_mouseup()
{
	rojk_mouse = false;
	if(rojk_long_click == false)
	{
		var rojk_comments = Array();

		rojk_comments[0] = 'Vill du se på soluppgången med mig?';
		rojk_comments[1] = 'Vet du om att Rojk spelar bas i kyrkan? Naken!';
		rojk_comments[2] = 'Come and get me! Im singel :-)';
		rojk_comments[3] = 'Jag er kristen och kåt, vad väntar du på?';

		var quote = Math.round(Math.random()*(rojk_comments.length-1));

		alert(rojk_comments[quote]);
	}
}

womAdd('rojk_activate()');