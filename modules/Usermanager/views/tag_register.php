<div class="usermanager_register">
	<ion:usermanager request="form" attr="has_errors" form_name="register">
		<h1><ion:translation term="module_usermanager_text_error" /></h1>
		<div class="usermanager_error"><ion:usermanager request="form" attr="error_string" /></div>
	</ion:usermanager>
	<form action="<ion:usermanager request='global' attr='url' />" method="post">

		<h1><ion:translation term="module_usermanager_text_mandatory_fields" /></h1>
		<p>
			<label for="title"><ion:translation term="module_usermanager_field_title" /></label>
			<select size="1" name="title" id="title" class="usermanager_input_select">
				<option value="0" <ion:usermanager request='user' attr='title' from_default_value='1' from_post_data='register' is_like='0'>SELECTED</ion:usermanager>><ion:translation term="module_usermanager_field_title_mr" /></option>
				<option value="1" <ion:usermanager request='user' attr='title' from_default_value='1' from_post_data='register' is_like='1'>SELECTED</ion:usermanager>><ion:translation term="module_usermanager_field_title_ms" /></option>
			</select>
		</p>
		<p><label for="firstname"><ion:translation term="module_usermanager_field_firstname" /></label><input class="usermanager_input_text" name="firstname" id="firstname" value="<ion:usermanager request='user' attr='firstname' from_default_value='1' from_post_data='register' />" /></p>
		<p><label for="screen_name"><ion:translation term="module_usermanager_field_screen_name" /></label><input class="usermanager_input_text" name="screen_name" id="screen_name" value="<ion:usermanager request='user' attr='screen_name' from_default_value='1' from_post_data='register' />" /></p>
		<ion:usermanager request="global" attr="not_email_as_username">
			<p><label for="username"><ion:translation term="module_usermanager_field_username" /></label><input class="usermanager_input_text" name="username" id="username" value="<ion:usermanager request='user' attr='username' from_default_value='1' from_post_data='register' />" /></p>
		</ion:usermanager>
		<p><label for="email"><ion:translation term="module_usermanager_field_email" /></label><input class="usermanager_input_text" name="email" id="email" value="<ion:usermanager request='user' attr='email' from_default_value='1' from_post_data='register' />" /></p>
		<p><label for="password"><ion:translation term="module_usermanager_field_password" /></label><input class="usermanager_input_password" name="password" id="password" type="password" value="<ion:usermanager request='user' attr='password' from_default_value='1' from_post_data='register' />" /></p>
		<p><label for="password2"><ion:translation term="module_usermanager_field_password2" /></label><input class="usermanager_input_password" name="password2" id="password2" type="password" value="<ion:usermanager request='user' attr='password2' from_default_value='1' from_post_data='register' />" /></p>

		<h1><ion:translation term="module_usermanager_text_optional_fields" /></h1>
		<p><label for="company"><ion:translation term="module_usermanager_field_company" /></label><input class="usermanager_input_text" name="company" id="company" value="<ion:usermanager request='user' attr='company' from_default_value='1' from_post_data='register' />" /></p>
		<p><label for="position"><ion:translation term="module_usermanager_field_position" /></label><input class="usermanager_input_text" name="position" id="position" value="<ion:usermanager request='user' attr='position' from_default_value='1' from_post_data='register' />" /></p>
		<p><label for="street"><ion:translation term="module_usermanager_field_street" /></label><input class="usermanager_input_text" name="street" id="street" value="<ion:usermanager request='user' attr='street' from_default_value='1' from_post_data='register' />" /></p>
		<p><label for="housenumber"><ion:translation term="module_usermanager_field_housenumber" /></label><input class="usermanager_input_text" name="housenumber" id="housenumber" value="<ion:usermanager request='user' attr='housenumber' from_default_value='1' from_post_data='register' />" /></p>
		<p><label for="zip"><ion:translation term="module_usermanager_field_zip" /></label><input class="usermanager_input_text" name="zip" id="zip" value="<ion:usermanager request='user' attr='zip' from_default_value='1' from_post_data='register' />" /></p>
		<p><label for="city"><ion:translation term="module_usermanager_field_city" /></label><input class="usermanager_input_text" name="city" id="city" value="<ion:usermanager request='user' attr='city' from_default_value='1' from_post_data='register' />" /></p>

		<h1><ion:translation term="module_usermanager_text_final_fields" /></h1>
		<p><input type="checkbox" name="infomails" id="infomails" <ion:usermanager request='user' attr='infomails' from_default_value='1' from_post_data='register' is_like='1'>CHECKED</ion:usermanager> /> <label class="usermanager_label_checkbox" for="infomails"><ion:translation term="module_usermanager_field_infomails_desc" /></label></p>
		<p><input type="checkbox" name="newsletter" id="newsletter" <ion:usermanager request='user' attr='newsletter' from_default_value='1' from_post_data='register' is_like='1'>CHECKED</ion:usermanager> /> <label class="usermanager_label_checkbox" for="newsletter"><ion:translation term="module_usermanager_field_newsletter_desc" /></label></p>
		<p><input type="checkbox" name="terms" id="terms" <ion:usermanager request='user' attr='terms' from_default_value='1' from_post_data='register' is_like='1'>CHECKED</ion:usermanager> /> <label class="usermanager_label_checkbox" for="terms"><ion:translation term="module_usermanager_field_terms_desc" /></label></p>
		<p><input class="usermanager_input_button" type="submit" name="submit_form" value="<ion:translation term='module_usermanager_button_register' />" /></p>

		<input type="hidden" name="form_name" value="register" />
	</form>
</div>

