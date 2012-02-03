<!-- Errors -->
<ion:simpleform:validation attr="has_errors" form_name="contact" is_like="1">

	<div id="error-content">
	
		<h3 class="error"><ion:translation term="module_simpleform_text_error" /></h3>

		<ion:simpleform:validation attr="error_string" />
		
	</div>

</ion:simpleform:validation>



<div class="white-content">
	

	<!-- Success message -->
	<ion:simpleform:validation attr="has_success" form_name="contact" is_like="1">
	
		<h3><ion:translation term="module_simpleform_text_success" /></h3>
		
		<p><ion:translation term="module_simpleform_text_thanks" /></p>
	
	</ion:simpleform:validation>




	<!-- Form -->
	<ion:simpleform:validation attr="has_success" form_name="contact" is_like="0">
	
	
		<!-- Form -->
		<ion:title tag="h3" />

		<ion:content />
		
		<form action="<?= current_url() ?>" method="post">
	
			<input name="form_name" type="hidden" value="contact" />
			
			<input id="city" name="city" type="hidden" value="" />
	
			<!-- Name -->
			<div class="contact-label-col">
				<label for="name"><em>*</em> <ion:translation term="module_simpleform_field_name" /></label>
			</div>
			<div class="contact-input-col-small">
				<input class="text" name="name" id="name" type="text" value="<ion:simpleform:field name="name" from_post_data="contact" />" />
				<div class="error-box"></div>
			</div>
	
			<!-- Email -->
			<div class="contact-label-col">
				<label for="email"><em>*</em> <ion:translation term="module_simpleform_field_email" /></label>
			</div>
			<div class="contact-input-col-small">
				<input class="text" name="email" id="email" type="text" value="<ion:simpleform:field name="email" from_post_data="contact" />" />
				<div class="error-box"></div>
			</div>
	
			
			<!-- Message -->
			<div class="contact-label-col">
				<label for="message"><em>*</em> <ion:translation term="module_simpleform_field_message" /></label>
			</div>
			<div class="contact-input-col">
				<textarea rows="5" id="message" name="message" class=""><ion:simpleform:field name="message" from_post_data="contact" /></textarea>		
				<div class="error-box"></div>
			</div>
	
	
			
			<input class="button submit" type="submit" name="submit_form" value="<ion:translation term='module_simpleform_button_send' />" />
	
			<div class="clear"></div>
			
		</form>
	
		
		<script type="text/javascript">
		
			jQuery(document).ready(function($) {
				$('#city').val('<ion:config item="form_antispam_key" />');
			});

		
		</script>
	
	
	</ion:simpleform:validation>

	
</div>



