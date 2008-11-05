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
			},
			
			update_photo_status: function(params)
			{
				switch(params.type)
				{
					case 'upload_progress':
						$('#photoblog_photo_properties_' + params.photo_id + ' .photoblog_photo_properties_uploading_progress_bar')
							.css('backgroundPosition', (100 - params.progress) + 'px 0px')
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