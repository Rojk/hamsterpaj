<?php
	// Någon funktion åt upp arrayen.
	$postarray = $_POST;

	require('../include/core/common.php');
	$ui_options['menu_path'] = array('mattan', 'ladda_ner_program');
	$ui_options['stylesheets'][] = 'loepsedel.css';

	
	ui_top($ui_options); ?>

<script type="text/javascript"><!--
jQuery.fn.resizehandle = function() {
  return this.each(function() {
    var me = jQuery(this);
    me.after(
      jQuery('<div class="resizehandle"></div>')
      .bind('mousedown', function(e) {
        var h = me.height();
        var y = e.clientY;
        var moveHandler = function(e) {
          me
          .height(Math.max(20, e.clientY + h - y))
		  .css('font-size', Math.max(20, e.clientY + h -y) + 'px')
		  .attr("name", 'text[][' + Math.max(20, e.clientY + h -y) + ']');
        }; 
        var upHandler = function(e) {	
          jQuery('html')
          .unbind('mousemove',moveHandler)
          .unbind('mouseup',upHandler);
        };
        jQuery('html')
        .bind('mousemove', moveHandler)
        .bind('mouseup', upHandler);
      })
    );
  });
}


$(document).ready(function(){
  $(".loepsedel")
  .resizehandle();
});
// --></script>

<style>
.resizehandle {
	border-bottom: 2px #ccc solid;
	border-top: 2px #eee solid;
	cursor:s-resize;
	font-size:0.1em;
	height:1px;
	width:100%;
	margin-bottom: 2px;
}
.loepsedel {
	width:100%;
	font-size: 16px;
	line-height: 0pt;
	margin: 0px;
	padding: 3px;
	font-family:Arial, Helvetica, sans-serif;
	overflow: hidden;
	text-transform: uppercase;
}
.loeptext {
	font-family:Arial, Helvetica, sans-serif;
	text-transform: uppercase; 
}
</style>
<h1>Välkommen till Hamsterpajs löpsedelfabrik!</h1>
Här kan du skapa dina egna löpsedelar som du sedan kan skicka iväg till kompisar!<br/>
<form action="?make=loepsedel" method="post">
<input type="text" name="text[][16]" class="loepsedel" />
<input type="text" name="text[][16]" class="loepsedel" />
<input type="text" name="text[][16]" class="loepsedel" />
<input type="text" name="text[][16]" class="loepsedel" />
<input type="text" name="text[][16]" class="loepsedel" />
<input name="" type="submit" value="Skapa löpsedel!" />
</form>
<div style="background: #fee904; color: #000000; text-align: center; padding-bottom: 50px;">
    <div style="background: #000000; color: #FFFFFF; font-family: Arial Black, Helvetica, sans-serif; font-size: 50px;font-stretch: wider; letter-spacing: 15px;">
        <em>EXTRA</em>
    </div>
	<?php
		if($_SERVER['REQUEST_METHOD'] == 'POST' && $_GET['make'] == 'loepsedel')
	{
		$ser = array();
		foreach($postarray as $key => $value)
		{
			foreach($value as $k => $v)
			{
				$arr = array_keys($v);
				$ser = array('size' => $arr[0], 'text' => strtoupper($v[$arr[0]]));
				echo '<div class="loeptext" style="font-size: ' . $arr[0] . 'px;">'. strtoupper($v[$arr[0]]) .'</div>';
			}
		}	
	}

	?>
</div>
	<?php ui_bottom(); ?>


