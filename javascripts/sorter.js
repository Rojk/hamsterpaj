/*
	jQuery Sorter class
	
	Because jQuery UI sortable is slow.
*/

var Sorter = function(elements, parents) {
	var self = this;
	
	this.setElements(elements);
	this.createGraveyard();
	this.parents = $(parents);
	
	this.eventMousemove = function(e) {
		return self.mousemove(e);
	};
	
	this.eventMouseup = function(e) {
		return self.mouseup(e);
	};
	
	this.direction = {left: true, up: true};
};

Sorter.prototype.setElements = function(elements) {
	var self = this;
	
	this.elements = $(elements);
	this.elements.mousedown(function(e) {
		return self.mousedown.call(self, e, this);
	});
	
	this.jElements = [];
	this.elements.each(function() {
		self.jElements[self.jElements.length] = $(this).css('position', 'relative');
		
		// Because opera likes to drag images
		$('<div style="position: absolute; top: 0; bottom: 0; left: 0; right: 0; width: 100%" />').appendTo(this);
	});
	
	this.posCache = [];
	this.updateCache();
};

Sorter.prototype.createGraveyard = function() {
	this.graveyard = $('<div style="list-style-type: none" />').appendTo(document.body);
};

Sorter.prototype.setActive = function(element) {
	this.activeElement = $(element);
	this.createGhost(this.activeElement);
	this.activeElement.addClass('sort-active');
};

Sorter.prototype.releaseActive = function() {
	this.activeElement.removeClass('sort-active');
	this.activeElement = null;
	this.ghost.remove();
};

Sorter.prototype.createGhost = function(from) {
	this.ghost = from.clone().appendTo(this.graveyard)
		.addClass('sort-ghost')
		.css('position', 'absolute')
		.css(this.realOffset(from));
};

Sorter.prototype.mousedown = function(e, element) {
 	e.preventDefault();
 	
 	// this because webkit needs it. It will cause a slight jump because updateCache is slooow.
	if ( jQuery.browser.safari ) {
		this.updateCache();
	}
	 	
	this.setActive(element);
	this.startDelta = {x: this.ghost.offset().left - e.pageX, y: this.ghost.offset().top - e.pageY};
	
	$(document).mousemove(this.eventMousemove)
		.mouseup(this.eventMouseup);
	
	this.prev = {x: e.pageX, y: e.pageY};
	
	return false;
};

Sorter.prototype.mousemove = function(e) {
 	e.preventDefault();
 	
 	var pos = {x: e.pageX, y: e.pageY};
 
	this.ghost.css({
		left: this.startDelta.x + pos.x,
		top: this.startDelta.y + pos.y
	});

	this.direction = {
		left: (this.prev.x === pos.x) ? this.direction.left : (pos.x < this.prev.x),
		up: (this.prev.y === pos.y) ? this.direction.top : (pos.y < this.prev.y)
	};
	
	this.sort(pos);
	this.prev = pos;
	
	return false;
};

Sorter.prototype.mouseup = function() {
	$(document).unbind('mousemove', this.eventMousemove)
		.unbind('mouseup', this.eventMouseup);
	
	this.releaseActive();
};

Sorter.prototype.sort = function(ref) {
 	var self = this;
	this.parents.each(function() {
	 	if ( this === self.activeElement.parent()[0] ) return;
		 
		var el = $(this);
		var pos = self.realOffset(el, true, true);
		
		if ( ref.y > pos.top && ref.y < pos.bottom ) {
			self.activeElement.appendTo(el);
		}
	});
 
	for ( var i = 0, j = this.jElements.length; i < j; i++ ) {
		var el = this.jElements[i];
		
	 	if ( el[0] === this.activeElement[0] ) continue;
	 
		var pos = this.posCache[i];
	
		if ( pos.left < ref.x && pos.right > ref.x && pos.top < ref.y && pos.bottom > ref.y ) {
		 	if ( this.direction.left === true ) this.activeElement.insertBefore(el);
		 	else this.activeElement.insertAfter(el);
		 	this.updateCache(i);
		 	break;
		}
	}
};

Sorter.prototype.updateCache = function(upto) {
	for ( var i = Math.max(upto - 3, 0) || 0, j = Math.min(upto + 3, this.jElements.length) || this.jElements.length; i < j; i++ ) {
		this.posCache[i] = this.realOffset(this.jElements[i], true, true);
	}
};

Sorter.prototype.realOffset = function(element, realPos, slow) {
	var offset = element.offset();
	if ( ! realPos ) {
		offset.left += parseInt(element.css('padding-left'), 10);
		offset.top += parseInt(element.css('padding-top'), 10);
	}
	if ( slow ) {
		offset.right = offset.left + element.outerWidth({margin: true});
		offset.bottom = offset.top + element.outerHeight({margin: true});
	} else {
		offset.right = offset.left + element.width();
		offset.bottom = offset.top + element.height();
	}
	return offset;
};
