<?php

$lang = array();

/* General */
$lang['title_ionize_installation'] = 		'Installation';

$lang['title_system_check'] = 		'Résultat du test système';
$lang['title_database_settings'] = 	'Paramètres de base de données';
$lang['title_user_account'] = 		'Compte Administrateur';
$lang['title_default_language'] = 	'Langue par défaut';
$lang['title_sample_data'] = 	'Installer le site exemple ?';

$lang['button_next_step'] = 		'Etape suivante';
$lang['button_skip_next_step'] = 	'Passer cette étape';
$lang['button_save_next_step'] = 	"Sauvegarder & Suite";
$lang['button_install_test_data'] = "Installer les données de test";
$lang['button_start_migrate'] = 	"Lancer la migration";

$lang['nav_check'] = 'Système';
$lang['nav_db'] = 'Base de données';
$lang['nav_settings'] = 'Paramètres';
$lang['nav_end'] = 'Fin';


/* System check */
$lang['php_version'] = 			'PHP >= 5';
$lang['php_version_found'] = 	'Version PHP';
$lang['mysql_support'] = 		'MySQL';
$lang['mysql_version_found'] = 	'MySQL version found';
$lang['file_uploads'] = 		'Upload de fichiers';
$lang['mcrypt'] = 				'PHP Mcrypt Lib';
$lang['gd_lib'] = 				'PHP GD Lib';
$lang['write_config_dir'] = 	"<b>/application/config/</b>";
$lang['write_files'] = 			"<b>/files/</b>";
$lang['write_themes'] = 		'<b>/themes/*</b>';
$lang['config_check_errors'] = 	"Certains prérequis ne sont pas remplis.<br/> Veuillez corriger avant de poursuivre l'installation.";
$lang['welcome_text'] = 		"<p>Bienvenue sur Ionize !<br/>Ces quelques étapes vont vous permettre d'installer Ionize.</p>";
$lang['write_check_text'] = 	"<p>Ionize doit pouvoir écrire les dossiers et fichiers suivants...</p>";
$lang['system_check_text'] = 	"<p>Tous ces indicateurs doivent être au vert pour continuer.</p>";
 

/* Database */
$lang['database_driver'] = 			'Pilote';
$lang['database_hostname'] = 		'Serveur';
$lang['database_name'] = 			'Nom de base';
$lang['database_username'] = 		'Utilsateur';
$lang['database_password'] = 		'Mot de passe';
$lang['database_create'] = 			'Créer la base';
$lang['title_database_create'] = 	'Création de la base de données';
$lang['db_create_text'] = 			"<p>Ionize va installer ou migrer votre base de données :</p><p><b class=\"highlight\">Nouvelle installation</b> : Base et tables vont être créées.<br/><b class=\"highlight2\">Upgrade</b> : La migration se fera à la prochaine étape.</p>";
$lang['db_create_prerequisite'] = 			"L'utilisateur doit posséder les droits de création de base.<br/> Si votre base existe déjà, ne cochez pas cette case.";
$lang['database_error_missing_settings'] = 	'Certaines informations sont manquantes.<br/>Veuillez remplir tous les champs.';
$lang['database_success_install'] = 		'<b class="ex">La base de donnée a été installée avec succès.</b>';
$lang['database_success_install_no_settings_needed'] = 		"<b class=\"ex\">Database OK.</b><br/>Comme la base de données existe déjà, l'étape de définition des paramètres du site n'est pas nécessaire.";
$lang['database_success_migrate'] = 		'<b class="ex">La base de donnée a été migrée avec succès.</b>';
$lang['database_error_coud_not_connect'] = 		'Connexion à la base de données impossible.<br/>Veuillez vérifier vos paramètres.';
$lang['database_error_database_dont_exists'] = 		"La base de données n'existe pas !";
$lang['database_error_writing_config_file'] = 		"<b>Erreur :</b><br/> Le fichier <b style=\"color:#000;\">/application/config/database.php</b> est protégé en écriture !<br/>Contrôlez vos droits.";
$lang['database_error_coud_not_write_database'] = 		"<b>Erreur :</b><br/> Impossible d'écrire dans la base de données.<br/>Contrôlez vos droits.";
$lang['database_error_coud_not_create_database'] = "Impossible de créer la base de données. Vérifiez le nom de votre base ou vos droits de création.";
$lang['database_error_no_ionize_tables'] = 			"La base de données que vous avez sélectionné semble ne pas contenir les tables Ionize.";
$lang['database_error_no_users_to_migrate'] = 		"Aucun utilisateur à migrer.";

