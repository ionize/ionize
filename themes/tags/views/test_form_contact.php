<?php
/**
 * Theme Contact form example
 *
 *
 *
 *
 */
?>
<h1>Contact form</h1>

<p>
	This is one example of theme defined form.<br/>
	To create this example form, you will need to:
</p>
<ol>
	<li>
		Declare and configure the form in your "forms" config file.<br/>
		<span class="note">Location of the config file : <b>themes/your_theme/config/forms.php</b></span>
	</li>
	<li>
		Create one view containing the form (it can be a page view with other data like articles)<br/>
		<span class="note">Location of the view: <b>themes/your_theme/views/contact.php</b></span>
	</li>
	<li>
		Create your own Tagmanager library which will process the form data.<br/>
		<span class="note">Location of the library: <b>themes/your_theme/libraries/Tagmanager/Contact.php</b></span>
	</li>
</ol>
<p>
	If you want to send one email after form processing, you will also need to:
</p>
<ul>
	<li>
		Create one email view.<br/>
		<span class="note">
			Location is up to you, but as convention, we put the emails views in the folder :
			<b>themes/your_theme/mail/</b>
		</span>
	</li>
	<li>
		Declare the email in <b>/themes/your_theme/config/forms.php</b>
	</li>
</ul>
<hr/>


<h2>Contact</h2>

<!--
	Success message
	Displayed if the form was successfuly validated
-->
<ion:form:contact:validation:success tag="p" class="green" />

<!--
	Error message
	Displayed if the form doesn't pass the validation
	the 'form_message_error' key is located in : themes/your_theme/language/xx/tags_lang.php

-->
<ion:form:contact:validation:error is="true" >
	<span class="red">
		<ion:lang key="form_message_error" />
	</span>
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

	<!--
		Input : Name
	-->
	<label for="name">
		<!-- This translated label key is already set in /application/language/xx/form_lang.php -->
		<ion:lang key="form_label_name" />
	</label>

	<!-- Fills again the user input in case of failed validation -->
	<input type="text" id="name" name="name" value="<ion:form:contact:field:name />"/>

	<!-- Displays the error linked to this input in the validation fails -->
	<ion:form:contact:error:name tag="p" class="input-error" />


    <!--
	   Input : Company
 	-->
    <label for="company">
        <!--
         	This translated label doesn't exists in standard : We add it in:
        	themes/your_theme/language/xx/tags_lang.php
        -->
        <ion:lang key="form_label_company" />
	</label>
	<input type="text" id="company" name="company" value="<ion:form:contact:field:company />"/>
	<ion:form:contact:error:company tag="p" class="input-error" />


    <!--
	   Input : Email
   -->
	<label for="email">
        <!-- This translated label key is already set in /application/language/xx/form_lang.php -->
        <ion:lang key="form_label_email" /><br/>
		<span class="note">The form data will be send to this email address.</span>
	</label>
	<input type="text" id="email" name="email" value="<ion:form:contact:field:email />"/>
	<ion:form:contact:error:email tag="p" class="input-error" />


    <!--
	   Input : You heard about our company...
   -->
    <label>
        <!--
         	This translated label doesn't exists in standard : We add it in:
        	themes/your_theme/language/xx/tags_lang.php
        -->
        <ion:lang key="form_label_heard_on" />
    </label>

    <div class="mb10">

        <!-- Fills again the user input in case of failed validation -->
        <input type="radio" name="heard" id="heard-contact-facebook" value="Facebook" class="left" <ion:form:contact:radio:heard value="Facebook" /> />
        <label for="heard-contact-facebook">
            <ion:lang key="form_label_heard_facebook" />
		</label>

        <input type="radio" name="heard" id="heard-contact-friend" value="one very good friend" class="clear left" <ion:form:contact:radio:heard value="one very good friend"  default="true" /> />
        <label for="heard-contact-friend" >
            <ion:lang key="form_label_heard_friend" /> <span class="note"> (default)</span>
		</label>

        <input type="radio" name="heard" id="heard-contact-website" value="another website" class="clear left" <ion:form:contact:radio:heard value="another website" /> />
        <label for="heard-contact-website" >
            <ion:lang key="form_label_heard_website" />
		</label>

        <ion:form:contact:error:heard tag="p" class="error" />
    </div>


	<!--
		Submit
	-->
	<input type="submit" value="Send the data" />

</form>

