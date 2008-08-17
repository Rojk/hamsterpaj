var questions = Array();

questions['q2_discussion'] = 'Letar du efter en diskussion som du redan har läst?';
questions['q2_post'] = 'Letar du efter ett inlägg som du redan har läst?';

questions['q3_discussion_read'] = 'Minns du vem som startade diskussionen?';
questions['q3_discussion_unread'] = 'Vill du bara se diskussioner som en särskild användare har startat?';
questions['q3_discussion_both'] = 'Vill du bara se diskussioner som en särskild användare har startat?';
questions['q3_post_read'] = 'Minns du vem som skrev inlägget?';
questions['q3_post_unread'] = 'Vill du bara se inlägg som en särskild användare har skrivit?';
questions['q3_post_both'] = 'Vill du bara se inlägg som en särskild användare har skrivit?';

questions['q4_discussion'] = 'Är det en ny eller gammal diskussion du letar efter?';
questions['q4_post'] = 'Är det ett nytt eller gammalt inlägg du letar efter?';

questions['q5_read'] = 'Kommer du ihåg något ovanligt ord eller citat som var med?';
questions['q5_unread'] = 'Letar du efter något särskilt ord eller någon mening?';
questions['q5_both'] = 'Letar du efter något särskilt ord eller någon mening?';

function radio_get_value(button_collection)
{
	for(var i = 0; i < button_collection.length; i++)
	{
		if(button_collection[i].checked)
		{
			return button_collection[i].value;
		}
	}
}


function forum_search_labels()
{
	document.getElementById('legend_q2').innerHTML = questions['q2_' + radio_get_value(document.advanced_search.search_type)];
	document.getElementById('legend_q3').innerHTML = questions['q3_' + radio_get_value(document.advanced_search.search_type) + '_' + radio_get_value(document.advanced_search.already_viewed)];
	document.getElementById('legend_q4').innerHTML = questions['q4_' + radio_get_value(document.advanced_search.search_type)];
	document.getElementById('legend_q5').innerHTML = questions['q5_' + radio_get_value(document.advanced_search.search_type)];
}

function forum_search_enable()
{
	var inputs = document.getElementById('forum_advanced_search').getElementsByTagName('input');
	
	for(var i = 0; i < inputs.length; i++)
	{
		inputs[i].onclick = forum_search_labels;
	}
}

womAdd('forum_search_enable()');
womAdd('forum_search_labels()');
