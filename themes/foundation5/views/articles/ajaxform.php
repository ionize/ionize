
<ion:article:title tag="h2" />


<ion:article:content />


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
<ion:form ajax="true" name="contact_ajax" submit="contactFormSubmit">

	<?php
	/*
	 * For Ajaxform, the form name must be set.
	 * This is needed for the javascript part of the Ajaxform module
	 *
	 * For ionize built in form label translation and other form translation terms, you can
	 * have a look at the file : /application/language/en/form_lang.php
	 * You can copy this file into your theme folder to adapt the translations.
	 *
	 * The form is posted to the URL : ajaxform/post by the JS added after the form
	 * This JS also handles error messages and creates error / success messages HTML elements.
	 */
	?>

	<?php
	/*
	 * Name of the form must be set again,
	 * so the JS script gets access to the DOM form element
	 *
	 */
	?>
	<form method="post" name="contact_ajax">

		<div>
			<label for="name"><ion:lang key='form_label_name' /></label>
			<input name="name" type="text" placeholder="<ion:lang key='form_placeholder_name' />" />
		</div>

		<div>
			<label for="email"><ion:lang key="form_label_email" /></label>
			<input name="email" type="email" placeholder="<ion:lang key='form_placeholder_email' />" />
		</div>

		<div>
			<label for="message"><ion:lang key='form_label_message' /></label>
			<textarea name="message" placeholder="<ion:lang key='form_placeholder_message' />"></textarea>
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
 * OR in our theme folder :
 * /themes/my_theme/views/ajaxformjs.php
 *
 */
?>
