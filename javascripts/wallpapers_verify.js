$(document).ready(function(){
	$('.wallpaper_boxes').hide();
	
	function ajax_loading(id)
	{
		if($('#ajax_icon_'+id).css('display') == 'none')
			$('#ajax_icon_'+id).show();
		else
			$('#ajax_icon_'+id).hide();
	}

	$('.box_link').click(
	function()
	{
		var id = $(this).attr('id').substr(5);

		//alert($('#box_' + id).css('display'));

		var id = toggle_boxes($(this));
		ajax_loading(id);
		if($('#box_'+id).css('display') != 'none')
		{
			$.get('/ajax_gateways/wallpapers_verify.php', {'action': 'get_info', 'id': id}, 
				function(data)
				{
					ajax_loading(id);
					$("form").bind("submit", function() { return false; })
					$('#box_'+id).html(data);
					$('#box_'+id+' form').submit(
						function()
						{
							var holder = $('input[name="approved"]');
							var approved = jQuery.makeArray(holder);
							for(i=0;i<approved.length;i++)
							{
								approved[i] = approved[i].checked;
							}
							
							if(approved != 'false,false,false')
							{
							var proceed = true;
								if(approved == 'false,true,false' || approved == 'false,false,true')
								{
									if($('#verify_comment_'+id).val() == '' || $('#verify_comment_'+id).val() == 'Skriv en kommentar här')
									{
										proceed = false;
										alert('Eftersom du nekat bilden och/eller blockat användaren MÅSTE du skriva en anledning.\n\nAnnars kommer dem bara att whina om maktmissbruk... :P');
									}
									else
									{
										proceed = true;
									}
								}

								if(proceed)
								{
									ajax_loading(id);
									$.ajax({
										url: '/ajax_gateways/wallpapers_verify.php?action=validate&id='+id,
										type: 'post',
										data: $('#box_'+id+' form').serialize(),
										complete: function(msg)
										{
											ajax_loading(id);
											alert(msg.responseText);
											$('li#li_'+id).slideUp('fast');								
										}
									});
								}
							}
							else
							{
								alert('Du måste ange om bilden nekades eller inte!');
							}
						return false;
						}
					);
				}
			);
		}
		else
		{
			$('h3#head_'+id+' span').fadeIn('fast');
		}
	});
	
	function toggle_boxes(obj)
	{
		var id = obj.attr('id').substr(5);

		if($('#box_' + id).css('display') == 'none')
		{
			$('#box_' + id).slideDown('fast', 
				function()
				{
					$('#image_' + id).attr('src', 'http://images.hamsterpaj.net/minus.gif');
					$('h3#head_'+id+' span').hide();
					$('h3#head_'+id).css('text-align', 'center');
				}
			);
		}
		else
		{
			$('#box_' + id).slideUp('fast',
				function()
				{
					$('#image_' + id).attr('src', 'http://images.hamsterpaj.net/plus.gif');
					$('h3#head_'+id+' span').show();
					$('h3#head_'+id).css('text-align', 'left');
				});
		}
		return id;
	}

});