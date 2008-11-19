<?php
	/* OPEN_SOURCE */
	
	require('../include/core/common.php');
	require(PATHS_INCLUDE  . 'libraries/photos.lib.php');
	
	$acceptance_text = '<h3>Att köpa en T-shirt</h3>
<p>När du lagt en beställning kommer du få ett referensnummer av systemet. Detta nummer måste du ange när du sätter in pengar på Hamsterpajs bankgirokonto.
Det är mycket viktigt att du anger detta numret och att du skriver rätt, annars kan vi inte se vilken beställning som betalts.</p>

<p>Ett beställningsnummer gäller i fem dagar från det att du fått det, betalar du inte inom denna tid måste du lägga en ny beställning och använda det nya numret du får.</p>

<p>Vi skall försöka skicka tröjan inom två dagar från att vi fått in din betalning, har paketet inte nått dig inom en vecka från att du betalt bör du börja ana ugglor i mossen och kontakta oss.</p>

<p>Skulle du vilja ändra en beställning eller har frågor, ta det direkt med oss. Skriv absolut inte meddelanden i meddelanderaden när du betalar!</p>

<p><strong>Försäljare:</strong><br />
Hamsterpaj AB<br />
556697-13<br />
Bankgiro: 5801-5025<br />
Vi innehar F-skattebevis<br />
Alla priser inkluderas 25% moms
</p>


