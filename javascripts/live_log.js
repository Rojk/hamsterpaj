var live_log_current = Array();
var live_log_active = true;
var live_log_rate = 0;

function live_log_loop()
{
	setTimeout('live_log_loop()', 1000);
	if(live_log_active == true)
	{
		if(live_log_current.length > 10)
		{
			live_log_current.shift();
		}
		live_log_current.push(live_log_qeue.pop());
	
		document.getElementById('module_live_log').innerHTML = '<h3>Livelogg</h3>';
		for(i = 0; i < live_log_current.length; i++)
		{
			document.getElementById('module_live_log').innerHTML += live_log_current[i] + '<br />';
		}
		document.getElementById('module_live_log').innerHTML += live_log_rate + ' händelser per sekund';
	}
}

function live_log_mouseover_enable()
{
	document.getElementById('module_live_log').onmouseover = function()
	{
		live_log_active = false;
	}
	document.getElementById('module_live_log').onmouseout = function()
	{
		live_log_active = true;
	}
}


//womAdd('live_log_mouseover_enable()');
//womAdd('live_log_loop()');