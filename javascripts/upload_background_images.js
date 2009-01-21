$(document).ready(function(){
	$("form").bind("submit", function() { return false; });//To cancel a default action and prevent it from bubbling up, return false
	var original_filename = '';
	var filesdir = '/ajax_gateways/wallpapers_';
	var ajaxurl = filesdir + 'gateway.php';
	var upload_org_file_href = filesdir + 'gateway_image.php';
	var result_div = 'result'; //id
	var mysql_insert_id = '';
	var original_width = '';
	var original_height = '';
	
	function ajax_loading()
	{
		return '<img src="http://images.hamsterpaj.net/wallpapers/load.gif" alt="Laddar..." />';
	}


	function ajaxFileUpload(theajaxurl, fileid)
	{

		$.ajaxFileUpload
		(
			{
				url:theajaxurl,
				secureuri:false,
				fileElementId:fileid,
				dataType: 'html',
				success: function (data)
				{	
					original_filename = data.split('::::::')[1];
					mysql_insert_id = data.split('::::::')[2];
					original_width = data.split('::::::')[3];
					original_height = data.split('::::::')[4];
					
					if(typeof(original_filename) == 'string')//no errors
						$('li#width_original span').html('<strong style="color:#008102">Klar</strong>').removeClass('error');
					else //omg, error!
						$('li#width_original span').html('<p>'+data+'</p>').addClass('error');

					$('div#sizes span').html('Hämtar upplösningar ' + ajax_loading());
					
					$.get(ajaxurl, {action: 'get_res', width: original_width, height: original_height}
					, function(data)
					{
						$('div#sizes').html('Välj upplösningar<br />' + data);
					});
					
					$('form input:submit').attr('disabled', false);
					$('form input:button').attr('disabled', false);
				},
				error: function (data, status, e)
				{
					alert('Data: ' + data + '\nStatus: ' + status + '\nError: ' + e);
				}
			}
		)
		
		return false;

	}
	
	$('#upload_image_button').click(function(){
		$('form input:submit').attr('disabled', true);
		$('form input:button').attr('disabled', true);
		//for the original file
		if($('li#width_original').html() == null)
		{
			$('#'+ result_div +' ul').append('<li id="width_original">Original <span>' + ajax_loading() + '</span>');
		}
		else
		{
			$('li#width_original span').html(ajax_loading());
		}
		ajaxFileUpload(upload_org_file_href, 'uploaded_image');
	});
	
	
	$('form').submit(
	function()
	{
		$('form input:submit').attr('disabled', true);
		$('form input:button').attr('disabled', true);
	
		var errors = '';
		if($('#form_title').val() == "")
			errors += 'Namnet får inte vara tomt!\n';
		if($('#form_tags').val() == "")
			errors += 'Bilden måste innehålla taggar!\n';
	if(errors == '')
	{
	
		$('#'+ result_div +' ul').append('<li id="form_data">Formulär <span>' + ajax_loading() + '</span></li>');
		// ************     this could be a flaw. **************
		//Because the ajaxqueue script does everything by GET, this means that the GET-header only can keep 
		//2083 (IE), 4050 (Opera) and Netscape 6 about 2000 characters. Hopefully this won't be an error =)
		var ajax_data = '&action=upload_form&id='+mysql_insert_id+'&'+$('#wallpapers_form').serialize();
		$.ajax({
			mode: "queue",
			data: ajax_data,
	        url: ajaxurl,
			success: function(data)
				{
					$('li#form_data span').html('<strong style="color:#008102">Klar</strong> Filnamn: ' + data);
				}
		});
		
		var arr = $("input[name='new_width']:checked");
		arr = jQuery.makeArray(arr);
		arr.unshift('preview', 'thumb');
		// list all resolutions
		jQuery.each(arr,
			function(i)
			{
				if($('li#width_'+this.value).html() == null)
				{
					$('#'+ result_div +' ul').append('<li id="width_' + (this != '[object HTMLInputElement]' ? this : this.value) + '">Typ: ' + (this != '[object HTMLInputElement]' ? this : this.value) + ' <span>' + ajax_loading() + '</span></li>');
				}
			}
		);
				
		//make the new images with different resolutions
		if(arr.length > 0)
		{
			jQuery.each(arr, 
				function(i)
				{
					$.ajax({
						mode: "queue",
						data: {
									action: 'resize_wallpapers', 
									width: (arr[i] != '[object HTMLInputElement]' ? arr[i] : arr[i].value.split('x')[0]), 
									height: (arr[i] != '[object HTMLInputElement]' ? arr[i] : arr[i].value.split('x')[1]), 
									original_name: original_filename, 
									id: mysql_insert_id, 
									'last': (i == (arr.length-1) ? true :false), 
									original: (arr[i].id == 'orignal_image_res' ? true : false)
								},
	                		url: ajaxurl,
						success: function(data)
							{
								$('li#width_' + (arr[i] != '[object HTMLInputElement]' ? arr[i] : arr[i].value) + ' span').html('<strong style="color:#008102">Klar</strong>');
							}
					});
				
				if((i == (arr.length-1)))
				{
					$('#result').append('<h2>Din bild laddades upp, nu ska den bara bli verifierad av admins.</h2>');
				}
				}
			);
		}
	}
	else
	{
		alert('Det var visst några fel...\n\n'+errors+'\nOrdna detta och prova igen ;)');
		$('form input:submit').attr('disabled', false);
		$('form input:button').attr('disabled', false);

	}
	
	
	});
	

});