<ion:partial view="header" />

<ion:partial view="page_header" />


	<div class="row">
        <div class="large-9 small-12 columns">

			<!-- User not logged in -->
            <ion:user:logged is="false">

				<!-- Articles of this page with the type "not-logged-in" -->
				<ion:page:articles type="not-logged-in">
					<ion:article>
						<ion:title tag="h3" />
						<ion:content />
					</ion:article>
				</ion:page:articles>


				<!--
					PROFILE : Success message
				-->
				<ion:form:profile:validation:success is="true">
					<div class="alert-box success">
						<ion:lang key="form_alert_success_title" tag="h4" />
						<ion:form:profile:validation:success tag="p" />
						<a href="" class="close">&times;</a>
					</div>
				</ion:form:profile:validation:success>


				<!--
					PROFILE : Error message
				-->
				<ion:form:profile:validation:error is="true">
					<div class="alert-box success">
						<ion:lang key="form_alert_error_title" tag="h4" />
						<ion:form:profile:validation:error tag="p" />
						<a href="" class="close">&times;</a>
					</div>
				</ion:form:profile:validation:error>


				<!--
					LOGIN : Error message
				-->
				<ion:form:login:validation:error is="true" >
					<div class="alert-box alert">
						<ion:lang key="form_alert_error_title" tag="h4" />
						<ion:form:login:validation:error tag="p" />
						<a href="" class="close">&times;</a>
					</div>
				</ion:form:login:validation:error>


				<!--
					PASSWORD BACK : Success message
				-->
				<ion:form:password:validation:success is="true">
					<div class="alert-box success">
						<ion:lang key="form_alert_success_title" tag="h4" />
						<ion:lang key="form_register_success_message" tag="p" />
						<a href="" class="close">&times;</a>
					</div>
				</ion:form:password:validation:success>

				<!--
					PASSWORD BACK : Error message
				-->
				<ion:form:password:validation:error is="true" >
					<div class="alert-box alert">
						<ion:lang key="form_alert_error_title" tag="h4" />
						<ion:form:password:validation:error tag="p" />
						<a href="" class="close">&times;</a>
					</div>
				</ion:form:password:validation:error>


				<!--
					REGISTRATION : Success message
					Displayed if the form was successfuly validated
				-->
				<ion:form:register:validation:success is="true">
					<div class="alert-box success">
						<ion:lang key="form_alert_success_title" tag="h4" />
						<ion:lang key="form_register_success_message" tag="p" />
						<a href="" class="close">&times;</a>
					</div>
				</ion:form:register:validation:success>

				<!--
					REGISTRATION : Error message
					Displayed if the form doesn't pass the validation
					the 'form_message_error' key is located in : themes/your_theme/language/xx/tags_lang.php
				-->
				<ion:form:register:validation:error is="true" >
					<div class="alert-box alert">
						<ion:lang key="form_alert_error_title" tag="h4" />
						<ion:form:register:validation:error tag="p" />
						<a href="" class="close">&times;</a>
					</div>
				</ion:form:register:validation:error>


				<!-- REGISTRATION : form -->
                <form method="post" action="">
                    <input type="hidden" name="form" value="register" />

                    <label for="firstname-reg"><ion:lang key="form_label_firstname" /></label>
                    <input type="text" id="firstname-reg" name="firstname" value="<ion:form:register:field:firstname />" />
                    <ion:form:register:error:firstname tag="small" class="error" />

                    <label for="lastname-reg"><ion:lang key="form_label_lastname" /></label>
                    <input type="text" id="lastname-reg" name="lastname" value="<ion:form:register:field:lastname />" />
                    <ion:form:register:error:lastname tag="small" class="error" />

                    <label for="screen_name-reg"><ion:lang key="form_label_screen_name" /></label>
                    <input type="text" id="screen_name-reg" name="screen_name" value="<ion:form:register:field:screen_name />" />
                    <ion:form:register:error:screen_name tag="small" class="error" />

                    <label for="email-reg"><ion:lang key="form_label_email" /></label>
                    <input type="text" id="email-reg" name="email" value="<ion:form:register:field:email />"/>
                    <ion:form:register:error:email tag="small" class="error" />

                    <label for="password-reg"><ion:lang key="form_label_password" /></label>
                    <input type="password" id="password-reg" name="password" value="<ion:form:register:field:password />"/>
					<ion:form:register:error:password tag="small" class="error" />

					<input type="submit" class="button submit success" value="<ion:lang key='form_button_register' />" />
                </form>


			</ion:user:logged>

            <!-- User logged in -->
            <ion:user:logged is="true">

                <!-- Articles of this page with the type "logged-in" -->
                <ion:page:articles type="logged-in">
                    <ion:article>
                        <h3><ion:title /> <ion:user:name /></h3>
                        <ion:content />
                    </ion:article>
                </ion:page:articles>

				<!-- Profile form -->
                <form method="post" action="" class="custom">

                    <input type="hidden" name="form" value="profile" />

                    <label for="firstname-profile"  <ion:form:profile:error:firstname is='true' return=' class="error"' />>
                    	<ion:lang key="form_label_firstname" />
                    </label>
                    <input type="text" name="firstname" id="firstname-profile" value="<ion:user:form:profile:field:firstname />" />
                    <ion:form:profile:error:firstname tag="small" class="error" />

                    <label for="lastname-profile">
                        <ion:lang key="form_label_lastname" />
                    </label>
                    <input type="text" name="lastname" id="lastname-profile" value="<ion:user:form:profile:field:lastname />"/>
                    <ion:form:profile:error:lastname tag="small" class="error" />

                    <label for="screen_name-profile">
                        <ion:lang key="form_label_screen_name" />
                    </label>
                    <input type="text" id="screen_name-profile" name="screen_name" value="<ion:user:form:profile:field:screen_name />" />
                    <ion:form:profile:error:screen_name tag="small" class="error" />
                    <br/>

                    <label for="email-profile" <ion:form:profile:error:email is='true' return=' class="error"' /> >
                    	<ion:lang key="form_label_email" />
                    </label>
                    <input type="text" name="email" id="email-profile" value="<ion:user:form:profile:field:email />" />
                    <ion:form:profile:error:email tag="small" class="error" />

                    <label for="birthdate-profile">
                        <ion:lang key="form_label_birthdate" />
                    </label>
                    <p class="note"><ion:lang key="form_note_birthdate_format" /></p>
                    <input type="text" name="birthdate" id="birthdate-profile"  value="<ion:user:form:profile:field:birthdate />"/>

                    <!-- Radio boxes : Gender -->
                    <label>
                        <ion:lang key="form_label_gender" />
                    </label>

					<ion:user:form:profile:field:gender />

                        <label for="gender-profile-male">
                            <input type="radio" name="gender" id="gender-profile-male" value="1" <ion:user:form:profile:radio:gender value="1" default="true" /> />
							<ion:lang key="form_label_gender_male" />
						</label>

                        <label for="gender-profile-female" >
                            <input type="radio" name="gender" id="gender-profile-female" value="2" <ion:user:form:profile:radio:gender value="2"  /> />
							<ion:lang key="form_label_gender_female" />
						</label>

                        <label for="gender-profile-unisex" >
                            <input type="radio" name="gender" id="gender-profile-unisex" value="3" class="clear left" <ion:user:form:profile:radio:gender value="3"  /> />
                            <ion:lang key="form_label_gender_unisex" />
                        </label>

                        <ion:form:profile:error:gender tag="small" class="error" />
						<br/>

                    <label for="password-profile">
                        <ion:lang key="form_label_password" />
                    </label>
                    <p class="note"><ion:lang key="form_note_password_change" /></p>
                    <input type="password" name="password" id="password-profile" />
                    <ion:form:profile:error:password tag="small" class="error" />

                    <label for="delete-profile">
                        <input type="checkbox" name="delete" value="1" id="delete-profile" />
                        <ion:lang key="form_label_delete_account" />
                    </label>

					<br/>
                    <input type="submit" class="button success" value="<ion:lang key='form_button_save_profile' />" />

                </form>

			</ion:user:logged>
		</div>

		<!-- Side bar -->
		<div class="large-3 small-12 columns">

			<div class="side-block">

				<ion:user:logged is="true">

                    <!-- Logout form -->
					<h3><ion:lang key="title_logout" /></h3>

					<form method="post" action="">
						<input type="hidden" name="form" value="logout" />
						<input type="submit" class="button success" value="<ion:lang key='form_button_logout' />" />
					</form>

				</ion:user:logged>


                <ion:user:logged is="false">

					<!-- Login form -->
                    <h3><ion:lang key="title_login" /></h3>

                    <form method="post" action="">
                        <input type="hidden" name="form" value="login" />

                        <label for="email"><ion:lang key="form_label_email" /></label>
                        <input type="text" id="email" name="email" value="<ion:form:login:field:email />"/>
                        <ion:form:login:error:email tag="small" class="error" />

                        <label for="password"><ion:lang key="form_label_password" /></label>
                        <input type="password" id="password" name="password" />
                        <ion:form:login:error:password tag="small" class="error" />

                        <input type="submit" class="button success" value="<ion:lang key='form_button_login' />" />
                    </form>


                    <!-- Password back form -->
                    <h3><ion:lang key="title_password_back" /></h3>

                    <form method="post" action="">
                        <input type="hidden" name="form" value="password" />
                        <label for="email-back"><ion:lang key="form_label_email" /></label>
                        <input type="text" name="email" id="email-back" />
                        <input type="submit"  class="button success" value="<ion:lang key='form_button_password_back' />" />
                    </form>

				</ion:user:logged>

			</div>
		</div>
    </div>



<ion:partial view="footer" />
