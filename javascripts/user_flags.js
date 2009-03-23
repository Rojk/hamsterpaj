var user_flags_info_active = 0;

function user_flags_load_info()
{
	if(user_flags_info_active != this.id)
	{
		loadFragmentInToElement('/traffa/user_flag_info.php?flag=' + this.id, 'flag_info');
		this.id.className = this.id;
		user_flags_info_active = this.id;
	}
	else
	{
		$('#flag_info').text('');
		user_flags_info_active = 0;
	}
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