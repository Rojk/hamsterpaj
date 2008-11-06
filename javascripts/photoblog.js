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
								+ '<p>Datepicker - save - set today | Select album - Create album</p><p>WYSIWYG-editor tinymce</p><p>Save</p></div>'
								+ '<div class="float">'
								+ '<div class="thumbnail_wrapper">'
								+ '<img src="http://images.hamsterpaj.net/photos/thumb/8/42818.jpg" class="thumbnail" />'
								+ '</div>'
								+ '<div class="rotate">'
								+ '<img src="" class="rotate_left" />'
								+ '<img src="" class="rotate_right" />'
								+ '</div>'
								+ '</div>');
					break;
				}
			}
		}
	}
};

jQuery.fn.extend({
	photoblog_scroller: function() {
		var thumbs = $('#photoblog_thumbs');
		var thumbsContainer = $('#photoblog_thumbs_container');
		
		// create scroller elements		
		thumbs.append('<div id="photoblog_thumbs_scroller"><div class="ui-slider-handle" id="photoblog_thumbs_handle"></div></div>');

		var scroller = $('#photoblog_thumbs_scroller');
		
		scroller.slider({
			slide: function(e, ui) {
			 	var percent = ui.value / 100;
			 	var width = thumbsContainer.container_width();
				thumbsContainer.scrollLeft(width * percent);
			}
		});
		
		scroller.centralize_active();
		
		return this;
	},
	
	photoblog_nextprev: function() {
	 	var timer;
		var imageContainer = $('#photoblog_image');
	 	
		// create next prev elements
		var html = '<a href="#" id="photoblog_prev">F&ouml;reg&aring;ende</a><a href="#" id="photoblog_next">N&auml;sta</a>';
		imageContainer.append(html);
		
		$(this).centralize_prevnext();
		
		var prev = $('#photoblog_prev').hide();
		var next = $('#photoblog_next').hide();
		
		var callback = function() {
		//	$(this).css('display', 'block')
		}
		
		imageContainer.mousemove(function(e) {
		 	e = e || window.event;
		 	var xPos = e.clientX - imageContainer.position().left;
		 	var half = imageContainer.width() / 2;
		 	
			// right (next)
			if ( xPos > half + 30 && next.css('display') == 'none' ) {
				next.fadeIn(callback);
				prev.fadeOut();
			// left (prev)
			} else if ( xPos < half - 30 && prev.css('display') == 'none' ) {
				prev.fadeIn(callback);
				next.fadeOut();
			} else if ( xPos > half - 30 && xPos < half + 30 ){
				next.fadeOut();
				prev.fadeOut();
			}
			clearTimeout(timer);
		});
		
		imageContainer.mouseout(function() {
		 	timer = setTimeout(function() {
				next.fadeOut();
				prev.fadeOut();
			}, 300)
		});
		
		return this;
	},
	
	centralize_prevnext: function() {
		var imageContainer = $('#photoblog_image');
		var prevnext = $('#photoblog_prev, #photoblog_next');
		
		var imgW = imageContainer.width() / 2;
		var top = imgW / 2 - prevnext.height() / 2;
		
		prevnext.css('top', top);
	},
	
	centralize_active: function() {
	 	var slider = $(this);
		var thumbsContainer = $('#photoblog_thumbs_container');
		var active = $('.photoblog_active', thumbsContainer);
		var position = ((active.position().left + active.width() / 2 - (thumbsContainer.width() / 2)) / thumbsContainer.container_width()) * 100;
		slider.slide_slider(position);
	},

	// small effects :)
	slide_slider: function(to) {
		var slider = $(this);
		var from = slider.slider('value');
		var change = to - from;
		var duration = 750;
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
		}, duration / 50);
	},
	
	container_width: function() {
	 	var thumbsContainer = $(this);
		var lastChild = $('dl > *:last-child', thumbsContainer);
		var width = lastChild.position().left + lastChild.width() - thumbsContainer.width();
		window.container_width = width;
		return width;
	},
	
	photoblog_ajax: function() {
		// things to ajaxify:
		//		next/prev
		//		thumbs
		// things to update
		//		image
		// 		description
		//		comments
		
		var thumbsContainer = $('#photoblog_thumbs');
		var scroller = $('#photoblog_thumbs_scroller');
		
		var setActive = function(active) {
			$('#photoblog_thumbs .photoblog_active').removeClass('photoblog_active');
			$(active).addClass('photoblog_active');
		};
		
		var json_callback = function(data) {
			console.dir(data)
			photoblog_set(data);
		};
		
		var click_callback = function(e) {
		 	var t = $(this);
		 	var id = t.attr('rel').replace('imageid_', '');
			setActive('a[rel=imageid_' + id + ']');
			scroller.centralize_active();
		 	$.getJSON('ajax_gallery.php?id=' + id, json_callback);
			return false;
		};
		
		var thumbs = $('#photoblog_thumbs a[rel^=imageid_]');
		thumbs.click(click_callback);
		
		var prevNext = $('#photoblog_next, #photoblog_prev');
		prevNext.click(click_callback);
	}
});

function photoblog_set(options) {
	var description = $('#photoblog_description');
	
	$('h2', description).text(options.header);
	$('#photoblog_description_text').html(options.description);
	$('#photoblog_prev').attr('rel', 'imageid_' + options.prev_id);
	$('#photoblog_next').attr('rel', 'imageid_' + options.next_id);
	$('#photoblog_image img').attr('src', options.src);
};

$(document).ready(function() {
	if ( $('#photoblog_image').length ) {
		$().photoblog_scroller()
		   .photoblog_nextprev()
		   .photoblog_ajax();
	}
});