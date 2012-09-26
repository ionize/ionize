<h1>User</h1>

<p>
</p>
<hr/>

<!--
	User login Status
-->

<ion:user:logged is="true">
    <p>
		<b>Status : </b>User <span class="red"><ion:user:name /></span> logged in
	</p>
	<p>
		First name : <b><ion:user:firstname /></b><br/>
		Last name : <b><ion:user:lastname /></b><br/>
		Email : <b><ion:user:email /></b><br/>
		Birthdate : <b><ion:user:birthdate /></b><br/>
		Gender : <b><ion:user:gender /></b><br/>
	</p>
</ion:user:logged>
<ion:user:logged is="false">
    <p><b>Status : </b> <span class="red">User not logged in</span></p>
</ion:user:logged>

<!--
	Login / Logout Forms
-->
<div class="left">
    <h2>Login</h2>

	<!--
		Global Success message
		Displayed if the user successfully logged in
	-->
    <ion:form:validation:success form="login" tag="p" class="green" />

    <!--
	   Global Error message
	   Validation wasn't OK or credentials aren't good
	-->
    <ion:form:validation:error form="login" tag="p" class="red" />


	<!--
		Login Form
		Only displayed if the user isn't logged in
	-->
    <ion:user:logged is="false">

        <form method="post" action="">
            <input type="hidden" name="form" value="login" />

            <label for="email">Email</label>
            <input type="text" id="email" name="email" value="<ion:form:field:login:email />"/>
            <ion:form:error:login:email tag="p" class="red" />

            <br/>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" />
            <ion:form:error:login:password tag="p" class="red" />
            <br/>

            <input type="submit" value="Login" />
        </form>

    </ion:user:logged>
    <ion:else>
        <p>
			User logged in.<br/>
			First logout to be able to login.
		</p>
    </ion:else>


	<!--
		Logout Form
	-->
    <h2>Logout</h2>

    <ion:user:logged is="true">

        <form method="post" action="">
            <input type="hidden" name="form" value="logout" />
            <input type="submit" value="Logout" />
        </form>

    </ion:user:logged>
    <ion:else>
        <p>
			User not logged in.<br/>
			First login to be able to logout.
		</p>
    </ion:else>


	<!--
		Password Back
	-->
    <h2>Password Back</h2>

    <form method="post" action="">

        <input type="hidden" name="form" value="password_back" />

        <label for="email-back"><ion:lang key="form_label_email" /></label>
        <input type="text" name="email" id="email-back" />

        <input type="submit" value="Get Password back" />

    </form>

    <h2>Account Activation</h2>
    <p>Activation link...</p>
    <p></p>


</div>


<!--
	Registration Form
-->
<div class="left">

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
    <ion:form:validation:result form="register">

        <p class="green">Form validation success !</p>

        <!-- Success validation -->
        <ion:form:validation:success form="register" is="true">
            <p class="green"><b>Validation OK</b></p>
            <!-- Success string -->
            <ion:form:validation:success form="register"/>
        </ion:form:validation:success>

    </ion:form:validation:result>

    <!--
	   Form validation errors OR Form not validated (first display)
   -->
    <ion:form:validation:result form="register" is="false">

		<!--
			Errors during validation ?
			Conditional tag : Expands if true
		-->
        <ion:form:validation:error form="register" is="true">
			<p class="red"><b>Errors during validation</b></p>
		</ion:form:validation:error>

		<ion:user:logged is="false">

			<ion:error is="false">
				<p>Please fill in the registration form to create one account.</p>
			</ion:error>

			<form method="post" action="">
				<input type="hidden" name="form" value="register" />

				<label for="firstname-reg">First Name</label>
				<input type="text" id="firstname-reg" name="firstname" value="<ion:form:field:register:firstname />" />
				<ion:form:error:register:firstname tag="p" class="red" />
				<br/>

				<label for="lastname-reg">Last Name</label>
				<input type="text" id="lastname-reg" name="lastname" value="<ion:form:field:register:lastname />" />
				<ion:form:error:register:lastname tag="p" class="red" />
				<br/>

				<label for="email-reg">Email</label>
				<input type="text" id="email-reg" name="email" value="<ion:form:field:register:email />"/>
				<ion:form:error:register:email tag="p" class="red" />
				<br/>

				<label for="password-reg">Password</label>
				<input type="password" id="password-reg" name="password" value="<ion:form:field:register:password />"/>
				<br/>

				<input type="submit" value="Register" />
			</form>

		</ion:user:logged>

    </ion:form:validation:result>
</div>


<div class="left">








    <h2>Profile</h2>
    <ion:user:logged is="false">
    	<p>
			User must be logged in to edit his profile
		</p>
	</ion:user:logged>

	<!--
	   Success message
	-->
    <ion:form:validation:success form="profile" tag="p" class="green" />

    <!--
	   Error message
	-->
    <ion:form:validation:error form="profile" tag="p" class="red" />


    <!--
	   Profile Form
   -->
    <ion:user:logged is="true">

		<form method="post" action="">

			<input type="hidden" name="form" value="profile" />

            <label for="firstname-profile"  <ion:form:error:profile:firstname is='true' return=' class="red"' />>
				<ion:lang key="form_label_firstname" />
			</label>
            <input type="text" name="firstname" id="firstname-profile" value="<ion:user:form:field:profile:firstname />" />
            <ion:form:error:profile:firstname tag="p" class="red" />

            <label for="lastname-profile">
				<ion:lang key="form_label_lastname" />
			</label>
            <input type="text" name="lastname" id="lastname-profile" value="<ion:user:form:field:profile:lastname />"/>
            <ion:form:error:profile:lastname tag="p" class="red" />

            <label for="email-profile" <ion:form:error:profile:email is='true' return=' class="red"' /> >
				<ion:lang key="form_label_email" />
			</label>
            <input type="text" name="email" id="email-profile" value="<ion:user:form:field:profile:email />" />
            <ion:form:error:profile:email tag="p" class="red" />

            <label for="birthdate-profile"><ion:lang key="form_label_birthdate" /></label>
            <input type="text" name="birthdate" id="birthdate-profile"  value="<ion:user:form:field:profile:birthdate />"/>
            <p>Example of Bithdate format : 1975-05-31</p>


            <label for="password-profile"><ion:lang key="form_label_password" /></label>
			<p>To keep the current one, leave empty</p>
            <input type="password" name="password" id="password-profile" />

            <label for="delete-profile"><ion:lang key="form_label_delete_account" /></label>
			<input type="checkbox" name="delete" value="1" id="delete-profile" />

            <br/>
			<input type="submit" value="Save profile" />

		</form>

    </ion:user:logged>


</div>