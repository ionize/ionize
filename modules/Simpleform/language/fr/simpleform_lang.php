<?php

$lang['module_simpleform_about'] = "Ce module permet l'ajout de formulaires simples à votre site.";
$lang['module_simpleform_doc_title'] = "Comment configurer ce module";
$lang['module_simpleform_doc_content'] = "
	<h4>1. Paramétrer le fichier config.php :</h4>
	<p>Chaque formulaire est déclaré dans le fichier <b>/modules/Simpleform/config/config.php</b><p>
	<pre>
// XXXXX représente le nom de votre formulaire.
// Example pour un formulaire nommé 'contact': \$config['simpleform_contact_email'] = 'yourname@yourdomain.tld';

\$config['simpleform_XXXXX_email'] = 'yourname@yourdomain.tld';

// Titre de l'email : Index de la traduction du fichier de langue : Simpleform/language/xxx/simpleform_lang.php
// Dans le cas ci-dessous,  \$lang['module_simpleform_email_title'] sera utilisé comme titrre de mail pour ce formulaire

\$config['simpleform_XXXXX_email_title'] = 'module_simpleform_email_title';

// Vue d'email à utiliser pour l'envoi des données (sans l'extension .php)
// Cette vue doit être placée dans /modules/Simpleform/views

\$config['simpleform_XXXXX_email_view'] = 'mail';

// Champs et règles du formulaire

\$config['simpleform_XXXXX'] = array(
	'name' => 'trim|required|min_length[4]|xss_clean',
	'email' => 'trim|required|min_length[5]|valid_email|xss_clean',
	'message' => 'required|xss_clean',
	'city' => 'antispam'
);
	</pre>

	<h4>2. Créer la page ou l'article contenant le formulaire</h4>
	<p>
		Pour créer un formulaire, crééz simplement une vue, déclarez-la et liez-la à une page ou un article
	</p>
	<p>
		Cette vue contiendra les tags du module Simpleforms. <br/>
		La vue <b>views/form_view.php</b> est un bon exemple.
	</p>
	
	<h4>3. Modifiez la librairie : libraries/simpleform_action.php file</h4>
	<p>
		La méthode nommée <b>process_data()</b> se charge de traiter le formulaire si besoin.<br/>
		Modifiez cette méthode pour qu'elle corresponde à vos besoins.
	</p>
	
";

$lang['module_simpleform_field_email'] = "Email";
$lang['module_simpleform_field_name'] = "Nom";
$lang['module_simpleform_field_firstname'] = "Prénom";
$lang['module_simpleform_field_lastname'] = "Nom";
$lang['module_simpleform_field_username'] = "Username";
$lang['module_simpleform_field_password'] = "Mot de passe";
$lang['module_simpleform_field_password2'] = "Mot de passe (conf.)";
$lang['module_simpleform_field_title'] = "Titre";
$lang['module_simpleform_field_title_mr'] = "Mr";
$lang['module_simpleform_field_title_ms'] = "Me";
$lang['module_simpleform_field_infomails_desc'] = "Je souhaite recevoir des informations par email.";
$lang['module_simpleform_field_newsletter_desc'] = "Je souhaite recevoir la newsletter";
$lang['module_simpleform_field_terms_desc'] = "J'accepte les termes d'utilisation";
$lang['module_simpleform_field_terms'] = "Termes";
$lang['module_simpleform_field_company'] = "Société";
$lang['module_simpleform_field_street'] = "Rue";
$lang['module_simpleform_field_city'] = "Ville";
$lang['module_simpleform_field_country'] = "Pays";
$lang['module_simpleform_field_housenumber'] = "N°";
$lang['module_simpleform_field_zip'] = "CP";
$lang['module_simpleform_field_website'] = "URL Site Internet";
$lang['module_simpleform_field_subject'] = "Sujet";
$lang['module_simpleform_field_message'] = "Message";
$lang['module_simpleform_all_fields_required'] = "Tous les champs sont obigatoires";

$lang['module_simpleform_button_send'] = "Envoyer";
$lang['module_simpleform_button_save'] = "Sauvegarder";

$lang['module_simpleform_text_error'] = "Oups, une erreur est survenue...";
$lang['module_simpleform_text_success'] = "Votre message est envoyé !";
$lang['module_simpleform_text_thanks'] = "Merci pour votre message. Nous alons vous répondre très vite.";

$lang['module_simpleform_text_vip_success'] = "Votre demande VIP est envoyée !";
$lang['module_simpleform_text_vip_thanks'] = "Nous vous répondrons très rapidement.";

$lang['module_simpleform_email_title'] = "Quelqu'un vous a envoyé un message depuis le site";
$lang['module_simpleform_vip_email_title'] = "Demande VIP !";



$lang['module_simpleform_error_javascript_required'] = "Javascript doit être activé sur votre navigateur pour envoyer ce message.";
$lang['module_simpleform_error_required'] = "Le champ <strong>%s</strong> est obligatoire.";
$lang['module_simpleform_error_isset'] = "Le champ <strong>%s</strong> doit posséder une valeur.";
$lang['module_simpleform_error_valid_email'] = "Le champ <strong>%s</strong> doit être une adresse Email valide";
$lang['module_simpleform_error_valid_emails'] = "Le champ <strong>%s</strong> doit contenir des adresses Email valides";
$lang['module_simpleform_error_valid_url'] = "Le champ <strong>%s</strong> doit être une URL valide.";
$lang['module_simpleform_error_valid_ip'] = "Le champ <strong>%s</strong> doit être une adresse IP.";
$lang['module_simpleform_error_min_length'] = "Le champ <strong>%s</strong> doit posséder au minimum %s caractères.";
$lang['module_simpleform_error_max_length'] = "Le champ <strong>%s</strong> ne peut excéder %s caractères.";
$lang['module_simpleform_error_exact_length'] = "Le champ <strong>%s</strong> doit posséder %s caractères.";
$lang['module_simpleform_error_alpha'] = "Le champ <strong>%s</strong> ne doit contenir que des caractères alphabétiques (a - z).";
$lang['module_simpleform_error_alpha_numeric'] = "Le champ <strong>%s</strong> ne doit contenir que des caractères alphanumériques (a - z et 0 - 9).";
$lang['module_simpleform_error_alpha_dash'] = "Le champ <strong>%s</strong> ne doit contenir que des caractères alphanumériques, underscores, ou tiret.";
$lang['module_simpleform_error_numeric'] = "Le champ <strong>%s</strong> doit être un nombre.";
$lang['module_simpleform_error_is_numeric'] = "Le champ <strong>%s</strong> doit être un nombre.";
$lang['module_simpleform_error_integer'] = "Le champ <strong>%s</strong> doit être un nombre entier.";
$lang['module_simpleform_error_matches'] = "Les champs <strong>%s</strong>s ne correspondent pas.";
$lang['module_simpleform_error_is_natural'] = "Le champ <strong>%s</strong> doit être un nombre naturel.";
$lang['module_simpleform_error_is_natural_no_zero']	= "Le champ <strong>%s</strong> doit être un nombre > 0.";
$lang['module_simpleform_error_restricted_field'] = "Des données non autorisées ont été transmises";
$lang['module_simpleform_error_terms'] = "Vous devez accepter les termes d'utilisation.";
$lang['module_simpleform_error_upload_something'] = "L'upload a échoué.";
$lang['module_simpleform_error_upload_file_size'] = "La taille d'un fichier ne peut excéder 1 MB.";
$lang['module_simpleform_error_upload_file_type'] = "Les fichiers acceptés sont : JPEGs, PNGs et GIFs.";


