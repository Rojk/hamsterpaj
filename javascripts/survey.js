function enable_survey()
{
	var survey_forms = getElementsByClassName(document, 'ol', 'survey_not_yet_voted');
	for(var i = 0; i < survey_forms.length; i++)
	{
		for(var j = 0; j < survey_forms[i].childNodes.length; j++)
		{
			survey_forms[i].childNodes[j].onclick = survey_alternative_click;
		}
	}
}

function survey_alternative_click()
{
	var survey_id = this.parentNode.id.substr(12);
	xmlhttp_ping('/survey/ajax_gateway.php?action=vote&survey=' + survey_id + '&alternative=' + this.id.substr(12));
	document.getElementById('survey_' + survey_id + '_chart').src = '/survey/chart.php?survey=' + survey_id;

	this.parentNode.className = 'survey_already_voted';	
	for(var j = 0; j < this.parentNode.childNodes.length; j++)
	{
		this.parentNode.childNodes[j].onclick = survey_already_clicked;
	}
	
	document.getElementById('survey_vote_count').innerHTML = parseInt(document.getElementById('survey_vote_count').innerHTML) + 1;
}

function survey_already_clicked()
{
	alert('Men, tjockis! Du har ju redan röstat!');
}

womAdd('enable_survey()');