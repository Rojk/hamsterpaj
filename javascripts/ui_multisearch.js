$(document).ready(function(){
	var ui_multisearch_default_text = $('#ui_multisearch').val();
	$('#ui_multisearch').focus(function (){
		if ($('#ui_multisearch').val() == ui_multisearch_default_text)
		{
			$('#ui_multisearch').val('');
		}
	});
	$('#ui_multisearch').blur(function (){
		if ($('#ui_multisearch').val() == '')
		{
			$('#ui_multisearch').val(ui_multisearch_default_text);
		}
	});
});
