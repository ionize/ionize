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

        <section class="span12 bg-white mb20 pull-left">
            <div class="span8 pull-left">
                <div id="contactForm" class="pos-rel p10">
                    <ion:lang key="title_contact_form" tag="h2" class="dotted-title" />

                    <!--
                        Success message
                        Displayed if the form was successfuly validated
                    -->
                    <ion:form:contact:validation:success is="true">
                        <div class="alert alert-block alert-success fade in">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <ion:lang key="form_alert_success_title" tag="h4" />
                            <ion:lang key="form_alert_success_message" tag="p" />
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
                            <ion:lang key="form_alert_error_title" tag="h4" />
                            <ion:lang key="form_alert_error_message" tag="p" />
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
                        <div class="control-group<ion:form:contact:error:form_firstname is="true"> error</ion:form:contact:error:form_firstname>">
                            <label class="control-label" for="form_firstname"><ion:lang key="form_label_form_firstname" /></label>
                            <div class="controls">
                                <input class="span4" name="form_firstname" type="text" id="form_firstname" placeholder="<ion:lang key='form_label_form_firstname' />" value="<ion:form:contact:field:form_firstname />" />
                                <ion:form:contact:error:form_firstname tag="span" class="help-inline" />
                            </div>
                        </div>

                        <!-- Input : Surname -->
                        <div class="control-group<ion:form:contact:error:form_lastname is="true"> error</ion:form:contact:error:form_lastname>">
                            <label class="control-label" for="form_lastname"><ion:lang key="form_label_form_lastname" /></label>
                            <div class="controls">
                                <input class="span4" name="form_lastname" type="text" id="form_lastname" placeholder="<ion:lang key='form_label_form_lastname' />" value="<ion:form:contact:field:form_lastname />" />
                                <ion:form:contact:error:form_lastname tag="span" class="help-inline" />
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
                        <div class="control-group<ion:form:contact:error:form_subject is="true"> error</ion:form:contact:error:form_subject>">
                            <label class="control-label" for="form_subject"><ion:lang key="form_label_form_subject" /></label>
                            <div class="controls">
                                <input class="span4" name="form_subject" type="text" id="form_subject" placeholder="<ion:lang key='form_label_form_subject' />" value="<ion:form:contact:field:form_subject />" />
                                <ion:form:contact:error:form_subject tag="span" class="help-inline" />
                            </div>
                        </div>

                        <!-- Input : Message -->
                        <div class="control-group<ion:form:contact:error:form_message is="true"> error</ion:form:contact:error:form_message>">
                            <label class="control-label" for="form_message"><ion:lang key="form_label_form_message" /></label>
                            <div class="controls">
                                <textarea name="form_message" class="span4" rows="7" placeholder="<ion:lang key="form_label_form_message" />"><ion:form:contact:field:form_message /></textarea>
                                <ion:form:contact:error:form_message tag="span" class="help-inline text-error" />
                            </div>
                        </div>


                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" class="btn btn-success"><ion:lang key="button_send" /></button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <div class="span4 pull-right">
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
