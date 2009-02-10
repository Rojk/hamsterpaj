$(document).ready(function () {
	$('.user_warning').tooltip({
		delay: 0, 
    showURL: false, 
    bodyHandler: function() { 
        return $('#user_warning_' + $(this).attr('id')).text();
    }
	});
	$('.user_read_only').tooltip({
		delay: 0, 
    showURL: false, 
    bodyHandler: function() { 
        return $('#user_read_only_' + $(this).attr('id')).text();
    }
	});
	$('.user_info').tooltip({
		delay: 0, 
    showURL: false, 
    bodyHandler: function() { 
        return $('#user_info_' + $(this).attr('id')).html();
    }
	});
});