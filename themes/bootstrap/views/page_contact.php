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

        <section class="span12 bg-white mb20">
            <div class="span8">
                <div id="contactForm" class="pos-rel p10">
                    <ion:lang key="title_contact_form" tag="h2" class="dotted-title" />

                    <!--
                        Success message
                        Displayed if the form was successfuly validated
                    -->
                    <ion:form:contact:validation:success is="true">
                        <div class="alert alert-block alert-success fade in">
                            <button type="button" class="close" data-dismiss="alert">×</button>
							<ion:lang key="form_contact_success_title" tag="h4" />
							<ion:lang key="form_contact_success_message" tag="p" />
                        </div>
                    </ion:form:contact:validation:success>

                    <!--
                        Error message
                        Displayed if the form doesn't pass the validation
                        the 'form_message_error' key is located in : themes/your_theme/language/xx/tags_lang.php

                    -->
                    <ion:form:contact:validation:error is="true" >
                        <div class="alert alert-block alert-error fade in">
                            <button type="button" class="close" data-dismiss="alert">×</button>
							<ion:lang key="form_contact_error_title" tag="h4" />
							<ion:lang key="form_contact_error_message" tag="p" />
                        </div>
                    </ion:form:contact:validation:error>

                    <!--
                        Form has no action because the same page will process the data.
                        POST data are catched by the global Tagmanager and processed by the Tagmanager's library method 'prcoess_data'
                        defined in : /themes/your_theme/libraries/Tagmanager/Contact.php
                        as declared in the form config file : /themes/your_theme/config/forms.php
                    -->
                    <form class="form-horizontal" method="post" action="#contactForm">

                        <!-- The form name must be set so the tags identify it -->
                        <input type="hidden" name="form" value="contact" />

                        <!-- Input : Name -->
                        <div class="control-group<ion:form:contact:error:name is="true"> error</ion:form:contact:error:name>">
                            <label class="control-label" for="form_name"><ion:lang key="form_label_name" /></label>
                            <div class="controls">
                                <input class="span4" name="name" type="text" id="form_name" placeholder="<ion:lang key='form_label_name' />" value="<ion:form:contact:field:name />" />
                                <ion:form:contact:error:name tag="span" class="help-inline" />
                            </div>
                        </div>

                        <!-- Input : Email -->
                        <div class="control-group<ion:form:contact:error:email is="true"> error</ion:form:contact:error:email>">
                            <label class="control-label" for="email"><ion:lang key="form_label_email" /></label>
                            <div class="controls">
                                <input class="span4" name="email" type="text" id="email" placeholder="<ion:lang key='form_label_email' />" value="<ion:form:contact:field:email />" />
                                <ion:form:contact:error:email tag="span" class="help-inline" />
                            </div>
                        </div>


                        <!-- Input : Subject -->
                        <div class="control-group<ion:form:contact:error:subject is="true"> error</ion:form:contact:error:subject>">
                            <label class="control-label" for="subject"><ion:lang key="form_label_subject" /></label>
                            <div class="controls">
                                <input class="span4" name="subject" type="text" id="subject" placeholder="<ion:lang key='form_label_subject' />" value="<ion:form:contact:field:subject />" />
                                <ion:form:contact:error:subject tag="span" class="help-inline" />
                            </div>
                        </div>

                        <!-- Input : Message -->
                        <div class="control-group<ion:form:contact:error:message is="true"> error</ion:form:contact:error:message>">
                            <label class="control-label" for="form_message"><ion:lang key="form_label_message" /></label>
                            <div class="controls">
                                <textarea name="form_message" class="span4" rows="7" placeholder="<ion:lang key="form_label_message" />"><ion:form:contact:field:message /></textarea>
                                <ion:form:contact:error:message tag="span" class="help-inline text-error" />
                            </div>
                        </div>


                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" class="btn btn-success"><ion:lang key="form_button_send" /></button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <div class="span3">
                <div class="pos-rel p10">
                    <ion:page>
                        <ion:articles type="bloc">

                            <ion:article>
                                <ion:title tag="h2" class="dotted-title" />
                                <ion:content />
                            </ion:article>

                            <div class="clearfix"></div>
                        </ion:articles>
                    </ion:page>
                </div>
            </div>
        </section>

    </section>

</section> <!-- Container Section End -->

<ion:partial view="footer" />
