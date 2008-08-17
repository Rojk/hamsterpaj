function api_documentation_init()
{
	$('#source_code_header').click(function()
	{
		$('#source_code').slideToggle('slow');
	});
}

womAdd('api_documentation_init()');