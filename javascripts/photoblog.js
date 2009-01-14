hp.photoblog = {
	upload:
	{
		flash_upload:
		{
			new_file: function(photo_id, photo_filename)
			{
				hp.photoblog.upload.photo_properties.create_photo({
					photo_id: photo_id,
					photo_filename: photo_filename
				});
			},
			
			onProgress: function(photo_id, progress)
			{
				hp.photoblog.upload.photo_properties.update_photo_status({
					type: 'upload_progress',
					photo_id: photo_id,
					progress: progress
				});
			},
			
			onComplete: function(photo_id)
			{
				hp.photoblog.upload.photo_properties.update_photo_status({
					type: 'upload_complete',
					photo_id: photo_id
				});
			}
		},
		
		photo_properties:
		{
			photos: new Array(),
			create_photo: function(params)
			{
				hp.photoblog.upload.photo_properties.photos[hp.photoblog.upload.photo_properties.photos.length] = params;
				
				var properties_div = $(document.createElement('div')).appendTo('#photoblog_photo_properties_container');
				$(properties_div)
					.attr('id', 'photoblog_photo_properties_' + params.photo_id)
					.attr('className', 'photoblog_photo_properties')
					.html('<div class="photoblog_photo_properties_uploading">'
						+ '<h2>Laddar upp ' + unescape(params.photo_filename) + '</h2>'
						+ '<div class="photoblog_photo_properties_uploading_progress_bar">Startar...</div>'
					+ '</div>');
					
				$('#photoblog_upload_rules').hide();
				$('#photoblog_photo_properties_save').show();
			},
			
			update_photo_status: function(params)
			{
				switch(params.type)
				{
					case 'upload_progress':
						$('#photoblog_photo_properties_' + params.photo_id + ' .photoblog_photo_properties_uploading_progress_bar')
							.css('backgroundPosition', (-250 +(params.progress * 2.5)) + 'px 0px')
							.html(params.progress + '%');
					break;
					
					case 'upload_complete':
						$('#photoblog_photo_properties_' + params.photo_id)
							.html('<div class="properties">'
									+ '<input class="photoblog_photo_properties_date" type="text" name="photoblog_photo_properties_' + params.photo_id + '_date" id="photoblog_photo_properties_' + params.photo_id + '_date" value="Idag">'
									+ '<input type="checkbox" name="photoblog_photo_properties_' + params.photo_id + '_autodate" id="photoblog_photo_properties_' + params.photo_id + '_autodate" value="1" /> <label for="photoblog_photo_properties_' + params.photo_id + '_autodate">Försök avgöra ifrån bilden, annars dagens datum.</label>'
	
									+ '<textarea class="photoblog_photo_properties_description" name="photoblog_photo_properties_' + params.photo_id + '_description"></textarea>'
								+ '</div>'

								+ '<div class="float">'
									+ '<div class="thumbnail_wrapper">'
										+ '<img src="/fotoblogg/upload_redirect_to_thumbnail.php?photo_id=' + params.photo_id + '&upload_ticket=' + $('#photoblog_upload_ticket').attr('value') + '" class="thumbnail" />'
									+ '</div>'
	
									+ '<div class="rotate">'
										+ '<img src="http://images.hamsterpaj.net/photoblog/rotate_left.png" class="rotate_left" />'
										+ '<img src="http://images.hamsterpaj.net/photoblog/rotate_right.png" class="rotate_right" />'
									+ '</div>'
								+ '</div>');
						
						$('#photoblog_photo_properties_' + params.photo_id + '_autodate').click(function()
						{
							var date_field = $('#photoblog_photo_properties_' + $(this).attr('id').split('photoblog_photo_properties_')[1].split('_')[0] + '_date');
							if(this.checked == true)
							{
								date_field.attr('disabled', true);
							}
							else
							{
								date_field.attr('disabled', false);
							}
						});
						
						$('#photoblog_photo_properties_' + params.photo_id + '_date').datepicker({
							showWeeks: true,
							dateFormat: 'yy-mm-dd'
						});
					break;
				}
			}
		}
	},
	
	mousepos: {x: 0, y: 0},
	
	view: {
	/*
		.photoblog_active is always fetched dynamically, because it changes constantly 
		do indent when we are done, I'm fed up of having to scroll sideways
	*/
	init: function() {
		this.thumbsContainer = $('#photoblog_thumbs_container');
		this.thumbs = $('#photoblog_thumbs');
		this.thumbsList = $('dl', this.thumbsContainer);
		
		this.imageContainer = $('#photoblog_image');
		this.image = $('img', this.imageContainer);
		
		this.make_scroller();
		this.make_nextprev();
		this.make_ajax();
		this.make_keyboard();
		this.make_comments();
		
		this.load_hashimage();
	},
	
	make_scroller: function() {
		var self = this;
		
		// create scroller elements		
		this.thumbs.append('<div id="photoblog_thumbs_scroller">'
					+ '<div class="ui-slider-handle" id="photoblog_thumbs_handle"></div>'
				    +'</div>');
		this.scroller = $('#photoblog_thumbs_scroller');
		
		// this has to be dynamically set because it's (probably) extremely slow
		this.thumbsContainer.sWidth = this.thumbsContainer.container_width();
		this.thumbsContainer.real_width = this.thumbsContainer.width();
		
		this.scroller.slider({
			//animate: true,
			slide: function(e, ui) {
				// calculate our own percentage, n / 100 is not precise enough
				var percent = self.handle.position().left;
				percent = percent / (self.scroller.pWidth);
				self.thumbsContainer.scrollLeft(self.thumbsContainer.sWidth * percent);// - self.thumbsContainer.real_width);
			},
			steps: 5000
		});
		
		this.handle = $('#photoblog_thumbs_handle', this.scroller);
		
		this.centralize_active();
		this.set_scroller_width();
		
		this.scroller.pWidth = this.scroller.width() - this.handle.width();
	},
	
	make_nextprev: function() {
		var timer;
		var self = this;
		
		// create next prev elements
		var html = '<a href="#" id="photoblog_prev">F&ouml;reg&aring;ende</a><a href="#" id="photoblog_next">N&auml;sta</a>';
		this.imageContainer.append(html);
		
		this.prev = $('#photoblog_prev').hide();
		this.next = $('#photoblog_next').hide();
		
		this.prevnext = $('#photoblog_prev, #photoblog_next');
		
		var prev = this.prev;
		var next = this.next;
		
		var active_id = $('.photoblog_active', this.thumbsContainer);
		if ( active_id.length ) {
			active_id = active_id.attr('rel').replace('imageid_', '');
			this.set_prevnext(active_id);
		}
		
		this.imageContainer.mousemove(function(e) {
			e = e || window.event;
			var xPos = e.clientX - self.imageContainer.position().left;
			var half = self.imageContainer.width() / 2;
			
			// right (next)
			if ( xPos > half + 30 && next.css('display') == 'none' ) {
				next.css('display', 'block').animate({opacity: .6});
				prev.fadeOut();
			// left (prev)
			} else if ( xPos < half - 30 && prev.css('display') == 'none' ) {
				prev.css('display', 'block').animate({opacity: .6});
				next.fadeOut();
			} else if ( xPos > half - 30 && xPos < half + 30 ){
				next.fadeOut();
				prev.fadeOut();
			}
			clearTimeout(timer);
		});
		
		$(document).mousemove(function(e) {
			e = jQuery.event.fix(e || window.event);
			hp.photoblog.mousepos = {
				x: e.pageX,
				y: e.pageY
			};
		});
		
		this.imageContainer.mouseout(function(e) {
			timer = setTimeout(function() {
				var pos = hp.photoblog.mousepos;
				var el = self.imageContainer;
				var elPos = el.position();
				if (
					(pos.x < elPos.left || pos.x > elPos.left + el.width())
					&& (pos.y < elPos.top || pos.y > elPos.top + el.height())
				) {
					next.fadeOut();
					prev.fadeOut();
				}
			}, 300)
		});
		
		this.centralize_prevnext();
	},
	
	make_ajax: function(thumbsOnly) {
		// things to ajaxify:
		//		next/prev
		//		thumbs
		// things to update
		//		image
		// 		description
		//		comments
		
		var self = this;
		
		var click_callback = function(e) {
			var t = $(this);
			var id = t.attr('rel').replace('imageid_', '');
			
			if (
				self.current_id == id
				&& ((t.attr('id') == 'photoblog_prev' && self.load_prev_month)
				|| (t.attr('id') == 'photoblog_next' && self.load_next_month))
			) {
				// load next/prev month
				// load first/last image from collection
				// this means load month will have to return the collection, I think
				var is_prev = (self.load_prev_month && t.attr('id') == 'photoblog_prev');
				self.load_month(
					hp.photoblog.current_user.id,
					hp.photoblog.year_month['get_' + (t.attr('id') == 'photoblog_prev' ? 'prev' : 'next') + '_date'](),
					function (data) {
						var image_id = (is_prev) ? data[data.length - 1] : data[0];
						console.log('image_id:', image_id);
						self.load_image(image_id);
					}
				);
			} else {
				self.load_image(id);
			}
		};
		
		var thumbs = $('#photoblog_thumbs a[rel^=imageid_]');
		thumbs.click(click_callback);
		
		if ( ! thumbsOnly) {
			this.prevnext.click(click_callback);
		}
	},
	
	make_ajax_thumbs: function() {
		this.make_ajax(true);
	},
	
	make_keyboard: function() {
		var self = this;
		
		var table = {
			37: 'left',
			38: 'up',
			39: 'right',
			40: 'down'
		};
		
		$(document).keydown(function(e) {
			e = e || window.event;
			var key = table[e.keyCode];
			
			switch (key) {
				case 'left':
					self.prev.click();
					return false;
				break;
				case 'right':
					self.next.click();
					return false;
				break;
			}
		});
	},
	
	make_comments: function() {
		$('.photoblog_comment_text textarea').focus(function() {
			if ( ! this.has_changed ) {
				this.orig_value = this.value;
				this.value = '';
				this.has_changed = true;
			}
			
			if ( this.value == this.orig_value ) {
				this.value = '';
			}
		}).blur(function() {
			if ( this.value == '' ) {
				this.value = this.orig_value;
				this.is_orig = true;
			}
		});
	},
	
	// import future
	make_cache: function() {
		this.cache = $('<div style="display: none" id="photoblog_cache"></div>').appendTo(document.body);
	},
	
	add_to_cache: function(id, image, description) {
		var cacheElement = $('<div id="photoblog_cache_' + id + '"></div>').appendTo(this.cache);
		image.clone().appendTo(cacheElement);
		description.clone().appendTo(cacheElement);
	},
	
	in_cache: function(id) {
		var cache = $('#photoblog_cache_' + id);
		if ( ! cache.length ) return false;
		return {
			'image': $('img', cache),
			'description': $('div', cache)
		};
	},
	
	set_active: function(active) {
		$('#photoblog_thumbs .photoblog_active').removeClass('photoblog_active');
		$(active).addClass('photoblog_active');
	},
	
	set_scroller_width: function() {
		var outerWidth = this.thumbsContainer.width();
		var innerWidth = this.thumbsContainer.container_width() + outerWidth;
		if ( innerWidth <= outerWidth ) {
			this.handle.css('width', '100%');
		} else {
			var w = outerWidth / innerWidth * outerWidth;
			this.handle.css('width', Math.max(w, 40));
		}
	},
	
	centralize_prevnext: function() {
		//var imgH = this.image.height() / 2;
		//var top = imgH - this.prevnext.height() / 2;
		
		//this.prevnext.animate({'background-position': '(top)'});
	},
	
	centralize_active: function() {
		var thumbsContainer = this.thumbsContainer;
		var active = $('.photoblog_active', thumbsContainer);
		if ( ! active.length || thumbsContainer.sWidth < thumbsContainer.real_width ) {
			this.scroller.slide_slider(0);
			return;
		}
		var position = ((active.position().left + (active.width() / 2) - (thumbsContainer.real_width / 2)) / thumbsContainer.sWidth) * 100;
		this.scroller.slide_slider(position);
	},
	
	set_data: function(options) {
		var description = $('#photoblog_description');
		var text = $('#photoblog_description_text');
		
		text.html(options.description);
		if ( options.description == 'Ingen beskrivning' || options.description == '' ) {
			description.css('display', 'none');
		} else {
			description.css('display', 'block');
		}
	},
	
	set_image: function(id) {
		var src = hp.photoblog.make_name(id);
		var self = this;
		var img = $('<img />');
		self.centralize_active();
		img.load(function() {
			self.remove_load();
			img.fadeInOnAnother(self.image, function() {
				self.image.remove();
				self.image = img.css('zIndex', 0);
				self.centralize_prevnext();
				self.image.parent().height('auto');
			}, self.image.parent());
		}).attr('src', src).hide().appendTo(self.imageContainer);
	},
	
	set_prevnext: function(id) {
		var cimg = 'a[rel=imageid_' + id + ']';
		var prevnext = this.get_prevnext_a(cimg);
		
		var prev_image = prevnext[0];
		var next_image = prevnext[1];
		
		if ( prev_image.attr('rel') ) {
			var rel = prev_image.attr('rel');
			var prev_id = rel.replace('imageid_', '');
			var url = '#image-' + prev_id
			if ( prevnext[2] ) {
				this.load_prev_month = true;
			} else {
				this.load_prev_month = false;
			}
			this.prev.attr('rel', rel).attr('href', url);
		}
		
		if ( next_image.attr('rel') ) {
			var rel = next_image.attr('rel');
			var next_id = rel.replace('imageid_', '');
			var url = '#image-' + next_id;
			if ( prevnext[3] ) {
				this.load_next_month = true;
			} else {
				this.load_next_month = false;
			}
			this.next.attr('rel', rel).attr('href', url);
		}
	},
	
	get_prevnext_a: function(from) {
		var current_image = $(from);
		var cp = current_image.parent();
		
		
		// get previous image
		var prev_image = cp.prev();
		if ( prev_image[0].tagName == 'DT' ) prev_image = prev_image.prev();
		
		if ( prev_image.attr('id') == 'photoblog_prevmonth' ) prev_image = current_image;
		else prev_image = prev_image.children('a');
	
		// get next image
		var next_image = cp.next();
		if ( next_image[0].tagName == 'DT' ) next_image = next_image.next();
		
		if ( next_image.attr('id') == 'photoblog_nextmonth' ) prev_image = current_image;
		else next_image = next_image.children('a');
		
		// is the same image
		var is_first = cp.hasClass('first-image');
		var is_last = cp.hasClass('last-image');
		
		return [prev_image, next_image, is_first, is_last];
	},
	
	create_load: function() {
		var self = this;
		
		if ( self.loader ) {
			self.loader.css('top', self.image.height() / 2);
			self.loader.css('visibility', 'visible');
		} else {
			self.loader = $('<img id="photoblog_loading" />').attr('src', 'http://images.hamsterpaj.net/photoblog/loading.gif');
			self.loader.css({
				zIndex: 100,
				position: 'absolute',
				top: self.image.height() / 2,
				left: self.imageContainer.width() / 2
			});
			
			self.loader.appendTo(self.imageContainer);
		}
	},
	
	remove_load: function() {
		if ( this.loader ) {
			this.loader.css('visibility', 'hidden');
		}
	},
	
	load_image: function(id) {
		var self = this;
		this.current_id = id;
		var json_callback = function(data) {
			self.set_data(data[0]);
		};
		
		this.set_prevnext(id);		
		this.set_active('a[rel=imageid_' + id + ']');
		this.set_image(id);
		this.create_load();
		$.getJSON('/ajax_gateways/photoblog.json.php?id=' + id, json_callback);
	},
	
	load_hashimage: function() {
		var hash = window.location.hash;
		if ( hash.indexOf('#image-') != -1 ) {
			var id = parseInt(hash.replace('#image-', ''), 10);
			if ( isNaN(id) ) {
				alert('Erronous image #ID');
				return;
			}
			this.load_image(id);
		}
	},
	
	load_month: function(user_id, month, callback) {
		var self = this;
		var nextMonth = $('#photoblog_nextmonth');		
		$.getJSON('/ajax_gateways/photoblog.json.php?id=' + user_id + '&month=' + month, function(data) {
			self.thumbsList.children().not('#photoblog_prevmonth, #photoblog_nextmonth').remove();
			var lastDay = null;
			$.each(data, function(i, item) {
				var date = item.date.split('-');
				if ( date[2] != lastDay ) {
					lastDay = date[2];
					var dt = $('<dt>' + parseInt(date[1], 10) + '/' + parseInt(date[2], 10) + '</dt>')
					nextMonth.before(dt);
				}
				var photoname = hp.photoblog.make_thumbname(item.id);
				
				var className = '';
				if ( i == 0 ) className = ' class="first-image"';
				else if ( i == data.length - 1 ) className = ' class="last-image"';
				
				var dd = $('<dd' + className + '><a rel="imageid_' + item.id + '" href="#image-' + item.id + '"></a></dd>');
				nextMonth.before(dd);
				var img = $('<img alt="" />');
				
				if ( i == data.length - 1 ) {
					img.load(function() {
						self.thumbsContainer.sWidth = self.thumbsContainer.container_width();
						self.set_scroller_width();
						self.scroller.pWidth = self.scroller.width() - self.handle.width();
						if ( typeof callback == 'function' ) {
							callback(data);
						}
					});
				}
				img.attr('src', photoname);
				img.appendTo(dd.children('a'))
			});
			self.make_ajax_thumbs();
		});
	}
	// end .view
	},
	
	year_month: {
		years: [],
		
		init: function() {
			if ( ! $('#photoblog_select_year') ) return;
			
			var self = this;
			
			var year = $('#photoblog_select_year');
			var months = $('#photoblog_select_months').children('select');
			
			this.year = year;			
			months.each(function() {
				self.years[self.years.length] = $(this);
			});
			
			this.show(year.get(0).value);
			this.current_month = this.current_month_select.val();
			year.change(function() {
				self.show(this.value);
				self.load(self.current_month_select.val());
			});
			
			months.change(function() {
				self.load(this.value.toString());
			});
		},
		
		load: function(month) {
			this.current_month = month;
			hp.photoblog.view.load_month(hp.photoblog.current_user.id, this.current_year.toString() + month);
		},
		
		show: function(new_year) {
			this.current_year = new_year;
			for ( var i = 0, year; year = this.years[i]; i++ ) {
				if ( year.attr('id') == 'photoblog_select_month_' + new_year ) {
					year.css('display', 'inline');
					this.current_month_select = year;
				} else {
					year.css('display', 'none');
				}
			}
		},
		
		get_x_date: function(type) {
			var delta = (type == 'next') ? -1 : 1;
			
			var month_index = this.current_month_select[0].selectedIndex;
			var year_index = this.year[0].selectedIndex;
			var years_available = this.current_month_select[0].options.length - 1;
			
			// we need to select a new year
			if ( (type == 'prev' && month_index == 0) || (type == 'next' && month_index == years_available) ) {
				if ( type == 'next' ) console.log('last month fucker');
				// out of luck, mate
				if ( (type == 'prev' && year_index == years_available) || (type == 'next' && year_index == 0) ) {
					return false;
				} else {
					var new_year = this.years[year_index + delta]; // -- type dependant
					var new_month = new_year[0]; // the <select> for the new month
					var value_year = this.year[0].options[year_index + delta].value; // -- type dependant
					var value_month = new_month.options[new_month.options.length - 1].value; // -- ?
					return value_year + value_month;
				}
			} else {
				var value_year = this.current_year;
				var new_month = this.years[year_index][0];
				var value_month = new_month.options[month_index - delta].value; // -- ???
				return value_year + value_month;
			}
			return false;
		},
		
		get_next_date: function() {
			return this.get_x_date('next');
		},
		
		get_prev_date: function() {
			return this.get_x_date('prev');
		}
	},
	
	make_name: function(id) {
		return 'http://images.hamsterpaj.net/photos/full/' + Math.floor(parseInt(id, 10) / 5000) + '/' + id + '.jpg';
	},
	
	make_thumbname: function(id) {
		return 'http://images.hamsterpaj.net/photos/mini/' + Math.floor(parseInt(id, 10) / 5000) + '/' + id + '.jpg';
	}
};

