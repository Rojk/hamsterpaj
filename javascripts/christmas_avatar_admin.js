function add_link()
{
   var num = document.getElementById('number_of_contenders');
   var the_div = document.getElementById('contenders');
   var num_next = (num.value - 1)+2;
   
	  num.value = num_next;
	  var divIdName = "contender_"+num.value;
	  var newdiv = document.createElement('li');
	  newdiv.setAttribute("id",divIdName);
	  newdiv.innerHTML = '<input type="text" tabindex="'+(num_next+1)+'" id="contender_input_'+num_next+'" name="contenders[]" /><img src="http://links.guida.nu/img/add.png" onclick="add_link();" alt="Add link" width="16" class="link_images" height="16" /><img src="http://links.guida.nu/img/delete.png" class="link_images" onclick="remove_link(\''+divIdName+'\')\" alt="Remove" width="16" height="16" />';
	  the_div.appendChild(newdiv);
}
 
function remove_link(divNum)
{
 
   var the_div = document.getElementById('contenders');
   var num = document.getElementById('number_of_contenders');
   var num_last = num.value - 1;
   num.value = num_last;
   var olddiv = document.getElementById(divNum);
   if(olddiv.innerHTML.match('<img src="http://links.guida.nu/img/delete.png"'))
   {
   	the_div.removeChild(olddiv);
   }
}

$(document).ready(function()
{
	$('#form_error, #form_result, #show_form_again').hide();
	$('form#christmas_avatar_form').submit(function()
	{
		var this_form = $(this);
		$('#form_error, #form_result, #show_form_again').hide();
		
		var ajax_href = $(this).attr('action');
		ajax_href = ajax_href.substr(15);
		
		if($('#poll_title').val() == '')
		{
			$('#form_error').html('Du har inte fyllt i <strong>omröstningens titel</strong>!').fadeIn('normal');
		}
		else
		{
			if($('#contender_input_0').val() == '')
			{
				$('#form_error').html('Du har inte fyllt i <strong>några deltagare</strong>!').fadeIn('normal');
			}
			else
			{
				$(this).slideUp('normal', function()
				{
					$('#form_result').html('<img src="http://images.hamsterpaj.net/loading_icons/ajax-loader3.gif" alt="Laddar..." style="margin: 0 280px;" />').slideDown('normal', function()
					{
						$.post(ajax_href, this_form.serialize(), function(msg)
						{
							$('#form_result').slideUp('normal', function()
							{
								$('#form_result').html(msg);
								$('#form_result').slideDown('normal');
								$('#show_form_again').fadeIn('normal');
							});	
						});
					});
				});
			}
		}
		
		return false;
	});
	
	$('#show_form_again').click(function()
	{
		$('form#christmas_avatar_form').slideDown('normal');
		$('#form_error, #form_result, #show_form_again').hide();
	});
});