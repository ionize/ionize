<h1>Parent tag</h1>

<p>
	Developper test
</p>
<hr/>

    <ion:user:logged is="true">

        Firstname : <ion:user:form:field:profile:firstname /><br/>
        Last : <ion:user:form:field:profile:lastname /><br/>


		<form method="post" action="">

			<input type="hidden" name="form" value="profile" />

            <label for="firstname-profile"><ion:lang key="form_label_firstname" /></label>
            <input type="text" name="firstname" id="firstname-profile" value="" />

            <label for="lastname-profile"><ion:lang key="form_label_lastname" /></label>
            <input type="text" name="lastname" id="lastname-profile" value="<ion:user:lastname />"/>

            <label for="email-profile"><ion:lang key="form_label_email" /></label>
            <input type="text" name="email" id="email-profile" value="<ion:user:email />" />

            <label for="birthdate-email"><ion:lang key="form_label_birthdate" /></label>
            <input type="text" name="birthdate" id="birthdate-email" />
            <p>Example of Bithdate format : 1975-05-31</p>


            <br/>
			<input type="submit" value="Save profile" />




		</form>

    </ion:user:logged>


