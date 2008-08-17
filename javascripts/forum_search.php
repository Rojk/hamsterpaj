var questions = Array();

questions['q2_discussion'] = 'Letar du efter en diskussion som du redan har läst?';
questions['q2_posts'] = 'Letar du efter ett inlägg som du redan har läst?';

questions['q3_discussion_read'] = 'Minns du vem som startade diskussionen?';
questions['q3_discussion_unread'] = 'Vill du bara se diskussioner som en särskild användare har startat?';
questions['q3_discussion_both'] = 'Vill du bara se diskussioner som en särskild användare har startat?';
questions['q3_post_read'] = 'Minns du vem som skrev inlägget?';
questions['q3_post_unread'] = 'Vill du bara se inlägg som en särskild användare har skrivit?';
questions['q3_post_both'] = 'Vill du bara se inlägg som en särskild användare har skrivit?';

questions['q4_discussion'] = 'Är det en ny eller gammal diskussion du letar efter?';
questions['q4_discussion'] = 'Är det ett nytt eller gammalt inlägg du letar efter?';

questions['q5_read'] = 'Kommer du ihåg något ovanligt ord eller citat som var med?';
questions['q5_unread'] = 'Letar du efter något särskilt ord eller någon mening?';
questions['q5_both'] = 'Letar du efter något särskilt ord eller någon mening?';


function forum_search_labels()
{
	document.getElementById('q2_label').innerHTML = questions['q2_posts'];
}

womAdd('forum_search_labels');

alert('Tjoho');