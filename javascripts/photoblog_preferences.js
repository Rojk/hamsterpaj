$(document).ready(function () {
	var photoblog_preferences_color_detail = $('#photoblog_preferences_color_detail').val();
	var photoblog_preferences_color_main = $('#photoblog_preferences_color_main').val();
	
	// DEBUG $('div#test').text(photoblog_preferences_color_detail + ' - ' + photoblog_preferences_color_main);
	
	$('#photoblog_preferences_color_detail_div div').css({'background-color' : '#' + photoblog_preferences_color_detail});
	$('#photoblog_preferences_color_main_div div').css({'background-color' : '#' + photoblog_preferences_color_main});
	
	$('#photoblog_preferences_color_detail_div').ColorPicker({
		color: photoblog_preferences_color_detail,
		onShow: function (colpkr) {
			$(colpkr).fadeIn(250);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(250);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#photoblog_preferences_color_detail_div div').css('background-color', '#' + hex);
			$('#photoblog_preferences_color_detail').val(hex);
		}
	});
	
	$('#photoblog_preferences_color_main_div').ColorPicker({
		color: photoblog_preferences_color_main,
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#photoblog_preferences_color_main_div div').css('background-color', '#' + hex);
			$('#photoblog_preferences_color_main').val(hex);
		}
	});
	
});
womAdd();