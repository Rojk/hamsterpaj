var bc_active_id = 0;

jQuery.fn.extend({
	// traverses through a node-list to check (and return) an element based on the selectors passed in
 	isHas: function(selector, returnVal) {
		var ret = false;
		var returnv = false;
		var chk = $(selector);
		this.each(function() {
			if ( ! ret ) {
				var el = this;
					do {
					 	if ( chk.index(el) !== -1 ) {
					 		ret = true;
					 		returnv = el;
					 	}
					} while ( el = el.parentNode );
			}
		});
		return (returnVal) ? returnv : ret;
	},
	
	business_card: function() {
		// create the HTML
	 	var html = '<div id="ui_business_card">' +
	 					'<a id="ui_business_card_close" href="#">x</a>' +
		 				'<a class="ui_business_card_profile_link" href=""><img class="ui_avatar" id="ui_business_card_avatar" src="" alt="" /></a>' +
		 				'<h1><img id="ui_business_card_online" src="" /> <a href="" class="ui_business_card_profile_link" id="ui_business_card_username"></a></h1>' +
		 				'<div id="ui_business_card_about">' +
		 					'<p id="ui_business_card_userinfo"></p>' +
		 					'<p id="ui_business_card_status"></p>' +
		 					'<p id="ui_business_card_flags"></p>' +
		 				'</div>' +
		 				'<div id="ui_business_card_guestbook">' +
		 					'<form action="/ajax_gateways/guestbook.json.php" method="post">' +
		 						'<textarea rows="3"></textarea>' +
		 						'<input type="submit" value="Skicka g&auml;stboksinl&auml;gg" />' +
		 					'</form>' +
		 				'</div>' +
		 			'</div>';
		
		// save the elements for later use (saves us a bit of time)
		var card = $(html).hide().appendTo(document.body);
		var close = $('#ui_business_card_close');
		
		var avatar = $('#ui_business_card_avatar');
		var profile_links = $('.ui_business_card_profile_link');
		var userinfo = $('#ui_business_card_userinfo');
		var status = $('#ui_business_card_status');
		var flags = $('#ui_business_card_flags');
		var username = $('#ui_business_card_username');
		var online = $('#ui_business_card_online');
			
		// close
		close.click(function() {
		 	card.fadeOut();
			return false;
		});
		
		// dcocument.click
		this.click(function(e) {
			var target = $(e.target);
			if ( target = target.isHas('.ui_business_card', true) ) {
				// we caught it!
				target = $(target);
				var href = target.attr('href');
				var user_id = 65654;
				href.replace(/id=([0-9]+)/, function() {
					user_id = arguments[1];
				});
				var w = $(window);			
				$.getJSON('/ajax_gateways/business_card.json.php?user_id=' + user_id, function(data) {
					bc_active_id = data.user_id;
			
					profile_links.attr('href', '/traffa/profile.php?user_id=' + data.user_id);
					avatar.attr('src', 'http://images.hamsterpaj.net/images/users/thumb/' + data.user_id + '.jpg');
					userinfo.html(data.user_info);
					status.html(data.status);
					flags.html(bc_make_flags(data.flags));
					username.html(data.username);
					
					// online
					if ( data.online == true ) {
						 online.attr('src', 'http://images.hamsterpaj.net/famfamfam_icons/status_online.png');
						 online.attr('title', 'Online');
					// offline
					} else {
						online.attr('src', 'http://images.hamsterpaj.net/famfamfam_icons/status_offline.png');
						online.attr('title', 'Offline');
					}
					
					card.css({
						'left': ($('#ui_wrapper').width() / 2) - (card.width() / 2),
						'top': w.scrollTop() + (w.height() / 2) - (card.height() / 2)
					}).fadeIn();
				});
				
				// close
				var d = $(document);
				d.bind('click.cardclose', function(ev) {
				 	var targ = $(ev.target);
				 	if ( ! targ.isHas('.ui_business_card, #ui_business_card') ) {
						d.unbind('click.cardclose');
						card.fadeOut();
					}
				});
				
				return false;
			}
		});
		
		// guestbook_form
		var txtarea = $('#ui_business_card_guestbook textarea');
		var submitbtn = $('#ui_business_card_guestbook input[type=submit]');
		var form = $('#ui_business_card_guestbook form').submit(function(e) {
			submitbtn.val('Sparar...').attr('disabled', 'true');
			
			var data = {
				action: 'insert',
				recipient: bc_active_id,
		//		message: encodeURIComponent(txtarea.val()),
				message: txtarea.val(),
				is_private: false
			};
		//	console.dir(data);
		//	console.log($.param(data));
			$.post('/ajax_gateways/guestbook.json.php', data, function() {
				submitbtn.val('Sparat');
				card.fadeOut();
				setTimeout(function() {
					txtarea.val('');
					submitbtn.val('Skicka gästboksinlägg').attr('disabled', false);
				}, 5000);
			});
			return false;
		});
		
		// avatar
		avatar.click(function() {
			window.open('/avatar.php?id=' + bc_active_id, 'avatar_window' + bc_active_id, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=410, height=600')
			return false;
		});
	}
});

function bc_make_flags(flags) {
	var html = '';
	$.each(flags, function(i, item) {
		html += '<img src="' + item + '" alt="' + i + '" title="' + i + '" />';
	});
	return html;
}

$(document).ready(function() {
	$(document).business_card();
});