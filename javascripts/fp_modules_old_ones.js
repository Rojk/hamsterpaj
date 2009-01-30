$(function() {
	$('.module_header').click(
		function() {
			var module_id = $(this).attr('id');
			if ($('#module_' + module_id + ' .module_content').is(':hidden'))
			{
				$('.module_toggle_guide').text('(Klicka för att visa modulen)');
				$('#module_' + module_id + ' .module_toggle_guide').text('(Klicka för att dölja modulen)');
				$('.module_content').slideUp('slow');
				$('#module_' + module_id + ' .module_content').slideDown('normal');
			}
			else
			{
				$('.module_toggle_guide').text('(Klicka för att visa modulen)');
				$('.module_content').slideUp('slow');
			}
		}
	);
});