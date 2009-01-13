
if(typeof(hp)=='undefined'){var hp=new Object();}
hp.profile={init:function()
{try
{this.presentation.change.init();}catch(E){}
try
{}catch(E){}}};hp.profile.presentation={};hp.profile.presentation.change={enabled_features:new Array('bold','italic','header','image','poll','link'),init:function()
{for(var feature=0;feature<this.enabled_features.length;feature++)
{document.getElementById('profile_presentation_change_'+this.enabled_features[feature]+'_control').onclick=function()
{hp.profile.presentation.change.parse_markup_click(this);return false;}}
document.getElementById('profile_presentation_change_preview_button').onclick=function()
{var loader=hp.give_me_an_AJAX();loader.onreadystatechange=function()
{if(loader.readyState==4&&loader.status==200)
{document.getElementById('profile_presentation_change_preview_area').innerHTML=loader.responseText;try
{enable_polls();}catch(E){}}}
loader.open('POST','/ajax_gateways/profile_presentation_change.php?action=preview',true);loader.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=utf-8');loader.send('data='+encodeURIComponent(document.getElementById('profile_presentation_change_presentation_text').value));return false;}},parse_markup_click:function(button)
{var control_handle=button.id.substring(28,button.id.length-8);switch(control_handle)
{case'bold':this.insert_markup({textfield:'profile_presentation_change_presentation_text',start_tag:'[b]',end_tag:'[/b]'});break;case'italic':this.insert_markup({textfield:'profile_presentation_change_presentation_text',start_tag:'[i]',end_tag:'[/i]'});break;case'spoiler':this.insert_markup({textfield:'profile_presentation_change_presentation_text',start_tag:'[spoiler]',end_tag:'[/spoiler]'});break;case'header':this.markup_properties.ask_to_complete({textfield:'profile_presentation_change_presentation_text',start_tag:'[%HEADER_SIZE%]',end_tag:'[/%HEADER_SIZE%]'},{html_source:'/ajax_gateways/profile_presentation_change.php?action=markup_properties&markup_handler=header'});break;case'image':this.markup_properties.ask_to_complete({textfield:'profile_presentation_change_presentation_text',start_tag:'[fotoalbum:%CHOOSEN_PHOTO_ID%]'},{html_source:'/ajax_gateways/profile_presentation_change.php?action=markup_properties&markup_handler=image'});break;case'link':this.markup_properties.ask_to_complete({textfield:'profile_presentation_change_presentation_text',start_tag:'[link:%LINK_TYPE%]%LINK_HREF%[/link]'},{html_source:'/ajax_gateways/profile_presentation_change.php?action=markup_properties&markup_handler=link'});break;case'poll':this.markup_properties.ask_to_complete({textfield:'profile_presentation_change_presentation_text',start_tag:'[poll:%CHOOSEN_POLL_ID%]'},{html_source:'/ajax_gateways/profile_presentation_change.php?action=markup_properties&markup_handler=poll'});break;}},insert_markup:function(params){params.end_tag=(typeof(params.end_tag)=="undefined")?"":params.end_tag;var textarea=document.getElementById(params.textfield);textarea.focus();var text_to_parse=(document.selection)?document.selection.createRange().text:textarea.value.substring(textarea.selectionStart,textarea.selectionEnd);var parsed_text=params.start_tag+text_to_parse+params.end_tag;if(typeof(document.selection)!="undefined"){document.selection.createRange().text=parsed_text;}
else
{var move_cursor_to=textarea.selectionStart+params.start_tag.length;var move_scrollbar_to=textarea.scrollTop;var replace=textarea.value.substring(0,textarea.selectionStart);replace+=parsed_text;replace+=textarea.value.substring(textarea.selectionEnd);textarea.value=replace;textarea.scrollTop=move_scrollbar_to;textarea.setSelectionRange(move_cursor_to,move_cursor_to);}},markup_properties:{open:function()
{document.getElementById('profile_presentation_change_markup_properties').style.display='block';},close:function()
{document.getElementById('profile_presentation_change_markup_properties').style.display='none';},write:function(text)
{document.getElementById('profile_presentation_change_markup_properties_content').innerHTML=text;},ask_to_complete:function(markup_params,properties_params)
{this.open();this.write('Laddar...');var loader=hp.give_me_an_AJAX();loader.onreadystatechange=function()
{if(loader.readyState==4&&loader.status==200)
{hp.profile.presentation.change.markup_properties.ask_to_complete_loaded(markup_params,properties_params,eval('('+loader.responseText+')'));}}
loader.open('GET',properties_params.html_source,true);loader.send(null);},ask_to_complete_loaded:function(markup_params,properties_params,json_data)
{this.write(json_data.html);for(var onchange_handler=0;onchange_handler<json_data.onchange_handlers.length;onchange_handler++)
{document.getElementById(json_data.onchange_handlers[onchange_handler].id).onchange=eval('function(){ eval(json_data.onchange_handlers['+onchange_handler+'].call); }');}
this.save=function(params)
{params=(typeof(params)!='undefined')?params:new Array();var start_tag_replacement=(typeof(params['override_start_tag'])!='undefined')?params['override_start_tag']:markup_params.start_tag;var end_tag_replacement=(typeof(params['override_end_tag'])!='undefined')?params['override_end_tag']:((typeof(markup_params.end_tag)!='undefined')?markup_params.end_tag:'');for(var parameter=0;parameter<json_data.html_parameters.length;parameter++)
{var element_data=json_data.html_parameters[parameter];var element=document.getElementById(element_data.id)
if(element_data.type=='select')
{start_tag_replacement=start_tag_replacement.replace('%'+element_data.id.toUpperCase()+'%',element.options[element.selectedIndex].value);end_tag_replacement=end_tag_replacement.replace('%'+element_data.id.toUpperCase()+'%',element.options[element.selectedIndex].value);}
else
{start_tag_replacement=start_tag_replacement.replace('%'+element_data.id.toUpperCase()+'%',element.value);end_tag_replacement=end_tag_replacement.replace('%'+element_data.id.toUpperCase()+'%',element.value);}}
markup_params.start_tag=start_tag_replacement;markup_params.end_tag=end_tag_replacement;hp.profile.presentation.change.insert_markup(markup_params);this.write('Laddar...');this.close();}},property_onevent:{image_select:function(id)
{document.getElementById('choosen_photo_id').value=id;hp.profile.presentation.change.markup_properties.save();},image_upload:function()
{if(!window.open('/traffa/photos.php'))
{alert('Det nya fönstret med fotouppladdnigen blockerades av din popupfönsterblockerare.');}},poll_select:function(id)
{document.getElementById('choosen_poll_id').value=id;hp.profile.presentation.change.markup_properties.save();},link_change_type:function(select)
{var new_handle=select.options[select.selectedIndex].value;var new_options='Laddar...';switch(new_handle)
{case'profile':new_options='Användare: <input type="text" id="link_href" />';break;case'webb':new_options='Webbadress: <input type="text" id="link_href" value="http://" />';break;case'photos':new_options='<input type="hidden" id="link_href" value="NO_HREF" />';break;case'guestbook':new_options='<input type="hidden" id="link_href" value="NO_HREF" />';break;default:new_options='<input type="hidden" id="link_href" value="NO_HREF" />';}
document.getElementById('profile_presentation_change_markup_properties_link_properties').innerHTML=new_options;},link_save:function()
{var params=new Array();var link_type_object=document.getElementById('link_type');var link_type=link_type_object.options[link_type_object.selectedIndex].value;var link_href=document.getElementById('link_href').value;if(link_href=='NO_HREF')
{params['override_start_tag']='[link:%LINK_TYPE%]';}
hp.profile.presentation.change.markup_properties.save(params);}}}};womAdd('hp.profile.init()');