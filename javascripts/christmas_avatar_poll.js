$(document).ready(function()
{
	$('form.christmas_avatar_poll').submit(function()
	{
		this_form = $(this);
		var ajax_href = this_form.attr('action');
		ajax_href = ajax_href.substr(15);
		var inputs = $(this).find(':radio');
		var inputs_array = jQuery.makeArray(inputs);

		inputs_array = jQuery.map(inputs_array, function (a) { return $(a).attr('checked'); });

		if(jQuery.inArray(true, inputs_array) == -1)
		{
			alert('Du har inte kryssat i någon bild!');
		}
		else
		{
			this_form.slideUp('normal', function()
			{
				this_form.html('<img src="http://images.hamsterpaj.net/loading_icons/ajax-loader3.gif" alt="Laddar..." style="margin: 0 330px;" />');
				this_form.slideDown('fast', function()
				{
					//user has checked an image
					$.post(ajax_href, inputs.serialize(), function(msg)
					{
						this_form.slideUp('fast', function()
						{
							this_form.html(msg).slideDown('normal');
						});
					});
				});
			});
			
		}
		return false;
	});
	
	$('.bar').addClass('zero');
	$('.box').click(function(){
		var myWidth = $('.bar', this).attr('title') + '%';
		$('.bar', this).animate({width: myWidth}, 2000);
		$('.bar', this).html(myWidth + '&nbsp;'); // adds some padding after the text, but doesn't effect the width;
	});
	$('.box').click();

});