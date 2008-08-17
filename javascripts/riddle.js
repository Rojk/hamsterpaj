function riddle_answer()
{
	alert('Du får klura i en halv minut till, sedan visas svaret!');
	setTimeout('document.getElementById("riddle_answer").style.display = "block"', 30000);
	document.getElementById('riddle_answer_button').style.display = 'none';
}