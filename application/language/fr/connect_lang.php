<?php

/*
|--------------------------------------------------------------------------
| Connect library Language file
| Language : 	Français
| Translater :	Partikule Studio
|
| This lang file can be replaced by a theme one.
| Simply copy this file in the folder /themes/your_theme/language/xx/
| and modify the translations elements.
|
|--------------------------------------------------------------------------
*/

// Main library language elements
$lang['connect_login_failed'] = "The login information you provided could not be authentificated. Either the username or the password you entered are wrong. Please try again.";
$lang['connect_access_denied'] = "You have been denied access to %s";
$lang['connect_missing_parameters'] = "The parameter(s) %s was missing";
$lang['connect_parameter_error'] = "The parameter passed to %s is wrong.";
$lang['connect_user_save_impossible'] = "We were not able to save your data into our system, please try again or contact us.";
$lang['connect_user_already_exists'] = "There is already a user existing in our system with the same data. Please try to use another username or email address.";
$lang['connect_blocked'] = "You have been blocked because of too many failed logins, please try again %s";
$lang['connect_cannot_ban_yourself'] = "You cannot ban yourself.";
$lang['connect_register_success'] = "You have successfully registered.";
$lang['connect_register_success_verify_user'] = "You have successfully registered but we must verify your data. An e-mail has been sent to you, please check it and click the activation link in the message to activate your account.";

// Activation mail to Admin
$lang['access_admin_mail_subject'] = 'Enregistrement';
$lang['access_admin_mail_title'] = 'Enregistrement';
$lang['access_admin_mail_intro'] = "Un utilisateur s'est enregistré sur votre site.";
$lang['access_admin_mail_nom'] = 'Nom';
$lang['access_admin_mail_login'] = 'Login';
$lang['access_admin_mail_email'] = 'Email';
$lang['access_admin_mail_activation_link'] = "Lien d'activation";

// Activation mail to User
$lang['access_user_mail_subject'] = 'Votre enregistrement';
$lang['access_user_mail_activated'] = 'Compte activé';
$lang['access_act_user_mail_title'] = 'Bienvenue !';
$lang['access_act_user_mail_intro'] = "Vous vous êtes enregistré sur le site.<br/>Nous vous en remercions.";
$lang['access_act_user_mail_text'] = "Pour confirmer votre enregistrement, cliquez sur le lien d'activation ci-dessous.";
$lang['access_act_user_mail_activation_link'] = "Votre lien d'activation";

// Registration confirmation mail to User
$lang['access_wait_user_mail_title'] = 'Bienvenue !';
$lang['access_wait_user_mail_intro'] = "Vous vous êtes enregistré sur le site.<br/>Nous vous en remercions.";
$lang['access_wait_user_mail_text'] = "Votre compte sera validé par l'administrateur du site au plus vite.";

// Registration views
$lang['access_user_registration_title'] = 'Enregistrement réussi';
$lang['access_user_registration_message'] = "Vous allez recevoir un email avec vos identifiants et les instructions d'activation de votre compte";

// Activation views
$lang['access_home_page'] = "Page d'accueil";
$lang['access_activation_title'] = "Activation du compte";
$lang['access_user_activated_message'] = "Votre compte est activé.<br/>Vous pouvez vous connecter depuis la page d'accueil du site.";
$lang['access_user_activated_error'] = "Une erreur est survenue à l'activation de ce compte.<br/>Peut-être ce compte est-il déjà activé ou peut-être les informations utilisées sont-elles erronées ? <br/>Essayez de vous connecter avec votre compte ou vérifiez le mail que vous avez reçu et tentez à nouveau une activation.";

$lang['access_admin_activated_message'] = "Ce compte est à présent activé.<br/>Un mail vient d'être envoyé à l'abonné pour le prévenir.";
$lang['access_admin_activated_error'] = "Une erreur est survenue à l'activation de ce compte.<br/>Peut-être ce compte est-il déjà activé ou peut-être les informations utilisées sont-elles erronées ?";


/* End of file connect_lang.php */
/* Location: ./application/language/fr/connect_lang.php */
