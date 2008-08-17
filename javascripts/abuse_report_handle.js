function handleReport(report_id)
{
	var reply_instructions = 'Glöm inte att skriva en anledning.';
	var reply = prompt(reply_instructions);
	if(reply.length < 5 && reply != "test")
	{
		alert('Rapporteringen har avbrutits, du måste skriva en ordentlig förklaring till rapporten!');
	}
	else
	{
		xmlhttp_ping('/ajax_gateways/abuse_report_handle.php?report_id=' + report_id + '&reply=' + reply);
		alert('Rapporten är fixad ;)');
		document.getElementById("abuse_report_" + report_id).innerHTML = "";
	}
}