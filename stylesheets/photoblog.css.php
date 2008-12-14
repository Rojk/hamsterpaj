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
    /* if the background image just looks weird just remove it */
    background: <?php echo PHOTOBLOG_BACKGROUND_COLOR; ?> repeat-x;
    border-radius: 5px;
    -moz-border-radius: 5px;
    -khtml-border-radius: 5px;
    padding: 5px 10px;
    color: #fff;
    margin: 0 auto;
}

    #photoblog_thumbs_container {
        overflow: hidden;
        position: relative;
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
	-moz-border-radius: 3px;
	-khtml-border-radius: 3px;
	border-radius: 3px;
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
    /*width: 600px;*/
    margin: 10px auto;
    position: relative;
    background: #000;
    text-align: center;
    -moz-border-radius: 4px;
  	-webkit-border-radius: 4px;
}
	#photoblog_image p {
		margin: 0;
   	padding: 4px 0 2px 0;
	}

    #photoblog_next,
    #photoblog_prev {
        display: block;
        position: absolute;
        width: 50%;
        height: 100%;
        top: 0;
        left: 0;
        overflow: hidden;
        text-indent: -10000px;
	outline: none;
    }
	
    #photoblog_next {
        right: 0;
        left: auto;
        background: url(http://images.hamsterpaj.net/photoblog/next.png) right center no-repeat;
    }
    
    #photoblog_prev {
        background: url(http://images.hamsterpaj.net/photoblog/prev.png) left center no-repeat;
    }

#photoblog_nextmonth a,
#photoblog_prevmonth a {
	text-indent: -10000px;
	display: block;
	width: 8px;
	overflow: hidden;
	height: 38px;
	background: url(http://images.hamsterpaj.net/photoblog/nextprev-month.png) left center no-repeat;
}

#photoblog_nextmonth a {
	background-position: right center;
}

#photoblog_comments,
#photoblog_description {
    margin: 0 auto 10px;
    border: 1px solid #ccc;
    background: #f9f9f9;
    padding: 10px;
}

    #photoblog_description h2 {
        text-align: center;
        margin: 0;
    }
    
#photoblog_loading {
	opacity: .6;
	filter: alpha(opacity=60);
	padding: 5px;
	border: 1px solid #333;
	background: #fff;
}

#photoblog_menu {
	background: url(http://images.hamsterpaj.net/photoblog/line-gradient.png) center bottom no-repeat;
	padding-bottom: 21px;
	text-align: center;
}

	#photoblog_menu li {
		display: inline;
	}