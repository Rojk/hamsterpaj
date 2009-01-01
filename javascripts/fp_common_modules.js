function enable_fp_module_voting()
{
	var controls = getElementsByClassName(document, 'img', 'fp_vote');
	for(var i = 0; i < controls.length; i++)
	{
		controls[i].onclick = fp_module_vote;
	}
}

function fp_module_vote()
{
	var module_id = this.id.substr(13);
	if(this.id.substr(8, 4) == 'plus')
	{
		var vote = 'plus';
		document.getElementById('fp_module_score_' + module_id).innerHTML = parseInt(document.getElementById('fp_module_score_' + module_id).innerHTML) + 1;
	}
	else
	{
		var vote = 'minu';
		document.getElementById('fp_module_score_' + module_id).innerHTML = parseInt(document.getElementById('fp_module_score_' + module_id).innerHTML) - 1;
	}
	
	xmlhttp_ping('/ajax_gateways/fp_module_vote.php?module_id=' + module_id + '&vote=' + vote);
	document.getElementById('fp_vote_minu_' + module_id).src = 'http://images.hamsterpaj.net/discussion_forum/thread_voting_minus_grey.png';
	document.getElementById('fp_vote_minu_' + module_id).onclick = '';
	document.getElementById('fp_vote_minu_' + module_id).style.cursor = 'default';

	document.getElementById('fp_vote_plus_' + module_id).src = 'http://images.hamsterpaj.net/discussion_forum/thread_voting_plus_grey.png';
	document.getElementById('fp_vote_plus_' + module_id).onclick = '';
	document.getElementById('fp_vote_plus_' + module_id).style.cursor = 'default';
}

womAdd('enable_fp_module_voting()');

function fp_module_news_switch(titleid)
{
	/*var tempsplit = titleid.split('_');
	var newsid = tempsplit[1];*/
	//document.getElementById('title_' + titleid).style.display = "block";
	var elems = getElementsByClassName(document, 'div', 'fp_news_main');
	for(i=0;i<elems.length;i++)
	{
		elems[i].className = 'fp_news_main_hidden';
		//if(i != titleid && document.getElementById('main_' + tempid)) document.getElementById('title_' + titleid).style.display = "none";
		//if(i != titleid && document.getElementById('main_' + tempid)) document.getElementById('main_' + titleid).style.display = "none";
	}
	document.getElementById('main_' + titleid).className = 'fp_news_main';
}