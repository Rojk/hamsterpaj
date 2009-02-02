$(document).ready(function(){
	$('#radio_dj_add_submit').click(function() {
		$('#form_notice').fadeOut('500');
		$('#form_notice').empty();
		var name = $('#radio_dj_add_name').val();
		var information = $('#radio_dj_add_information').val();
		$.ajax({
			url: '/ajax_gateways/radio.php?action=dj_add',
			type: 'POST',
			data: 'radio_dj_add_name=' + name + '&radio_dj_add_information=' + information,
			success: function(result) {
				$('#form_notice').append(result);
				$('#form_notice').fadeIn('500');
			}
		});
		
		return false;
	});
});