$(document).ready(function () {
	var photoblog_settings_color_detail = $('#photoblog_settings_color_detail').val();
	var photoblog_settings_color_main = $('#photoblog_settings_color_main').val();
	
	// DEBUG $('div#test').text(photoblog_settings_color_detail + ' - ' + photoblog_settings_color_main);
	
	$('#photoblog_settings_color_detail_div div').css({'background-color' : '#' + photoblog_settings_color_detail});
	$('#photoblog_settings_color_main_div div').css({'background-color' : '#' + photoblog_settings_color_main});
	
	$('#photoblog_settings_color_detail_div').ColorPicker({
		color: photoblog_settings_color_detail,
		onShow: function (colpkr) {
			$(colpkr).fadeIn(250);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(250);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#photoblog_settings_color_detail_div div').css('background-color', '#' + hex);
			$('#photoblog_settings_color_detail').val(hex);
		}
	});
	
	$('#photoblog_settings_color_main_div').ColorPicker({
		color: photoblog_settings_color_main,
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#photoblog_settings_color_main_div div').css('background-color', '#' + hex);
			$('#photoblog_settings_color_main').val(hex);
		}
	});
	
});
womAdd();