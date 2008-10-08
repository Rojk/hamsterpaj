jQuery.fn.extend({
	HPModules: function() {
		doCloseMin();
		
		this.sortable({
			opacity: .6,
			revert: true,
			revertDuration: 100,
			
			handle: 'h2',
			items: '.ui_module',
			
			start: function() {
			 
			},
			
			stop: function() {
				// because I dont understand jQuery UI's serialize-function I'll do it myself.
				// FUCK IT! WE'll DO IT LIVE!
				var str = '', i = 0;
				$('#ui_modulebar div[id^=ui_module]').each(function() {
					str += this.getAttribute('id').replace('ui_module_', '') + '|';
				});
				str = str.replace(/\|$/, '');
				$.get('/ajax_gateways/save_module_order.php?' + str);
			}
		});
		
		return this;
	},
	
	HPAddModules: function() {
		var container = $(
			'<div id="modules-add"><h2 class="main-header">L&auml;gg till moduler</h2><div id="add-spacer">&nbsp;</div><p>H&auml;r kan du dra och sl&auml;ppa moduler till din h&ouml;germodul.<div>Laddar...</div></div>'
		).prependTo(this).hide();
		
		$('#modules-add-link').click(function(e) {
		 	if ( ! $('#modules-add-container', container).length ) {
		 	 	container.slideDown('normal', function() {
					$('div:last-child', container).load('modules.php', {}, function() {
					 	doCloseMin();
					 	$('#modules-add-container', container).sortable({
							connectWith: ['#module_bar']
						});
						
						container.slideDown();
					});					
				});
			} else {
				container.slideToggle();
			}
			
			return false;
		});
	}
});

function doCloseMin() {
 	$('.ui_module_header').each(function(index, item) {
		if ( $(item).children('a').length > 0 ) return;
		
		var minimize = $('<a class="ui_module_minimize" href="#">_</a>').prependTo(item);
 	 	//var remove = $('<a class="ui_module_close" href="#">x</a>').prependTo(item);
 	 	
 	 	//remove.click(killModule);
 	 	minimize.click(minModule);
		
		$('h2', this).css('cursor', 'move');
	});
}

function killModule() {
	var m = this.parentNode.parentNode;
	
	$.get('/ajax_gateways/save_module_state.php?module=' + m.getAttribute('id').replace('ui_module_', '') + '&state=kill');
	
	$(m).css({
			'overflow': 'hidden',
			'width': $(m).width() - 5
	}).animate({ height: 0, width: 0 }, function() {
		$(this).remove();
	});
	return false;
}

function minModule() {
	var m = this.parentNode.parentNode;
	m = $(m);
	
	m.children('.ui_module_content').slideToggle(null, function() {
		var state = (m.hasClass('ui_module_state_min')) ? 'max' : 'min';
		$.get('/ajax_gateways/save_module_state.php?module=' + m.attr('id').replace('ui_module_', '') + '&state=' + state);
		m.removeClass('ui_module_state_max').removeClass('ui_module_state_min').addClass('ui_module_state_' + state);
	});
	return false;
}

$(window).ready(function() {
	$('#ui_modulebar').HPModules().HPAddModules();
});