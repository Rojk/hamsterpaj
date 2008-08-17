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
 	 	var remove = $('<a class="ui_module_close" href="#">x</a>').prependTo(item);
 	 	
 	 	remove.click(killModule);
 	 	minimize.click(minModule);
	}).css('cursor', 'move');
}

function killModule() {
	var m = this.parentNode.parentNode;
	
	$.post({
		url: 'close-module.php?id=' + m.getAttribute('id')
	});
	
	$(m).css('overflow', 'hidden').animate({ height: 0 }, function() {
		$(this).remove();
	});
}

function minModule() {
	var m = this.parentNode.parentNode;
	
	$.post({
		url: 'min-module.php?id=' + m.getAttribute('id')
	});
	
	$(m).children('*:not(.ui_module_header)').slideToggle();
}

$(window).ready(function() {
	$('#ui_modulebar').HPModules().HPAddModules();
});