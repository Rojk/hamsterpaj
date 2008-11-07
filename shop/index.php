<?php
	/* OPEN_SOURCE */
	
	require('../include/core/common.php');
	require(PATHS_INCLUDE  . 'libraries/photos.lib.php');
	
	$acceptance_text = 'Jag accepterar blaha!';
	try
	{
		if (!login_checklogin())
		{
			throw new Exception('Du måste vara inloggad och medlem för att kunna beställa en Hamsterpaj-tröja');
		}
		$ui_options['stylesheets'][] = 'shop.css';
		$ui_options['stylesheets'][] = 'forms.css';
		$ui_options['javascripts'][] = 'shop.js';
		$ui_options['title'] = 'Tröjshop - Hamsterpaj.net';
		$ui_options['menu_path'] = array('hamsterpaj');
		$post = $_POST;
		if ($_GET['action'] == 'submit')
		{
			$fields_aliases = array(
				'shop_gender' => 'tröjmodell',
				'shop_size' => 'tröjstorlek',
				'shop_real_name' => 'ditt namn',
				'shop_address' => 'din adress',
				'shop_zipcode' => 'postnummer',
				'shop_city' => 'stad',
				'shop_phone' => 'telefonnummer',
				'shop_acceptance' => 'acceptera avtalet'
			);
			$fields_cannotbe = array(
				'shop_gender' => 'choose',
				'shop_size' => 'choose'
			);
			$fields_strlen = array(
				'shop_gender',
				'shop_size',
				'shop_real_name',
				'shop_address',
				'shop_zip_code',
				'shop_phone',
				'shop_city'
			);
			$fields_is_numeric = array(
				'shop_zip_code',
				'shop_phone'
			);
			foreach ($fields_cannotbe as $field => $not_good)
			{
				if ($_POST[$field] == $not_good)
				{
					throw new Exception('Du måste välja ' . $fields_aliases[$field] . ' ');
				}
			} 
			foreach ($fields_strlen as $field)
			{
				if (strlen($_POST[$field]) == 0)
				{
					throw new Exception('Fältet "' . $fields_aliases[$field] . '" var inte ifyllt');
				}
			}
			foreach ($fields_is_numeric as $field)
			{
				$processed_field[$field] = str_replace(' ', '', $_POST[$field]);
				if (!is_numeric($processed_field[$field]))
				{
					throw new Exception('Fältet "' . $fields_aliases[$field] . '" var inte ett numeriskt värde');
				}
			}
			if ($_POST['shop_acceptance'] != 1)
			{
				throw new Exception('Du måste acceptera avtalet.');
			}
			
			// Om allt är fint så borde man hamna här till slut.
			$sql = 'INSERT INTO shop_orders SET';
			$sql .= ' user_id = ' . $_SESSION['login']['id'] . ',';
			$sql .= ' real_name = "' . $_POST['shop_real_name'] . '",';
			$sql .= ' address = "' . $_POST['shop_address'] . '",';
			$sql .= ' zip_code = "' . $processed_field['shop_zip_code'] . '",';
			$sql .= ' city = "' . $_POST['shop_city'] . '",';
			$sql .= ' size = "' . $_POST['shop_size'] . '",';
			$sql .= ' gender = "' . $_POST['shop_gender'] . '",';
			$sql .= ' phone = "' . $processed_field['shop_phone'] . '"';
			mysql_query($sql) or report_sql_error(__FILE__, __LINE__, $sql);
			
			$sql = 'SELECT order_id FROM shop_orders WHERE user_id = ' . $_SESSION['login']['id'] . ' LIMIT 1';
			$result = mysql_query($sql);
			$data = mysql_fetch_assoc($result);
			$out .= '
				<h1>Tack för din beställning!</h1>
				' . rounded_corners_top(array('color' => 'green'), true) . '
				<h2 style="margin-top: 0;">För att bekräfta betalning</h2>
				<p><strong>För att bekräfta din betalning</strong> så sätter du in <strong>200</strong> SEK på <strong>bankgiro ABC-12345</strong> med kommentaren
				<strong>HP-' . $data['order_id'] . '</strong>
				</p>
				<p style="margin-bottom: 0;">
					Din tröja bör efter att du satt in pengarna plumsa ner i din brevlåda om cirka 2-5 arbetsdagar.
				</p>
				' . rounded_corners_bottom(array('color' => 'green'), true) . '
			';
		}
		else
		{
			$out .= '
			<img src="http://images.hamsterpaj.net/shop/hamstershirt.png" />
			<img id="shirt_boy" src="http://images.hamsterpaj.net/shop/hamstershirt_boy.png" />
			<img id="shirt_girl" src="http://images.hamsterpaj.net/shop/hamstershirt_girl.png" />
			<fieldset>
				<legend>Beställ din helt egna Hamsterpaj-tröja</legend>
			<form action="?action=submit" method="post">
			    <div class="form">
				<div id="shirt">
				<table class="form">
			               	<tr>
			                    <th><label for="shop_gender">Tröjmodell <strong>*</strong></label></th>
			                    <td>
						<select name="shop_gender" id="shop_gender">
							<option value="choose">Välj</option>
							<option value="male">Kille</option>
							<option value="female">Tjej</option>
						</select>
					    </td>
			                    <th><label for="shop_size">Tröjstorlek <strong>*</strong></label></th>
			                    <td>
						<select name="shop_size" id="shop_size">
							<option value="choose">Välj</option>
							<option value="small">Small</option>
							<option value="medium">Medium</option>
							<option value="large">Large</option>
						</select>
					    </td>
			                </tr>
				</table>
				</div>
				<div id="address">
			            <table class="form">
			                <tr>
			                    <th><label for="shop_real_name">Ditt namn <strong>*</strong></label></th>
			                    <td><input id="shop_real_name" name="shop_real_name" size="20" type="text" /></td>
			                </tr>
			
			                <tr>
			                    <th><label for="shop_address">Din adress <strong>*</strong></label></th>
			                    <td><input id="shop_address" name="shop_address" size="30" type="text" /></td>
			                </tr>
					<tr>
			                    <th><label for="shop_zip_code">Postnr <strong>*</strong></label></th>
			                    <td><input id="shop_zip_code" name="shop_zip_code" size="8" type="text" />
											</tr>
					<tr>
			                    <th><label for="shop_city">Postort <strong>*</strong></label></th>
			                    <td><input id="shop_city" name="shop_city" size="16" type="text" /></td>
			                </tr>
					<tr>
			                    <th><label for="shop_phone">Telefonnummer <strong>*</strong></label></th>
			                    <td><input id="shop_phone" name="shop_phone" size="20" type="text" /></td>
			                </tr>
			
			            </table>
				</div>
				<div id="acceptance">
					<input id="shop_acceptance" name="shop_acceptance" type="checkbox" value="1" /> 
					<label for="shop_acceptance">
						' . $acceptance_text . '
					</label>
				</div>
				<input class="button_60" name="commit" type="submit" value="Beställ" />
			    </div>
			</form>
			</fieldset>';
		}
	}
	catch (Exception $error)
	{
		$out .= '<div class="error">';
		$out .= '<p>';
		$out .= $error->getMessage();
		$out .= '</p>';
		$out .= '<p>';
		$out .= '<a href="/shop/">&laquo; Tillbaka</a>';
		$out .= '</p>';
		$out .= '</div>';
	}
	
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
