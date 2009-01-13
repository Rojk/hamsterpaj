
if(typeof(hp)=='undefined'){var hp=new Object();}
hp.user_box={last_user_box:0,object_count:0,draw:function(params)
{this.last_user_box++;var box_element=document.createElement('div');box_element.id='user_box_'+this.last_user_box;document.body.appendChild(box_element);var box=document.getElementById('user_box_'+this.last_user_box);box.className='user_box_'+params.styling;box.style.position='absolute';box.style.left=(hp.mouse.x-10)+'px';box.style.top=(hp.mouse.y-10)+'px';box.onmouseout=function()
{}
var output='';var onevent_handlers=new Array();for(var item in params.items)
{var current_item=params.items[item];switch(item)
{case'general_userinfo':output+='<div class="item">'
+current_item.info
+'</div>';break;case'button_profile':this.object_count++;output+='<div class="item_left_aligned">'
+'<input type="button" id="user_box_profile_button_'+(this.object_count)+'" class="blue_button_70" value="Till profil &raquo;" />'
+'</div>'
var event_handler=new Array();event_handler['type']='onclick';event_handler['target']='user_box_profile_button_'+this.object_count;event_handler['callback']=function()
{hp.go_to_user.profile(params.user_id);};onevent_handlers[onevent_handlers.length]=event_handler;break;case'button_guestbook':this.object_count++;output+='<div class="item_left_aligned">'
+'<input type="button" id="user_box_guestbook_button_'+(this.object_count)+'" class="blue_button_70" value="Gästbok &raquo;" />'
+'</div>'
var event_handler=new Array();event_handler['type']='onclick';event_handler['target']='user_box_guestbook_button_'+this.object_count;event_handler['callback']=function()
{hp.go_to_user.guestbook(params.user_id);};onevent_handlers[onevent_handlers.length]=event_handler;break;}}
box.innerHTML='<div class="user_box_top">&nbsp;</div>'
+'<div class="user_box_content">'
+output
+'</div>'
+'<div class="user_box_bottom">&nbsp;</div>';for(var event_handler=0;event_handler<onevent_handlers.length;event_handler++)
{if(onevent_handlers[event_handler]['type']!='oninsert')
{document.getElementById(onevent_handlers[event_handler]['target'])[onevent_handlers[event_handler]['type']]=onevent_handlers[event_handler]['callback'];}
else
{var loaded_object=document.getElementById(onevent_handlers[event_handler]['target']);eval(onevent_handlers[event_handler]['callback']);}}}}