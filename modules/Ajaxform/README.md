Ionize Ajaxform module
=======================

Version : 1.0

Ionize version : 1.0.6

Released on april 2014

### About

Post forms through Ajax with this module.


### Authors

[Michel-Ange Kuntz](http://www.partikule.net)


### Installation

* Copy the folder "Ajaxform" into the "/modules" folder of your Ionize installation.
* In the ionize backend, go to : Modules > Administration
* Click on "install"

### How does it work ?

1.  The form must be declared in /themes/your_theme/config/forms.php.
	See the file /modules/Ajaxform/config/forms.php for more details.
	This file should be copied and adapted in /themes/your_theme/config/forms.php.

2.  The form is displayed in one page or article view,through ionize tags.
	One JS script, added to the form by the <ion:form /> tag handles the Ajax post





### Usage

1. Create your form view and declare your form.

Example :


<ion:form ajax="true" name="contact" submit="contactFormSubmit">

	<form method="post" name="contact">

		<div class="form-group">
            <label for="name"><ion:lang key='form_label_name' /></label>
            <input type="text" value="<ion:contact:field:name />" placeholder="<ion:lang key='form_placeholder_name' />" name="name" class="form-control" >
        </div>

		<div class="form-group col-xs-6">
			<label for="email"><ion:lang key='form_label_email' /></label>
			<input type="email" value="<ion:contact:field:email />" placeholder="<ion:lang key='form_placeholder_email' />" name="email" class="form-control">
		</div>

	</form>

</ion:form>

2. Setup your form in /themes/your_theme/config/form.php

Copy the file /modules/Ajaxform/config/forms.php and adapt it to your fields.







