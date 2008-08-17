function user_flags_load_info()
{
	loadFragmentInToElement('/traffa/user_flag_info.php?flag=' + this.id, 'flag_info');
	this.id.className = this.id;
}

function enable_user_flags()
{
	if(document.getElementById('user_flags'))
	{
		var flag_container = document.getElementById('user_flags');
		
		var flags = flag_container.getElementsByTagName('img');
		for(i = 0; i < flags.length; i++)
		{
			flags[i].onclick = user_flags_load_info;
		}
		
	}
}

womAdd('enable_user_flags()');