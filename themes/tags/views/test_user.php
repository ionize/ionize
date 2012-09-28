<h1>User</h1>

<p>
	This view tests 2 tags : <b>user</b> and <b>form</b>.<br/>
</p>
<p>
	Have a look at the source code to see details of tgas usage.
</p>
<hr/>


<!--

	User login Status

------------------------------------------------------------------------------ -->

<h2>User Infos</h2>

<ion:user:logged is="true">

    <p>
        <b>Status : </b>User <span class="green"><ion:user:name /></span> logged in
    </p>

    <div class="left">
		<!-- User data, as stored in the user tag -->
		<p>
			First name : <b><ion:user:firstname /></b><br/>
			Last name : <b><ion:user:lastname /></b><br/>
			Email : <b><ion:user:email /></b><br/>
			Birthdate : <b><ion:user:birthdate /></b><br/>
			Gender : <b><ion:user:gender /></b><br/>
		</p>
	</div>

    <div class="left">
		<p>
			Group : <b><ion:user:group:name /></b><br />
			Group Title : <b><ion:user:group:title /></b><br />
			Group Level : <b><ion:user:group:level /></b><br />
		</p>
	</div>
</ion:user:logged>

<ion:user:logged is="false">
    <p><b>Status : </b> <span class="red">User not logged in</span></p>
</ion:user:logged>

<hr style="clear:both;" />


<!--

	Login / Logout

------------------------------------------------------------------------------ -->

<div class="bloc left">

	<h2>Login</h2>

	
	<!--
		Global Success message
		Displayed if the user successfully logged in
	-->
    <ion:form:login:validation:success tag="p" class="green" />

    <!--
	   Global Error message
	   Validation wasn't OK or credentials aren't good
	-->
    <ion:form:login:validation:error tag="p" class="input-error" />


	<!--
		Login Form
		Only displayed if the user isn't logged in
	-->
    <ion:user:logged is="false">

        <form method="post" action="">
            <input type="hidden" name="form" value="login" />

            <label for="email">Email</label>
            <input type="text" id="email" name="email" value="<ion:form:login:field:email />"/>
            <ion:form:login:error:email tag="p" class="input-error" />

            <br/>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" />
            <ion:form:login:error:password tag="p" class="input-error" />
            <br/>

            <input type="submit" value="Login" />
        </form>

    </ion:user:logged>
    <ion:else>
        <p>User <ion:user:name tag="span" class="green" /> logged in.</p>
		<p>First logout to be able to login.</p>
    </ion:else>


	<!--
		Logout
	-->
    <h2>Logout</h2>

    <ion:user:logged is="true">

        <form method="post" action="">
            <input type="hidden" name="form" value="logout" />
            <input type="submit" value="Logout" />
        </form>

    </ion:user:logged>
    <ion:else>
        <span class="red">User not logged in</span>
        <p>First login to be able to logout.</p>
    </ion:else>

</div>


<!--

	Registration / Password back

------------------------------------------------------------------------------ -->

