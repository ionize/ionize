<ion:partial view="header" />

<ion:partial view="page_header" />


<div class="row">
	<div class="large-8 small-12 columns">

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
			POST data are catched by the global Tagmanager and processed by the Tagmanager's library method 'process_data'
			defined in : /themes/your_theme/libraries/Tagmanager/Contact.php
			as declared in the form config file : /themes/your_theme/config/forms.php
		-->
		<form method="post" action="">

			<!-- The form name must be set so the tags identify it -->
			<input type="hidden" name="form" value="contact" />

			<!-- Input : Name -->

			<label for="name"><ion:lang key="form_label_name" /></label>
			<input name="name" type="text" id="name" value="<ion:form:contact:field:name />"<ion:form:contact:error:name is="true"> class="error" </ion:form:contact:error:name>/>
			<ion:form:contact:error:name tag="small" class="error" />

			<!-- Input : Email -->
			<label for="email"><ion:lang key="form_label_email" /></label>
			<input name="email" type="text" id="email" value="<ion:form:contact:field:email />"<ion:form:contact:error:email is="true"> class="error" </ion:form:contact:error:email> />
			<ion:form:contact:error:email tag="small" class="error" />


			<!-- Input : Topic -->
			<label for="topic"><ion:lang key="form_label_topic" /></label>
			<input name="topic" type="text" id="topic" value="<ion:form:contact:field:topic />"<ion:form:contact:error:topic is="true"> class="error" </ion:form:contact:error:topic> />
			<ion:form:contact:error:topic tag="small" class="error" />

			<!-- Input : Message -->
			<label for="message"><ion:lang key="form_label_message" /></label>
			<textarea name="message" id="message" rows="7" placeholder="<ion:lang key="form_label_message" />"<ion:form:contact:error:message is="true"> class="error" </ion:form:contact:error:message>><ion:form:contact:field:message /></textarea>
			<ion:form:contact:error:message tag="small" class="error" />


			<button type="submit" class="button success right"><ion:lang key="form_button_send" /></button>
		</form>

	</div>

	<!-- 
		Contact page articles 
	-->
	<div class="large-4 small-12 columns">
		
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
