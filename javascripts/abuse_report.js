function abuse_report(report_type, reference_id)
{
	var confirm_messages = Array();
	confirm_messages['post'] = 'Här borde det stå en förklaringstext för vad som ska rapporteras och inte.';
	confirm_messages['user'] = 'Här borde det stå en förklaringstext för vad som ska rapporteras och inte.';
	
	report_instructions = Array();
	report_instructions['post'] = 'Ordningsvakten får en direktlänk till det rapporterade inlägget, men beskriv gärna kortfattat varför du rapporterat inlägget.';
	report_instructions['user'] = 'Glöm inte att tala om varför du rapporterar...';
	
	if(confirm(confirm_messages[report_type]))
	{
		var reason = prompt(report_instructions[report_type]);
		if(reason.length < 5)
	{
			alert('Rapporteringen har avbrutits, du måste skriva en ordentlig förklaring till rapporten!');
		}
		else
		{
			xmlhttp_ping('/ajax_gateways/abuse_report.php?action=report&report_type=' + report_type + '&reference_id=' + reference_id + '&comment=' + reason);
			alert('Tack, din rapport har sparats och en ordningsvakt kommer titta på den strax!');
		}
	}
}

function abuse_unreport(report_type, reference_id)
{
	xmlhttp_ping('/ajax_gateways/abuse_report.php?action=unreport&report_type=' + report_type + '&reference_id=' + reference_id);
	document.getElementById('abuse_report_' + report_type + '_' + reference_id).style.display = 'none';
}