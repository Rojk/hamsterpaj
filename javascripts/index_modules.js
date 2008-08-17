function index_modules()
{
	var modules = getElementsByClassName(document, 'h1', 'index_module_title');
	for(var i = 0; i < modules.length; i++)
	{
		modules[i].onclick = function()
		{
			if(this.parentNode.className == 'index_module_open')
			{
				this.parentNode.className = 'index_module_closed';
			}
			else
			{
				this.parentNode.className = 'index_module_open';
			}
		}
	}
}

womAdd('index_modules()');