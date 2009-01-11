
function enable_flags_customize_navigation()
{if(document.getElementById('flags_customize_navigation'))
{var categories=document.getElementById('flags_customize_navigation').childNodes;for(var i=0;i<categories.length;i++)
{categories[i].onclick=flags_customize_navigation_click;}}}
function flags_customize_navigation_click()
{var flags_nav_chosen_tab;var hide_categories=getElementsByClassName(document,'div','flags_customize_category');for(var i=0;i<hide_categories.length;i++)
{hide_categories[i].style.display='none';}
document.getElementById('flags_form_'+this.className).style.display='block';}
function flags_customize_submit(form_id)
{var inputs=document.getElementById(form_id).getElementsByTagName('input');var querystring='';for(i=0;i<inputs.length;i++)
{if(inputs[i].checked)
{querystring+='&'+escape(inputs[i].name)+'='+escape(inputs[i].value);}
else if(inputs[i].type=='checkbox')
{querystring+='&'+escape(inputs[i].name)+'='+'disabled';}}
xmlhttp_ping('/traffa/flags_ajax_gateway.php?action=update'+querystring);document.getElementById('flags_customize_message').innerHTML='Dina flaggor har sparats!';setTimeout("clear_customize_message();",1500)}
function clear_customize_message()
{document.getElementById('flags_customize_message').innerHTML='';}
womAdd('enable_flags_customize_navigation()');