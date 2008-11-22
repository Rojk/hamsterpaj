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
	
	view: {
		/*
			.photoblog_active is always fetched dynamically, because it changes constantly 
		*/
		init: function() {
			this.thumbsContainer = $('#photoblog_thumbs_container');
			this.thumbs = $('#photoblog_thumbs');
			
			this.imageContainer = $('#photoblog_image');
			this.image = $('img', this.imageContainer);
			
			this.make_scroller();
			this.make_nextprev();
			this.make_ajax();
		},
		
		make_scroller: function() {
			var self = this;
			
			// create scroller elements		
			this.thumbs.append('<div id="photoblog_thumbs_scroller"><div class="ui-slider-handle" id="photoblog_thumbs_handle"></div></div>');
			this.scroller = $('#photoblog_thumbs_scroller');
			
			// this has to be dynamically set because it's (probably) extremely slow
			this.thumbsContainer.sWidth = this.thumbsContainer.container_width();
			
			this.scroller.slider({
				//animate: true,
				slide: function(e, ui) {
					// calculate our own percentage, n / 100 is not precise enough
					var percent = self.handle.position().left;
					percent = percent / (self.scroller.pWidth);
					
					self.thumbsContainer.scrollLeft(self.thumbsContainer.sWidth * percent);
				},
				steps: 5000
			});
			
			this.handle = $('#photoblog_thumbs_handle', this.scroller);
			this.scroller.pWidth = this.scroller.width() - this.handle.width();
			
			this.centralize_active();
			this.set_scroller_width();
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
			
			this.imageContainer.mousemove(function(e) {
				e = e || window.event;
				var xPos = e.clientX - self.imageContainer.position().left;
				var half = self.imageContainer.width() / 2;
				
				// right (next)
				if ( xPos > half + 30 && next.css('display') == 'none' ) {
					next.fadeIn();
					prev.fadeOut();
				// left (prev)
				} else if ( xPos < half - 30 && prev.css('display') == 'none' ) {
					prev.fadeIn();
					next.fadeOut();
				} else if ( xPos > half - 30 && xPos < half + 30 ){
					next.fadeOut();
					prev.fadeOut();
				}
				clearTimeout(timer);
			});
			
			this.imageContainer.mouseout(function() {
				timer = setTimeout(function() {
					next.fadeOut();
					prev.fadeOut();
				}, 300)
			});
			
			this.centralize_prevnext();
		},
		
		make_ajax: function() {
			// things to ajaxify:
			//		next/prev
			//		thumbs
			// things to update
			//		image
			// 		description
			//		comments
			
			var self = this;
			//var thumbsContainer = $('#photoblog_thumbs');
			//var scroller = $('#photoblog_thumbs_scroller');
			//var image = $('#photoblog_image img');
			
			var setActive = function(active) {
				$('#photoblog_thumbs .photoblog_active').removeClass('photoblog_active');
				$(active).addClass('photoblog_active');
			};
			
			var json_callback = function(data) {
				self.set_data(data[0]);
			};
			
			var click_callback = function(e) {
				var t = $(this);
				var id = t.attr('rel').replace('imageid_', '');
				
				setActive('a[rel=imageid_' + id + ']');
				//self.centralize_active();
				
				self.set_image(id);
				
				$.getJSON('/ajax_gateways/photoblog.json.php?id=' + id, json_callback);
			};
			
			var thumbs = $('#photoblog_thumbs a[rel^=imageid_]');
			thumbs.click(click_callback);
			
			this.prevnext.click(click_callback);
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
			var imgW = this.imageContainer.width() / 2;
			var top = imgW / 2 - this.prevnext.height() / 2;
			
			this.prevnext.css('top', top);
		},
		
		centralize_active: function() {
			var thumbsContainer = this.thumbsContainer;
			var active = $('.photoblog_active', thumbsContainer);
			var position = ((active.position().left + active.width() / 2 - (thumbsContainer.width() / 2)) / thumbsContainer.container_width()) * 100;
			this.scroller.slide_slider(position);
		},
		
		set_data: function(options) {
			var description = $('#photoblog_description');
			var text = $('#photoblog_description_text');
			
			//$('h2', description).text(options.header);
			text.html(options.description);
			if ( options.description == 'Ingen beskrivning' ) {
				description.css('display', 'none');
			} else {
				description.css('display', 'block');
			}
			
			$('#photoblog_prev').attr('rel', 'imageid_' + options.prev_id);
			$('#photoblog_next').attr('rel', 'imageid_' + options.next_id);
		},
		
		set_image: function(id) {
			var src = hp.photoblog.make_name(id);
			var self = this;
			$('<img />').load(function() {
				self.centralize_active();
				self.imageContainer.fadeOut(function() {
					self.image.attr('src', src);
					self.imageContainer.fadeIn();
					self.centralize_prevnext();
				});
			}).attr('src', src);
		}
	},
	
	make_name: function(id) {
		return 'http://images.hamsterpaj.net/photos/full/' + Math.floor(parseInt(id, 10) / 5000) + '/' + id + '.jpg';
	}
};

jQuery.fn.extend({
	// small effects :)
	slide_slider: function(to) {
		var slider = $(this);
		var tc = $('#photoblog_thumbs_container');
		var from = slider.slider('value');
		var change = to - from;
		var duration = 500;
		var startTime = (new Date().getTime());
		slider.slide_interval = setInterval(function() {
			var nowTime = (new Date().getTime());
			var delta = (nowTime - startTime) / duration;
			
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
		}, 1000 / 30);
	},
	
	container_width: function() {
	 	var thumbsContainer = $(this);
		var lastChild = $('dl > *:last-child', thumbsContainer);
		var width = lastChild.position().left + lastChild.width() - thumbsContainer.width();
		return width;
	}
});

$(window).load(function() {
	if ( $('#photoblog_image').length ) {
		hp.photoblog.view.init();
	}
});