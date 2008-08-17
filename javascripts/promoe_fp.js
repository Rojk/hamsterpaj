var promoe_fp = 'active';

var fp_promoes = Array(19853, 19752, 19315, 19453, 12279, 19607, 14106, 14673, 19816, 19734, 1821, 9396, 19384, 19847, 19583, 19854, 19297, 1795, 2905, 19005, 525, 15707);

function promoe_fp_start()
{
	if(promoe_fp == 'active')
	{
		var current_promoe = fp_promoes[Math.round(Math.random()*fp_promoes.length)];
		document.getElementById('promoe_fp_highlight').innerHTML = '<a href="/annat/promoe.php?view=' + current_promoe + '"><img src="http://images.hamsterpaj.net/promoe_highlights/' + current_promoe + '.png" width="120" height="75" /></a>';
	}
	setTimeout('promoe_fp_start();', 1500);
}

function promoe_fp_pause()
{
	promoe_fp = 'disabled';
}

function enable_promoe_fp()
{
	if(document.getElementById('promoe_fp_highlight'))
	{
		document.getElementById('promoe_fp_highlight').onmouseover = promoe_fp_pause;
		document.getElementById('promoe_fp_highlight').onmouseout = function()
		{
			promoe_fp = 'active';
		}
		
		promoe_fp_start();	
	}
}

womAdd('enable_promoe_fp();');