<div class="bloc left">

    <h2>Registration</h2>

	<!--
		Message to the not logged user : The registration form is only available for looged users
	-->
    <ion:user:logged is="true">
		<p>
			The registration form is only available for not logged user. <br/>
			If you're logged in, you're supposed to have one account...
		</p>
    </ion:user:logged>


    <!--
	   Form validation SUCCESS
	   "validation:result" is a conditional tag.
	   If the attribute "is" is not set, the tag suppose we ask for is="true"
   -->
    <ion:form:register:validation:result>

        <!-- Success validation message test -->
        <ion:form:register:validation:success is="true">

			<!-- Success message display -->
            <ion:form:register:validation:success tag="p" class="green"/>

		</ion:form:register:validation:success>

    </ion:form:register:validation:result>

    <!--
	   Form validation errors OR Form not validated (first display)
   -->
    <ion:form:register:validation:result is="false">

		<!--
			Errors during validation ?
			Conditional tag : Expands if true
		-->
        <ion:form:register:validation:error is="true">
			<p class="red"><b>Errors during validation</b></p>
		</ion:form:register:validation:error>

		<ion:user:logged is="false">

            <ion:form:register:validation:error is="false">
				<p>Please fill in the registration form to create one account.</p>
            </ion:form:register:validation:error>

			<form method="post" action="">
				<input type="hidden" name="form" value="register" />

				<label for="firstname-reg">First Name</label>
				<input type="text" id="firstname-reg" name="firstname" value="<ion:form:register:field:firstname />" />
				<ion:form:register:error:firstname tag="p" class="input-error" />
				<br/>

				<label for="lastname-reg">Last Name</label>
				<input type="text" id="lastname-reg" name="lastname" value="<ion:form:register:field:lastname />" />
				<ion:form:register:error:lastname tag="p" class="input-error" />
				<br/>

				<label for="screen_name-reg">Displayed Screen name</label>
				<input type="text" id="screen_name-reg" name="screen_name" value="<ion:form:register:field:screen_name />" />
				<ion:form:register:error:screen_name tag="p" class="input-error" />
				<br/>

				<label for="email-reg">Email</label>
				<input type="text" id="email-reg" name="email" value="<ion:form:register:field:email />"/>
				<ion:form:register:error:email tag="p" class="input-error" />
				<br/>

				<label for="password-reg">Password</label>
				<input type="password" id="password-reg" name="password" value=""/>
				<br/>

				<input type="submit" value="Register" />
			</form>

		</ion:user:logged>

    </ion:form:register:validation:result>


    <!--
	   Password Back
	-->
    <h2>Password Back</h2>

    <ion:user:logged is="true">
		<p>
			The "password back" form is supposed to be available only if the user isn't logged in.
		</p>
	</ion:user:logged>

    <ion:user:logged is="false">

		<!-- Success / Error messages -->
		<ion:form:password:validation:success tag="p" class="green" />
		<ion:form:password:validation:error tag="p" class="input-error" />

		<form method="post" action="">

			<input type="hidden" name="form" value="password" />

			<label for="email-back"><ion:lang key="form_label_email" /></label>
			<input type="text" name="email" id="email-back" />

			<br/>
			<input type="submit" value="Get Password back" />

		</form>

	</ion:user:logged>


</div>



<!--

	Profile

------------------------------------------------------------------------------ -->
<div class="bloc left">

    <h2>Profile</h2>

	<!-- Message if user is not logged in : No profile edition -->
    <ion:user:logged is="false">
		<span class="red">User not logged in</span>
        <p>User must be logged in to edit his profile</p>
	</ion:user:logged>

	<!--
	   Success message
	-->
    <ion:form:profile:validation:success tag="p" class="green" />

    <!--
	   Error message
	-->
    <ion:form:profile:validation:error tag="p" class="input-error" />


    <!--
	   Profile Form
   -->
    <ion:user:logged is="true">

		<form method="post" action="">

			<input type="hidden" name="form" value="profile" />

            <label for="firstname-profile"  <ion:form:profile:error:firstname is='true' return=' class="error"' />>
				<ion:lang key="form_label_firstname" />
			</label>
            <input type="text" name="firstname" id="firstname-profile" value="<ion:user:form:profile:field:firstname />" />
            <ion:form:profile:error:firstname tag="p" class="input-error" />

            <label for="lastname-profile">
				<ion:lang key="form_label_lastname" />
			</label>
            <input type="text" name="lastname" id="lastname-profile" value="<ion:user:form:profile:field:lastname />"/>
            <ion:form:profile:error:lastname tag="p" class="input-error" />

            <label for="screen_name-profile">
                <ion:lang key="form_label_screen_name" />
            </label>
            <input type="text" id="screen_name-profile" name="screen_name" value="<ion:form:profile:field:screen_name />" />
            <ion:form:profile:error:screen_name tag="p" class="input-error" />
            <br/>

            <label for="email-profile" <ion:form:profile:error:email is='true' return=' class="error"' /> >
				<ion:lang key="form_label_email" />
			</label>
            <input type="text" name="email" id="email-profile" value="<ion:user:form:profile:field:email />" />
            <ion:form:profile:error:email tag="p" class="input-error" />

            <label for="birthdate-profile">
				<ion:lang key="form_label_birthdate" />
			</label>
            <p class="note">Example of Bithdate format : 1975-05-31</p>
            <input type="text" name="birthdate" id="birthdate-profile"  value="<ion:user:form:profile:field:birthdate />"/>


			<!--
				Radio boxes : Gender
			-->
            <label>
                <ion:lang key="form_label_gender" />
            </label>

            <div class="mb10">
                <input type="radio" name="gender" id="gender-profile-male" value="1" class="left" <ion:user:form:profile:radio:gender value="1" default="true" /> />
                <label for="gender-profile-male">Male</label>

                <input type="radio" name="gender" id="gender-profile-female" value="2" class="clear left" <ion:user:form:profile:radio:gender value="2"  /> />
                <label for="gender-profile-female" >Female</label>

                <input type="radio" name="gender" id="gender-profile-unisex" value="3" class="clear left" <ion:user:form:profile:radio:gender value="3"  /> />
                <label for="gender-profile-unisex" >I don't know</label>

                <ion:form:profile:error:gender tag="p" class="error" />
            </div>

            <label for="password-profile">
				<ion:lang key="form_label_password" />
			</label>
			<p class="note">To keep the current one, leave empty</p>
            <input type="password" name="password" id="password-profile" />
            <ion:form:profile:error:password tag="p" class="input-error" />

            <label for="delete-profile">
				<ion:lang key="form_label_delete_account" />
			</label>
			<input type="checkbox" name="delete" value="1" id="delete-profile" />

            <br/>
			<input type="submit" value="Save profile" />

		</form>

    </ion:user:logged>
