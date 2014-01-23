<?php
/*
|--------------------------------------------------------------------------
| Ionize Connect library Language file
|
| This lang file can be replaced by a theme one.
| Simply copy this file in the folder /themes/your_theme/language/xx/
| and modify the translations elements.
|
|--------------------------------------------------------------------------
*/

// Main library language elements
$lang['connect_login_failed'] = 'A informação de inicio de sessão não foi autenticada. O nome de utilizador ou a palavra-passe estão incorretos.';
$lang['connect_access_denied'] = 'O acesso foi negado a %s';
$lang['connect_missing_parameters']	= 'O(s) parametro(s) %s estão em falta';
$lang['connect_parameter_error'] = 'O parametro %s está errado.';
$lang['connect_user_save_impossible'] = 'Não foi possivel guardar os dados no sistema, por favor tente de novo ou contate-nos se o problema persistir.';
$lang['connect_user_already_exists'] = 'Já existe um utilizador no sistem com os mesmo dados. Por favor tente usar outro nome de utilizador ou endereço de e-mail.';
$lang['connect_blocked'] = 'O seu acesso foi bloqueado devido a demasiadas falhas na tentativa de iniciar sessão, por favor tente de novo em %s';
$lang['connect_cannot_ban_yourself'] = 'Não pode banir o seu próprio IP.';
$lang['connect_register_success'] = 'O seu registo foi efetuado com sucesso.';
$lang['connect_register_success_verify_user'] = 'O seu registo foi concluído mas deve esperar pelo nosso contato. Brevemente receberá um e-mail com um link de ativação; clique no link para ativar a sua conta de utilizador.';

// Activation mail to Admin
$lang['connect_admin_mail_subject'] = 'Registo';
$lang['connect_admin_mail_title'] = 'Registo';
$lang['connect_admin_mail_intro'] = 'Um utilizador acabou de efetuar um registo no site.';
$lang['connect_admin_mail_nom'] = 'Nome completo';
$lang['connect_admin_mail_login'] = 'Nome de Utilizador';
$lang['connect_admin_mail_email'] = 'E-mail';
$lang['connect_admin_mail_activation_link'] = 'Link de Activação';

// Activation mail to User
$lang['connect_user_mail_subject'] = 'O seu registo';
$lang['connect_user_mail_activated'] = 'A sua conta foi activada';
$lang['connect_act_user_mail_title'] = 'Bem vindo !';
$lang['connect_act_user_mail_intro'] = 'Agradecemos o seu registo no nosso site.';
$lang['connect_act_user_mail_text'] = 'Para confirmar o registo, clique no link de ativação.';
$lang['connect_act_user_mail_activation_link'] = 'Link de Ativação';

// Registration confirmation mail to User
$lang['connect_wait_user_mail_title'] = 'Bem vindo !';
$lang['connect_wait_user_mail_intro'] = 'Agradecemos o seu registo no nosso site.';
$lang['connect_wait_user_mail_text'] = 'Em breve a sua conta será ativada por um administrador. Aguarde.';

// Registration views
$lang['connect_user_registration_title'] = 'Registo realizado com sucesso';
$lang['connect_user_registration_message'] = 'Em breve irá recebr um e-mail com a suas informações e instruções para continuar.';

// Activation views
$lang['connect_home_page'] = 'Início';
$lang['connect_activation_title'] = 'Ativação da conta de utilizador';
$lang['connect_user_activated_message'] = 'A sua conta está ativada.<br/>Pode navegar para a homepage';
$lang['connect_user_activated_error'] = 'Aconteceu um erro inesperado com a conta que está a tentar ativar. Talvez a informação submetida não seja válida? Tente iniciar sessão com os seus dados, ou tente ativar a conta de novo.';

$lang['connect_admin_activated_message'] = 'A conta está agora ativada.<br/>Foi enviado automaticamente um e-mail a informar o utilizador.';
$lang['connect_admin_activated_error'] = 'Aconteceu um erro inesperado com a conta que está a tentar ativar. Talvez a informação submetida não seja válida?';

