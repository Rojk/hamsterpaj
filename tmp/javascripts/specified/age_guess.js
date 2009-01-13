
var age_guess_xmlhttp=false;if(!age_guess_xmlhttp&&typeof XMLHttpRequest!='undefined'){age_guess_xmlhttp=new XMLHttpRequest();}
function age_guess_load(guessed_age)
{age_guess_xmlhttp.open('GET','/ajax_gateways/age_guess.php?cache_prevention='+Math.random()+'&guessed_age='+guessed_age);age_guess_xmlhttp.onreadystatechange=function()
{if(age_guess_xmlhttp.readyState==4&&age_guess_xmlhttp.status==200)
{age_guess_json_actions(age_guess_xmlhttp.responseText);}}
age_guess_xmlhttp.send(null);}
function age_guess_json_actions(json_data)
{var return_data=eval('('+json_data+')');document.getElementById('age_guess_toplist').innerHTML=return_data.toplist;document.getElementById('age_guess_statistics').innerHTML=return_data.statistics;document.getElementById('age_guess_main').innerHTML=return_data.main;document.getElementById('age_guess_result').innerHTML=return_data.result;document.body.style.cursor='default';}
function age_guess_input()
{var guessed_age=this.id.substring(16);if(this.id=='age_guess_skip')
{guessed_age='skip';}
age_guess_load(guessed_age);document.getElementById('age_guess_result').innerHTML='<h1>Laddar...</h1>';document.body.style.cursor='wait';}
function age_guess_enable_inputs()
{if(document.getElementById('age_guess_inputs'))
{for(var i=6;i<26;i++)
{document.getElementById('age_guess_input_'+i).onclick=age_guess_input;}
document.getElementById('age_guess_skip').onclick=age_guess_input;}}
womAdd('age_guess_enable_inputs()');