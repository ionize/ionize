<?php

$lang = array();

/* General */
$lang['title_ionize_installation'] = 		'Yükleme';
$lang['title_step_1'] = 			'Adım 1/5 : Sistem Ayarları Denetimi';
$lang['title_step_2'] = 			'Adım 2/5 : Veritabanı';
$lang['title_step_3.1'] = 			'Adım 3/5 : Veritabanı Aktarımı';
$lang['title_step_3.2'] = 			'Adım 3/5 : Site Ayarları';
$lang['title_step_4'] = 			'Adım 4/5 : Kullanıcı Ayarları';
$lang['title_step_5'] = 			'Adım 5/5 : Örnek Verilerin Yüklenmesi';
$lang['button_next_step'] = 		'Sonraki Adım';
$lang['button_skip_next_step'] = 	'Atla & Sonraki Adım';
$lang['button_save_next_step'] = 	'Kaydet & Sonraki Adıma Git';
$lang['button_install_test_data'] = 	"Örnek Verileri Yükle";
$lang['button_start_migrate'] = 		'Veri Tabanı Aktarımını Başlat';


/* System check */
$lang['php_version'] = 			'PHP >= 5';
$lang['php_version_found'] = 	'PHP Versiyonu';
$lang['mysql_support'] = 		'MySQL Desteği';
$lang['mysql_version_found'] = 	'MySQL Versiyonu';
$lang['file_uploads'] = 		'Dosya Yükleme';
$lang['mcrypt'] = 				'PHP Mcrypt Lib';
$lang['gd_lib'] = 				'PHP GD Lib';
$lang['write_config_dir'] = 	'Yazma İzni <b>/config/</b>';
$lang['write_files'] = 			'Yazma İzni <b>/files/</b>';
$lang['config_check_errors'] = 	'Bazı gereksinimler tamam değil.<br/>Lütfen gereksinimleri tamamlayıp tekrar deneyin.';
 

/* Database */
$lang['database_driver'] = 			'Sürücü';
$lang['database_hostname'] = 		'Hostadı';
$lang['database_name'] = 			'Veritabanı';
$lang['database_username'] = 		'Kullanıcı Adı';
$lang['database_password'] = 		'Şifre';
$lang['database_create'] = 			'Veritabanını Oluştur';
$lang['title_database_create'] = 	'Veritabanı Oluşturma';
$lang['db_create_text'] = 			"<b class=\"highlight\">Yeni Yükleme</b> : Veritabanı ve Tablolar Oluşturulacak<br/><b class=\"highlight2\">Yükseltme</b> : Sonraki Adım Güncellemeye İhtiyaç Olup Olmadığını Kontrol Edecek<br/>";
$lang['db_create_prerequisite'] = 			"Kullanıcının Veritabanı Oluşturma Hakkının Olması Lazım.<br/>Eğer Veritabanınız Var İse, Kontrol Etmenize Gerek Yok.";
$lang['database_error_missing_settings'] = 	'Bazı Bilgiler Eksik.<br/>Lütfen Tüm Alanları Doldurun !';
$lang['database_success_install'] = 		'<b class="ex">Veritabanı Başarıyla Yüklendi.</b>';
$lang['database_success_install_no_settings_needed'] = 		'<b class="ex">Veritabanı TAMAM.</b><br/>Veritabanı Zaten Mevcut, Site Ayarları Adımı Atlanacak.';
$lang['database_success_migrate'] = 		'<b class="ex">Veritabanı Başarıyla Güncellendi.</b>';
$lang['database_error_coud_not_connect'] = 		'Veritabanı Bağlantısı Girilen Ayarlarla Başarısız Oldu.';
$lang['database_error_database_dont_exists'] = 		"Veritabanı Yok !";
$lang['database_error_writing_config_file'] = 		"<b>Hata :</b><br/>The file <b style=\"color:#000;\">/application/config/database.php</b> could not be written!<br/>Check your permissions.";
$lang['database_error_coud_not_write_database'] = 		"<b>Hata :</b><br/> Veritabanına Veriler Yazılamıyor<br/>Veritabanı İzinlerini Kontrol Edin.";
$lang['database_error_coud_not_create_database'] = "Yükleyici Veritabanını Oluşturamıyor. Veritabanını yada İzinlerinizi Kontrol Ediniz";
$lang['database_error_no_ionize_tables'] = 			"Seçtiğiniz Veritabanı Ionize Veritabanı Gibi Görünmemektedir. Lütfen Tekrar Kontrol Ediniz.";
$lang['database_error_no_users_to_migrate'] = 		"Güncellenecek Kullanıcı Hesabı";

