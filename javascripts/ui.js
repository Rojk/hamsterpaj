// If hp, "The Hamsterpaj Namespace", wasn't defined before then define it!
if(typeof(hp) == 'undefined'){ var hp=new Object(); }

hp.ui = {
	init: function()
	{
		this.noticebar.init();
		this.user_search.init();
		this.hackerlund.init();
		this.grotescopaj.init();
		this.statusbar.init();
		this.full_page_notice.init();
		this.flash_menu_fix();
		this.fix_ie6_menu();
		this.friends_notices_remove_all_from_user();
		this.avatar.init();
	},
	
	full_page_notice: {	
		init: function()
		{
			$('#ui_full_page_notice_close').click(function()
			{
				$('#ui_full_page_notice').slideUp();
				$.cookie($('#ui_full_page_notice').attr('class'), 'closed', { expires: 30 });
			});
		}
	},
	
	user_search: {
		init: function()
		{
			try
			{
				var user_search_box = document.getElementById('quicksearch_input');
				
				user_search_box.onfocus = function()
				{
				 if(this.value == this.defaultValue)
				 {
				  this.style.color = '#000000';
				  this.value = '';
				 }
				}
				
				user_search_box.onblur = function()
				{
				 if(this.value == '')
				 {
				  this.style.color = '#757575';
				  this.value = this.defaultValue;
				 }
				}
			} catch(E){}
		}
	},
	
	hackerlund:
	{
		doubleclick_counter: 0,
		size: 200,
		
		init: function()
		{
			try
			{
				document.getElementById('user_info').ondblclick = function()
				{
					hp.ui.hackerlund.start();
				}
			} catch(E){}
		},
		
		start: function()
		{
			if(this.doubleclick_counter++ == 1)
			{
				var all_images = document.getElementsByTagName('IMG');
				for(var this_image = 0; this_image < all_images.length; this_image++)
				{
					all_images[this_image].style.position = 'absolute';
					all_images[this_image].style.zIndex = 9000 + this_image;
				}
				
				// setInterval is a bad idea...
				this.update(0);
			}
		},
		
		update: function(step)
		{
			/*if(!confirm('Hej'))
			{
				alert('Die');
				return;
			}*/
			var all_images = document.getElementsByTagName('IMG');
			(Math.cos(Math.PI / 8) * this.size)
			for(var this_image = 0; this_image < all_images.length; this_image++)
			{
				//all_images[this_image].style.top = Math.floor(Math.random() * (600 - all_images[this_image].offsetTop)) + 'px';
				//all_images[this_image].style.left = Math.floor(Math.random() * (600 - all_images[this_image].offsetLeft)) + 'px';
				all_images[this_image].style.left = Math.floor(hp.mouse.x + Math.cos((this_image + step) / all_images.length * 2 * Math.PI) * this.size) + 'px';
				all_images[this_image].style.top = Math.floor(hp.mouse.y + Math.sin((this_image + step) / all_images.length * 2 * Math.PI) * this.size) + 'px';
			}
			
			step++;
			setTimeout('hp.ui.hackerlund.update(' + step + ')', 1);
		}
	},
	
	grotescopaj:
	{
		init: function()
		{
			try
			{
				document.getElementById('site_top_rounded_corners').onclick = function()
				{
					alert('Ååååå, ETT KILO MJÖÖL!!!');
					//setInterval('hp.ui.grotescopaj.draw_more_powder()', 50);
				}
			}
			catch(E){  }
		},
		
		draw_more_powder: function()
		{
			document.getElementById('quicksearch').innerHTML += '<img src="http://images.hamsterpaj.net/floor_mjoel.png" style="position: absolute; left: ' + hp.mouse.x + 'px; top: ' + hp.mouse.y + 'px" />';
		}
	},
	
	noticebar: {
		init: function()
		{
			try
			{
				hp.synchronize.add({
					handle: 'ui_noticebar_guestbook',
					on_response: function(){
						$('#ui_noticebar_guestbook').html((this.json_data > 0) ? ((this.json_data == 1) ? 'Ett nytt' : this.json_data + ' nya') : 'Gästbok');
						$('#ui_noticebar_guestbook_active').html((this.json_data > 0) ? ((this.json_data == 1) ? 'Ett nytt' : this.json_data + ' nya') : 'Gästbok');
					}
				});
				
				hp.synchronize.add({
					handle: 'ui_noticebar_discussion_forum',
					on_response: function(){
						$('#ui_noticebar_forum').html((this.json_data['new_notices'] > 0) ? ((this.json_data['new_notices'] == 1) ? 'Ny notis' : this.json_data['new_notices'] + ' nya') : 'Forum');
						$('#ui_noticebar_forum_active').html((this.json_data['new_notices'] > 0) ? ((this.json_data['new_notices'] == 1) ? 'Ny notis' : this.json_data['new_notices'] + ' nya') : 'Forum');
						$('#ui_noticebar_forum_container ul li:not(:first)').remove();
						for(var subscription = 0; subscription < this.json_data['subscriptions'].length; subscription++)
						{
							var item = this.json_data['subscriptions'][subscription];
							$('#ui_noticebar_forum_container ul li:last').after('<li><a href="' + item['url'] + '">' + item['title'] + ' (<strong>' + item['unread_posts'] + ' nya</strong>)</a></li>');
						}
					}
				});
				
				hp.synchronize.add({
					handle: 'ui_noticebar_groups',
					on_response: function(){
						$('#ui_noticebar_groups').html((this.json_data['unread_notices'] > 0) ? ((this.json_data['unread_notices'] == 1) ? 'Ett nytt' : this.json_data['unread_notices'] + ' nya') : 'Grupper');
						$('#ui_noticebar_groups_active').html((this.json_data['unread_notices'] > 0) ? ((this.json_data['unread_notices'] == 1) ? 'Ett nytt' : this.json_data['unread_notices'] + ' nya') : 'Grupper');
						$('#ui_noticebar_groups_container ul li:not(:first)').remove();
						for(var group = 0; group < this.json_data['groups'].length; group++)
						{
							var item = this.json_data['groups'][group];
							$('#ui_noticebar_groups_container ul li:last').after('<li><a href="/traffa/groups.php?action=goto&groupid=' + item['group_id'] + '">' + ((item['unread_messages'] > 0) ? '<strong>' : '') + item['title'] + ' (' + item['unread_messages'] + ' nya)' + ((item['unread_messages'] > 0) ? '</strong>' : '') + '</a></li>');
						}
					}
				});
			}catch(E){  }
		}
	},
	
	statusbar: {
		init: function() {
			$('#ui_statusbar_forumstatus').click(function() {
				var t = $(this);
				
				if ( $('span', this).length ) {
					var span = $('span', this);
					var inpt = $('<input type="text"/>');
					inpt.css('width', 140);
					inpt.attr('value', $('span', this).attr('title'));
					while ( this.firstChild ) this.removeChild(this.firstChild);
					inpt.appendTo(t);
					inpt.focus();
					inpt.keydown(function(e) {
						if ( e.keyCode == 13 ) {
							hp.ui.statusbar.hideAndSend(inpt.val());
						}
					});
					$(document).bind('click.statusbar', function(e) {
						if ( inpt.get(0) == e.target || e.target == span.get(0)) return;
						hp.ui.statusbar.hideAndSend(inpt.val());
						$(document).unbind('click.statusbar');
					});
				}
			});
		},
		
		hideAndSend: function(value) {
			$.get('/ajax_gateways/set_user_status.php?status=' + encodeURIComponent(value));
			$('#ui_statusbar_forumstatus').html('<span title="' + value + '">' + (value.length > 22 ? value.substring(0, 22) + '...' : value) + '</span>');
		}
	},
	
	flash_menu_fix: function()
	{
		$('#ui_menu > ul > li').hover(function() {
			$('#ui_content object').css('visibility', 'hidden');
		}, function() {
			$('#ui_content object').css('visibility', 'visible');
		});
	},
	
	fix_ie6_menu: function()
	{
		if ( jQuery.browser.version == '6.0' && jQuery.browser.msie )
		{
			$('#ui_menu > ul > li').hover(function() {
				$(this).addClass('hover');
			}, function() {
				$(this).removeClass('hover');
			});
		}
	},
	
	friends_notices_remove_all_from_user: function()
	{
		$('.friends_notices_remove_all_from_user').click(function() {
			var friend_id = $(this).attr("id");
			$.ajax({
				url: '/ajax_gateways/friends_notices_remove_all_from_user.php',
				type: 'GET',
				data: 'friend_id=' + friend_id
			});
			$(this).parent().slideUp('500');
			return false;
		});
	},
	
	avatar:
	{
		init: function()
	  {
	    var lock = false;
	    $(document).click(function(e) {
		var avatar = $(e.target);
		if ( ! $(e.target).hasClass('user_avatar') ) {
			avatar = avatar.parents('.user_avatar');
			if (! avatar.length) return;
		}
		
	    	if (!lock) {
	    		lock = true;
		    	var avatar_id = $(avatar).attr('id');
		    	var user_id = avatar_id.substring(7);
		    	if (user_id != 'no_avatar') {
			    	var original = $('#' + avatar_id).offset();
			      var avatar_original = original;
			      if(!$("#avatar_wnd").text())
			      {
			        var content = '<div id="avatar_wnd"><img src="http://images.hamsterpaj.net/images/users/full/' + user_id + '.jpg" /><div></div></div>';
			        $(content).appendTo("#ui_content");
			        $("#avatar_wnd").css({"opacity" : "0.1", "left" : original.left + "px", "top" : original.top + "px", "width" : $("#" + avatar_id).width() + "px", "height" : $("#" + avatar_id).height() + "px"});
			        var ajax_request = '/ajax_gateways/forum_signature.php?id=' + user_id;
			        $.get(ajax_request, function(data){
			          $("#avatar_wnd div").append(data);
			        });
			        $("#avatar_wnd div").hide();
			        $("#avatar_wnd").animate({"opacity" : "1.0", "left" : "250px", "top" : $(document).scrollTop() + 50 + "px", "width" : "320px", "height" : "427px"}, "slow", function() {
			          $("#avatar_wnd div").slideDown("normal");
			        });
			        // Close avatar popup
			        $("#avatar_wnd").click(function() {
								$("#avatar_wnd").animate({"opacity" : "0.0", "left" : original.left + "px", "top" : original.top + "px", "width" : $("#" + avatar_id).width() + "px", "height" : $("#" + avatar_id).height() + "px"}, "normal", function() {
									$("#avatar_wnd").remove();
									lock = false;
								});
							});
			      }
			} else {
				lock = false;
			}
		}
	});//<---- ); = TADA!
  	},
  	
  	enable_new: function()
  	{
  		//this.init();
  	}
	}
}

womAdd('hp.ui.init()');

