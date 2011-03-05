<ion:usermanager request="form" attr="has_success" form_name="profile_save">
	<h1><ion:translation term="module_usermanager_text_success" /></h1>
	<div class="usermanager_success"><ion:usermanager request="form" attr="success_string" /></div>
</ion:usermanager>
<ion:usermanager request="form" attr="has_errors" form_name="profile_save">
	<h1><ion:translation term="module_usermanager_text_error" /></h1>
	<div class="usermanager_error"><ion:usermanager request="form" attr="error_string" /></div>
</ion:usermanager>
<ion:usermanager request="form" attr="has_errors" form_name="random_fields">
	<h1><ion:translation term="module_usermanager_text_error" /></h1>
	<div class="usermanager_error"><ion:usermanager request="form" attr="error_string" /></div>
</ion:usermanager>
<ion:usermanager request="form" attr="has_success" form_name="random_fields_form">
	<h1><ion:translation term="module_usermanager_text_success" /></h1>
	<div class="usermanager_success"><ion:usermanager request="form" attr="success_string" /></div>
</ion:usermanager>
<ion:usermanager request="form" attr="has_errors" form_name="random_fields_form">
	<h1><ion:translation term="module_usermanager_text_error" /></h1>
	<div class="usermanager_error"><ion:usermanager request="form" attr="error_string" /></div>
</ion:usermanager>
<ion:usermanager request="form" attr="has_errors" form_name="random_fields_form">
	<h1><ion:translation term="module_usermanager_text_error" /></h1>
	<div class="usermanager_error"><ion:usermanager request="form" attr="error_string" /></div>
</ion:usermanager>

<ion:usermanager request="user" attr="is_logged_in" is_like="0">
	<h1><ion:translation term="module_usermanager_text_error" /></h1>
	<p><ion:translation term="module_usermanager_text_not_logged_in" /></p>
</ion:usermanager>

