<?php

/*
|--------------------------------------------------------------------------
| Connect library Language file
| Language : 	Portuguese
| Translater :	hort, 18 November 2010
|
| This lang file can be replaced by a theme one.
| Simply copy this file in the folder /themes/your_theme/language/xx/
| and modify the tranlations elements.
|
|--------------------------------------------------------------------------
*/

// Main library language elements
$lang['connect_login_failed'] = 'As informações de login fornecido não podem ser autenticadas. O nome de usuário ou a senha digitada está errada. Por favor, tente novamente.';
$lang['connect_access_denied'] = 'Foi negado acesso a %s';
$lang['connect_missing_parameters']	= 'O(s) parâmetro(s) %s está(ão) em falta';
$lang['connect_parameter_error'] = 'O parâmetro enviado para %s está errado.';
$lang['connect_user_save_impossible'] = 'Não foi possível salvar os seus dados no sistema, por favor, tente novamente ou entre em contacto connosco.';
$lang['connect_user_already_exists'] = 'Já existe um utilizador no sistema com os mesmos dados. Por favor, tente mudar o nome de utilizador ou endereço de email.';
$lang['connect_blocked'] = 'Foi bloqueado devido aos muitos logins que falharam, por favor tente novamente %s';
$lang['connect_cannot_ban_yourself'] = 'Não se pode banir a si mesmo.';
$lang['connect_register_success'] = 'Foi registrado com sucesso.';
$lang['connect_register_success_verify_user'] = 'Registou-se com sucesso, mas temos de verificar seus dados. Um e-mail foi enviado, por favor verifique-o e clique no link de activação na mensagem para poder activar a sua conta.';

// Activation mail to Admin
$lang['connect_admin_mail_subject'] = 'Registo';
$lang['connect_admin_mail_title'] = 'Registo';
$lang['connect_admin_mail_intro'] = 'Um novo utilizador registou-se no website.';
$lang['connect_admin_mail_nom'] = 'Nome';
$lang['connect_admin_mail_login'] = 'Login';
$lang['connect_admin_mail_email'] = 'Email';
$lang['connect_admin_mail_activation_link'] = 'Link de activação';

// Activation mail to User
$lang['connect_user_mail_subject'] = 'O seu registo';
$lang['connect_user_mail_activated'] = 'Conta activada';
$lang['connect_act_user_mail_title'] = 'Bem-vindo !';
$lang['connect_act_user_mail_intro'] = 'Acabou de registrar no nosso site o que agradecemos.';
$lang['connect_act_user_mail_text'] = 'Para confirmar o seu registo, clique no link de activação.';
$lang['connect_act_user_mail_activation_link'] = 'Link de activação';

// Registration confirmation mail to User
$lang['connect_wait_user_mail_title'] = 'Bem-vindo !';
$lang['connect_wait_user_mail_intro'] = 'Acabou de registrar no nosso site o que agradecemos.';
$lang['connect_wait_user_mail_text'] = 'A sua conta será activada pelo administrador dentro em breve.';

// Registration views
$lang['connect_user_registration_title'] = 'Registo com sucesso';
$lang['connect_user_registration_message'] = 'Irá receber um email com a informação da sua inscrição e instruções para confirmar o seu registo.';

// Activation views
$lang['connect_home_page'] = 'Início';
$lang['connect_activation_title'] = 'Conta activada';
$lang['connect_user_activated_message'] = 'A sua conta está activada.<br/>Pode ligar-se desde a home page';
$lang['connect_user_activated_error'] = 'Aconteceu algo de errado com a conta que está tentando activar. Talvez já tenha activado, ou talvez esteja utilizando a informação errada? Tente fazer login com as informações da sua conta, ou verificar o e-mail que foi enviado e tente novamente.';

$lang['connect_admin_activated_message'] = 'Esta conta está activada.<br/>Um e-mail foi enviado para o utilizador a informar.';
$lang['connect_admin_activated_error'] = 'Aconteceu algo de errado com a conta que está tentando activar. Talvez já tenha activado, ou talvez esteja utilizando a informação errada?';


/* End of file connect_lang.php */
/* Location: ./application/language/en/connect_lang.php */
