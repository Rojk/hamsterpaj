$(document).ready(function () {
	$('#photoblog_settings_detail_color_div').ColorPicker({
		color: '#' + $('#photoblog_settings_detail_color').val(),
		onShow: function (colpkr) {
			$(colpkr).fadeIn(250);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(250);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#photoblog_settings_detail_color_div div').css('backgroundColor', '#' + hex);
			$('#photoblog_settings_detail_color').val(hex);
			$('#test').text(hex + rgb + hsb);
		}
	});
	$('#photoblog_settings_main_color_div').ColorPicker({
		color: '#' + $('#photoblog_settings_main_color').val(),
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#photoblog_settings_main_color_div div').css('backgroundColor', '#' + hex);
			$('#photoblog_settings_main_color').val(hex);
			$('#test').text(hex + rgb + hsb);
		}
	});
	
});
womAdd();