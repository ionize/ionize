
<!-- Success message -->
<ion:simpleform:validation attr="has_success" form_name="contact" is_like="1">

	<p><ion:translation term="module_simpleform_text_thanks" /></p>

</ion:simpleform:validation>



<!-- No form sent or no success : Displays the form -->
<ion:simpleform:validation attr="has_success" form_name="contact" is_like="0">

	<!-- Errors -->
	<ion:simpleform:validation attr="has_errors" form_name="contact" is_like="1">
		
		<h1><ion:translation term="module_simpleform_text_error" /></h1>
	
		<ion:simpleform:validation attr="error_string" form_name="contact" />
	
	</ion:simpleform:validation>

	
	<!-- Form -->
	<h1><ion:translation term="module_simpleform_text_form" /></h1>
	
	<form action="<?= current_url() ?>" method="post">

		<input name="form_name" type="hidden" value="contact" />
		<input id="city" name="city" type="hidden" value="" />
		
		<!-- Name field -->
		<p>
			<label for="name"><ion:translation term="module_simpleform_field_name" /></label>
			<input class="text" name="name" id="name" type="text" value="<ion:simpleform:field name="name" from_post_data="contact" />" />
		</p>
		
		<!-- Email field -->
		<p>
			<label for="email"><ion:translation term="module_simpleform_field_email" /></label>
			<input class="text" name="email" id="email" type="text" value="<ion:simpleform:field name="email" from_post_data="contact" />" />
		</p>

		<!-- Message field -->
		<p>
			<label for="message"><ion:translation term="module_simpleform_field_message" /></label>
			<textarea rows="5" id="message" name="message" class=""><ion:simpleform:field name="message" from_post_data="contact" /></textarea>
		</p>

		<!-- Submit button -->
		<p>
			<label for="submit">&nbsp;</label><input class="" type="submit" name="submit_form" value="<ion:translation term='module_simpleform_button_send' />" />
		</p>
		
	</form>


	<!-- Antispam 
		 How it works : 
		 In this example, the field "city" is filled through JS with the antispam key (application/config/ionize.php)
		 In the Simpleform config file (modules/Simpleform/config/config.php), the field "city" has the validation rule "antispam"
		 When the form is sent, the field value is compared to the antispam key.
		 As bots don't care about JS, they will fill this field with some data, which will not match the antispam key.
		 
		 Why name the field "city" ? : Because this name isn't used by the form and looks like "common one"...
		 
	-->
	<script type="text/javascript">
		
		// jQuery code
		jQuery(document).ready(function($)
		{
			$('#city').val('<ion:config item="form_antispam_key" />');
		});

		// Mootools code
		/*
		window.addEvent('load', function() //using load instead of domready for IE8
		{ 
			$('city').value = '<ion:config item="form_antispam_key" />';
		});
		*/

	</script>

</ion:simpleform:validation>

