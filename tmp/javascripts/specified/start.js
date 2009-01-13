
function enable_fp_spotlight()
{var thumbnails=getElementsByClassName(document,'img','fp_user_list_thumb');for(var i=0;i<thumbnails.length;i++)
{thumbnails[i].onclick=fp_spotlight_scroll;}}
function fp_spotlight_scroll()
{var new_offset=this.id.substr(14)*310;$('#fp_spotlight').animate({scrollLeft:new_offset},250);}
womAdd('enable_fp_spotlight()');function enable_fp_module_voting()
{var controls=getElementsByClassName(document,'img','fp_vote');for(var i=0;i<controls.length;i++)
{controls[i].onclick=fp_module_vote;}}
function fp_module_vote()
{var module_id=this.id.substr(13);if(this.id.substr(8,4)=='plus')
{var vote='plus';document.getElementById('fp_module_score_'+module_id).innerHTML=parseInt(document.getElementById('fp_module_score_'+module_id).innerHTML)+1;}
else
{var vote='minu';document.getElementById('fp_module_score_'+module_id).innerHTML=parseInt(document.getElementById('fp_module_score_'+module_id).innerHTML)-1;}
xmlhttp_ping('/ajax_gateways/fp_module_vote.php?module_id='+module_id+'&vote='+vote);document.getElementById('fp_vote_minu_'+module_id).src='http://images.hamsterpaj.net/discussion_forum/thread_voting_minus_grey.png';document.getElementById('fp_vote_minu_'+module_id).onclick='';document.getElementById('fp_vote_minu_'+module_id).style.cursor='default';document.getElementById('fp_vote_plus_'+module_id).src='http://images.hamsterpaj.net/discussion_forum/thread_voting_plus_grey.png';document.getElementById('fp_vote_plus_'+module_id).onclick='';document.getElementById('fp_vote_plus_'+module_id).style.cursor='default';}
womAdd('enable_fp_module_voting()');