<p><strong>Kontakt:</strong><br />
Eric Jerlin<br />
VD<br />
<a href="mailto:eric@hamsterpaj.net">eric@hamsterpaj.net</a><br />
0768 999 585<br />
</p>
<p>(Hamsterpaj är inget kontorsbolag, vi är mest en hobbysajt. Därför finns det inte något fastnätsnummer, och Eric är inte alltid tillgänglig via telefon)</p>
';
			/*$time = time();
			$shirt_excluded_letters = array('1', '0', 'L', 'O', 'I');
			$order_hash = md5($_SESSION['login']['id'] . $time);
			$order_hash = strtoupper($order_hash);
			echo $order_hash . "<br />\n";
			$order_hash = str_replace($shirt_excluded_letters, '', $order_hash);
			$order_hash = substr($order_hash, 0, 6);
			echo $order_hash;*/
	try
	{
		if (!login_checklogin())
		{
			throw new Exception('Du måste vara inloggad och medlem för att kunna beställa en Hamsterpaj-tröja');
		}
		$ui_options['stylesheets'][] = 'shop.css';
		$ui_options['stylesheets'][] = 'forms.css';
		$ui_options['javascripts'][] = 'jquery.selectboxes.pack.js';
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
			
			switch ($_POST['shop_gender'])
			{
				case 'male':
					$shop_gender_short = 'm';
				break;
				case 'female':
					$shop_gender_short = 'f';
				break;
				default:
					throw new Exception('Error in defining $shop_gender_short.<br /> Kontakta <a href="/Joar">Joar</a> och försök att beskriva hela händelseförloppet så detaljerat som möjligt.');
				break;
			}
			switch ($_POST['shop_size'])
			{
				case 'small':
					$shop_size_safe = 'small';
				break;
				case 'medium':
					$shop_size_safe = 'medium';
				break;
				case 'large':
					$shop_size_safe = 'large';
				break;
				default:
					throw new Exception('Error in defining $shop_size_safe.<br /> Kontakta <a href="/Joar">Joar</a> och försök att beskriva hela händelseförloppet så detaljerat som möjligt.');
				break;
			}
			$shirt_order_exists_sql = 'SELECT order_id FROM shop_orders WHERE user_id = ' . $_SESSION['login']['id'];
			$shirt_order_exists_result = mysql_query($shirt_order_exists_sql);
			$shirt_order_exists_num_rows = mysql_num_rows($shirt_order_exists_result);
			
			if ($shirt_order_exists_num_rows > 0)
			{
				throw new Exception('Endast en beställning per person.<br />Smek <a href="/Joar">Joar</a> så kanske du får en extra ;) ');
			}
			$shirt_safe_size_definition = $shop_gender_short . '_' . $shop_size_safe;
			
			$check_shirt_availability_sql = 'SELECT ' . $shirt_safe_size_definition . ' FROM shop_shirts_available WHERE handle = "pantone_165"';
			
			if (!$check_shirt_availability_result = mysql_query($check_shirt_availability_sql))
			{
				 throw new Exception(mysql_error());
			}
			if (!$check_shirt_availability_data = mysql_fetch_assoc($check_shirt_availability_result))
			{
				 throw new Exception(mysql_error());
			}
			
			if ($check_shirt_availability_data[$shirt_safe_size_definition] > 0)
			{
				$shirt_new_quantity = $check_shirt_availability_data[$shirt_safe_size_definition] - 1;
				$shirt_update_quantity_sql = 'UPDATE shop_shirts_available SET ' . $shirt_safe_size_definition . ' = ' . $shirt_new_quantity . ' WHERE handle = "pantone_165"';
				mysql_query($shirt_update_quantity_sql);
			}
			else
			{
				throw new Exception('Det finns tyvärr inga tröjor kvar i den storleken. Det kan dock hända att det blir återbud eller så syr hamstern några till, så håll dig uppdaterad!');
			}
			
			$time = time();
			$shirt_excluded_letters = array('1', '0', 'L', 'O', 'I');
			$order_hash = md5($_SESSION['login']['id'] . $time);
			$order_hash = strtoupper($order_hash);
			$order_hash = str_replace($shirt_excluded_letters, '', $order_hash);
			$order_hash = substr($order_hash, 0, 6);
			
			//var_dump($order_hash);
			
			// Om allt är fint så borde man hamna här till slut.
			$sql = 'INSERT INTO shop_orders SET';
			$sql .= ' order_hash = "' . $order_hash . '",';
			$sql .= ' user_id = ' . $_SESSION['login']['id'] . ',';
			$sql .= ' real_name = "' . $_POST['shop_real_name'] . '",';
			$sql .= ' address = "' . $_POST['shop_address'] . '",';
			$sql .= ' zip_code = "' . $processed_field['shop_zip_code'] . '",';
			$sql .= ' city = "' . $_POST['shop_city'] . '",';
			$sql .= ' size = "' . $_POST['shop_size'] . '",';
			$sql .= ' gender = "' . $_POST['shop_gender'] . '",';
			$sql .= ' phone = "' . $processed_field['shop_phone'] . '", ';
			$sql .= ' timestamp = ' . $time . '';
			mysql_query($sql) or report_sql_error(__FILE__, __LINE__, $sql);
			
			$sql = 'SELECT order_hash FROM shop_orders WHERE user_id = ' . $_SESSION['login']['id'] . ' LIMIT 1';
			$result = mysql_query($sql);
			$data = mysql_fetch_assoc($result);
			$out .= '
				<h1>Tack för din beställning!</h1>
				' . rounded_corners_top(array('color' => 'green'), true) . '
				<h2 style="margin-top: 0;">För att bekräfta betalning</h2>
				<p><strong>För att bekräfta din betalning</strong> så sätter du in <strong>200 SEK</strong> på <strong>bankgiro 5801-5025</strong> med kommentaren
				<strong>' . $data['order_hash'] . '</strong>
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
				<p>Tjejtröjorna finns i storlek 36, 38 och XX men vi orkade inte göra två system så välj Small om ni vill ha 36 osv. så får moii säga vad hon vill till Ace och Joar!<br /><span style="font-size: 10px;"><em>Skämtade bara, om man väljer "tjej" så får man upp rätt nummer :)</em></span></p>
				
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
					<div id="shop_acceptance_text">
						' . $acceptance_text . '
					</div>
					<input id="shop_acceptance" name="shop_acceptance" type="checkbox" value="1" /> 
					<label for="shop_acceptance">
						Jag accepterar avtalet.
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
