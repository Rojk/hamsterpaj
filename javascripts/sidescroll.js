	function scrollscript(direction)
	{
		scrolls = Array(79, 256, 128, 64, 32, 16, 8, 4, 2);
		if(direction == 'left')
		{
			for(var i = 0; i < scrolls.length; i++)
			{
				setTimeout("document.getElementById('slide_container').scrollLeft -= " + scrolls[i], 50*i);
			}
		}
		else
		{
			for(var i = 0; i < scrolls.length; i++)
			{
				setTimeout("document.getElementById('slide_container').scrollLeft += " + scrolls[i], 50*i);
			}
		}
	}