function enable_suggestions_category_dropdown()
{
	if(document.getElementById('category_select'))
	{
		document.getElementById('category_select').onchange = function()
		{
			var hide_divs = getElementsByClassName(document, 'div', 'suggestion_help_text');
			for(var i = 0; i < hide_divs.length; i++)
			{
				hide_divs[i].style.display = 'none';
			}
			
			if(document.getElementById('suggestion_' + this.value + '_help_text'))
			{
				document.getElementById('suggestion_' + this.value + '_help_text').style.display = 'block';
			}
		}
	}
}

womAdd('enable_suggestions_category_dropdown()');