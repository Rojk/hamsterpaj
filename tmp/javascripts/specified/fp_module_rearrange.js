
function enable_fp_module_rearrange()
{if(document.getElementById('fp_module_rearrange_list'))
{var list=document.getElementById('fp_module_rearrange_list');var lis=list.getElementsByTagName('li');for(var i=0;i<lis.length;i++)
{lis[i].ondblclick=fp_module_rearrange_click;}}
if(document.getElementById('fp_moudle_rearrange_save'))
{document.getElementById('fp_moudle_rearrange_save').onclick=fp_module_rearrange_save_list;}}
function fp_module_rearrange_click()
{var list=document.getElementById('fp_module_rearrange_list');var lis=list.getElementsByTagName('li');for(var i=0;i<lis.length;i++)
{if(this.innerHTML==lis[i].innerHTML)
{var position=i;}}
if(i>0)
{list.insertBefore(this,lis[position-1]);}}
function fp_module_rearrange_save_list()
{var list=document.getElementById('fp_module_rearrange_list');var lis=list.getElementsByTagName('li');var querystring='';for(var i=0;i<lis.length;i++)
{querystring+='&pos_'+i+'='+lis[i].id;}
xmlhttp_ping('/ajax_gateways/fp_module_rearrange.php?TRAMS'+querystring);;}
womAdd('enable_fp_module_rearrange()');