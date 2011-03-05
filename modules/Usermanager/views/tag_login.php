<ion:usermanager request="user" attr="is_logged_in" is_like="0">
	<ion:usermanager request="form" attr="has_errors" form_name="login">
		<h1><ion:translation term="module_usermanager_text_error" /></h1>
		<div class="usermanager_error"><ion:usermanager request="form" attr="error_string" /></div>
	</ion:usermanager>
	<div class="usermanager_login">
		<h1><ion:translation term="module_usermanager_text_login" /></h1>
		<form action="<ion:usermanager request='global' attr='url' />" method="post">
			<p><label for="<ion:usermanager request='global' attr='login_field_name' />"><ion:usermanager request="global" attr="login_field_label" /></label><input class="usermanager_input_text" name="<ion:usermanager request='global' attr='login_field_name' />" id="<ion:usermanager request='global' attr='login_field_name' />" value="<ion:usermanager request='user' attr='username' from_post_data='login' />" /></p>
			<p><label for="password"><ion:translation term="module_usermanager_field_password" /></label><input class="usermanager_input_password" name="password" id="password" type="password" /></p>
			<p><label for="submit">&nbsp;</label><input class="usermanager_input_button" type="submit" name="submit_form" value="<ion:translation term='module_usermanager_button_login' />" /></p>
			<input type="hidden" name="form_name" value="login" />
		</form>
		<p class="footer_line"><ion:translation term="module_usermanager_text_ask_registered" /> <a href="<ion:usermanager request='global' attr='register_url' />"><ion:translation term="module_usermanager_text_ask_registered_here" /></a>.</p>
	</div>
</ion:usermanager>

<ion:usermanager request="user" attr="is_logged_in" is_like="1">
	<div class="usermanager_login_logged_in">
		<p><ion:translation term="module_usermanager_text_login" /></p>
		<p><ion:translation term="module_usermanager_text_logged_in" /></p>
	</div>
</ion:usermanager>
