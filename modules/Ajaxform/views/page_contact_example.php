<?php
/*
 * For Ajaxform, add the attributes :
 *
 * - ajax : 	Set to true
 * - name : 	Name of the form, as declared in /themes/your_theme/config/forms.php
 * - submit : 	ID of the DOM element which will send the form (button, link, ...)
 * - nojs :		If set to true, the JS part of the module will not be add after the form.
 * 				If set to true, you'll need to add the Ajax part in your own JS script.
 *
 */
?>

<ion:form ajax="true" name="contact" submit="contactFormSubmit">

	<?php
	/*
	 * For Ajaxform, the form name must be set.
	 * This is needed for the javascript part of the Ajaxform module
	 *
	 * For ionize built in form label translation and other form translation terms, you can
	 * have a look at the file : /application/language/en/form_lang.php
	 * You can copy this file into your theme folder to adapt the translations.
	 *
	 */
	?>
	<form method="post" name="contact">

		<div>
			<label for="name"><ion:lang key='form_label_name' /></label>
			<input type="text" value="<ion:contact:field:name />" placeholder="<ion:lang key='form_placeholder_name' />" name="name" />
		</div>

		<div>
			<label for="message"><ion:lang key='form_label_message' /></label>
			<textarea placeholder="<ion:lang key='form_placeholder_message' />" name="message"></textarea>
		</div>

		<div>
			<button id="contactFormSubmit" type="submit">
				<ion:lang key='form_button_send_message' />
			</button>
		</div>

	</form>


</ion:form>

<?php
/*
 * The JS part of the form will be added here and can be find in the view :
 * /modules/Ajaxform/views/ajaxformjs.php
 *
 */
?>