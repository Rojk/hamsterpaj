function server_message_init()
{
	$('.server_message_collapse_header').click(function()
	{
		//var informationId = $(this).attr('id')).split('_')[4]);
		$('#server_message_collapse_information_' + ($(this).attr('id')).split('_')[4]).slideToggle('slow');
	});
}

womAdd('server_message_init()');