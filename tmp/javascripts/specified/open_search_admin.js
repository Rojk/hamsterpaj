
$(document).ready(function(){$("form.open_search_form").bind("submit",function(){return false;})
$("a.open_search_help").bind("click",function(){return false;})
$("form.open_search_form").submit(function()
{var action=$(this).attr('id');var form_serialized=$(this).serialize();var errors='';if(action=='edit')
{var id=$(this).parent().attr('id').substr(4);form_serialized+='&id='+id;}
if($('#name_form').val()=='')
errors+='<li>Namnet får inte vara tomt</li>\n';if($('#ShortName_form').val()=='')
errors+='<li>ShortName får inte vara tomt</li>\n';if($('#Description_form').val()=='')
errors+='<li>Description får inte vara tomt</li>\n';if($('#Tags_form').val()=='')
errors+='<li>Tags får inte vara tomt</li>\n';if($('#LongName_form').val()=='')
errors+='<li>LongName får inte vara tomt</li>\n';if($('#Link_form').val()=='')
errors+='<li>Länken får inte vara tom!</li>\n';if(!$('#Link_form').val().match(/\{Search\}/))
errors+='<li>Länken måste innehålla {Search}</li>\n';if(errors=='')
{if(action=='add')
{$('#help_box h2').html('Laddar...');$('#help_box #content').html('<img src="http://images.hamsterpaj.net/loading_icons/ajax-loader1.gif" alt="Loading" /> Laddar...');}
else if(action=='edit')
{$('#help_box_'+id+' h2').html('Laddar...');$('#help_box_'+id+' #content').html('<img src="http://images.hamsterpaj.net/loading_icons/ajax-loader1.gif" alt="Loading" /> Laddar...');}
$.post('/ajax_gateways/open_search.php?action='+action,form_serialized,function(data)
{if(action=='add')
{$('#help_box h2').html(data.h2);$('#help_box #content').html(data.content);}
else if(action=='edit')
{$('#help_box_'+id+' h2').html(data.h2);$('#help_box_'+id+' #content').html(data.content);}},'json');}
else
{if(action=='add')
{$('#help_box h2').text('Opps...');$('#help_box #content').html('Vi fann några fel!<br /><ul>'+errors+'</ul>');}
else if(action=='edit')
{$('#help_box_'+id+' h2').html('Opps...');$('#help_box_'+id+' #content').html('Vi fann några fel!<br /><ul>'+errors+'</ul>');}}});$("a.open_search_help").click(function()
{var title_and_id=$(this).attr('id').split('_');var title=title_and_id[0];var id=title_and_id[1];$.get('/admin/open_search.php',{action:'help',what:title,'ajax':'true'},function(data)
{if($('.help_box').attr('id').length>8)
{var help_box_id=id;$('#help_box_'+help_box_id+' h2').html(title);$('#help_box_'+help_box_id+' #content').html(data);}
$('#help_box h2').html(title);$('#help_box #content').html(data);});});$('.open_search_box_info').hide();$('.box_link').click(function()
{var id=$(this).attr('id').substr(5);if($('#box_'+id).css('display')=='none')
{$('#box_'+id).slideDown('fast');$('#image_'+id).attr('src','http://images.hamsterpaj.net/minus.gif');}
else
{$('#box_'+id).slideUp('fast');$('#image_'+id).attr('src','http://images.hamsterpaj.net/plus.gif');}});});