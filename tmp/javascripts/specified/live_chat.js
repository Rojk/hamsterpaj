
var live_chat_xmlhttp=false;if(!live_chat_xmlhttp&&typeof XMLHttpRequest!='undefined'){live_chat_xmlhttp=new XMLHttpRequest();}
var live_chat_xmlhttp_send=false;if(!live_chat_xmlhttp_send&&typeof XMLHttpRequest!='undefined'){live_chat_xmlhttp_send=new XMLHttpRequest();}
var live_chat_render_order='latest_last';var live_chat_chatrooms=Array();function live_chat_xmlhttp_json()
{if(live_chat_xmlhttp.readyState==0||live_chat_xmlhttp.readyState==4)
{var request_uri='/ajax_gateways/live_chat.json.php?cache_prevention='+Math.random();for(var i=0;i<live_chat_chatrooms.length;i++)
{request_uri+='&'+live_chat_chatrooms[i];}
live_chat_xmlhttp.open('GET',request_uri);live_chat_xmlhttp.onreadystatechange=function()
{if(live_chat_xmlhttp.readyState==4&&live_chat_xmlhttp.status==200)
{if(live_chat_xmlhttp.responseText!='null')
{live_chat_json_actions(live_chat_xmlhttp.responseText);}}}
live_chat_xmlhttp.send(null);}}
function live_chat_json_actions(json_encoded)
{var json=eval('('+json_encoded+')');for(var i=0;i<json.length;i++)
{for(var j=0;j<json[i].events.length;j++)
{if(json[i].events[j].event_type=='message')
{live_chat_render_entry(json[i].chatroom,json[i].events[j]);}
if(json[i].events[j].event_type=='join')
{live_chat_add_user(json[i].chatroom,json[i].events[j]);}
if(json[i].events[j].event_type=='part')
{live_chat_user_part(json[i].chatroom,json[i].events[j]);}}}}
function live_chat_add_user(chatroom,user)
{var target_select=document.getElementById('live_chat_'+chatroom+'_user_list');var highlight_list=document.getElementById('live_chat_'+chatroom+'_highlight_users');var usernames=Array();for(var i=0;i<target_select.options.length;i++)
{if(user.username==target_select.options[i].text)
{return true;}
usernames[i]=target_select.options[i].text;}
usernames.push(user.username)
live_chat_render_user_list(chatroom,usernames);}
function js_trace(trace_msg)
{document.getElementById('js_trace').innerHTML+=trace_msg+'<br />';}
function live_chat_user_part(chatroom,user)
{var target_select=document.getElementById('live_chat_'+chatroom+'_user_list');var highlight_list=document.getElementById('live_chat_'+chatroom+'_highlight_users');var usernames=Array();for(var i=0;i<target_select.options.length;i++)
{if(user.username!=target_select.options[i].text)
{usernames[i]=target_select.options[i].text;}}
live_chat_render_user_list(chatroom,usernames);}
function live_chat_render_user_list(chatroom,usernames)
{var target_select=document.getElementById('live_chat_'+chatroom+'_user_list');var highlight_list=document.getElementById('live_chat_'+chatroom+'_highlight_users');target_select.options.length=null;highlight_list.options.length=null;usernames.sort();for(var i=0;i<usernames.length;i++)
{var this_option=document.createElement('option');this_option.text=usernames[i];var highlight_option=document.createElement('option');highlight_option.text=usernames[i];try
{target_select.add(this_option,null);highlight_list.add(highlight_option,null);}
catch(exception)
{target_select.add(this_option);highlight_list.add(highlight_option,null);}}
document.getElementById('live_chat_'+chatroom+'_user_count').innerHTML=usernames.length;}
function live_chat_set_recipient(username,chatroom)
{window.location='#live_chat_'+chatroom+'_anchor';document.getElementById('live_chat_'+chatroom+'_text_input').focus();if(document.getElementById('live_chat_'+chatroom+'_text_input').value.length>0)
{document.getElementById('live_chat_'+chatroom+'_text_input').value+="\n"+username+': ';}
else
{document.getElementById('live_chat_'+chatroom+'_text_input').value=username+': ';}}
function live_chat_render_entry(chatroom,entry)
{var li_entry=document.createElement('li');var timestamp=document.createElement('span');var username=document.createElement('span');var age=document.createElement('span');var profile_link=document.createElement('a');var message=document.createElement('p');var clearboth=document.createElement('div');li_entry.id='live_chat_entry_'+entry.id;timestamp.className='timestamp';username.className='username';age.className='age';profile_link.className='profile_link';message.className='message';clearboth.className='clearboth';if(entry.highlight)
{li_entry.className='highlight';}
li_entry.style.Height='0px';timestamp.innerHTML=entry.timestamp;username.innerHTML=entry.username;if(entry.age>0)
{age.innerHTML=entry.age+' år';}
profile_link.href='/traffa/profile.php?id='+entry.user_id;profile_link.target='_blank';profile_link.innerHTML='Besök';message.innerHTML=entry.message;username.onclick=function()
{live_chat_set_recipient(entry.username,chatroom);}
var user_photo=document.createElement('img');user_photo.className='user_photo';if(entry.user_photo)
{user_photo.src='http://images.hamsterpaj.net/images/users/thumb/'+entry.user_photo+'.jpg';user_photo.onclick=avatar_popup;}
else
{user_photo.src='http://images.hamsterpaj.net/live_chat/user_no_image.png';}
li_entry.appendChild(user_photo);li_entry.appendChild(timestamp);li_entry.appendChild(username);li_entry.appendChild(age);li_entry.appendChild(profile_link);li_entry.appendChild(message);li_entry.appendChild(clearboth);var entry_area=document.getElementById('live_chat_'+chatroom+'_entry_area');if(live_chat_render_order=='latest_first')
{entry_area.insertBefore(li_entry,entry_area.childNodes[0]);}
else
{entry_area.appendChild(li_entry);entry_area.scrollTop=entry_area.scrollHeight;}
$('#live_chat_entry_'+entry.id).hide(0);$('#live_chat_entry_'+entry.id).show(1250);}
function live_chat_xmlhttp_loop()
{live_chat_xmlhttp_json()
setTimeout('live_chat_xmlhttp_loop();',6000);}
function live_chat_timeout(chatroom,timeout)
{if(timeout==0)
{document.getElementById('live_chat_'+chatroom+'_submit').disabled=false;document.getElementById('live_chat_'+chatroom+'_timer').innerHTML='';}
else
{document.getElementById('live_chat_'+chatroom+'_submit').disabled=true;setTimeout('live_chat_timeout("'+chatroom+'", '+(timeout-1000)+')',1000);document.getElementById('live_chat_'+chatroom+'_timer').innerHTML=(timeout/1000);}}
function live_chat_submit(input_id)
{if(this.id)
{var chatroom=this.id.substr(10);chatroom=chatroom.substr(0,chatroom.length-7);}
else
{var chatroom=input_id.substr(10);chatroom=chatroom.substr(0,chatroom.length-11);}
var message=document.getElementById('live_chat_'+chatroom+'_text_input').value;document.getElementById('live_chat_'+chatroom+'_text_input').value='';document.getElementById('live_chat_'+chatroom+'_text_input').focus();live_chat_xmlhttp_send.open('GET','/ajax_gateways/live_chat.json.php?chatroom='+chatroom+'&message='+escape(message));live_chat_xmlhttp_send.send(null);live_chat_timeout(chatroom,10000);return false;}
function live_chat_input_keydown(e)
{var chatroom=this.id.substr(10);chatroom=chatroom.substr(0,chatroom.length-11);if(document.getElementById('live_chat_'+chatroom+'_enter_submit').value=='disabled')
{return true;}
if(window.event)
{keynum=e.keyCode;}
else if(e.which)
{keynum=e.which;}
if(keynum==13)
{live_chat_submit(this.id);return false;}}
function live_chat_init()
{var chat_identifiers=getElementsByClassName(document,'input','live_chat_identifier');for(var i=0;i<chat_identifiers.length;i++)
{live_chat_chatrooms[i]=chat_identifiers[i].value;if(document.getElementById('live_chat_'+chat_identifiers[i].value+'_submit'))
{document.getElementById('live_chat_'+chat_identifiers[i].value+'_submit').onclick=live_chat_submit;document.getElementById('live_chat_'+chat_identifiers[i].value+'_text_input').onkeydown=live_chat_input_keydown;}
if(document.getElementById('live_chat_'+chat_identifiers[i].value))
{if(document.getElementById('live_chat_'+chat_identifiers[i].value).className=='live_chat_scribble_board')
{live_chat_render_order='latest_first';}}}
live_chat_xmlhttp_loop();}
womAdd('live_chat_init()');