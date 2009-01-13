
$(document).ready(function(){$('.open_search_box_info').hide();$('.box_link').click(function()
{var id=$(this).attr('id').substr(5);if($('#box_'+id).css('display')=='none')
{$('#box_'+id).slideDown('fast');$('#image_'+id).attr('src','http://images.hamsterpaj.net/minus.gif');}
else
{$('#box_'+id).slideUp('fast');$('#image_'+id).attr('src','http://images.hamsterpaj.net/plus.gif');}});});