jQuery.fn.extend({
	// small effects :)
	slide_slider: function(to) {
		var slider = $(this).slider('moveTo', to);
		
		/*// setup
		var fps = 20;
		var duration = 1000;
		
		var from = slider.slider('value');
		var change = to - from;
		var startTime = new Date().getTime();
		
		slider.slide_interval = setInterval(function() {
			var delta = ((new Date()).getTime() - startTime) / duration;
				
			// stop
			if ( delta > 1 ) {
				clearInterval(slider.slide_interval);
				slider.slider('moveTo', to);
				return false;
			}
			
			// transition
			//delta = Math.pow(delta, 2);
			
			slider.slider('moveTo', change * delta + from);
			return true;
		}, 1000 / fps);*/
	},
	
	container_width: function() {
		//var width = 0;
		//$(this).children().each(function() {
		//	width += $(this).width();
		//});
	 	var thumbsContainer = $(this);
		var lastChild = $('#photoblog_nextmonth');
		var width = lastChild.position().left + lastChild.width() - thumbsContainer.width();
		return width;
	},
	
	fadeInOnAnother: function(theOther, callback, a_parent) {
		var t = $(this).css({position: 'relative', zIndex: 1});
		var h = t.height();//Math.max(t.height(), theOther.height());
		var parent = a_parent || t.parent();
		parent.animate({'height': h + 2});
		var p = theOther.position();
		theOther.css({
			position: 'absolute',
			top: p.top,
			left: p.left,
			zIndex: 0
		});
		t.insertBefore(theOther);
		theOther.fadeOut();
		t.fadeIn(callback);
	}
});

$(window).load(function() {
	if ( $('#photoblog_image').length ) {
		hp.photoblog.view.init();
		hp.photoblog.year_month.init();
	}
});