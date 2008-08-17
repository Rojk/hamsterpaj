// If hp, "The Hamsterpaj Namespace", wasn't defined before then define it!
if(typeof(hp) == 'undefined'){ var hp=new Object(); }

hp.notices = {
	infobubble: {
		bubble_count: 0,
		draw: function(params)
		{
			var e_output = document.createElement("div");

			e_output.style.background = "#ffffff";
			e_output.style.color = "#000000";
			e_output.style.border = "1px solid #000000";
			e_output.style.padding = "15px";

			e_output.style.position = "absolute";
			e_output.style.left = hp.mouse.x+"px";
			e_output.style.top = hp.mouse.y+"px";

			e_output.innerHTML = params.msg;
			e_output.id = "js_hp_notice_infobubble_"+this.bubble_count;
			document.body.appendChild(e_output);
			setTimeout("hp.notices.infobubble.hide("+this.bubble_count+")", params.duration * 1000);
			this.follow_mouse(this.bubble_count);
			this.bubble_count++;
		},
		hide: function(id)
		{
			document.getElementById("js_hp_notice_infobubble_"+id).style.display="none";
		},
		follow_mouse: function(id)
		{
			var bubble=document.getElementById("js_hp_notice_infobubble_"+id);
			bubble.style.left = hp.mouse.x+"px";
			bubble.style.top = hp.mouse.y+"px";

			if(bubble.style.display!="none")
			{
				setTimeout("hp.notices.infobubble.follow_mouse("+id+")", 1);
			}
		}
	}
}