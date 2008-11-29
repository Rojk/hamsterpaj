<?php
	header('Content-type: text/css');

	define('PHOTOBLOG_BACKGROUND_COLOR', '#333');
	define('PHOTOBLOG_DETAIL_COLOR', 'orange');
?>

#photoblog_upload_wrapper {
	border: 1px solid #cfe2f1;
	-moz-border-radius: 5px;
	background: #f1f5ff;
	padding: 10px;
	margin-top: 20px;
}

#photoblog_photo_properties_save
{
	display: none;
	float: right;
	clear: both;
	margin: 10px 0px 10px 0px;
}

.photoblog_photo_properties {
	border: 1px solid #cfe2f1;
	-moz-border-radius: 5px;
	background: #f1f5ff;
	margin-top: 5px;
	height: 172px;
}
	.photoblog_photo_properties_uploading {
		padding: 10px;
		text-align: center;
	}
		.photoblog_photo_properties_uploading h2 {
			font-size: 14px;
			margin-top: 50px;
		}
		.photoblog_photo_properties_uploading_progress_bar {
			width: 250px;
			text-align: center;
			margin: 0 auto;
			border: 1px solid #cfe2f1;
			-moz-border-radius: 5px;
			padding: 3px 0;
			background: #fff;
			background-image: url(http://images.hamsterpaj.net/photoblog/loader.png);
			background-repeat: no-repeat;
			background-position: -250px 0;
		}
	
	.photoblog_photo_properties .float {
		width: 176px;
	}
		.photoblog_photo_properties .thumbnail_wrapper {
			padding: 1px;
			border: 2px solid;
			width: 150px;
			margin: 10px;
		}
		.photoblog_photo_properties .rotate {
			margin: 0 55px 10px 50px;
		}
			.photoblog_photo_properties .rotate_left {
				margin-right: 10px;
				display: inline;
			}
			.photoblog_photo_properties .rotate_right {
				display: inline;
			}
	.photoblog_photo_properties .properties {
		margin-left: 166px;
		padding: 10px;
		position: absolute;
	}
		.photoblog_photo_properties_date {
			border: 1px solid #cfe2f1;
		}
		.photoblog_photo_properties_description {
			width: 450px;
			display: block;
			height: 130px;
			border: 1px solid #cfe2f1;
		}
        
#photoblog_header {
    margin: 0 0 10px 20px;
}

#photoblog_thumbs {
    background: <?php echo PHOTOBLOG_BACKGROUND_COLOR; ?> url(scroller-default-bg.png);
    border-radius: 5px;
    -moz-border-radius: 5px;
    -khtml-border-radius: 5px;
    padding: 5px 10px;
    color: #fff;
    width: 580px;
    margin: 0 auto;
}

    #photoblog_thumbs_container {
        overflow: hidden;
        position: relative;
        width: 560px;
    }

    div#photoblog_thumbs dl {
        width: 10000px;
        position: relative;
        padding: 3px 0 0 0;
    }

    #photoblog_thumbs dl,
    #photoblog_thumbs dl * {
        margin: 0;
        padding: 0;
    }
	
        #photoblog_thumbs dt,
        #photoblog_thumbs dd {
            float: left;
            margin-right: 10px;
        }
    
            #photoblog_thumbs img {
                display: block;
            }
		
		#photoblog_thumbs dt {
                    line-height: 38px;
                    margin-right: 10px;
                    font-size: 12px;
                    font-weight: bold;
		}
		
    #photoblog_thumbs dl {
        _height: 1%;
        *display: inline-block;
        _display: block;
    }

    #photoblog_thumbs dl:after {
        content: ".";
        display: block;
        height: 0;
        visibility: hidden;
        clear: both;
    }
	
    .photoblog_active img {
        border: 2px solid yellow;
    }
	
	
    .photoblog_active {
        position: relative;
        top: -2px;
    }

#photoblog_thumbs_scroller {
    width: 100%;
    background: url(http://images.hamsterpaj.net/photoblog/white-50-percent-opacity.png);
    height: 15px;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px;
    margin-top: 5px;
    position: relative;
}

    #photoblog_thumbs_handle {
        display: block;
        width: 40px;
        background: <?php echo PHOTOBLOG_DETAIL_COLOR; ?> url(http://images.hamsterpaj.net/photoblog/scroller-overlay.png) repeat-x;
        -moz-border-radius: 4px;
    	-webkit-border-radius: 4px;
        height: 100%;
        cursor: pointer;
        position: absolute;
    }
	
#photoblog_image {
    width: 600px;
    margin: 10px auto;
    position: relative;
    /*background: #000;*/
    text-align: center;
}

    #photoblog_next,
    #photoblog_prev {
        display: block;
        position: absolute;
        width: 35px;
        height: 90px;
	/* prevnext.png is not present yet and I have no idea how to add images to hamsterpaj... */
        background: url(http://images.hamsterpaj.net/photoblog/prevnext.png) left top no-repeat;
        top: 0;
        left: 10px;
        overflow: hidden;
        text-indent: -10000px;
    }
	
    #photoblog_next {
        right: 10px;
        left: auto;
        background-position: right top;
    }

#photoblog_comments,
#photoblog_description {
    width: 580px;
    margin: 0 auto 10px;
    border: 1px solid #ccc;
    background: #f9f9f9;
    padding: 10px;
}

    #photoblog_description h2 {
        text-align: center;
        margin: 0;
    }