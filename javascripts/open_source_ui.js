function open_source_init()
{
	$('#demo_header').click(function()
	{
			$('#demo').slideToggle('slow');
	});
	
	$('#attributes_header').click(function()
	{
			$('#attributes').slideToggle('slow');
	});
	
	$('#source_code_header').click(function()
	{
			$('#source_code').slideToggle('slow');
	});
}

womAdd('open_source_init()');