<h1>Theme custom form</h1>

<p>
	This form is defined in the theme
</p>
<hr/>


<h2>My form</h2>

<!--
	Success message
	Displayed if the form was successfuly validated
-->
<ion:form:myform:validation:success tag="p" class="green" />

<!--
	Error message
	Displayed if the form doesn't pass the validation
-->
<ion:form:myform:validation:error tag="p" class="input-error" />

<form method="post" action="">

	<!-- The form name must be set so the tags identify it -->
	<input type="hidden" name="form" value="myform" />

	<label for="name">Name</label>
	<input type="text" id="name" name="name" value="<ion:form:myform:field:name />"/>
	<ion:form:login:error:name tag="p" class="input-error" />

	<label for="company">Company</label>
	<input type="text" id="company" name="company" value="<ion:form:myform:field:company />"/>
	<ion:form:login:error:name tag="p" class="input-error" />

	<label for="email">Email</label>
	<input type="text" id="email" name="email" value="<ion:form:myform:field:email />"/>
	<ion:form:login:error:email tag="p" class="input-error" />

	<input type="submit" value="Send the data" />
</form>