</div>


<hr class="clear"/>



	
<!--

	Code

------------------------------------------------------------------------------ -->

<h2>Login</h2>
	
<pre>
&lt;!--
	Global Success message
	Displayed if the user successfully logged in
-->
&lt;ion:form:login:validation:success tag="p" class="green" />

&lt;!--
   Global Error message
   Validation wasn't OK or credentials aren't good
-->
&lt;ion:form:login:validation:error tag="p" class="input-error" />


&lt;!--
	Login Form
	Only displayed if the user isn't logged in
-->
&lt;ion:user:logged is="false">

	&lt;form method="post" action="">
		&lt;input type="hidden" name="form" value="login" />

		&lt;label for="email">Email&lt;/label>
		&lt;input type="text" id="email" name="email" value="&lt;ion:form:login:field:email />"/>
		&lt;ion:form:login:error:email tag="p" class="input-error" />

		&lt;br/>

		&lt;label for="password">Password&lt;/label>
		&lt;input type="password" id="password" name="password" />
		&lt;ion:form:login:error:password tag="p" class="input-error" />
		&lt;br/>

		&lt;input type="submit" value="Login" />
	&lt;/form>

&lt;/ion:user:logged>
&lt;ion:else>
	&lt;p>User &lt;ion:user:name tag="span" class="green" /> logged in.&lt;/p>
	&lt;p>First logout to be able to login.&lt;/p>
&lt;/ion:else>
</pre>


<h2>Logout</h2>

<pre>
&lt;ion:user:logged is="true">

	&lt;form method="post" action="">
		&lt;input type="hidden" name="form" value="logout" />
		&lt;input type="submit" value="Logout" />
	&lt;/form>

&lt;/ion:user:logged>
&lt;ion:else>
	&lt;span class="red">User not logged in&lt;/span>
	&lt;p>First login to be able to logout.&lt;/p>
&lt;/ion:else>
</pre>


<h2>Register</h2>
<pre>
&lt;!--
	Message to the not logged user : The registration form is only available for looged users
-->
&lt;ion:user:logged is="true">
	&lt;p>
		The registration form is only available for not logged user. &lt;br/>
		If you're logged in, you're supposed to have one account...
	&lt;/p>
&lt;/ion:user:logged>


&lt;!--
   Form validation SUCCESS
   "validation:result" is a conditional tag.
   If the attribute "is" is not set, the tag suppose we ask for is="true"
-->
&lt;ion:form:register:validation:result>

	&lt;!-- Success validation message test -->
	&lt;ion:form:register:validation:success is="true">

		&lt;!-- Success message display -->
		&lt;ion:form:register:validation:success tag="p" class="green"/>

	&lt;/ion:form:register:validation:success>

&lt;/ion:form:register:validation:result>

&lt;!--
   Form validation errors OR Form not validated (first display)