$lang['database_migration_from'] = 			'Cette base de données nécessite une migration.<br/>Migration depuis la version : ';

$lang['database_migration_text'] = 		"<p class=\"error\"><b>ATTENTION :</b><br/> La base de données va maintenant être mise à jour.<br/> Faites impérativement une sauvegarde de votre base avant de lancer la migration.";


/* Settings */
$lang['lang_code'] = 		'Code (2 car.)';
$lang['lang_name'] = 		'Libellé';
$lang['settings_default_lang_title'] = 		'Langue par défaut';
$lang['settings_default_lang_text'] = 		"Votre site doit posséder une langue par défaut. <br/>Vous pouvez visiter <a target=\"_blank\" href=\"http://en.wikipedia.org/wiki/ISO_639-1\">la page Wikipedia au sujet de ISO 639-1 </a> pour plus d'informations concernant les codes langues.";
$lang['settings_error_missing_lang_code'] = "Le code langue est obligatoire";
$lang['settings_error_missing_lang_name'] = "Le libellé de la langue est obligatoire";
$lang['settings_error_lang_code_2_chars'] = "Le code langue doit être sur 2 caractères. Exemple : \"fr\"";
$lang['settings_error_write_rights'] = "Erreur à l'écriture du fichier <b>/application/config/laguage.php</b>. Vérifiez les droits d'écriture de PHP sur ce fichier.";
$lang['settings_error_write_rights_config'] = "Erreur à l'écriture du fichier <b>/application/config/config.php</b>. Vérifiez les droits d'écriture de PHP sur ce fichier.";
$lang['settings_error_admin_url'] = "L'URL d'administration doit être une chaine de caractère simple, sans caractères spéciaux.";
$lang['settings_admin_url_title'] = 		'URL de l\'Administration du site';
$lang['settings_admin_url_text'] = 		'Il est fortement recommandé de changer cette URL.';
$lang['admin_url'] = 'URL Admin';


/* User */
$lang['user_introduction'] = 	"Ce compte vous permettra de vous connecter à Ionize.";
$lang['username'] = 			'Login (min. 4 car.)';
$lang['screen_name'] = 			'Nom complet';
$lang['email'] = 				'Email';
$lang['password'] = 			'Mot de passe (min. 4 car.)';
$lang['password2'] = 			'Confirmer mot de passe';
$lang['user_error_missing_settings'] = 			'Veuillez renseigner tous les champs !';
$lang['user_error_not_enough_char'] = 			"Les nom d'utilisateur et mot de passe doivent au moins faire 4 caractères !";
$lang['user_error_email_not_valid'] = 			"L'adresse email ne semble pas valide.<br/> Veuillez corriger.";
$lang['user_error_passwords_not_equal'] = 		'Le mot de passe et sa confirmation ne sont pas égaux';
$lang['user_info_admin_exists'] = 		'Un compte Administrateur existe déjà.<br/>Vous pouvez passer cette étape si vous ne souhaitez pas créer ou mettre à jour de compte Admin.';
$lang['encryption_key'] = 			"Clé d'encryption";
$lang['encryption_key_text'] = 		"Ionize a besoin d'une clé d'encryption de 128 bits.<br />
									Cette clé sert à l'encodage des comptes utilisateurs et de toutes les données sensibles.<br/>
									Elle est enregistrée dans le fichier <b>/application/config/config.php</b>.";
$lang['no_encryption_key_found'] = 	"La clé d'encryption n'a pas été trouvée. <b>Vous devez créer un utilisateur Admin.</b>";


/* Example data */
$lang['data_install_intro'] = 	"Si vous découvrez ionize, il est fortement recommandé d'installer les données de test. Elles vous permettront de vous familiariser avec Ionize.<br/>
								Ces données comprennent : ";
$lang['data_install_list'] = 	"<li>Des données exemples installées dans la base de données,</li>
								<li>1 template fonctionnel</li>";
$lang['title_skip_this_step'] = 	"Passer cette étape";


/* Finish screen */
$lang['title_finish'] = 		'Installation terminée';
$lang['finish_text'] = 			"<b>IMPORTANT</b>: <br/>Vous devez supprimer le dossier \"<b>/install</b>\" manuellement avant de pouvoir utiliser l'application.";
$lang['button_go_to_admin'] = 	"Aller à l'administration";
$lang['button_go_to_site'] = 	"Aller au site";
