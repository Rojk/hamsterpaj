<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/documentation.lib.php');

			$out .= '<h3>Server_message - ui_server_message()</h3>' . "\n";
			$out .= '<p>Creates a warning/notification</p>' . "\n";
			
			$out .= '<div id="demo_header" class="content_box_header">' . "\n";
			$out .= '<h4>Demo</h4>' . "\n";
			$out .= '</div>' . "\n";
			$out .= '<div id="demo" class="content_box">' . "\n";
			
			$out .= '<div class="example">' . "\n";
				$code = '<?php	
	$options[\'type\'] = \'notification\';
	$options[\'title\'] = \'This is a notification\';
	$options[\'message\'] = \'More information about the notification\';
	$out .= ui_server_message($options);
?>';
				$out .= highlight_string($code, true);
				$out .= '<div class="server_message_notification">' . "\n";
				$out .= '<h2>This is a notification</h2>' . "\n";
				$out .= '<p>More information about the notification</p>' . "\n";
				$out .= '</div>' . "\n";
				$out .= '</div>' . "\n";
			
			$out .= '<div class="example">' . "\n";
				$code = '<?php	
	$options[\'type\'] = \'warning\';
	$options[\'title\'] = \'This is a warning\';
	$options[\'message\'] = \'More information about the warning\';
	$out .= ui_server_message($options);
?>';
				$out .= highlight_string($code, true);
				$out .= '<div class="server_message_warning">' . "\n";
				$out .= '<h2>This is a warning</h2>' . "\n";
				$out .= '<p>More information about the warning</p>' . "\n";
				$out .= '</div>' . "\n";
				$out .= '</div>' . "\n";
				
			$out .= '<div class="example">' . "\n"; 
				$code = '<?php	
	$options[\'type\'] = \'error\';
	$options[\'title\'] = \'This is an error\';
	$options[\'message\'] = \'More information about the error\';
	$out .= ui_server_message($options);
?>';
				$out .= highlight_string($code, true);
				$out .= '<div class="server_message_error">' . "\n";
				$out .= '<h2>This is an error</h2>' . "\n";
				$out .= '<p>More information about the error</p>' . "\n";
				$out .= '</div>' . "\n";
				$out .= '</div>' . "\n";
				
				$out .= '<div class="example">' . "\n"; 
				$code = '<?php	
	$options[\'type\'] = \'system_error\';
	$options[\'title\'] = \'This is a system-error\';
	$options[\'message\'] = \'More information about the system-error\';
	$out .= ui_server_message($options);
?>';
				$out .= highlight_string($code, true);
				$out .= '<div class="server_message_system_error">' . "\n";
				$out .= '<h2>This is a system-error</h2>' . "\n";
				$out .= '<p>More information about the system-error</p>' . "\n";
				$out .= '</div>' . "\n";
				$out .= '</div>' . "\n";
			
			$out .= '<div class="example">' . "\n";
				$code = '<?php	
	$options[\'type\'] = \'notification\';
	$options[\'title\'] = \'This is a notification\';
	$options[\'message\'] = \'More information about the notification\';
	$options[\'collapse_link\'] = \'A custom link\';
	$options[\'collapse_information\'] = \'<p>More information, collapsed</p>\';
	$out .= ui_server_message($options);
?>';
				$out .= highlight_string($code, true);
				$out .= '<div class="server_message_notification">' . "\n";
				$out .= '<h2>This is a notification</h2>' . "\n";
				$out .= '<p>More information about the notification</p>' . "\n";
				$out .= '<h3 class="server_message_collapse_header" id="server_message_collapse_header_22">Show more information</h3>' . "\n";
				$out .= '<div class="server_message_collapsed_information" id="server_message_collapse_information_22">' . "\n";
					$out .= '<p>More information, collapsed</p>' . "\n";
				$out .= '</div>' . "\n";
				$out .= '</div>' . "\n";
			$out .= '</div>' . "\n";
			$out .= '</div>' . "\n";
			
			$out .= '<div id="attributes_header" class="content_box_header">' . "\n";
				$out .= '<h4>Attributes</h4>' . "\n";
			$out .= '</div>' . "\n";
			$out .= '<div id="attributes" class="content_box">' . "\n";
				$out .= '<dl>' . "\n";
					$out .= '<dt>' . "\n";
					$out .= 'Type' . "\n";
					$out .= '</dt>' . "\n";
						$out .= '<dd>' . "\n";
							$out .= '<strong>Notification</strong>, ' . "\n";
							$out .= 'Warning, ' . "\n";
							$out .= 'Error, ' . "\n";
							$out .= 'System_error' . "\n";
						$out .= '</dd>' . "\n";
					$out .= '<dt>' . "\n";
					$out .= 'Title' . "\n";
					$out .= '</dt>' . "\n";
						$out .= '<dd>' . "\n";
							$out .= '<strong>String</strong>' . "\n";
						$out .= '</dd>' . "\n";
					$out .= '<dt>' . "\n";
					$out .= 'Message' . "\n";
					$out .= '</dt>' . "\n";
						$out .= '<dd>' . "\n";
							$out .= '<strong>String</strong>' . "\n";
						$out .= '</dd>' . "\n";
					$out .= '<dt>' . "\n";
					$out .= 'Collapse_information' . "\n";
					$out .= '</dt>' . "\n";
						$out .= '<dd>' . "\n";
							$out .= '<strong>String</strong>, ' . "\n";
						$out .= '</dd>' . "\n";
					$out .= '<dt>' . "\n";
					$out .= 'Collapse_link' . "\n";
					$out .= '</dt>' . "\n";
						$out .= '<dd>' . "\n";
							$out .= '<strong>String</strong>, ' . "\n";
						$out .= '</dd>' . "\n";
						$out .= '<dt>' . "\n";
					$out .= 'Collapse_id' . "\n";
					$out .= '</dt>' . "\n";
						$out .= '<dd>' . "\n";
							$out .= '<strong>String</strong>, ' . "\n";
						$out .= '</dd>' . "\n";
				$out .= '</dl>' . "\n";
			$out .= '</div>' . "\n";
				
			$out .= '<div id="source_code_header" class="content_box_header">' . "\n";
			$out .= '<h4>Source code</h4>' . "\n";
			$out .= '</div>' . "\n";
			$out .= '<div id="source_code" class="content_box">' . "\n";
				$out .= '<h5>include/configs/ui.conf.php</h5>' . "\n";
				$code = '<?php
	$UI_SERVER_MESSAGE[\'types\'] = array(\'notification\', \'warning\', \'error\', \'system_error\');
?>';
					$out .= highlight_string($code, true);
					
					$out .= '<h5>include/lib/ui.lib.php</h5>' . "\n";
					$out .= '<h6>ui_server_message()</h6>' . "\n";
					$code = '<?php
	function ui_server_message($options)
	{
		$options[\'collapse_link\'] = (isset($options[\'collapsed_link\']) ? $options[\'collapsed_id\'] : \'Visa mer information\';
		$options[\'collapse_id\'] = (isset($options[\'collapsed_id\']) ? $options[\'collapsed_id\'] : \'rand()\'; // fix the fucking random
		$options[\'type\'] = (in_array($options[\'type\'], $SERVER_MESSAGE[\'types\'])) ? $options[\'type\'] : \'notification\';
		
		$output .= \'<div class="\' . $options[\'type\'] . \'">\';
			$output .= \'<h2>\' . $options[\'title\'] . \'</h2>\' . "\n";
			$output .= \'<p>\' . $options[\'message\'] . \'</p>\' . "\n";
			if (isset($options[\'collapse_information\']))
			{
				$output .= \'<a href="#">\' . $options[\'collapse_link\'] . \'</a>\' . "\n";
				$output .= \'<div class="collapsed_\' . $options[\'collapse_id\'] . \'">\';
					$output .= $options[\'collapse_information\'] . "\n";
				$output .= \'</div>\' . "\n";
			}
		$output .= \'</div>>\' . "\n";
		return $output;
	}
?>';
					$out .= highlight_string($code, true);
				$out .= '</div>' . "\n";
?>

<?php
	echo open_source_ui_top($open_source_ui_options);
	echo $out;
	echo open_source_ui_bottom();
?>