-->
&lt;ion:form:register:validation:result is="false">

	&lt;!--
	  Errors during validation ?
	  Conditional tag : Expands if true
  	-->
	&lt;ion:form:register:validation:error is="true">
		&lt;p class="red">&lt;b>Errors during validation&lt;/b>&lt;/p>
	&lt;/ion:form:register:validation:error>

	&lt;ion:user:logged is="false">

		&lt;ion:form:register:validation:error is="false">
			&lt;p>Please fill in the registration form to create one account.&lt;/p>
		&lt;/ion:form:register:validation:error>

		&lt;form method="post" action="">
			&lt;input type="hidden" name="form" value="register" />

			&lt;label for="firstname-reg">First Name&lt;/label>
			&lt;input type="text" id="firstname-reg" name="firstname" value="&lt;ion:form:register:field:firstname />" />
			&lt;ion:form:register:error:firstname tag="p" class="input-error" />
			&lt;br/>

			&lt;label for="lastname-reg">Last Name&lt;/label>
			&lt;input type="text" id="lastname-reg" name="lastname" value="&lt;ion:form:register:field:lastname />" />
			&lt;ion:form:register:error:lastname tag="p" class="input-error" />
			&lt;br/>

			&lt;label for="screen_name-reg">Displayed Screen name&lt;/label>
			&lt;input type="text" id="screen_name-reg" name="screen_name" value="&lt;ion:form:register:field:screen_name />" />
			&lt;ion:form:register:error:screen_name tag="p" class="input-error" />
			&lt;br/>

			&lt;label for="email-reg">Email&lt;/label>
			&lt;input type="text" id="email-reg" name="email" value="&lt;ion:form:register:field:email />"/>
			&lt;ion:form:register:error:email tag="p" class="input-error" />
			&lt;br/>

			&lt;label for="password-reg">Password&lt;/label>
			&lt;input type="password" id="password-reg" name="password" value=""/>
			&lt;br/>

			&lt;input type="submit" value="Register" />
		&lt;/form>

	&lt;/ion:user:logged>

&lt;/ion:form:register:validation:result>
</pre>


<h2>Password back</h2>
<pre>
&lt;ion:user:logged is="true">
	&lt;p>
		The "password back" form is supposed to be available only if the user isn't logged in.
	&lt;/p>
&lt;/ion:user:logged>

&lt;ion:user:logged is="false">

	&lt;!-- Success / Error messages -->
	&lt;ion:form:password:validation:success tag="p" class="green" />
	&lt;ion:form:password:validation:error tag="p" class="input-error" />

	&lt;form method="post" action="">

		&lt;input type="hidden" name="form" value="password" />

		&lt;label for="email-back">&lt;ion:lang key="form_label_email" />&lt;/label>
		&lt;input type="text" name="email" id="email-back" />

		&lt;br/>
		&lt;input type="submit" value="Get Password back" />

	&lt;/form>

&lt;/ion:user:logged>
</pre>

<h2>Profile</h2>
<pre>
&lt;!-- Message if user is not logged in : No profile edition -->
&lt;ion:user:logged is="false">
	&lt;span class="red">User not logged in&lt;/span>
	&lt;p>User must be logged in to edit his profile&lt;/p>
&lt;/ion:user:logged>

&lt;!--
   Success message
-->
&lt;ion:form:profile:validation:success tag="p" class="green" />

&lt;!--
   Error message
-->
&lt;ion:form:profile:validation:error tag="p" class="input-error" />


&lt;!--
   Profile Form
