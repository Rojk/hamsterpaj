$(document).ready(function(){
	$('#group_message_submit').click(function() {
		var group_message = $('#group_message').val();
		var group_id = $(this).parent().attr('id'); // The forms ID
		
		if(group_message == ''){
			$('#form_notice').css("display", "none");
			$('#form_notice').removeClass();
			$('#form_notice').toggleClass('form_notice_error');
			$('#form_notice').text('Du måste skriva in ett meddelande, dah?');
			$('#form_notice').fadeIn(500);
			return false;
		}
		
		$.ajax({
			url: '/ajax_gateways/groups.php?action=new_post',
			type: 'POST',
			data: 'groupid=' + group_id + '&group_message=' + group_message,
			success: function(result) {
				$('#posted_messages').prepend(result);
				$('#group_message').attr("value", "");
				$('#form_notice').css("display", "none");
				$('#form_notice').removeClass();
				$('#form_notice').toggleClass('form_notice_success');
				$('#form_notice').text('Meddelandet skickat!');
				$('#form_notice').fadeIn(500);
				$('#group_message').focus();
			}
		});
		return false;
	});
	
	updateScribble();
});

	function updateScribble() {
		var groupid = $('.group_header').attr('id');
		$.ajax({
			url: '/ajax_gateways/groups.php?action=fetch_new_posts',
			type: 'POST',
			data: 'groupid=' + groupid,
			success: function(result) {
				$('#posted_messages').prepend(result);
			}
		});
		setTimeout('updateScribble()', 6000);
	}