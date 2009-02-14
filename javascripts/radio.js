$(document).ready(function(){
	// Add DJ
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
				$('#radio_dj_add_name').attr("value", "");
				$('#radio_dj_add_information').attr("value", "");
			}
		});
		
		return false;
	});
	// Remove DJ
	$('.dj_remove').click(function() {
		var id = $(this).parent().parent().attr('id');
		$.ajax({
			url: '/ajax_gateways/radio.php?action=dj_remove',
			type: 'GET',
			data: 'id=' + id,
			success: function(result) {
				$('#' + id).slideUp('500');
				$('#' + id).before(result);
				
			}
		});
		
		return false;
	});
	// Add program
	$('#radio_program_add_submit').click(function() {
		$('#form_notice').fadeOut('500');
		$('#form_notice').empty();
		var name = $('#radio_program_add_name').val();
		var dj = $('#radio_program_add_dj').val();
		var sendtime = $('#radio_program_add_sendtime').val();
		var information = $('#radio_program_add_information').val();
		$.ajax({
			url: '/ajax_gateways/radio.php?action=program_add',
			type: 'POST',
			data: 'name=' + name + '&dj=' + dj + '&sendtime=' + sendtime + '&information=' + information,
			success: function(result) {
				$('#form_notice').append(result);
				$('#form_notice').fadeIn('500');
				$('#radio_program_add_name').attr("value", "");
				$('#radio_program_add_information').attr("value", "");
				$('#radio_program_add_sendtime').attr("value", "");
			}
		});
		
		return false;
	});
	// Remove Program
	$('.program_remove').click(function() {
		var id = $(this).parent().parent().attr('id');
		$.ajax({
			url: '/ajax_gateways/radio.php?action=program_remove',
			type: 'GET',
			data: 'id=' + id,
			success: function(result) {
				$('#' + id).slideUp('500');
				$('#radio_menu').after(result);
			}
		});
		return false;
	});
	
	// Add schedule
	$('#radio_schedule_add_submit').click(function() {
		$('#form_notice').fadeOut('500');
		$('#form_notice').empty();
		var program = $('#radio_schedule_add_program').val();
		var starttime = $('#radio_schedule_add_starttime').val();
		var endtime = $('#radio_schedule_add_endtime').val();
		$.ajax({
			url: '/ajax_gateways/radio.php?action=schedule_add',
			type: 'POST',
			data: 'program=' + program + '&starttime=' + starttime + '&endtime=' + endtime,
			success: function(result) {
				$('#form_notice').append(result);
				$('#form_notice').fadeIn('500');
				$('#radio_schedule_add_program').attr("value", "");
			}
		});
		return false;
	});
	
	// Remove schedule
	$('.schedule_remove').click(function() {
		var id = $(this).parent().parent().attr('id');
		$.ajax({
			url: '/ajax_gateways/radio.php?action=schedule_remove',
			type: 'GET',
			data: 'id=' + id,
			success: function(result) {
				$('#' + id).slideUp('500');
				$('#radio_menu').after(result);
			}
		});
		return false;
	});
});