function testPassword(passwd)
{
		var intScore   = 0
		var strVerdict = ""
		var strLog     = ""
		

		// LETTERS (Not exactly implemented as dictacted above because of my limited understanding of Regex)
		if (passwd.match(/[a-zåäö]/))                              // [verified] at least one lower case letter
		{
			document.getElementById('lower_case').style.color = 'green'
		}
		else
		{
			document.getElementById('lower_case').style.color  = ''
		}
		
		if (passwd.match(/[A-ZÅÄÖ]/))                              // [verified] at least one upper case letter
		{
				document.getElementById('upper_case').style.color  = 'green'
		}
				else
		{
			document.getElementById('upper_case').style.color  = ''
		}
	
		// NUMBERS
		if (passwd.match(/\d+/))                                 // [verified] at least one number
		{
			document.getElementById('numbers').style.color  = 'green'
		}
		else
		{
			document.getElementById('numbers').style.color  = ''
		}
		
		// SPECIAL CHAR
		if (passwd.match(/[^A-Za-z0-9åäöÅÄÖ]/))            // [verified] at least one special character
		{
			document.getElementById('special').style.color  = 'green'
		}
		else
		{
			document.getElementById('special').style.color  = ''
		}
}

