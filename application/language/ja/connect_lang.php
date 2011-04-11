<?php

/*
|--------------------------------------------------------------------------
| Connect library Language file
| Language : 	Japanese
| Translater :	Senri  
|
| This lang file can be replaced by a theme one.
| Simply copy this file in the folder /themes/your_theme/language/xx/
| and modify the translations elements.
|
|--------------------------------------------------------------------------
*/

// Main library language elements
$lang['connect_login_failed'] = '入力されたログイン情報は受け付けられませんでした。ユーザ名もしくはパスワードに誤りがあります。再入力してください。';
$lang['connect_access_denied'] = '%s へのアクセスが拒否されました。';
$lang['connect_missing_parameters']	= 'パラメタ %s がありません';
$lang['connect_parameter_error'] = '指定されたパラメタ %s は誤りです';
$lang['connect_user_save_impossible'] = 'システムに情報を保存できませんでした。再度実行していただくか管理者にご連絡ください。';
$lang['connect_user_already_exists'] = 'このユーザは既に存在しています。他のユーザ名もしくはメールアドレスを試してください。';
$lang['connect_blocked'] = '多重ログインのため現在接続できません。%s を試してください。';
$lang['connect_cannot_ban_yourself'] = '自分自身は拒否できません';
$lang['connect_register_success'] = '正常に登録されました';
$lang['connect_register_success_verify_user'] = '正常に登録されましたが、データを確認中です。メールを送信しましたので、メッセージを確認してアカウントを有効にするためにリンクをクリックしてください。';

// Activation mail to Admin
$lang['connect_admin_mail_subject'] = '登録';
$lang['connect_admin_mail_title'] = '登録';
$lang['connect_admin_mail_intro'] = '一人のユーザがサイトに登録しました。';
$lang['connect_admin_mail_nom'] = '氏名';
$lang['connect_admin_mail_login'] = 'ログイン';
$lang['connect_admin_mail_email'] = 'メール';
$lang['connect_admin_mail_activation_link'] = 'リンクの有効化';

// Activation mail to User
$lang['connect_user_mail_subject'] = '登録内容について';
$lang['connect_user_mail_activated'] = 'アカウントは有効になりました';
$lang['connect_act_user_mail_title'] = 'ようこそ !';
$lang['connect_act_user_mail_intro'] = '本サイトに登録していただきありがとうございます。';
$lang['connect_act_user_mail_text'] = '登録内容の確認のため、有効化リンクをクリックしてください。';
$lang['connect_act_user_mail_activation_link'] = '有効化へのリンク';

// Registration confirmation mail to User
$lang['connect_wait_user_mail_title'] = 'ようこそ !';
$lang['connect_wait_user_mail_intro'] = '本サイトに登録していただきありがとうございます。';
$lang['connect_wait_user_mail_text'] = 'アカウントは管理者によりすぐに有効化されます。';

// Registration views
$lang['connect_user_registration_title'] = '正常登録';
$lang['connect_user_registration_message'] = '登録された内容と手続きをお知らせするメールが届きますのでお待ちください。';

// Activation views
$lang['connect_home_page'] = 'ホームページ';
$lang['connect_activation_title'] = 'アカウントの有効化';
$lang['connect_user_activated_message'] = 'アカウントは有効化されました。<br/>このホームページよりログインできます。';
$lang['connect_user_activated_error'] = '何らかの理由によりアカウントの有効化に支障が発生しました。既に有効になっているか誤った情報が存在するかもしれません。アカウント情報で再ログインしていただくか、送信したメールを確認してから再試行してください。';

$lang['connect_admin_activated_message'] = 'アカウントは現在有効化されています。<br/>ユーザへ通知メールを送信しました。';
$lang['connect_admin_activated_error'] = '何らかの理由によりアカウントの有効化に支障が発生しました。既に有効になっているか誤った情報が存在するかもしれません。';


/* End of file connect_lang.php */
/* Location: ./application/language/en/connect_lang.php */
