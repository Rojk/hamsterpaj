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
			url: '/ajax_gateways/groups.php',
			type: 'POST',
			data: 'action=new_post&groupid=' + group_id + '&group_message=' + group_message
		});
		
		$('#form_notice').css("display", "none");
		$('#form_notice').removeClass();
		$('#form_notice').addClass('form_notice_success');
		$('#form_notice').html('Meddelandet skrickat!');
		$('#form_notice').fadeIn(500);
		$('#group_message').attr("value", "");
		
		return false;
	});
});