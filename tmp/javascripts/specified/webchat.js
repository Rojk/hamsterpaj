
var entry_class;var webchat_xmlhttp=false;if(!webchat_xmlhttp&&typeof XMLHttpRequest!='undefined'){webchat_xmlhttp=new XMLHttpRequest();}
function webchat_load()
{if(webchat_xmlhttp.readyState==0||webchat_xmlhttp.readyState==4)
{webchat_xmlhttp.open('GET','/ajax_gateways/webchat.json.php?cache_prevention='+Math.random());webchat_xmlhttp.onreadystatechange=function()
{if(webchat_xmlhttp.readyState==4&&webchat_xmlhttp.status==200)
{webchat_render(webchat_xmlhttp.responseText);}}
webchat_xmlhttp.send(null);}}
function webchat_render(json_data)
{if(json_data!='null')
{var entries=eval('('+json_data+')');for(var i=0;i<entries.length;i++)
{var entry=entries[i];var entry_html='';if(entry.highlight=='true')
{entry_class='webchat_entry_highlight';}
else if(entry_class=='webchat_entry_even')
{entry_class='webchat_entry_odd';}
else
{entry_class='webchat_entry_even';}
entry_html+='<div class="'+entry_class+'" id="webchat_entry_'+entry.id+'">';entry_html+='<div class="webchat_entry_info">';entry_html+='<span class="webchat_entry_time">'+entry.time_readable+'</span>';entry_html+='<span class="webchat_entry_username" onclick="webchat_load_user('+entry.user+')">'+entry.username+'</span>';entry_html+='</div>';entry_html+='<div class="text" onclick="webchat_reply_user(\''+entry.username+'\')">'+entry.text+'</div>';entry_html+='<br style="clear: both;" />';entry_html+='</div>';document.getElementById('webchat_entry_list').innerHTML=entry_html+document.getElementById('webchat_entry_list').innerHTML;}}}
function webchat_load_user(user_id)
{loadFragmentInToElement('/ajax_gateways/webchat_user_info.php?user='+user_id,'webchat_user_info');}
function webchat_reply_user(username)
{document.getElementById('webchat_message_input').value=username+': ';document.getElementById('webchat_message_input').focus();}
function webchat_send_message(e)
{if(window.event)
{keynum=e.keyCode;}
else if(e.which)
{keynum=e.which;}
if(keynum==13)
{xmlhttp_ping('/ajax_gateways/webchat.json.php?message='+encodeURIComponent(document.getElementById('webchat_message_input').value));document.getElementById('webchat_message_input').value='';setTimeout("webchat_load()",500);document.getElementById('webchat_message_input').disabled=true;setTimeout("document.getElementById('webchat_message_input').disabled = false;",750);setTimeout("document.getElementById('webchat_message_input').focus()",750);}}
function webchat_enable_form()
{if(document.getElementById('webchat_message_input'))
{document.getElementById('webchat_message_input').onkeydown=webchat_send_message;}}
function webchat_loop()
{setTimeout("webchat_loop()",5000);webchat_load();}
womAdd('webchat_enable_form()');womAdd('webchat_loop()');