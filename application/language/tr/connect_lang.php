<?php

/*
|--------------------------------------------------------------------------
| Connect library Language file
| Language : 	Turkish
| Translater :	Ukyo -> İskender TOTOĞLU
|
| This lang file can be replaced by a theme one.
| Simply copy this file in the folder /themes/your_theme/language/xx/
| and modify the translations elements.
|
|--------------------------------------------------------------------------
*/

// Main library language elements
$lang['connect_login_failed'] = "Girmiş olduğunuz giriş bilgileri onaylanamadı. Kullanıcı adınız yada parolanız yanlış. Lütfen tekrar deneyin.";
$lang['connect_access_denied'] = "Erişiminiz engellendi %s";
$lang['connect_missing_parameters'] = "Parametre(ler) %s eksik";
$lang['connect_parameter_error'] = "Parametre %s yanlış.";
$lang['connect_user_save_impossible'] = "Bilgilerinizi sistemimize kaydedemedik, lütfen tekrar deneyin yada bizimle iletişim kurun.";
$lang['connect_user_already_exists'] = "Aynı bilgileri içeren bi kullanıcı sistemimizde mevcut. Lütfen başka bir e-posta ve kullanıcı adı deneyin.";
$lang['connect_blocked'] = "Çok fazla başarısız giriş yaptığınız için engellendiniz, lütfen sonra tekrar deneyiniz %s";
$lang['connect_cannot_ban_yourself'] = "Kendinizi engelleyemezsiniz.";
$lang['connect_register_success'] = "Başarıyla kayıt oldunuz.";
$lang['connect_register_success_verify_user'] = "Kayıt işleminiz başarıyla gerçekleşti fakat bilgilerinizi doğrulamak zorundayız. Size bir e-posta gönderdik, lütfen bu postayı inceleyin ve hesbınızı aktive etmek için aktivasyon linkine tıklayın.";

// Activation mail to Admin
$lang['connect_admin_mail_subject'] = 'Kullanıcı kaydı';
$lang['connect_admin_mail_title'] = 'Kullanıcı kaydı';
$lang['connect_admin_mail_intro'] = 'Bir kullanıcı internet sitemize kayıt oldu.';
$lang['connect_admin_mail_nom'] = 'İsim';
$lang['connect_admin_mail_login'] = 'Kullanıcı Adı';
$lang['connect_admin_mail_email'] = 'E-Posta';
$lang['connect_admin_mail_activation_link'] = 'Aktivasyon adresi';

// Activation mail to User
$lang['connect_user_mail_subject'] = 'Kullanıcı Kaydınız';
$lang['connect_user_mail_activated'] = 'Hesabınız aktive edildi';
$lang['connect_act_user_mail_title'] = 'Hoş Geldiniz !';
$lang['connect_act_user_mail_intro'] = 'İneternet sitemize henüz kayıt oldunuz, bunun için teşekkür ederiz.';
$lang['connect_act_user_mail_text'] = 'Kaydınızı onaylamak için, aktivasyon adresine tıklayınız.';
$lang['connect_act_user_mail_activation_link'] = 'Aktivasyon Adresi';

// Registration confirmation mail to User
$lang['connect_wait_user_mail_title'] = 'Hoş Geldiniz !';
$lang['connect_wait_user_mail_intro'] = 'İneternet sitemize henüz kayıt oldunuz, bunun için teşekkür ederiz.';
$lang['connect_wait_user_mail_text'] = 'Hesabınız yönetici tarafından hızlı bir şekilde aktive edilecektir.';

// Registration views
$lang['connect_user_registration_title'] = 'Kullanıcı Kaydı Başarılı';
$lang['connect_user_registration_message'] = "Kullanıcı bilgilerinizi içeren bir e-posta alacaksınız, talimatları takip ederek kaydınızı onaylayın.";

// Activation views
$lang['connect_home_page'] = 'Ana Sayfa';
$lang['connect_activation_title'] = 'Hesap Aktivasyonu';
$lang['connect_user_activated_message'] = 'Hesabınız Aktive Edildi.<br/>Ana Sayfaya Bağlanabilirsiniz.';
$lang['connect_user_activated_error'] = "Hesabınızla ilgili bazı sorunlar oluştu, aktive etmeyi deniyorsunuz. Hesabınızı daha önce aktive etmiş olabilirsiniz, yada yanlış bilgi giriyor olabilirsiniz?  Hesap bilgilerinizle giriş yapmayı deneyin, yada e-posta adresinize gönderdiğimiz onay mailını tekrar deneyiniz.";
$lang['connect_admin_activated_message'] = "Hesabınız şu anda aktive edildi.<br/>Bir bildirim e-postası hesabınıza gönderildi.";
$lang['connect_admin_activated_error'] = "Hesabınızla ilgili bazı sorunlar oluştu, aktive etmeyi deniyorsunuz. Hesabınız daha önceden aktive etmiş olabilirsiniz, yada yanlış bilgiler kullanıyorsunuz?";


/* End of file connect_lang.php */
/* Location: ./application/language/tr/connect_lang.php */
