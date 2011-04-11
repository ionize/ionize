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
$lang['connect_login_failed'] = "Dati di login non corretti: user o password sbagliati. Riprova.";
$lang['connect_access_denied'] = "Non hai accesso a %s";
$lang['connect_missing_parameters'] = "Il/i parametro(i) %s è mancante";
$lang['connect_parameter_error'] = "Il/i parametro(i) passato a %s è sbagliato.";
$lang['connect_user_save_impossible'] = "Impossibile salvare i dati nel sistema, prova più tardi o contatta l'amministratore.";
$lang['connect_user_already_exists'] = "Un utente con le stesse credenziali è già loggato nel sistema. Prova con altro user o con altra email.";
$lang['connect_blocked'] = "Tentativi di login superiori al massimo consentito, per favore prova di nuovo %s";
$lang['connect_cannot_ban_yourself'] = "Non puoi segnalare come indesiderato te stesso.";
$lang['connect_register_success'] = "La tua registrazione ha avuto successo.";
$lang['connect_register_success_verify_user'] = "La tua registrazione si è concluse. Una e-mail è stata inviata all'indirizzo inserito: verifica la tua casella e fai click sul link di attivazione contenuto nella email per attivare il tuo account.";

// Activation mail to Admin
$lang['access_admin_mail_subject'] = 'Registrazione';
$lang['access_admin_mail_title'] = 'Registrazione';
$lang['access_admin_mail_intro'] = "Un nuovo utente si è registrato nel sito.";
$lang['access_admin_mail_nom'] = 'Nome';
$lang['access_admin_mail_login'] = 'Login';
$lang['access_admin_mail_email'] = 'Email';
$lang['access_admin_mail_activation_link'] = "Link per l\'attivazione";

// Activation mail to User
$lang['access_user_mail_subject'] = 'La tua registrazione';
$lang['access_user_mail_activated'] = 'Account attivato';
$lang['access_act_user_mail_title'] = 'Benvenuto !';
$lang['access_act_user_mail_intro'] = "Vi siete registrati sul sito.";
$lang['access_act_user_mail_text'] = "Per confermare la vostra registrazione fate click sul link sotto, cliquez sur le lien d'activation ci-dessous.";
$lang['access_act_user_mail_activation_link'] = "Il vostro link per l'attivazione";

// Registration confirmation mail to User
$lang['access_wait_user_mail_title'] = 'Benvenuto !';
$lang['access_wait_user_mail_intro'] = "Vi siete registrati sul sito.";
$lang['access_wait_user_mail_text'] = "Il vostro account sarà attivato dall'amministratore al più presto.";

// Registration views
$lang['access_user_registration_title'] = 'Registrazione del sito';
$lang['access_user_registration_message'] = "Riceverete una email di conferma con la procedura necessaria a concludere la registrazione";

// Activation views
$lang['access_home_page'] = "Pagina iniziale";
$lang['access_activation_title'] = "Attivazione account";
$lang['access_user_activated_message'] = "Account attivo.<br/>Potete accedere al sito tramite la pagina di login.";
$lang['access_user_activated_error'] = "Si è verificato un errore in fase di creazione dell'account.<br/>L'account potrebbe essere già attivo o le informazioni inserite sono errate ? <br/>Verificate nella email ricevuta i vostri dati.";

$lang['access_admin_activated_message'] = "L'account è stato attivato.<br/>Una email è stata inviata all'utente con le informazioni del caso.";
$lang['access_admin_activated_error'] = "Si è verificato un errore in fase di creazione dell'account.<br/>L'account potrebbe essere già attivo o le informazioni inserite sono errate ?";


/* End of file connect_lang.php */
/* Location: ./application/language/fr/connect_lang.php */
