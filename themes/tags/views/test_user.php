<h1>User</h1>

<p>
</p>
<hr/>


<ion:user:logged is="true">
    <p>User <span class="red"><ion:user:name /></span> logged in</p>
</ion:user:logged>
<ion:user:logged is="false">
    <p class="red">User not logged in</p>
</ion:user:logged>

<h2>Registration</h2>


<!--
	Form validation SUCCESS
	"validation" is a conditional tag.
	No attribute is same than adding the attribute : is="true"
-->
<ion:form:validation name="register">

	<p class="green">Form validation success !</p>

    <!-- Success validation -->
    <ion:success is="true">
		<p class="green"><b>Validation OK</b></p>
        <!-- Success string -->
        <ion:string />
	</ion:success>

</ion:form:validation>

<!--
	Form validation errors OR Form not validated (first display)
-->
<ion:form:validation name="register" is="false">

    <!--
    	Errors during validation ?
    	Conditional tag : Expands if true
    -->
    <ion:error is="true">
        <p class="red"><b>Errors during validation</b></p>
        <!-- Errors string -->
        <ion:string />
    </ion:error>


    <ion:user:logged is="false">

        <ion:error is="false">
            <p>Please fill in the registration form to create one account.</p>
		</ion:error>

        <form method="post" action="">
            <input type="hidden" name="form" value="register" />

            <label for="firstname-reg">First Name</label>
            <input type="text" id="firstname-reg" name="firstname" value="<ion:form:field:register:firstname />" />
            <ion:form:error:register:firstname tag="span" class="red" />
            <br/>

            <label for="lastname-reg">Last Name</label>
            <input type="text" id="lastname-reg" name="lastname" value="<ion:form:field:register:lastname />" />
            <ion:form:error:register:lastname tag="span" class="red" />
            <br/>

            <label for="email-reg">Email</label>
            <input type="text" id="email-reg" name="email" value="<ion:form:field:register:email />"/>
            <ion:form:error:register:email tag="span" class="red" />
            <br/>

            <label for="password-reg">Password</label>
            <input type="password" id="password-reg" name="password" value="<ion:form:field:register:password />"/>
            <br/>

            <input type="submit" value="Register" />
        </form>

    </ion:user:logged>


</ion:form:validation>



<h2>Login</h2>

<ion:user:logged is="false">

	<form method="post" action="">
		<input type="hidden" name="form" value="login" />

		<label for="email">Email</label>
		<input type="text" id="email" name="email"/>
		<br/>

		<label for="password">Password</label>
		<input type="password" id="password" name="password"/>
		<br/>

		<input type="submit" value="Login" />
	</form>

</ion:user:logged>
<ion:else>
	<p>User logged in. First logout to be able to login.</p>
</ion:else>



<h2>Logout</h2>

<ion:user:logged is="true">

    <form method="post" action="">
        <input type="hidden" name="form" value="logout" />
        <input type="submit" value="Logout" />
    </form>

</ion:user:logged>
<ion:else>
    <p>User not logged. First login to be able to logout.</p>
</ion:else>





<h2>Password Back</h2>

<h2>Account Activation</h2>

<h2>Profile</h2>
<p>User must be logged in to edit his profile</p>