<div class="usermanager_profile_edit">

	<ion:usermanager request="user" attr="is_logged_in" is_like="1">
		<form name="random_fields_form" id="random_fields_form" action="<ion:usermanager request='global' attr='url' />" method="post">
			<input type="hidden" name="form_name" value="random_fields_form" />
			<ion:usermanager request='user' attr='company_profile' is_like='0' from_user_field='1'><input type="hidden" name="company_profile" value="1" /></ion:usermanager>
			<ion:usermanager request='user' attr='company_profile' is_like='1' from_user_field='1'><input type="hidden" name="company_profile" value="0" /></ion:usermanager>
		</form>

		<form name="profile_form" id="profile_form" action="<ion:usermanager request='global' attr='url' />" method="post" enctype="multipart/form-data">
			<!--
				Action
			-->
			<h1><ion:translation term="module_usermanager_text_action" /></h1>
			<input class="usermanager_input_button" type="submit" name="submit_form" value="<ion:translation term='module_usermanager_button_save' />" />
			<input class="usermanager_input_button" type="button" name="delete" value="<ion:translation term='module_usermanager_button_delete' />" onclick="javascript:document.getElementById('delete').value='1'; document.profile_form.submit();" />
			<ion:usermanager request='user' attr='company_profile' is_like='0' from_user_field='1'><div style="float: left; margin-right: 20px;"><input class="usermanager_input_button" type="button" name="company_prof" value="<ion:translation term='module_usermanager_button_company_profile' />" onclick="document.random_fields_form.submit();" /></div></ion:usermanager>
			<ion:usermanager request='user' attr='company_profile' is_like='1' from_user_field='1'><div style="float: left; margin-right: 20px;"><input class="usermanager_input_button" type="button" name="company_prof" value="<ion:translation term='module_usermanager_button_nocompany_profile' />" onclick="document.random_fields_form.submit();" /></div></ion:usermanager>

			<!--
				Required Fields
			-->
			<h1><ion:translation term="module_usermanager_text_mandatory_fields" /></h1>
			<p>
				<label for="title"><ion:translation term="module_usermanager_field_title" /></label>
				<select size="1" name="title" id="title" class="usermanager_input_select">
					<option value="0" <ion:usermanager request='user' attr='title' is_like='0' from_user_field='1' from_post_data='profile_save'>SELECTED</ion:usermanager>><ion:translation term="module_usermanager_field_title_mr" /></option>
					<option value="1" <ion:usermanager request='user' attr='title' is_like='1' from_user_field='1' from_post_data='profile_save'>SELECTED</ion:usermanager>><ion:translation term="module_usermanager_field_title_ms" /></option>
				</select>
			</p>
			<p><label for="firstname"><ion:translation term="module_usermanager_field_firstname" /></label><input class="usermanager_input_text" name="firstname" id="firstname" value="<ion:usermanager request='user' attr='firstname' from_user_field='1' from_post_data='profile_save' />" /></p>
			<p><label for="screen_name"><ion:translation term="module_usermanager_field_screen_name" /></label><input class="usermanager_input_text" name="screen_name" id="screen_name" value="<ion:usermanager request='user' attr='screen_name' from_user_field='1' from_post_data='profile_save' />" /></p>
			<ion:usermanager request="global" attr="not_email_as_username">
				<p><label for="username"><ion:translation term="module_usermanager_field_username" /></label><input class="usermanager_input_text" name="username" id="username" value="<ion:usermanager request='user' attr='username' from_user_field='1' from_post_data='profile_save' />" /></p>
			</ion:usermanager>
			<p><label for="email"><ion:translation term="module_usermanager_field_email" /></label><input class="usermanager_input_text" name="email" id="email" value="<ion:usermanager request='user' attr='email' from_user_field='1' from_post_data='profile_save' />" /></p>
			<p><label for="password"><ion:translation term="module_usermanager_field_password" /></label><input class="usermanager_input_password" name="password" id="password" type="password" value="<ion:usermanager request='user' attr='password' from_post_data='profile_save' />" /></p>
			<p><label for="password2"><ion:translation term="module_usermanager_field_password2" /></label><input class="usermanager_input_password" name="password2" id="password2" type="password" value="<ion:usermanager request='user' attr='password2' from_post_data='profile_save' />" /></p>

			<!--
				Optional Fields
			-->
			<h1><ion:translation term="module_usermanager_text_optional_fields" /></h1>
			<p><label for="company"><ion:translation term="module_usermanager_field_company" /></label><input class="usermanager_input_text" name="company" id="company" value="<ion:usermanager request='user' attr='company' from_user_field='1' from_post_data='profile_save' />" /></p>
			<p><label for="position"><ion:translation term="module_usermanager_field_position" /></label><input class="usermanager_input_text" name="position" id="position" value="<ion:usermanager request='user' attr='position' from_user_field='1' from_post_data='profile_save' />" /></p>
			<p><label for="street"><ion:translation term="module_usermanager_field_street" /></label><input class="usermanager_input_text" name="street" id="street" style="width: 245px" value="<ion:usermanager request='user' attr='street' from_user_field='1' from_post_data='profile_save' />" /><input class="usermanager_input_text" name="housenumber" id="housenumber" style="width: 20px" value="<ion:usermanager request='user' attr='housenumber' from_user_field='1' from_post_data='profile_save' />" /></p>
			<p><label for="zip"><ion:translation term="module_usermanager_field_zip" /> / <ion:translation term="module_usermanager_field_city" /></label><input class="usermanager_input_text" name="zip" id="zip" style="width: 40px;" value="<ion:usermanager request='user' attr='zip' from_user_field='1' from_post_data='profile_save' />" /><input class="usermanager_input_text" name="city" id="city" style="width: 225px;" value="<ion:usermanager request='user' attr='city' from_user_field='1' from_post_data='profile_save' />" /></p>
			<p><label for="website"><ion:translation term="module_usermanager_field_website" /></label><input class="usermanager_input_text" name="website" id="website" value="<ion:usermanager request='user' attr='website' from_user_field='1' from_post_data='profile_save' />" /></p>
			<p><label for="xing"><ion:translation term="module_usermanager_field_xing" /></label><input class="usermanager_input_text" name="xing" id="xing" value="<ion:usermanager request='user' attr='xing' from_user_field='1' from_post_data='profile_save' />" /></p>
			<p><label for="linkedin"><ion:translation term="module_usermanager_field_linkedin" /></label><input class="usermanager_input_text" name="linkedin" id="linkedin" value="<ion:usermanager request='user' attr='linkedin' from_user_field='1' from_post_data='profile_save' />" /></p>
			<p><label for="twitter"><ion:translation term="module_usermanager_field_twitter" /></label><input class="usermanager_input_text" name="twitter" id="twitter" value="<ion:usermanager request='user' attr='twitter' from_user_field='1' from_post_data='profile_save' />" /></p>
			<p><label for="facebook"><ion:translation term="module_usermanager_field_facebook" /></label><input class="usermanager_input_text" name="facebook" id="facebook" value="<ion:usermanager request='user' attr='facebook' from_user_field='1' from_post_data='profile_save' />" /></p>

			<!--
				User Image
			-->
			<h1><ion:translation term="module_usermanager_text_picture" /></h1>
				<p class="autoheight" style="text-align: center;"><img src="<ion:usermanager request='user' attr='get_picture' field='picture' dimensions='profile' />" /></p>
				<p><input class="usermanager_input_file" name="picture" id="picture" type="file" value="" /></p>

			<!--
				About Me
			-->
			<h1><ion:translation term="module_usermanager_text_about_me" /></h1>
			<textarea class="usermanager_input_textarea" id="about_me" name="about_me"><ion:usermanager request="user" attr="about_me" from_user_field="1" from_post_data="profile_save" /></textarea>

			<!--
				References
			-->
			<h1><ion:translation term="module_usermanager_text_references" /></h1>
			<textarea class="usermanager_input_textarea" id="my_references" name="my_references"><ion:usermanager request="user" attr="my_references" from_user_field="1" from_post_data="profile_save" /></textarea>

			<!--
				Finish
			-->
			<h1><ion:translation term="module_usermanager_text_options" /></h1>
			<p><input type="checkbox" name="infomails" id="infomails" <ion:usermanager request='user' attr='infomails' is_like='1' from_user_field='1' from_post_data='profile_save'>CHECKED</ion:usermanager> /> <label class="usermanager_label_checkbox" for="infomails"><ion:translation term="module_usermanager_field_infomails_desc" /></label></p>
			<p><input type="checkbox" name="newsletter" id="newsletter" <ion:usermanager request='user' attr='newsletter' is_like='1' from_user_field='1' from_post_data='profile_save'>CHECKED</ion:usermanager> /> <label class="usermanager_label_checkbox" for="newsletter"><ion:translation term="module_usermanager_field_newsletter_desc" /></label></p>
			<p><input type="checkbox" name="terms" id="terms" <ion:usermanager request='user' attr='terms' is_like='1' from_user_field='1' from_post_data='profile_save'>CHECKED</ion:usermanager> /> <label class="usermanager_label_checkbox" for="terms"><ion:translation term="module_usermanager_field_terms_desc" /></label></p>

			<input type="hidden" name="delete" id="delete" value="0" />
			<input type="hidden" name="form_name" value="profile_save" />
		</form>
	</ion:usermanager>

</div>
