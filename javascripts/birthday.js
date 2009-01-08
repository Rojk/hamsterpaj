// If hp, "The Hamsterpaj Namespace", wasn't defined before then define it!
if(typeof(hp) == "undefined"){ var hp=new Object(); }

hp.birthday = {
	
	compose_gb: function(userid, button){
		button.style.display = 'none';
		
		var textbox = document.getElementById('birthday_compose_gb_' + userid);
		var statusbox = document.getElementById('birthday_compose_gb_' + userid + '_ajax_status');
		var loader = hp.give_me_an_AJAX();
		
		//loader.open('POST', '/traffa/gb-reply.php?action=send_reply&userid=' + userid, true);
		loader.open('POST', '/ajax_gateways/my_visitors_send_gb.php', true);
		loader.onreadystatechange = function(){
			if(loader.readyState == 4 && loader.status == 200){
				statusbox.innerHTML = loader.responseText;
			}
		}
		loader.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8');
		loader.send('recipient=' + userid + '&message=' + encodeURIComponent(textbox.value));
		
		textbox.style.display = 'none';
		statusbox.innerHTML = 'Skickar...';
	}
}

//womAdd("hp.birthdayinit()");