<ion:partial view="header" />

<ion:partial view="page_header" />


<div class="row">
	<div class="eight columns">

		<!-- Articles with no type -->
        <ion:page:articles type="">

			<ion:article>
                <ion:title tag="h3" />
                <ion:content />
			</ion:article>

        </ion:page:articles>

		
        <!--
			Success message
			Displayed if the form was successfuly validated
		-->
		<ion:form:contact:validation:success is="true">
			<div class="alert-box success">
				<ion:lang key="form_alert_success_title" tag="h4" />
				<ion:lang key="form_alert_success_message" tag="p" />
				<a href="" class="close">&times;</a>
			</div>
		</ion:form:contact:validation:success>

		<!--
			Error message
			Displayed if the form doesn't pass the validation
			the 'form_message_error' key is located in : themes/your_theme/language/xx/tags_lang.php
		-->
		<ion:form:contact:validation:error is="true" >
			<div class="alert-box alert">
				<ion:lang key="form_alert_error_title" tag="h4" />
				<ion:lang key="form_alert_error_message" tag="p" />
				<a href="" class="close">&times;</a>
			</div>
		</ion:form:contact:validation:error>

		<!--
			Form has no action because the same page will process the data.
			POST data are catched by the global Tagmanager and processed by the Tagmanager's library method 'prcoess_data'
			defined in : /themes/your_theme/libraries/Tagmanager/Contact.php
			as declared in the form config file : /themes/your_theme/config/forms.php
		-->
		<form method="post" action="">

			<!-- The form name must be set so the tags identify it -->
			<input type="hidden" name="form" value="contact" />

			<!-- Input : Name -->

			<label for="firstname"><ion:lang key="form_label_form_firstname" /></label>
			<input name="firstname" type="text" id="firstname" value="<ion:form:contact:field:firstname />"<ion:form:contact:error:firstname is="true"> class="error" </ion:form:contact:error:firstname>/>
			<ion:form:contact:error:firstname tag="small" class="error" />

			<!-- Input : Surname -->
			<label for="lastname"><ion:lang key="form_label_form_lastname" /></label>
			<input name="lastname" type="text" id="lastname" value="<ion:form:contact:field:lastname />"<ion:form:contact:error:lastname is="true"> class="error" </ion:form:contact:error:lastname> />
			<ion:form:contact:error:lastname tag="small" class="error" />

			<!-- Input : Email -->
			<label for="email"><ion:lang key="form_label_email" /></label>
			<input name="email" type="text" id="email" value="<ion:form:contact:field:email />"<ion:form:contact:error:email is="true"> class="error" </ion:form:contact:error:email> />
			<ion:form:contact:error:email tag="small" class="error" />


			<!-- Input : Subject -->
			<label for="subject"><ion:lang key="form_label_form_subject" /></label>
			<input name="subject" type="text" id="subject" value="<ion:form:contact:field:subject />"<ion:form:contact:error:subject is="true"> class="error" </ion:form:contact:error:subject> />
			<ion:form:contact:error:subject tag="small" class="error" />

			<!-- Input : Message -->
			<label for="message"><ion:lang key="form_label_form_message" /></label>
			<textarea name="message" id="message" rows="7" placeholder="<ion:lang key="form_label_form_message" />"<ion:form:contact:error:message is="true"> class="error" </ion:form:contact:error:message>><ion:form:contact:field:message /></textarea>
			<ion:form:contact:error:message tag="small" class="error" />


			<button type="submit" class="button success right"><ion:lang key="button_send" /></button>
		</form>

		<!--
			Honny pot javascript feeding of the fake "city" field
		-->
		<script type="text/javascript">
			window.addEvent('load', function() //using load instead of domready for IE8
			{
				$('city').value = '<ion:config key="form_antispam_key" />';
			});
		</script>
			

	</div>

	<!-- 
		Contact page articles 
	-->
	<div class="four columns">
		
		<!-- Displaying Article -->
		<ion:page:articles type="bloc">
			
			<ion:article>
			<!-- Article Title -->
			<ion:title tag="h3" />
			
			<!-- Article Content -->
			<ion:content />
			
			<!-- Article's media, if any -->
			<ion:medias type="picture">
				<img src="<ion:media:src size='430' />" />
			</ion:medias>
            </ion:article>
		</ion:page:articles>
		
	</div>
</div>

	

<ion:partial view="footer" />
