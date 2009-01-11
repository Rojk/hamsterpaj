
function getElementsByClassName(oElm,strTagName,strClassName){var arrElements=(strTagName=="*"&&oElm.all)?oElm.all:oElm.getElementsByTagName(strTagName);var arrReturnElements=new Array();strClassName=strClassName.replace(/-/g,"\-");var oRegExp=new RegExp("(^|\s)"+strClassName+"(\s|$)");var oElement;for(var i=0;i<arrElements.length;i++){oElement=arrElements[i];if(oRegExp.test(oElement.className)){arrReturnElements.push(oElement);}}
return(arrReturnElements)}
function enable_modules()
{var module_controls=getElementsByClassName(document,'h3','right_module_heading');for(var i=0;i<module_controls.length;i++)
{module_controls[i].onclick=right_module_toggle;}}
function right_module_toggle()
{var module_id='right_module_'+this.id.substr(21);if(document.getElementById(module_id).style.display=='none')
{$('#'+module_id).show(100);}
else
{$('#'+module_id).hide(100);}}
function hide_show_module()
{var module_id=this.parentNode.id;var module=this.parentNode.parentNode;if(module.className=='module_container_open')
{modules_save_state(module_id,'closed');module.className='module_container_closed';}
else
{modules_save_state(module_id,'open');module.className='module_container_open';}}
function modules_save_state(module_id,state)
{var request_url='/save_module_state.php?module='+module_id+'&state='+state;xmlhttp_ping(request_url);}
function make_modules_clickable()
{var modules=getElementsByClassName(document,'div','module_container_open');for(var i=0;i<modules.length;i++)
{var header_obj=modules[i].getElementsByTagName('h3');if(header_obj.length==1)
{header_obj[0].onclick=hide_show_module;}}
modules=getElementsByClassName(document,'div','module_container_closed');for(var i=0;i<modules.length;i++)
{var header_obj=modules[i].getElementsByTagName('h3');if(header_obj.length==1)
{header_obj[0].onclick=hide_show_module;}}}
womAdd('make_modules_clickable()');womAdd('enable_modules()');womOn();