-->
&lt;ion:user:logged is="true">

	&lt;form method="post" action="">

		&lt;input type="hidden" name="form" value="profile" />

		&lt;label for="firstname-profile"  &lt;ion:form:profile:error:firstname is='true' return=' class="error"' />>
		&lt;ion:lang key="form_label_firstname" />
		&lt;/label>
		&lt;input type="text" name="firstname" id="firstname-profile" value="&lt;ion:user:form:profile:field:firstname />" />
		&lt;ion:form:profile:error:firstname tag="p" class="input-error" />

		&lt;label for="lastname-profile">
			&lt;ion:lang key="form_label_lastname" />
		&lt;/label>
		&lt;input type="text" name="lastname" id="lastname-profile" value="&lt;ion:user:form:profile:field:lastname />"/>
		&lt;ion:form:profile:error:lastname tag="p" class="input-error" />

		&lt;label for="screen_name-profile">
			&lt;ion:lang key="form_label_screen_name" />
		&lt;/label>
		&lt;input type="text" id="screen_name-profile" name="screen_name" value="&lt;ion:form:profile:field:screen_name />" />
		&lt;ion:form:profile:error:screen_name tag="p" class="input-error" />
		&lt;br/>

		&lt;label for="email-profile" &lt;ion:form:profile:error:email is='true' return=' class="error"' /> >
		&lt;ion:lang key="form_label_email" />
		&lt;/label>
		&lt;input type="text" name="email" id="email-profile" value="&lt;ion:user:form:profile:field:email />" />
		&lt;ion:form:profile:error:email tag="p" class="input-error" />

		&lt;label for="birthdate-profile">
			&lt;ion:lang key="form_label_birthdate" />
		&lt;/label>
		&lt;p class="note">Example of Bithdate format : 1975-05-31&lt;/p>
		&lt;input type="text" name="birthdate" id="birthdate-profile"  value="&lt;ion:user:form:profile:field:birthdate />"/>


		&lt;!--
			Radio boxes : Gender
	 	-->
		&lt;label>
			&lt;ion:lang key="form_label_gender" />
		&lt;/label>

		&lt;div class="mb10">
			&lt;input type="radio" name="gender" id="gender-profile-male" value="1" class="left" &lt;ion:user:form:profile:radio:gender value="1" default="true" /> />
			&lt;label for="gender-profile-male">Male&lt;/label>

			&lt;input type="radio" name="gender" id="gender-profile-female" value="2" class="clear left" &lt;ion:user:form:profile:radio:gender value="2"  /> />
			&lt;label for="gender-profile-female" >Female&lt;/label>

			&lt;input type="radio" name="gender" id="gender-profile-unisex" value="3" class="clear left" &lt;ion:user:form:profile:radio:gender value="3"  /> />
			&lt;label for="gender-profile-unisex" >I don't know&lt;/label>

			&lt;ion:form:profile:error:gender tag="p" class="error" />
		&lt;/div>

		&lt;!--
			Example with checkboxes
		-->
        &lt;div class="mb10">
            &lt;input type="checkbox" name="gender[]" id="gender-profile-male" value="1" class="left" &lt;ion:user:form:profile:checkbox:gender value="1" default="true" /> />
            &lt;label for="gender-profile-male">Male&lt;/label>

            &lt;input type="checkbox" name="gender[]" id="gender-profile-female" value="2" class="clear left" &lt;ion:user:form:profile:checkbox:gender value="2"  /> />
            &lt;label for="gender-profile-female" >Female&lt;/label>

            &lt;input type="checkbox" name="gender[]" id="gender-profile-unisex" value="3" class="clear left" &lt;ion:user:form:profile:checkbox:gender value="3"  /> />
            &lt;label for="gender-profile-unisex" >I don't know&lt;/label>

            &lt;ion:form:profile:error:gender tag="p" class="error" />
        &lt;/div>

		&lt;!--
			Example with multi select
		-->
        &lt;div class="mb10">
            &lt;select type="select" name="gender[]" multiple="true">
                &lt;option value="1" &lt;ion:user:form:profile:select:gender value='1' />>Male&lt;/option>
                &lt;option value="2" &lt;ion:user:form:profile:select:gender value='2' />>Female&lt;/option>
                &lt;option value="3" &lt;ion:user:form:profile:select:gender value='3' />>I don't know&lt;/option>
            &lt;/select>

            &lt;ion:form:profile:error:gender tag="p" class="error" />
        &lt;/div>

        &lt;label for="password-profile">
			&lt;ion:lang key="form_label_password" />
		&lt;/label>
		&lt;p class="note">To keep the current one, leave empty&lt;/p>
		&lt;input type="password" name="password" id="password-profile" />
		&lt;ion:form:profile:error:password tag="p" class="input-error" />

		&lt;label for="delete-profile">
			&lt;ion:lang key="form_label_delete_account" />
		&lt;/label>
		&lt;input type="checkbox" name="delete" value="1" id="delete-profile" />

		&lt;br/>
		&lt;input type="submit" value="Save profile" />

	&lt;/form>

&lt;/ion:user:logged>
</pre>