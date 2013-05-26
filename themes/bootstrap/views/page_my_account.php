<ion:partial view="header" />

<section class="container">

    <section>
        <div class="span12 page-header m0">
            <h1>
                <ion:page:title />
                <ion:page:subtitle tag="small" />
            </h1>
        </div>
        <div class="clearfix"></div>

        <section>
            <div class="span8">
                <div class="p10">
                    <!-- User not logged in -->
                    <ion:user:logged is="false">

                        <!-- Articles of this page with the type "not-logged-in" -->
                        <ion:page:articles type="not-logged-in">
                            <ion:article>
                                <ion:title tag="h3" class="dotted-title" />
                                <ion:content />
                            </ion:article>
                        </ion:page:articles>

                        <!-- Registration form -->
                        <form class="form-horizontal" method="post" action="">
                            <input type="hidden" name="form" value="register" />

                            <div class="control-group<ion:form:register:error:firstname is="true"> error</ion:form:register:error:firstname>">
                                <label class="control-label" for="firstname-reg"><ion:lang key="form_label_firstname" /></label>
                                <div class="controls">
                                    <input class="span4" name="firstname" type="text" id="firstname-reg" value="<ion:form:register:field:firstname />" />
                                    <ion:form:register:error:firstname tag="span" class="help-inline" />
                                </div>
                            </div>

                            <div class="control-group<ion:form:register:error:lastname is="true"> error</ion:form:register:error:lastname>">
                                <label class="control-label" for="lastname-reg"><ion:lang key="form_label_lastname" /></label>
                                <div class="controls">
                                    <input class="span4" name="lastname" type="text" id="lastname-reg" value="<ion:form:register:field:lastname />" />
                                    <ion:form:register:error:lastname tag="span" class="help-inline" />
                                </div>
                            </div>

                            <div class="control-group<ion:form:register:error:screen_name is="true"> error</ion:form:register:error:screen_name>">
                                <label class="control-label" for="screen_name-reg"><ion:lang key="form_label_screen_name" /></label>
                                <div class="controls">
                                    <input class="span4" name="screen_name" type="text" id="screen_name-reg" value="<ion:form:register:field:screen_name />" />
                                    <ion:form:register:error:screen_name tag="span" class="help-inline" />
                                </div>
                            </div>

                            <div class="control-group<ion:form:register:error:email is="true"> error</ion:form:register:error:email>">
                                <label class="control-label" for="email-reg"><ion:lang key="form_label_email" /></label>
                                <div class="controls">
                                    <input class="span4" name="email" type="text" id="email-reg" value="<ion:form:register:field:email />" />
                                    <ion:form:register:error:email tag="span" class="help-inline" />
                                </div>
                            </div>

                            <div class="control-group<ion:form:register:error:password is="true"> error</ion:form:register:error:password>">
                                <label class="control-label" for="password-reg"><ion:lang key="form_label_password" /></label>
                                <div class="controls">
                                    <input class="span4" name="password" type="password" id="password-reg" value="" />
                                    <ion:form:register:error:password tag="span" class="help-inline" />
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="controls">
                                    <button type="submit" class="btn btn-success"><ion:lang key="button_register" /></button>
                                </div>
                            </div>
                        </form>


                    </ion:user:logged>

                    <!-- User logged in -->
                    <ion:user:logged is="true">

                        <!-- Articles of this page with the type "logged-in" -->
                        <ion:page:articles type="logged-in">
                            <ion:article>
                                <h3 class="dotted-title"><ion:title /> <ion:user:name /></h3>
                                <ion:content />
                            </ion:article>
                        </ion:page:articles>

                        <!-- Profile form -->
                        <form class="form-horizontal" method="post" action="">

                            <input type="hidden" name="form" value="profile" />

                            <div class="control-group<ion:form:profile:error:firstname is="true"> error</ion:form:profile:error:firstname>">
                                <label class="control-label" for="firstname-profile"><ion:lang key="form_label_firstname" /></label>
                                <div class="controls">
                                    <input class="span4" name="firstname" type="text" id="firstname-profile" value="<ion:user:form:profile:field:firstname />" />
                                    <ion:form:profile:error:firstname tag="span" class="help-inline" />
                                </div>
                            </div>

                            <div class="control-group<ion:form:profile:error:lastname is="true"> error</ion:form:profile:error:lastname>">
                                <label class="control-label" for="lastname-profile"><ion:lang key="form_label_lastname" /></label>
                                <div class="controls">
                                    <input class="span4" name="lastname" type="text" id="lastname-profile" value="<ion:user:form:profile:field:lastname />" />
                                    <ion:form:profile:error:lastname tag="span" class="help-inline" />
                                </div>
                            </div>

                            <div class="control-group<ion:form:profile:error:screen_name is="true"> error</ion:form:profile:error:screen_name>">
                                <label class="control-label" for="screen_name-profile"><ion:lang key="form_label_screen_name" /></label>
                                <div class="controls">
                                    <input class="span4" name="screen_name" type="text" id="screen_name-profile" value="<ion:user:form:profile:field:screen_name />" />
                                    <ion:form:profile:error:screen_name tag="span" class="help-inline" />
                                </div>
                            </div>

                            <div class="control-group<ion:form:profile:error:email is="true"> error</ion:form:profile:error:email>">
                                <label class="control-label" for="email-profile"><ion:lang key="form_label_email" /></label>
                                <div class="controls">
                                    <input class="span4" name="email" type="text" id="email-profile" value="<ion:user:form:profile:field:email />" />
                                    <ion:form:profile:error:email tag="span" class="help-inline" />
                                </div>
                            </div>

                            <div class="control-group<ion:form:profile:error:birthdate is="true"> error</ion:form:profile:error:birthdate>">
                                <label class="control-label" for="birthdate-profile"><ion:lang key="form_label_birthdate" /></label>
                                <div class="controls">
                                    <input class="span4" name="birthdate" type="text" id="birthdate-profile" value="<ion:user:form:profile:field:birthdate />" />

                                    <ion:form:profile:error:birthdate tag="span" class="help-inline" />
                                </div>
                            </div>

                            <div class="control-group<ion:form:profile:error:birthdate is="true"> error</ion:form:profile:error:birthdate>">
                                <label class="control-label" for="birthdate-profile"><ion:lang key="form_label_gender" /></label>
                                <div class="controls">
                                    <label class="radio">
                                        <input type="radio" name="gender" id="gender-profile-male" value="1" <ion:user:form:profile:radio:gender value="1" default="true" />>
                                        <ion:lang key="form_label_gender_male" />
                                    </label>
                                    <label class="radio">
                                        <input type="radio" name="gender" id="gender-profile-female" value="2" <ion:user:form:profile:radio:gender value="2" />>
                                        <ion:lang key="form_label_gender_female" />
                                    </label>
                                    <label class="radio">
                                        <input type="radio" name="gender" id="gender-profile-unisex" value="3" <ion:user:form:profile:radio:gender value="3" />>
                                        <ion:lang key="form_label_gender_unisex" />
                                    </label>
                                    <ion:form:profile:error:gender tag="span" class="help-inline" />
                                </div>
                            </div>

                            <div class="control-group<ion:form:profile:error:password is="true"> error</ion:form:profile:error:password>">
                                <label class="control-label" for="password-profile"><ion:lang key="form_label_password" /></label>
                                <div class="controls">
                                    <input class="span4" name="password" type="password" id="password-profile" />

                                    <ion:form:profile:error:password tag="span" class="help-inline" />
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="delete-profile"><ion:lang key="form_label_delete_account" /></label>
                                <div class="controls">
                                    <label class="checkbox">
                                        <input type="checkbox"name="delete" value="1" id="delete-profile" />
                                    </label>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="controls">
                                    <button type="submit" class="btn btn-success"><ion:lang key="form_button_save_profile" /></button>
                                </div>
                            </div>

                        </form>

                    </ion:user:logged>

                </div>
            </div>
            <div class="span3">
                <div class="p10">
                    <ion:user:logged is="true">

                        <!-- Logout form -->
                        <h3 class="dotted-title"><ion:lang key="title_logout" /></h3>

                        <form method="post" action="">
                            <input type="hidden" name="form" value="logout" />
                            <div class="control-group">
                                <div class="controls">
                                    <button type="submit" class="btn btn-danger btn-large"><ion:lang key='form_button_logout' /></button>
                                </div>
                            </div>
                        </form>

                    </ion:user:logged>


                    <ion:user:logged is="false">

                        <!-- Login form -->
                        <h3 class="dotted-title"><ion:lang key="title_login" /></h3>

                        <form method="post" action="">
                            <input type="hidden" name="form" value="login" />

                            <div class="control-group<ion:form:login:field:email is="true"> error</ion:form:login:field:email>">
                                <label class="control-label" for="email"><ion:lang key="form_label_email" /></label>
                                <div class="controls">
                                    <input class="span3" name="email" type="text" id="email" value="<ion:form:login:field:email />" />
                                    <ion:form:login:error:email tag="span" class="help-inline" />
                                </div>
                            </div>

                            <div class="control-group<ion:form:login:field:password is="true"> error</ion:form:login:field:password>">
                                <label class="control-label" for="password"><ion:lang key="form_label_password" /></label>
                                <div class="controls">
                                    <input class="span3" name="password" type="password" id="password" />
                                    <ion:form:login:error:password tag="span" class="help-inline" />
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="controls">
                                    <button type="submit" class="btn btn-primary"><ion:lang key='form_button_login' /></button>
                                </div>
                            </div>
                        </form>


                        <!-- Password back form -->
                        <h3 class="dotted-title"><ion:lang key="title_password_back" /></h3>

                        <form method="post" action="">
                            <input type="hidden" name="form" value="password" />

                            <div class="control-group">
                                <label class="control-label" for="email-back"><ion:lang key="form_label_email" /></label>
                                <div class="controls">
                                    <input class="span3" name="email" type="text" id="email-back" />
                                    <ion:form:login:error:email tag="span" class="help-inline" />
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="controls">
                                    <button type="submit" class="btn btn-info"><ion:lang key='form_button_password_back' /></button>
                                </div>
                            </div>
                        </form>

                    </ion:user:logged>
                </div>
            </div>

        </section>

    </section>

</section> <!-- Container Section End -->

<ion:partial view="footer" />
