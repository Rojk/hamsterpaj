function enable_tinymce()
{
	alert('Enabling MCE');
	tinyMCE.init({
		mode : "textareas",
		theme: "advanced",
		theme_advanced_buttons1 : "undo,redo,separator,bold,italic,underline,separator,bullist,numlist,separator,sup,charmap",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
	});
}

//womAdd('enable_tinymce()');

setTimeout('enable_tinymce()', 5000);