$lang['database_migration_from'] = 			'Versiyonundan Güncelle : ';
$lang['database_no_migration_needed'] = 		'Aktarım gerekmiyor.';

$lang['database_migration_text'] = 		"<p class=\"error\"><b>ÖNEMLİ :</b><br/> Veritabanı güncellenecek.<b><br/>Lütfen Güncellemeden Önce Veritabanınızın Yedeğini Alın.</p>";


/* Settings */
$lang['lang_code'] = 		'Kod (2 karakter)';
$lang['lang_name'] = 		'Etiket';
$lang['settings_default_lang_title'] = 		'Varsayılan Dil';
$lang['settings_default_lang_text'] = 		'İnternet Sitenizin Varsayılan Bir Dile İhtiyacı Var.<br/><a target="_blank" href="http://en.wikipedia.org/wiki/ISO_639-1">the Wikipedia ISO 639-1</a>Bu Sayfadan  Dil Kodlarıyla Hakkında Daha Fazla Bilgi Edinebilirsiniz.';
$lang['settings_error_missing_lang_code'] = "Dil Kodu Zorunludur";
$lang['settings_error_missing_lang_name'] = "Dil Adı Zorunludur";
$lang['settings_error_lang_code_2_chars'] = "Dil Kodu 2 Karakterden Oluşmak Zorundadır. Örnek : \"tr\"";
$lang['settings_error_write_rights'] = "Yazma İzni <b>/application/config/laguage.php</b> klasörü için yok. Lütfen Bu Dosyanın Yazma Haklarını Kontrol Edin.";
$lang['settings_error_write_rights_config'] = "Yazma İzni <b>/application/config/config.php</b> klasörü için yok. Lütfen Bu Dosyanın Yazma Haklarını Kontrol Edin.";


/* User */
$lang['user_introduction'] = 	'Yönetici Hesabınızı Oluşturun.<br/> Yönetim Paneline Erişmek İçin Bu Bilgileri Kullanacaksınız.';
$lang['username'] = 			'Kullanıcı Adı (min. 4 karakter)';
$lang['screen_name'] = 			'Tam Adınız';
$lang['email'] = 				'E-Posta';
$lang['password'] = 			'Parola (min. 4 karakter)';
$lang['password2'] = 			'Parola Onayı';
$lang['user_error_missing_settings'] = 			'Lütfen Tüm Alanları Doldurunuz !';
$lang['user_error_not_enough_char'] = 			'Kullanıcı Adı ve Parola Uzunluğu 4 Karakterden Uzun Olmak Zorunda!';
$lang['user_error_email_not_valid'] = 			'E-Posta Adresiniz Geçerli Görünmüyor. Lütfen Düzeltiniz.';
$lang['user_error_passwords_not_equal'] = 		'Parola ve Parola Onaylama Uyuşmuyor.	';
$lang['user_info_admin_exists'] = 		'Bir Kullanıcı Yönetici Konumunda Veritabanında Mevcut.<br/>Bu Adımı Atlayabilirsiniz Eğer İsterseniz Yönetici Oluşturmayabilir veya Güncellemeyebilirsiniz.';
$lang['encryption_key'] = 			'Şifreleme Anahtarı';
$lang['encryption_key_text'] = 		"Ionize Şifreleme Anahtarına İhtiyaç Duyuyor.<br />
									Bu Anahtar Kullanıcı Hesapları ve Hassas Verileri Şifreler.<br/>
									<b>/application/config/config.php</b> dosyası içerisine yazılacak.";
$lang['no_encryption_key_found'] = 	"Şifreleme Anahtarı Bulunamadı. Kullanıcı Hesabı Aktarılmayacak. <b>Yeni Bir Yönetici Hesabı Oluşturmanız Gerekiyor</b>.";


/* Example data */
$lang['data_install_intro'] = 	"Eğer Ionize'yi ilk defa kullanıyorsanız, Önemle örnek veriyi yüklemenizi tavsiye ederiz.<br/>
								İçerdiği Veriler : ";
$lang['data_install_list'] = 	"<li>Komple Veritabanı Verisi, Ionize'yi Test Etmek İçin,</li>
								<li>1 çalışan örnek tema</li>";
$lang['title_skip_this_step'] = 	"Bu Adımı Atla";


/* Finish screen */
$lang['title_finish'] = 		'Yükleme Tamamlandı.';
$lang['finish_text'] = 			'<b>ÖNEMLİ</b>: <br/>Yönetim Paneline Erişebilmeniz İçin "<b>/install</b>" Yükleme Klasörünü Manuel Olarak Silmeniz Gerekiyor.';
$lang['button_go_to_admin'] = 	'Yönetim Paneline Git';
$lang['button_go_to_site'] = 	'Ön Sayfaya Git';
