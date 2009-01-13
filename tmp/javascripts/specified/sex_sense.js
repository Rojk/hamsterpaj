
function checkChosenCategory(){if(document.getElementById('sex_category').value=='X')
{alert('Du glömde att välja en kategori.');return false;}
else if(document.getElementById('title').value=='')
{alert('Du glömde att fylla i en titel');return false;}
else
{return true;}}
function sex_sense_confirm_removal(id)
{if(confirm('Är du säker på att du vill ta bort frågan?'))
{window.location='/sex_och_sinne/admin.php?action=remove&id='+id;}}
function sex_sense_init()
{var myFile=document.location.toString();$('.sex_sense_post_header').click(function()
{$('.hidden_content').hide('slow');$('.content').hide('slow');var elementname=$('#content_'+new String($(this).attr('id')).split('_')[1]);var isVisible=$(elementname).is(':visible');if(isVisible==false)
{$('#content_'+new String($(this).attr('id')).split('_')[1]).slideToggle('slow');}});}
womAdd('sex_sense_init()');