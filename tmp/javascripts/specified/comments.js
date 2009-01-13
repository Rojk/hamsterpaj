
function comment_answer(comment_id)
{var reply_instructions='Här kan du svara på kommentaren =)';var reply=prompt(reply_instructions);if(reply.length<2)
{alert('Bläh, kan du inte skriva något mer?');}
else
{xmlhttp_ping('/ajax_gateways/comments.php?action=comment_answer&id='+comment_id+'&reply='+reply);loadFragmentInToElementByPOST('/ajax_gateways/comments.php','comments_list',"update=true"+"&item_id="+document.getElementById('comment_item_id').value+"&item_type="+document.getElementById('comment_item_type').value+"&return_list=1");return false;}}
function comments_view_all_button_click_enable()
{if(document.getElementById('comments_view_all_button'))
{document.getElementById('comments_view_all_button').onclick=comments_view_all_button_click;}}
function comments_view_all_button_click()
{popup=window.open("http://www.hamsterpaj.net/ajax_gateways/comments.php"+"?action=comments_list_all"+"&item_id="+document.getElementById('comment_item_id').value+"&item_type="+document.getElementById('comment_item_type').value,"Alla kommentarer","width=600,height=600");popup.moveTo(170,100);return false;}
function comment_submit_click_enable()
{if(document.getElementById('comment_submit'))
{document.getElementById('comment_submit').onclick=comment_submit_click;}}
function comment_submit_click()
{loadFragmentInToElementByPOST('/ajax_gateways/comments.php','comments_list',"comment="+document.getElementById('comment_input_text').value+"&item_id="+document.getElementById('comment_item_id').value+"&item_type="+document.getElementById('comment_item_type').value+"&return_list=1");return false;}
function comment_remove(id,comment_owner)
{if(confirm('Är du säker på att du vill radera kommentaren?'))
{var xmlhttp=hp.give_me_an_AJAX();xmlhttp.open('GET','/ajax_gateways/comments.php?action=comment_remove&id='+id,true);xmlhttp.onreadystatechange=function()
{if(xmlhttp.readyState==4&&xmlhttp.status==200)
{document.getElementById('comment_'+id).innerHTML='';}}
xmlhttp.send(null);}}
womAdd('comment_submit_click_enable()');womAdd('comments_view_all_button_click_enable()');