<ion:partial path="header" />


<div class="row">
	<div class="eight columns">

		<!-- Articles with the type "intro" -->
		<ion:articles type="intro">
			
			<!-- Article Title -->
			<ion:title tag="h2" />
			
			<!-- Article Content -->
			<ion:content />
		
		</ion:articles>	

		
	<!-- 
		The Form
		
		We choose for the demo to directly display the form in the page view.
		That means when a page has the view called "Contact Page", this form will appear.
		
		But...
		It is also possible in put this form code into an article view and to assign a "Contact Article" view to the article.
		
		To do this : 
		
		1. Create an article_form.php view and put the whole form code into this view
		2. Declare the view in Ionize (Settings > Theme)
		3. Use the article tag like this :
		
			< ion:articles>
				<!-- In this case, the article will use its assigned view 
				< ion:article />
			< /ion:article>
			
	-->
		
		<!--
			If simpleform validation is OK, display the message inside the tag.
		-->
		<ion:simpleform:validation request="form" attr="has_success" is_like="1" form_name="contact">
			
			<div class="success">
				<!-- Success messag from static translation in Ionize Admin panel -->
				<ion:translation term="module_simpleform_text_thanks" />
			</div>
		
		</ion:simpleform:validation>
		
		<!-- 
			If the form wasn't validated (means first entry into the page or error during validation)
			display the form
		-->
		<ion:simpleform:validation attr="has_success" is_like="0" form_name="contact">
			
			<h2><ion:translation term="title_contact_form" /></h2>
			
			<!-- 
				If a notice is present, display the message (notice) between the tags
			-->
			<ion:simpleform:validation request="form" attr="has_notice" form_name="contact" is_like="1">
				
				<div class="notice">
					<ion:translation term="module_simpleform_all_fields_required" />
				</div>
			
			</ion:simpleform:validation>
			
			<!-- 
				If an error occured during the validation process, display the message (notice) between the tags
			-->
			<ion:simpleform:validation attr="has_errors" form_name="contact" is_like="1">
				<div class="error">
					<h3><ion:translation term="module_simpleform_text_error" /></h3>
					<ion:simpleform:validation request="form" attr="error_string" />
				</div>
			</ion:simpleform:validation>
			
			<!-- 
				Form display
				The action attribute of the form is the current URL (tags will catch the form data)
			-->
			<form id="contactform" action="<?= current_url() ?>" method="post">
			
				<fieldset>
				
					<!-- 
						Form name
						Mandatory hidden field
						This value is used by the tag library to identify the form and retrieve data
					-->
					<input name="form_name" type="hidden" value="contact" />
					
					<!-- Antispam Honny pot field
						 The form doesn't need this field
						 The default value of this feld is the antispam key, feeded through Javascript
						 The name of the field must be set in simplefomr/config/config.php
						 Advice : Change the default field name to something which seems "natural", so robots and spammers will feed this field with their data
						 and not pass the form validation.
					--> 
					<input id="city" name="city" type="hidden" value="" />
					
					<!-- 
						Name field	
					-->
					<!-- The label translation is located in /modules/Simpleform/language/xx/simpleform_lang.php -->
					<label for="name"><ion:translation term="module_simpleform_field_name" /></label>					
					<!--
						Name input field
						The value is got from the form through the "field" tag
					-->
					<input class="inputtext w360" name="name" id="name" type="text" value="<ion:simpleform:field name="name" from_post_data="contact" />" />
					
					<!-- Email field -->
					<label for="email"><ion:translation term="module_simpleform_field_email" /></label>
					<input class="inputtext w360" name="email" id="email" type="text" value="<ion:simpleform:field name="email" from_post_data="contact" />" />
					
					<!-- Subject field -->
					<label for="subject"><ion:translation term="module_simpleform_field_subject" /></label>
					<input class="inputtext w360" name="subject" id="subject" type="text" value="<ion:simpleform:field name="subject" from_post_data="contact" />" />
					
					<!-- Message field -->
					<label for="message"><ion:translation term="module_simpleform_field_message" /></label>
					<textarea id="message" name="message"><ion:simpleform:field name="message" from_post_data="contact" /></textarea>
					
					<!-- Submit -->			
					<input class="button w40 h60" type="submit" name="submit_form" value="<ion:translation term='module_simpleform_button_send' />" />
				
				</fieldset>    
			</form>
			
			<!-- 
				Honny pot javascript feeding of the fake "city" field
			-->
			<script type="text/javascript">
				window.addEvent('load', function() //using load instead of domready for IE8
				{ 
					$('city').value = '<ion:config item="form_antispam_key" />';
				});
			</script>
			
		</ion:simpleform:validation>

	</div>

	<!-- 
		Contact page articles 
	-->
	<div class="four columns">
		
		<!-- Displaying Article -->
		<ion:articles>
			
				
			<!-- Article Title -->
			<ion:title tag="h3" />
			
			<!-- Article Content -->
			<ion:content />
			
			
			<!-- Article's media, if any -->
			<ion:medias type="picture">
				
				<img src="<ion:src folder="430" />" />
			
			</ion:medias>
			
		
		</ion:articles>
		
	</div>


</div>

	

<ion:partial path="footer" />
