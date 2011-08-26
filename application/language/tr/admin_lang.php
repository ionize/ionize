<?php

/*
|--------------------------------------------------------------------------
| Ionize Dil Dosyası
| Language : Türkçe
| Translater : Ukyo, 27 07 2011
|
| Kullanımı :
| Form Etiketleri :					ionize_label_*
| Form Tuşları :					ionize_button_*
| Menü Öğeleri :					ionize_menu_*
| Sayfa Başlıkları , Başlıklar :	ionize_title_*
| Mesajlar :						ionize_message_*
|									ionize_*_message_*
| Tebliğ :							ionize_notify_*
|									ionize_*_notify_*
| Yardım (inline) :					ionize_help_*
|
| Notlar : 	Modül çevirileri öğeleri 'module_name' önekiyle başlayabilir
|			Örnek :
|			$lang['module_fancyupload_label_folder'] =			'Hedef Klasör';
|
| Etiket Dökümantasyou :	Her etiket bir "başlık" özelliği içerebilir
| 							Başlık özelliği değeri kullanıcıların anlamasına yardımcı olacaktır
|							Örnek : 
|							Etiket : 			$lang['ionize_label_appears'] = 		'Menüde Görünsün';
|							Etiket Başlığı : 	$lang['ionize_help_appears'] = 			'Öğe Navigasyonda Görünür Olacak';
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Admin login panel
|--------------------------------------------------------------------------
*/
$lang['ionize_admin_title'] = 'Yönetim Paneli';
$lang['ionize_home'] =				'Ana Sayfa';
$lang['ionize_login'] =				'Giriş Yap';
$lang['ionize_logout'] =			'Çıkış Yap';
$lang['ionize_goback'] =			'Yönetim Paneline Git';
$lang['ionize_website'] =			'Ön Sayfaya Gözat';
$lang['ionize_logged_as'] =			'Giriş yapıldı';
$lang['ionize_login_name'] =		'Kullanıcı Adı';
$lang['ionize_login_password'] =	'Şifre';
$lang['ionize_login_remember'] =	'Beni Hatırla';
$lang['ionize_login'] =				'Giriş';
$lang['ionize_forgot_password'] =	'Şifremi Unuttum ?';
$lang['ionize_session_expired'] = 'Oturumunuz zaman aşımına uğradı. Lütfen tekrar giriş yapın.';
$lang['ionize_login_error'] = 'Yolunda gitmeyen bişey var...';


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
$lang['ionize_dashboard_icon_add_page'] =		'Yeni Sayfa';
$lang['ionize_dashboard_icon_mediamanager'] =	'Medyalar';
$lang['ionize_dashboard_icon_translation'] =	'Çeviriler';
$lang['ionize_dashboard_icon_google_analytics'] =	'Analytics';
$lang['ionize_dashboard_icon_articles'] = 'Makaleler';

$lang['ionize_dashboard_title_content'] =		'İçerik';
$lang['ionize_dashboard_title_tools'] =			'Araçlar';
$lang['ionize_dashboard_title_settings'] =		'Ayarlar';

$lang['ionize_dashboard_title_last_modified_articles'] = 'Son Güncellenen Makaleler';
$lang['ionize_dashboard_title_last_connected_users'] =	'Son Bağlanan Kullanıcılar';
$lang['ionize_dashboard_title_last_registered_users'] = 'Son Kayıt Olan Kullanıcılar';
$lang['ionize_dashboard_title_orphan_pages'] = 'Orphan Sayfaları';
$lang['ionize_dashboard_title_orphan_articles'] = 'Yetim makaleler';

/*
|--------------------------------------------------------------------------
| Yapı
|--------------------------------------------------------------------------
*/
$lang['ionize_structure_main_menu'] = 'Ana Menü';
$lang['ionize_structure_system_menu'] = 'Sistem içeriği';
$lang['ionize_button_toggle_header'] = 'Çubuk başlığı';


/*
|--------------------------------------------------------------------------
| Admin navigation menu
|--------------------------------------------------------------------------
*/
$lang['ionize_menu_dashboard'] = 'Panel';
$lang['ionize_menu_google_analytics'] = 'Google Analytics';
$lang['ionize_menu_content'] =				'İçerik';
$lang['ionize_menu_articles'] = 'Makaleler';
$lang['ionize_menu_translation'] =			'Çeviriler';
$lang['ionize_menu_modules'] =				'Modüller';
$lang['ionize_menu_tools'] =				'Araçlar';
$lang['ionize_menu_settings'] =				'Ayarlar';
$lang['ionize_menu_help'] =					'Yardım';

$lang['ionize_menu_menu'] = 'Menüleri yönet...';
$lang['ionize_menu_page'] =					'Sayfa oluştur...';
$lang['ionize_menu_article'] =				'Makale Oluştur...';
$lang['ionize_menu_media_manager'] =		'Medya yöneticisi';

$lang['ionize_menu_modules_admin'] =		'Yönetim...';

$lang['ionize_menu_site_settings'] =		'Site Ayarları...';
$lang['ionize_menu_global'] =				'Genel...';
$lang['ionize_menu_theme'] =				'Tema...';
$lang['ionize_menu_technical_settings'] =	'Gelişmiş Ayarlar...';
$lang['ionize_menu_translations'] =			'Statik Çeviri';

$lang['ionize_menu_site_settings_global'] =			'Site Ayarları';
$lang['ionize_menu_site_settings_translations'] =	'Statik Çeviri';
$lang['ionize_menu_site_settings_technical'] =		'Gelişmiş Ayarlar';
$lang['ionize_menu_ionize_settings'] = 'Ionize Arayüzü';

$lang['ionize_menu_users'] =			'Kullanıcılar...';
$lang['ionize_menu_languages'] =		'Diller...';

$lang['ionize_menu_about'] =			'Hakkında';
$lang['ionize_menu_documentation'] =	'Dökümantasyon';
$lang['ionize_menu_extend_fields'] =	'Genişletilmiş Alanlar';
$lang['ionize_menu_content_elements'] = 'İçerik Öğeleri';


/*
|--------------------------------------------------------------------------
| Genel Başlıklar
|--------------------------------------------------------------------------
*/
$lang['ionize_title_welcome'] =				'Ionize\'ye Hoş Geldiniz';
$lang['ionize_title_structure'] =			'Yapı';
$lang['ionize_title_options'] = 'Ayarlar';
$lang['ionize_title_attributes'] = 'Öznitelikler';
$lang['ionize_title_options'] =				'Ayarlar';
$lang['ionize_title_advanced'] =			'Gelişmiş Ayarlar';
$lang['ionize_title_dates'] =				'Tarihler';
$lang['ionize_title_informations'] =		'Bilgi';
$lang['ionize_title_authorization'] =		'Erişim Yetkileri';
$lang['ionize_title_metas'] =				'Anahtar Kelimeler';
$lang['ionize_title_modules'] =				'Modül Yönetimi';
$lang['ionize_title_menu'] = 'Menü yönetimi';
$lang['ionize_title_documentation'] = 'Dökümantasyon';
$lang['ionize_title_ionize_settings'] = 'Ionize Arayüzü';
$lang['ionize_title_help'] = 'Yardım';
$lang['title_delete_installer'] =			'INSTALL Klasörünü silin';
$lang['title_ionize_installation'] = 		'Ionize Yükleyici';

$lang['ionize_title_site_settings'] = 'Site Ayarları';
$lang['ionize_title_technical_settings'] = 'Gelişmiş Ayarlar';

/*
|--------------------------------------------------------------------------
| Modal windows
|--------------------------------------------------------------------------
*/
$lang['ionize_modal_confirmation_title'] =	'Onaylıyormusun ?';
$lang['ionize_modal_information_title'] = 'Bilgi';
$lang['ionize_modal_alert_title'] = 'Uyarı';
$lang['ionize_modal_error_title'] = 'Hata';


/*
|--------------------------------------------------------------------------
| Menus 
|--------------------------------------------------------------------------
*/
$lang['ionize_title_add_menu'] = 'Menü Ekle';
$lang['ionize_button_save_new_menu'] = 'Menüyü Kaydet';

$lang['ionize_title_existing_menu'] = 'Varolan Menü';
$lang['ionize_message_menu_saved'] = 'Menü Kaydedildi';
$lang['ionize_message_menu_not_saved'] = 'Menü Kaydedilemedi';
$lang['ionize_message_menu_already_exists'] = 'Böyle Bir Menü Zaten Var';
$lang['ionize_message_menu_updated'] = 'Menü Güncellendi';
$lang['ionize_message_menu_ordered'] = 'Menü Sıralandı';
$lang['ionize_message_menu_deleted'] = 'Menü Silindi';
$lang['ionize_message_menu_not_deleted'] = 'Menü Silinmedi';


/*
|--------------------------------------------------------------------------
| Content Elements 
|--------------------------------------------------------------------------
*/
$lang['ionize_button_save_element'] = 'Öğeyi Kaydet';

$lang['ionize_title_content_element_list'] = 'İçerik Öğeleri';
$lang['ionize_title_add_content_element'] = 'Element Ekle';
$lang['ionize_title_edit_content_element'] = 'Elementi Düzenle';
$lang['ionize_title_element_edit'] = 'Elementi Düzenle';
$lang['ionize_title_element_new'] = 'Yeni İçerik Elementi';
$lang['ionize_title_element_field_edit'] = 'İçerik Elementini Düzenle';

$lang['ionize_title_element_fields'] = 'Alanlar';

$lang['ionize_label_element_set_name'] = 'İsmi kaydet';
$lang['ionize_label_content_element'] = 'İçerik Elementi';
$lang['ionize_label_add_content_element'] = 'Element Ekle';
$lang['ionize_label_back_to_element_list'] = 'Elementlere Geri Dön';
$lang['ionize_label_see_element_detail'] = 'Element Detayları...';



$lang['ionize_message_content_element_name_saved'] = 'Yeni İsim Kaydedildi';
$lang['ionize_message_content_element_saved'] = 'İçerik Elementi Kaydedildi';
$lang['ionize_message_content_element_not_saved'] = 'İçerik Elementi Kaydedilemedi';
$lang['ionize_message_delete_element_definition'] = 'Bu içerik elementi tanımını sil ?';
$lang['ionize_message_element_ordered'] = 'Element Sıralandı';
$lang['ionize_message_element_copied'] = 'Element kapyalandı !';
$lang['ionize_message_element_moved'] = 'Element taşındı !';

$lang['ionize_label_create_element'] = 'Element Oluştur';
$lang['ionize_message_element_field_deleted'] = 'Element Alanı Silindi';
$lang['ionize_message_element_field_not_deleted'] = 'Element Alanı Silinemedi';
$lang['ionize_message_element_field_saved'] = 'İçerik Elementi Kaydedildi';
$lang['ionize_message_element_field_not_saved'] = 'İçerik Elementi Kaydedilemedi';
$lang['ionize_message_element_in_use'] = 'Bu elementin alanları veya element kullanımda ve silinemez.';
$lang['ionize_message_element_cannot_be_added_to_parent'] = 'Bu element alt öğe olarak eklenemez !';
$lang['ionize_message_element_definition_name_already_exists'] = 'Aynı isme sahip başka bir element kayıtlı !';

$lang['ionize_title_element_field_new'] = 'Yeni Element Alanı';
$lang['ionize_message_element_field_name_exists'] = 'Var olan bir alan eklemeye çalışıyorsunuz !';

/*
| Global forms labels & help
| Labels are also commonly used for table head column titles
|--------------------------------------------------------------------------
*/

$lang['ionize_label_change'] = 'Değiştir';
$lang['ionize_label_multilingual'] = 'Çokludil';
$lang['ionize_label_internal_id'] = 'İç Kimlik';
$lang['ionize_label_online'] =		'Yayında';
$lang['ionize_label_offline'] =		'Yayında Değil';
$lang['ionize_label_set_offline'] =	'Yayınlama';
$lang['ionize_label_set_online'] =	'Yayınla';
$lang['ionize_label_edit'] =		'Düzenle';
$lang['ionize_label_delete'] =		'Sil';
$lang['ionize_label_unlink'] = 'Linki Kaldır';
$lang['ionize_label_status'] =		'Durum';
$lang['ionize_label_max_upload_size'] =	'En Fazla Yükleme Boyutu?';
$lang['ionize_label_file_uploads'] =	'Yükle ?';
$lang['ionize_label_site_email'] =		'E-Posta Site';
$lang['ionize_label_linkto'] = 'Bağla...';
$lang['ionize_label_drop_link_here'] = 'bağlantıyı buraya bırak...';
$lang['ionize_label_drop_page_here'] = 'sayfayı buraya bırak...';
$lang['ionize_label_drop_article_here'] = 'makaleyi buraya bırak...';
$lang['ionize_label_add_link'] = 'Bağlantıyı Ekle';
$lang['ionize_label_url'] = 'URL';
$lang['ionize_label_see_online'] = 'Yayında Gör';
$lang['ionize_label_flag'] = 'İşaretleyici';
$lang['ionize_label_flags'] = 'İşaretleyiciler';
$lang['ionize_label_edit_flags'] = 'İşaretleyiciyi düzenle';

$lang['ionize_label_page'] = 'Sayfa';
$lang['ionize_label_article'] = 'Makale';
$lang['ionize_label_media'] = 'Medya';
$lang['ionize_label_users'] = 'Kullanıcılar';
$lang['ionize_label_user'] = 'Kullanıcı';
$lang['ionize_label_table'] = 'Tablo';

$lang['ionize_label_name'] =				'İsim';
$lang['ionize_label_id'] =					'ID';
$lang['ionize_label_parent'] =				'Ana / Kaynak';
$lang['ionize_label_permanent_url'] =		'Kalıcı URL';
$lang['ionize_label_view'] = 'Görünüm';
$lang['ionize_label_article_template'] =		'Makale Görünümü';

$lang['ionize_label_title'] =		'Başlık';
$lang['ionize_label_subtitle'] =	'Alt Başlık';
$lang['ionize_label_meta_title'] =	'Pencere Başlığı'; 
$lang['ionize_label_nav_title'] = 'Navigasyon başlığı';
$lang['ionize_label_text'] =		'Metin';
$lang['ionize_label_content'] =		'İçerik';
$lang['ionize_label_category'] =	'Kategori';
$lang['ionize_label_ordering'] =	'Sıralama';
$lang['ionize_label_pictures'] =	'Resim';
$lang['ionize_label_music'] =		'Müzik';
$lang['ionize_label_videos'] =		'Video';
$lang['ionize_label_files'] =		'Dosya';
$lang['ionize_label_default'] =		'Varsayılan';
$lang['ionize_label_code'] =		'Kod';
$lang['ionize_label_toggle_editor'] =	'Göster / Gizle HTML';

$lang['ionize_label_in_menu'] =			'Menüde görünüyor';
$lang['ionize_label_parent'] =			'Ana / Kaynak';
$lang['ionize_label_meta_keywords'] =	'Anahtar Kelimeler';
$lang['ionize_label_meta_description']= 'Açıklama';
$lang['ionize_label_created'] =			'Oluşturulma Tarihi';
$lang['ionize_label_updated'] =			'Güncelleme Tarihi';
$lang['ionize_label_publish_on'] =		'Yayınla';
$lang['ionize_label_publish_off'] =		'Yayınlama';
$lang['ionize_label_permanent_url'] =	'Kalıcı URL';
$lang['ionize_label_add_media'] =		'Medya Ekle';
$lang['ionize_label_author'] = 'Yazar';
$lang['ionize_label_updater'] = 'Güncelleyen';

$lang['ionize_label_groups'] =			'Gruplar';

$lang['ionize_label_installed'] =			'Yüklendi';
$lang['ionize_label_hide_options'] =		'Ayarları Gizle';
$lang['ionize_label_show_options'] = 'Ayarları Göster';

$lang['ionize_label_copy_to_other_languages'] = 'Diğer dillere kopyala';
$lang['ionize_help_status'] =			'Yönetici olarak bağlı iseniz, Bağlantıyı sonlandırana kadar bu elementi göreceksiniz';
$lang['ionize_help_online'] = 'Bu element genel olarak online mı?';
$lang['ionize_help_online_lang'] =		'Bu dil için bu öğe yayınlansın mı ?';
$lang['ionize_help_publish_on'] = 		'Öğeyi belirtilen tarihte yayınla ve görüntülenen öğe tarihiyle değiştir';
$lang['ionize_help_publish_off'] = 		'Belirtilen tarihte öğeyi yayından kaldır';
$lang['ionize_help_url'] = 'Element URL\'si';
$lang['ionize_help_flag'] = 'İç işaretleyici organize edildi.';
$lang['ionize_help_flags'] = 'İç işaretleyiciler, henüz organize edildi.';
$lang['ionize_help_help'] = 'Yardım ipuçlarını şu anda okuduğum gibi görüntüle, yada görüntüleme';
$lang['ionize_help_copy_to_other_languages'] = 'Diğer dillere kopyala';

/*
|--------------------------------------------------------------------------
| Global forms buttons
|--------------------------------------------------------------------------
*/
$lang['ionize_button_save'] =		'Kaydet';
$lang['ionize_button_save_close'] =	'Kaydet & Kapat';
$lang['ionize_button_send'] =		'Gönder';
$lang['ionize_button_add'] = 'Ekle';
$lang['ionize_button_next'] =		'İleri';
$lang['ionize_button_delete'] =		'Sil';
$lang['ionize_button_new'] =		'Yeni';
$lang['ionize_button_close'] =		'Kapat';
$lang['ionize_button_yes'] =		'Evet';
$lang['ionize_button_no'] =			'Hayır';
$lang['ionize_button_ok'] = 'Tamam';
$lang['ionize_button_confirm'] =	'Onayla';
$lang['ionize_button_cancel'] =		'İptal';
$lang['ionize_button_add_page'] =	'Sayfa Ekle';
$lang['ionize_button_switch_online'] =	'Bağlı / Bağlı Değil';
$lang['ionize_button_filter'] = 'Filtrele';

$lang['ionize_button_save_new_lang'] = 'Bu dili ekle';
$lang['ionize_button_save_page'] = 'Sayfayı Kaydet';
$lang['ionize_button_save_article'] = 'Makaleyi Kaydet';
$lang['ionize_button_save_module_settings'] = 'Ayarları Kaydet';
$lang['ionize_button_save_views'] = 'Görünümü Kaydet';
$lang['ionize_button_save_themes'] = 'Bu Temaları Kullan';
$lang['ionize_button_save_settings'] = 'Ayarları Kaydet';
/*
|--------------------------------------------------------------------------
| Global Messages
|--------------------------------------------------------------------------
*/

$lang['ionize_message_element_not_found'] = 'Bu element bulunamadı !';
$lang['ionize_confirm_element_delete'] =	'Bu elementi kesinlikle silmek istediğinizden eminmisiniz ?';
$lang['ionize_message_missing_params'] =	'Eksik Ayarlar';
$lang['ionize_message_operation_ok'] =		'Operasyon Başarılı';
$lang['ionize_message_operation_nok'] =		'Operasyon Başarısız';
$lang['ionize_message_delete_installer'] =  'ÖNEMLİ : <br/>Güvenlik nedeniyle, lütfen <b>\'/install\'</b> klasörünü silin. Ionize bu dosya silinene kadar kullanılabilir olmayacak.';
$lang['ionize_message_no_circular_link'] = 'Dairesel bağlantıya izin verilmez';
$lang['ionize_message_link_added'] = 'Bağlantı Eklendi';
$lang['ionize_message_target_link_not_unique'] = 'Hedef elementin ağaç menüde eşi olmamalıdır!';
$lang['button_delete_installer_done_admin'] =  	'Tamam! Yönetim Paneline Git';
$lang['button_delete_installer_done_site'] =  	'Tamam! Ön Sayfaya Git';
$lang['ionize_message_item_used_by_data_no_delete'] = 'Bu öğe kullanımda ve silinemez';
$lang['ionize_message_url_not_found'] = 'URL bulunamadı';
$lang['ionize_message_url_got_404'] = 'Bu URL 404 sayfasına gidiyor';

/*
|--------------------------------------------------------------------------
| Admin : Language
|--------------------------------------------------------------------------
*/
$lang['ionize_label_copy_content'] = 'İçeriği kopyala';
$lang['ionize_label_copy_all_content'] = 'Tüm içeriği kopyala';
$lang['ionize_label_copy_article_content'] = 'İçerdiği makaleler';
$lang['ionize_label_force_lang_urls'] = 'Force lang in URLs'; //Çevrilecek
$lang['ionize_title_language'] =			'Dil Yönetimi';
$lang['ionize_title_existing_languages'] =	'Var Olan Diller';
$lang['ionize_title_add_language'] =		'Dil Ekle';
$lang['ionize_title_advanced_language'] = 'Gelişmiş fonksiyonlar';
$lang['ionize_title_lang_urls'] = 'URL ler ve Diller';
$lang['ionize_message_no_languages'] = 		'<b>Var Olan Dil Yok</b>. <br/> Lütfen Bir Dil Oluşturun.';
$lang['ionize_message_lang_saved'] = 		'Dil Kaydedildi';
$lang['ionize_message_lang_not_saved'] = 	'Dil Kaydedilemedi';
$lang['ionize_message_lang_file_not_saved'] = 	'Hata : config/language.php yazılamıyor';
$lang['ionize_message_lang_code_already_exists'] = 	'Bu dil kodu zaten kullanılıyor.';
$lang['ionize_message_lang_not_deleted'] = 	'Dil Silinemedi';
$lang['ionize_message_lang_deleted'] = 		'Dil Silindi';
$lang['ionize_message_lang_ordered'] = 		'Diller Sıralandı';
$lang['ionize_message_lang_not_ordered'] = 	'Diller Sıralanamadı';
$lang['ionize_message_lang_updated'] = 		'Dil Güncellendi';
$lang['ionize_notify_advanced_language'] = 'Ne yaptığınızdan eminseniz bu fonksiyonu kullanın.';
$lang['ionize_button_clean_lang_tables'] = 'Dil tablolarını temizle';
$lang['ionize_button_copy_content'] = 'İçeriği kopyala';
$lang['ionize_help_clean_lang_tables'] = 'Varolmayan dillerin içeriğinin dil tablolarını temizler.';
$lang['ionize_help_copy_content'] = 'Bir elementin içeriğini diğer dile kopyalar. Kopyalananlar (Başlık, alt başlık, içerik, vb.)';
$lang['ionize_help_copy_all_content'] = 'Başka bir dilden sitenin içeriğini komple kopyalar. Varolan içeriğe zarar gelmez';
$lang['ionize_help_copy_article_content'] = 'Ayrıca bağlantısı oluşturulmuş makalelerin içeriklerini kopyalar';
$lang['ionize_confirmation_clean_lang'] = 'Tanımlanmamış dil tabloları dil tablosundan silinecek...';
$lang['ionize_message_lang_tables_cleaned'] = 'İçerik tabloları temizlendi';

$lang['ionize_message_article_content_copied'] = 'Makale içeriği kopyalandı';
$lang['ionize_message_source_destination_lang_not_different'] = 'Kaynak ve hedef dil farklı olmalıdır!';
$lang['ionize_message_page_content_copied'] = 'Sayfa içeriği kopyalandı';
$lang['ionize_message_page_article_content_copied'] = 'Sayfa & makalelerin içerikleri kopyalandı';
$lang['ionize_message_confirm_copy_whole_content'] = 'Bu dilin tüm içeriğini gerçekten kopyalamak istiyormusunuz?';
$lang['ionize_message_lang_content_copied'] = 'Dil içeriği kopyalama başarılı';

/*
|--------------------------------------------------------------------------
| Admin : Users & groups
|--------------------------------------------------------------------------
*/
$lang['ionize_title_users'] =				'Kullanıcı Yönetimi';
$lang['ionize_title_user_edit'] =			'Kullanıcı Düzenle';
$lang['ionize_title_existing_users'] =		'Var Olan Kullanıcı';
$lang['ionize_title_existing_groups'] = 'Varolan Gruplar';
$lang['ionize_title_group_edit'] = 'Grup Düzenle';
$lang['ionize_title_add_user'] =			'Kullanıcı Ekle';
$lang['ionize_title_add_group'] = 'Bir Grup Ekle';
$lang['ionize_title_change_password'] =		'Şifreyi Değiştir';
$lang['ionize_title_users_export'] = 'Kullanıcıları Aktar';
$lang['ionize_title_user_meta'] = 'Kullanıcı Meta Verileri';
$lang['ionize_title_filter_userslist'] = 			'Kullanıcı Listesi Filtreleme';
$lang['ionize_label_username'] =					'ID (kullanıcı adı)';
$lang['ionize_label_screen_name'] = 		'Tam İsim';
$lang['ionize_label_email'] = 				'E-Posta';
$lang['ionize_label_group'] = 				'Grup';
$lang['ionize_label_password'] = 			'Şifre';
$lang['ionize_label_password2'] = 			'Onay';
$lang['ionize_label_group_name'] = 'İsim';
$lang['ionize_label_group_title'] = 'Başlık';
$lang['ionize_label_group_level'] = 'Seviye';
$lang['ionize_label_group_description'] = 'Açıklama';
$lang['ionize_label_export_meta'] = 'Dışarı Aktar';
$lang['ionize_label_export_format'] = 'Biçim';
$lang['ionize_label_last_visit'] = 'Son Ziyaret';
$lang['ionize_label_join_date'] = 'Kayıt tarihi';
$lang['ionize_label_users_count'] = 'Kullanıcı sayısı';
$lang['ionize_label_all_groups'] = '-- Tüm Gruplar --';
$lang['ionize_label_last_registered'] = 'Son kayıt olan';
$lang['ionize_label_users_per_page'] = 'Kullanıcılar / sayfa';
$lang['ionize_label_filter_result'] = 'Filtreleme sonucu';
$lang['ionize_message_user_updated'] = 		'Kullanıcı Güncellendi';
$lang['ionize_message_user_not_saved'] = 	'Kullanıcı Güncellenemedi';
$lang['ionize_message_user_saved'] = 		'Kullanıcı Kaydedildi';
$lang['ionize_message_user_exists'] = 'Kullanıcı Zaten Veritabanında Mevcut!';
$lang['ionize_message_user_deleted'] = 		'Kullanıcı Silindi';
$lang['ionize_message_user_cannot_delete_yourself'] = 	'Kendinizi Silemezsiniz!';
$lang['ionize_message_group_updated'] = 'Grup Güncellendi';
$lang['ionize_message_group_not_saved'] = 'Grup Güncellenemedi';
$lang['ionize_message_group_saved'] = 'Grup Kaydedildi';
$lang['ionize_message_group_deleted'] = 'Grup Silindi';
$lang['ionize_message_users_exported'] = 'Kullanıcılar Aktarıldı';
$lang['ionize_message_users_not_exported'] = 'Kullanıcılar Aktarılamadı';

$lang['ionize_button_export'] =	'Dışarı Aktar';

/*
|--------------------------------------------------------------------------
| Admin : Ayarlar
|--------------------------------------------------------------------------
*/

$lang['ionize_label_site_title'] =			'Site Başlığı';
$lang['ionize_message_settings_saved'] = 	'Ayarlar Kaydedildi';
$lang['ionize_title_visual_help'] =	'Görsel Yardım';
$lang['ionize_label_show_help_tips'] =		'Alanlarda Yardım Görüntüle';
$lang['ionize_title_admin_panel_languages'] = 'Yönetim paneli arayüz dili';
$lang['ionize_title_admin_panel_datetime'] =  'Tarih ve Zaman';
$lang['ionize_label_display_connected_label'] = 'Bağlı etiket';
$lang['ionize_help_display_connected_label'] = 'Yönetim paneline giriş yapıldığında internet sitesinin sol üst köşesinde etiket gösterilecek';
$lang['ionize_onchange_ionize_settings'] = 'Yönetim panelini ayarları kaydettikten sonra sayfa otomatik olarak yenilenecektir.';

$lang['ionize_label_sitemaps_gzip'] = 'Site haritasını sıkıştır';
$lang['ionize_help_setting_sitemaps_gzip'] = 'Site haritası dosyasını gzip ile sıkıştır.';


/*
|--------------------------------------------------------------------------
| Admin : Teknik Ayarlar
|--------------------------------------------------------------------------
*/
$lang['ionize_title_themes'] =				'Temalar';
$lang['ionize_title_theme'] =				'Tema';
$lang['ionize_title_translation'] =			'Statik Çeviri';
$lang['ionize_title_database'] =			'Veritabanı';
$lang['ionize_title_mail_send'] =			'E-posta gönderiliyor';
$lang['ionize_title_media_management'] =	'Medya Yönetimi';
$lang['ionize_title_google_analytics'] =	'İstatistikler';
$lang['ionize_title_thumb_new'] =			'Yeni Küçük Resim';

$lang['ionize_title_thumbs'] =				'Küçük Resimler';
$lang['ionize_title_thumbs_system'] =		'Ionize sistem küçük resmi';
$lang['ionize_title_form_antispam_key'] = 'Antispam JS anahtarı';
$lang['ionize_title_article_management'] = 'Makale Yönetimi';
$lang['ionize_title_admin_url'] = 'Yönetim Paneli Yolu';
$lang['ionize_title_encryption_key'] = 'Şifreleme Anahtarı';
$lang['ionize_title_cache'] = 'Ön Bellek Sistemi';
$lang['ionize_title_allowed_mimes'] = 'İzin Verilen Dosya Uzantıları';
$lang['ionize_label_article_allowed_tags'] = 'İzin Verilen Taglar';
$lang['ionize_title_seo'] = 'SEO';
$lang['ionize_title_permalink_ping_server'] = 'Permalink Ping servers';
$lang['ionize_title_sitemap_search_engine'] = 'Sitemap Ping Search Engines';

$lang['ionize_title_maintenance'] = 'Bakım Modu';
$lang['ionize_title_maintenance_page'] = 'Bakım Sayfası';
$lang['ionize_label_maintenance'] = 'Bakımda';
$lang['ionize_label_maintenance_ips'] = 'IPleri kısıtla';
$lang['ionize_drop_maintenance_page_here'] = 'İstediğiniz bakım sayfasını bırakın...';
$lang['ionize_label_maintenance_help'] = 'İşaretlendiğinde, internet siteniz bakım mesajını görüntüleyecek. Bakım sırasında Ionize yi kullanabilirsiniz.';
$lang['ionize_label_maintenance_ips_help'] = 'İnternet sitenizin ön sayfası sadece belirlediğiniz IP ler tarafından görünebilir olacak';
$lang['ionize_label_your_ip'] = 'IP adresin';
$lang['ionize_label_maintenance_page_help'] = 'Ionize nin HTML sayfasını düzgün ve doğru oluşturabilmesi için bu sayfanın bakım modunu aktiv etmeden ayarlanması gerekiyor.';
$lang['ionize_message_maintenance_page_curl_error'] = 'PHP eklentisi "cURL" nin yüklü olması gerekiyor';
$lang['ionize_label_thumb_automatic'] = 'Otomatik';
$lang['ionize_label_files_path'] =			'Medya ana dizini';
$lang['ionize_label_media_type_picture'] =	'Resim Uzantıları';
$lang['ionize_label_media_type_video'] =	'Video Uzantıları';
$lang['ionize_label_media_type_music'] =	'Müzik Uzantıları';
$lang['ionize_label_media_type_file'] =		'Dosya Uzantıları';

$lang['ionize_label_filemanager'] =			'Dosya Yöetimi';
$lang['ionize_label_media_thumb_size'] = 'Küçük resim boyutu';
$lang['ionize_label_theme'] = 				'Tema';
$lang['ionize_label_theme_admin'] = 		'Yönetici Teması';
$lang['ionize_label_db_driver'] = 			'Sürücü';
$lang['ionize_label_db_host'] = 			'Host';
$lang['ionize_label_db_name'] = 			'Veritabanı Adı';
$lang['ionize_label_db_user'] = 			'Kullanıcı Adı';
$lang['ionize_label_db_pass'] = 			'Şifre';
$lang['ionize_label_google_analytics'] = 	'Google Analytics';
$lang['ionize_label_tinybuttons'] = 'TinyMCE Araç Çubuğu';
$lang['ionize_label_restore_tinybuttons'] = 'TinyMCE varsayılan araç çubuğu';
$lang['ionize_label_tinybuttons'] = 'TinyMCE Araç Çubuğu';
$lang['ionize_label_tinyblockformats'] = 'TinyMCE Biçim Seçimi';
$lang['ionize_label_restore_tinyblockformats'] = 'TinyMCE Varsayılan Biçimi Geri Yükle';
$lang['ionize_label_ping_url'] = 'URL';
$lang['ionize_label_sitemap_url'] = 'URL';

$lang['ionize_text_sitemaps_url_list'] = 'Eklenecek URL listesi (satırlarla ayrılması gerekiyor)';
$lang['ionize_text_ping_url_list'] = 'Eklenecek URL listesi (satırlarla ayrılması gerekiyor)';
$lang['ionize_label_smtp_protocol'] =		'Protokol';
$lang['ionize_label_smtp_host'] =			'Host';
$lang['ionize_label_smtp_user'] =			'Kullcanıcı Adı';
$lang['ionize_label_smtp_pass'] =			'Şifre';
$lang['ionize_label_smtp_port'] =			'Port';
$lang['ionize_label_email_charset'] =		'Karakter Kodlaması';
$lang['ionize_label_email_mailtype'] =		'Format';
$lang['ionize_label_mailpath'] =			'Posta Yolu';

$lang['ionize_label_cache_enabled'] = 'Aktif';
$lang['ionize_label_cache_expiration'] = 'Süre (dk.)';
$lang['ionize_label_clear_cache'] = 'Önbelleği Temizle';
$lang['ionize_button_clear_cache'] = 'Temizle';
$lang['ionize_label_thumb_dir'] = 			'Dosya';
$lang['ionize_label_thumb_size'] = 			'Boyut';
$lang['ionize_label_thumb_sizeref'] = 		'Referans';
$lang['ionize_label_thumb_sizeref_width'] = 	'Genişlik';
$lang['ionize_label_thumb_sizeref_height'] = 	'Yükseklik';
$lang['ionize_label_thumb_square'] = 		'Kare';
$lang['ionize_label_thumb_unsharp'] = 		'Keskin Olmayan Filtre';
$lang['ionize_label_thumb_list'] = 			'Resim Listesi';
$lang['ionize_label_thumb_edition'] = 		'Resim Baskısı';
$lang['ionize_label_thumbs_system'] = 		'Resim Liste / Düzenle';
$lang['ionize_label_setting_picture_max_width'] = 'Resim Genişliği maks.';
$lang['ionize_label_setting_picture_max_height'] = 'Resim Yüksekliği maks.';

$lang['ionize_onchange_filemanager'] = 'Değişiklikleri kaydettikten sonra yönetici panelini yeniden yükleyin.';
$lang['ionize_onchange_texteditor'] = 'Değişiklikleri kaydettikten sonra yönetici panelini yeniden yükleyin.';
$lang['ionize_label_antispam_key'] = 'Varolan anahtar';
$lang['ionize_label_refresh_antispam_key'] = 'Anahtarı yenile';
$lang['ionize_label_texteditor'] = 'Yazı Editörü';
$lang['ionize_title_db_version'] = 			'Veritabanı';
$lang['ionize_title_php_version'] = 		'PHP';

$lang['ionize_message_database_not_saved'] = 		'Yanlış veritabanı ayarları';
$lang['ionize_message_database_not_exist'] = 		'Seçili veritabanı mevcut değil';
$lang['ionize_message_database_connection_error'] = 'Veritabanı bağlantısı şuanda mümkün değil';
$lang['ionize_message_database_saved'] = 				'Veritabanı ayarları başarıyla kaydedildi';

$lang['ionize_message_smtp_not_saved'] = 		'E-posta ayarları eksik veya yanlış';
$lang['ionize_message_smtp_saved'] = 			'E-posta ayarları kaydedildi';

$lang['ionize_message_thumb_saved'] = 					'Küçük Resim Kaydedildi';
$lang['ionize_message_thumb_not_saved'] = 				'Küçük Resim Kaydedilemedi';
$lang['ionize_message_thumb_deleted'] = 				'Küçük Resim Silindi';
$lang['ionize_message_thumb_not_deleted'] = 			'Küçük Resim Silinemedi';

$lang['ionize_message_admin_url_error'] = 'Yönetim paneli URL si boş bırakılamaz ve sadece Alfanümerik karakterler içermek zorundadır.';
$lang['ionize_confirm_change_admin_url'] = 'Yeni URL yi hatırlayacağımızdam emin olun ! Kayıt ettikten sonra, Yönetim Paneli sayfası yeniden yüklenecek.';
$lang['ionize_message_error_no_files_path'] = 'Medya klasörü oluşturulmalı!';
$lang['ionize_message_error_writing_file'] = 'Dosya yazma hatası';
$lang['ionize_message_error_writing_config_file'] = 'application/config/config.php  yazılamıyor !';
$lang['ionize_message_error_writing_medias_file'] = 	'/config/medias.php yazılamıyor !';
$lang['ionize_message_error_writing_database_file'] = 	'/config/database.php yazılamıyor !';
$lang['ionize_message_error_writing_email_file'] = 		'/config/email.php yazılamıyor !';
$lang['ionize_message_error_writing_ionize_file'] = 'application/config/ionize.php yazılamıyor !';

$lang['ionize_message_cache_saved'] = 'Önbellek Ayarları Kaydedildi.';
$lang['ionize_message_cache_cleared'] = 'Önbellek Temizlendi.';
$lang['ionize_message_urls_saved'] = 'URL ler Kaydedildi';
$lang['ionize_message_setting_saved'] = 'Ayarlar Kaydedildi';

$lang['ionize_help_setting_google_analytics'] = 'Google Analytics web sitesinden kopyaladığınız script ile analytics işlemini tamamlayın';
$lang['ionize_help_setting_files_path'] =			'Medya Dosya Yolu Belirtin. Fiziksel dosya adını değiştirmeyin';
$lang['ionize_help_setting_system_thumb_list'] =	'Küçük resimler ionize resim listesi ve resim düzenleme penceresinde gösterildi';
$lang['ionize_help_setting_media_type_picture'] =	'Uzantılar, noktasız, virgülle ayrılmış';
$lang['ionize_help_setting_media_type_music'] =		'Uzantılar, noktasız, virgülle ayrılmış';
$lang['ionize_help_setting_media_type_video'] =		'Uzantılar, noktasız, virgülle ayrılmış';
$lang['ionize_help_setting_media_type_file'] =		'Uzantılar, noktasız, virgülle ayrılmış';
$lang['ionize_help_media_thumb_size'] = 'Sistem küçük resim boyutu, pixel olarak';
$lang['ionize_help_tinybuttons'] = 'TinyMCE dökümantasyonuna gözat';
$lang['ionize_help_setting_picture_max_height'] = 'Yükeleme esnasında resim boyutları belirtilen boyutları aşarsa yeniden boyutlandırılır.';
$lang['ionize_help_setting_picture_max_width'] = 'Yükeleme esnasında resim boyutları belirtilen boyutları aşarsa yeniden boyutlandırılır.';
$lang['ionize_help_tinyblockformats'] = 'Varsayılan TinyMCE Blok Biçimi (Seç)';

$lang['ionize_help_cache_enabled'] = 'Ön Belleği Aç / Kapat. Ön belleği kapatmak varolan önbeller verilerini temizleyecek.';
$lang['ionize_help_cache_expiration'] = 'Belirtiğiniz dakika içerisinde sayfalar yeniden önbelleğe alınacak.';
$lang['ionize_help_clear_cache'] = 'Tüm önbellek dosyalarını sil.';
$lang['ionize_help_article_allowed_tags'] = 'Makale içeriğinde izin verilen HTML tagları';
/*
|--------------------------------------------------------------------------
| Admin : Temalar
|--------------------------------------------------------------------------
*/
$lang['ionize_title_views_list'] =		'Şu anki tema görünüm listesi';
$lang['ionize_title_view_edit'] =		'Düzenle';
$lang['ionize_title_views_translations'] =		'Şu anki tema statik element çevirileri';
$lang['ionize_label_view_filename'] =	'Dosya';
$lang['ionize_label_view_folder'] =		'Klasör';
$lang['ionize_label_view_name'] =		'Mantıksal İsim';
$lang['ionize_label_view_type'] =		'Tipi';
$lang['ionize_label_current_theme'] =	'Şu Anki Tema';

$lang['ionize_select_no_type'] =		'-- Tip Yok --';
$lang['ionize_message_views_saved'] =	'Görüntüleme Ayarları Kaydedildi';

$lang['ionize_message_view_saved'] =	'Görünüm Kaydedildi';


/*
|--------------------------------------------------------------------------
| Admin : Sayfa
|--------------------------------------------------------------------------
*/
$lang['ionize_title_pages'] =					'Sayfalar';
$lang['ionize_title_create_page'] = 'Sayfa oluştur';
$lang['ionize_title_new_page'] =				'Sayfa Oluştur';
$lang['ionize_title_edit_page'] =				'Sayfa Düzenle';
$lang['ionize_title_page_parent'] = 'Ana / Kaynak';
$lang['ionize_title_sub_navigation'] = 'Alt Navigasyon';

$lang['ionize_title_help_articles_types'] = 'Tipler Hakkında';
$lang['ionize_label_page_online'] = 'Sayfa yayında';
$lang['ionize_label_page_content_online'] = 'Bu dil için yayında';
$lang['ionize_label_articles'] =				'Makaleler';
$lang['ionize_label_add_article'] =				'Bir Makale Ekle';
$lang['ionize_label_appears'] =					'Menüde Göster';
$lang['ionize_label_link'] =					'Adres';
$lang['ionize_label_pagination_nb'] =			'Makaleler / sayfa';
$lang['ionize_label_article_list_template'] =	'Görüntü Listesi';
$lang['ionize_label_page_delete_date'] = 'Sayfa tarih silme';
$lang['ionize_label_menu'] = 'Menü';
$lang['ionize_label_home_page'] = 'Ana sayfa';
$lang['ionize_label_sitemap_priority'] = 'Site Haritasında Öncelik';
$lang['ionize_label_article_reorder'] = 'Kayıtlı Makaleler';
$lang['ionize_label_date_asc'] = 'Tarih Artan';
$lang['ionize_label_date_desc'] = 'Tarih Azalan';
$lang['ionize_label_no_sub_navigation'] = '-- Hiçbiri --';



$lang['ionize_button_reorder'] = 'Tekrar Sırala';
$lang['ionize_select_default_view'] =			'-- Varsayılan Görüntü --';
$lang['ionize_select_everyone'] =				'-- Herkes --';

$lang['ionize_message_page_name_exists'] =		'Bu sayfa zaten var!';
$lang['ionize_message_page_url_exists'] = 'Aynı URL\'ye sahip bir sayfa zaten var !';
$lang['ionize_message_page_saved'] =			'Sayfa kaydedildi';
$lang['ionize_message_page_not_saved'] =		'Sayfa kaydedilemedi';
$lang['ionize_message_page_not_exist'] =		'Sayfa yok';
$lang['ionize_message_page_ordered'] =			'Sayfa Sıralandı';
$lang['ionize_message_page_needs_url_or_title'] = 'Lütfen varsayılan dil için URL yada Başlık giriniz !';
$lang['ionize_message_drop_only_article'] = 'Lütfen sadece bir makale bırakın!';
$lang['ionize_message_articles_ordered'] = 'Makaleler Sıralandı !';

$lang['ionize_help_page_online'] =				'Bu sayfa yayındamı?';
$lang['ionize_help_page_content_online'] = 'Bu sayfa bu dil için yayındamı?';
$lang['ionize_help_page_url'] = 'Sayfaların URL\'leri. Eşsiz olmalıdır';
$lang['ionize_help_page_link'] = 'İç yada Dış HTTP bağlantısı. Yerine varsayılan sayfa bağlantısı';
$lang['ionize_help_pagination'] =				'Eğer (if > 0), Sayfalamayı aktive et.';
$lang['ionize_help_article_list_template'] =	'Eğer sayfada 1 den fala makale varsa, her makale için ayrı görüntüleme kullan';
$lang['ionize_help_appears'] =					'Sayfa navigasyon menüsünde görünsünmü ?';
$lang['ionize_help_page_meta'] =				'Boş olmadığında genel site META larını kullan';
$lang['ionize_help_page_window_title'] =		'Browser pencere başlığı';
$lang['ionize_help_page_nav_title'] = 'Navigasyon Öğesi (Memü) başlığı';
$lang['ionize_help_home_page'] = 'Bu sayfa web sitenizin ana sayfasımı ?';
$lang['ionize_help_add_page_to_menu'] = 'Bu menüye sayfa ekle';
$lang['ionize_help_page_drop_article_here'] = 'Soldaki ağaç menüden makalenin ismini seçerek taşıyın.';
$lang['ionize_label_help_articles_types_and_views'] = 'Tipler & Görünümler Hakkında';
$lang['ionize_label_help_articles_types'] = 'Tipler Hakkında';
$lang['ionize_label_help_articles_reorder'] = 'Makaleleri tarihe göre sırala. Tarih hesaplaması : Makul yada Yayınlanma veya Oluşturulma.';
$lang['ionize_help_sitemap_priority'] = 'Sayfa Önceliği, 0 ile 10 arasında olmalıdır';

/*
|--------------------------------------------------------------------------
| Admin : Medya
|--------------------------------------------------------------------------
*/
$lang['ionize_title_medias'] =				'Medyalar';
$lang['ionize_title_thumbs_status'] =		'Küçük Resim Durumu';
$lang['ionize_title_informations'] =		'Bilgiler';

$lang['ionize_label_file_size'] =			'Dosya Boyutu';
$lang['ionize_label_reload_picture_list'] =	'Listeyi yeniden yükle';
$lang['ionize_message_no_picture'] =		'Resim yok';
$lang['ionize_message_no_music'] =			'Müzik yok';
$lang['ionize_message_no_video'] =			'Video yok';
$lang['ionize_message_no_file'] =			'Dosya yok';

$lang['ionize_label_init_thumb'] =			'Küçük Resimleri Yenile';
$lang['ionize_label_attach_media'] =		'Medya Ekle';
$lang['ionize_label_detach_media'] =		'Medya bağlantısını kaldır';
$lang['ionize_label_detach_all_pictures'] =	'Tüm Resim bağlantılarını kaldır';
$lang['ionize_label_detach_all_videos'] =	'Tüm Video bağlantılarını kaldır';
$lang['ionize_label_detach_all_musics'] =	'Tüm Müzik bağlantılarını kaldır';
$lang['ionize_label_detach_all_files'] =	'Tüm Dosya bağlantılarını kaldır';
$lang['ionize_label_init_all_thumbs'] =		'Küçük Resimleri Yenile';
$lang['ionize_label_copyright'] =			'Telif Hakkı';
$lang['ionize_label_date'] =				'Tarih';
$lang['ionize_label_alt'] =					'Alternatif Metin';
$lang['ionize_label_link'] =				'Bağlantı';
$lang['ionize_label_description'] =			'Açıklama';
$lang['ionize_label_reload_media_list'] =	'Medya Listesini Tekrar Yükle';
$lang['ionize_label_media_container'] = 'Albüm / Serisi';
$lang['ionize_label_media_crop_picture'] = 'Resmi Kes';

$lang['ionize_message_please_save_first'] =		'Lütfen medya eklemeden önce kaydedin';
$lang['ionize_message_media_not_authorized'] =	'Medya türü desteklenmiyor !';
$lang['ionize_message_media_attached'] =		'Medya Bağlantısı Oluşturuldu';
$lang['ionize_message_media_detached'] =		'Medya Bağlantısı Kaldırıldı';
$lang['ionize_message_no_media_to_detach'] =		'Bağlantısı Kaldırılacak Element Bulunamadı !';
$lang['ionize_message_no_picture'] =			'Resim Bağlantısı Yok';
$lang['ionize_message_no_video'] =				'Video Bağlantısı Yok';
$lang['ionize_message_no_music'] =				'Müzik Bağlantısı Yok';
$lang['ionize_message_no_file'] =				'Dosya Bağlantısı Yok';
$lang['ionize_message_media_not_detached'] =		'Medya Bağlantısı Kaldırılmadı';
$lang['ionize_message_media_already_attached'] =	'Media Bağlantısı Eklendi';
$lang['ionize_message_media_data_saved'] =			'Medya Verisi Kaydedildi';
$lang['ionize_message_media_data_not_saved'] =		'Medya Verisi Kaydedilemedi';
$lang['ionize_message_thumb_initialized'] =			'Küçük Resim Tekrar Oluşturuldu';

$lang['ionize_message_media_reordered'] =			'Medyalar Sıralandı';
$lang['ionize_message_alt_desc_for_mp3'] = 'Bu medya bir MP3 olduğundan, aşağıdaki tanımı ve Alternatif metin MP3 dosyasının ID3 etiketleri üretilir.';

// Exceptions
$lang['ionize_exception_folder_creation'] =		'Klasör oluşturma sırasında hata oluştu';
$lang['ionize_exception_no_thumbs_settings'] =	'Ayarlar : Küçük resim bulunamadı!';
$lang['ionize_exception_getimagesize'] =		'PHP : getimagesize fonksiyonu mevcut değil!';
$lang['ionize_exception_getimagesize_get'] =	'Görüntü boyutunu elde etmek mümkün değil';
$lang['ionize_exception_chmod'] =				'PHP : CHMOD imkansız';
$lang['ionize_exception_unlink'] =				'PHP : dosya silmek inkansız';
$lang['ionize_exception_image_resize'] =		'Resim Küt. : Resim boyutlandırma imkansız';
$lang['ionize_exception_image_crop'] =			'Resim Küt. : Resim kırpma imkansız';
$lang['ionize_exception_copy'] =				'PHP : Resmi kopyalamak mümkün değil';
$lang['ionize_exception_no_source_file'] =		'Kaynak dosya bulunamadıs';
$lang['ionize_exception_memory_limit'] =		'PHP Hafıza limiti aşıldı';
$lang['ionize_exception_image_lib'] =			'Resim Kütüphanesi Hatası';



/*
|--------------------------------------------------------------------------
| Admin : Article
|--------------------------------------------------------------------------
*/
$lang['ionize_title_articles'] =			'Makaleler';
$lang['ionize_select_no_parent'] = '-- Yok --';
$lang['ionize_select_no_category'] =		'-- Yok --';

$lang['ionize_title_create_article'] =		'Yeni Makale';
$lang['ionize_title_new_article'] =		'Yeni Makale';
$lang['ionize_title_edit_article'] = 'Makale Düzenle';
$lang['ionize_title_comments'] =			'Yorumlar';
$lang['ionize_title_duplicate_article'] = 'Makaleyi kopyala';
$lang['ionize_title_duplicate_source_context'] = 'Makale Kaynağı';
$lang['ionize_title_duplicate_destination'] = 'Hedef';
$lang['ionize_title_article_context'] = 'Makale bağlamı';
$lang['ionize_title_content'] = 'İçerik';

$lang['ionize_label_article_in'] = 'içinde';
$lang['ionize_label_article_online'] = 'Makale yayında';
$lang['ionize_label_article_content_online'] = 'İçerik yayında';
$lang['ionize_label_parents'] = 'Ana / Kaynaklar';
$lang['ionize_label_indexed'] =				'Listelenen';
$lang['ionize_label_categories'] =			'Kategoriler';
$lang['ionize_label_edit_categories'] =		'Kategorileri Düzenle';
$lang['ionize_label_new_category'] =		'Kategori Oluştur';
$lang['ionize_label_comment_allow'] =		'İzin Ver';
$lang['ionize_label_comment_autovalid'] =	'Otomatik Doğrulama';
$lang['ionize_label_comment_expire'] =		'Tarih Limiti';
$lang['ionize_label_tags'] =				'Anahtar Kelimeler';
$lang['ionize_label_existing_tags'] =		'Var Olan Anahtar Kelimeler';
$lang['ionize_label_ordering_first'] =		'ilk';
$lang['ionize_label_ordering_last'] =		'son';
$lang['ionize_label_ordering_after'] =		'sonra...';
$lang['ionize_label_content_for_lang'] = 'İçerik';
$lang['ionize_label_online_for_lang'] = 'Yayınla';
$lang['ionize_label_actions'] = 'Hareketler';
$lang['ionize_label_pages'] = 'Sayfalar';
$lang['ionize_label_drag_to_page'] = 'Bir sayfaya sürükleyin';
$lang['ionize_label_article_edit_context'] = 'Ayarlar';
$lang['ionize_label_article_filter'] = 'Filtrele';
$lang['ionize_label_article_context_edition'] = 'Düzenleme Kapsamında';

$lang['ionize_message_article_not_saved'] =		'Makale Kaydedilemedi';
$lang['ionize_message_article_saved'] =			'Makale Kaydedildi';
$lang['ionize_message_article_name_exists'] =	'Aynı isimde bir makale zaten mevcut!';
$lang['ionize_message_article_url_exists'] = 'Aynı URL\'ye sahip bir makale var!';
$lang['ionize_message_article_ordered'] =		'Makaleler sıralandı';
$lang['ionize_message_article_duplicate_no_name_change'] = 'Kopyalanan makale başka bir isimde olmak zorunda!';
$lang['ionize_message_article_duplicated'] = 'Makale kopyalandı';
$lang['ionize_message_article_not_duplicated'] = 'Makale kopyalanamadı';
$lang['ionize_message_article_needs_url_or_title'] = 'Varsayılan dil için lütfen başlık yada URL kısmını doldurun !';
$lang['ionize_message_drop_only_page'] = 'Lütfen sadece bir sayfa taşıyın!';
$lang['ionize_message_parent_page_unlinked'] = 'Sayfa ve Makalenin ağlantısı başarıyla kaldırıldı';
$lang['ionize_confirm_article_page_unlink'] = 'Makaleden sayfa bağlantısını kaldır?';
$lang['ionize_message_article_already_linked_to_page'] = 'Sayfaya makale bağlantısı zaten oluşturulmuş';
$lang['ionize_message_article_linked_to_page'] = 'Sayfaya makale bağlantısı yapıldı';
$lang['ionize_message_article_context_saved'] = 'Makale ayarları kaydedildi';
$lang['ionize_message_article_lang_copied'] = 'Dil verileri kopyalandı. Lütfen Kaydedin !';
$lang['ionize_message_article_main_parent_saved'] = 'Ana Alt Sayfa kaydedildi !';

// Inline help
$lang['ionize_help_article_online'] = 'Bu makale yayındamı?';
$lang['ionize_help_article_content_online'] = 'Bu içerik yayındamı?';
$lang['ionize_help_indexed'] =				'Eğer İşaretliyse, makale içerisindeki yazı FULLTEXT olarak indexlenecek (İç Aramalar İçin Hazır)';
$lang['ionize_help_article_link'] =			'HTTP bağlantısı. Bağlantı tagı ile kullanılabilir';
$lang['ionize_help_articles_types'] =			'Makale Tipi. Makaleleri ayırmak için faydalı bir sayfa görünümü';
$lang['ionize_help_article_window_title'] =		'Tarayıcı Penceresi Başlığı';
$lang['ionize_help_article_context'] = 'Makele sayfasının kaynak verisini düzenle';
$lang['ionize_help_missing_translated_content'] = 'Bazı içerik dil çevirileri eksik';
$lang['ionize_help_orphan_article'] = 'Yetim Makale. Hiçbir sayfaya bağlantısı oluşturulmadı.';
$lang['ionize_help_article_filter'] = 'Makaleleri Filtrele (tam olarak desteklenmiyor)';

$lang['ionize_button_duplicate_article'] = 'Makaleyi kopyala';


/*
|--------------------------------------------------------------------------
| Admin : Kategoriler, Tipler & Genişletilmiş Alanlar
|--------------------------------------------------------------------------
*/
$lang['ionize_title_categories'] =			'Kategoriler';
$lang['ionize_title_category_new'] =		'Yeni Kategori';
$lang['ionize_title_category_edit'] =		'Kategori : Düzenle';
$lang['ionize_title_category_exist'] =		'Var Olan Kategoriler';

$lang['ionize_title_types'] =				'Tipler';
$lang['ionize_title_types_exist'] = 'Varolan tipler';
$lang['ionize_title_type_new'] =	'Yeni Tip';
$lang['ionize_title_type_edit'] =	'Makele Tipi Düzenle';
$lang['ionize_title_extend_table_field'] = 'Tablo alanı';

$lang['ionize_label_categories'] =			'Kategoriler';
$lang['ionize_label_category'] =			'Kategori';
$lang['ionize_label_edit_categories'] =		'Kategorileri Düzenle';
$lang['ionize_label_edit_category'] =		'Kategori Düzenle';
$lang['ionize_label_new_category'] =		'Kategori Oluştur';

$lang['ionize_label_type'] =				'Tip';
$lang['ionize_label_label'] =				'Etiket';
$lang['ionize_label_edit_types'] =			'Tipleri Düzenle';
$lang['ionize_label_new_type'] =			'Yeni Tip';
$lang['ionize_label_values'] =				'Değerler';	
$lang['ionize_label_default_value'] =		'Varsayılan Değerler';
$lang['ionize_label_field_length'] = 'Uzunluk';
$lang['ionize_label_field_auto_increment'] = 'Otomatik Artış';
$lang['ionize_label_field_null'] = 'BOŞ ?';
$lang['ionize_label_field_unsigned'] = 'İmzasız';

$lang['ionize_message_category_name_exists'] =		'Aynı isimde başka bir kategori mevcut!';
$lang['ionize_message_category_saved'] =			'Kategori Kaydedildi';
$lang['ionize_message_category_not_saved'] =		'Kategori Kaydedilemedi';
$lang['ionize_message_category_deleted'] =			'Kategori Silindi';
$lang['ionize_message_category_not_deleted'] =		'Kategori Silinemedi';
$lang['ionize_message_category_name_exists'] =		'Bu Kategori Mevcut!';

$lang['ionize_message_article_type_saved'] =			'Makale Tipi Kaydedildi';
$lang['ionize_message_article_type_not_saved'] =		'Makale Tipi Kaydedilemedi';
$lang['ionize_message_article_type_deleted'] =			'Tip Silindi';
$lang['ionize_message_article_type_not_deleted'] =		'Tip Silinemedi';
$lang['ionize_message_type_exists'] =					'Makele Tipi Zaten Var';

$lang['ionize_label_type_text'] =				'Text input';
$lang['ionize_label_type_textarea'] =			'Textarea';
$lang['ionize_label_type_editor'] =				'Textarea + Editor';
$lang['ionize_label_type_checkbox'] =			'Checkbox';
$lang['ionize_label_type_radio'] =				'Radio';
$lang['ionize_label_type_select'] =				'Select';
$lang['ionize_label_type_datetime'] = 'Tarih & Zaman';

$lang['ionize_label_add_field'] = 'Alan ekle';
$lang['ionize_label_extend_field_for_all'] = 'Genel';
$lang['ionize_label_extend_field_for_pages'] = 'Sayfalara';
$lang['ionize_label_extend_field_for_articles'] = 'Makalelere';
$lang['ionize_label_extend_field_for_medias'] = 'Medyalara';
$lang['ionize_label_extend_field_parent'] = 'Alt Öğe';
$lang['ionize_label_extend_field_global'] = 'Global';
$lang['ionize_title_extend_field_new'] = 'Yeni genişletilmiş alan';
$lang['ionize_title_extend_fields'] =					'Genişletilmiş Alanlar';
$lang['ionize_title_extend_field'] =					'Genişletilmiş Alan';

$lang['ionize_label_extend_fields_activate'] =			'Aktive Et';
$lang['ionize_label_extend_field_type'] =				'Tip';
$lang['ionize_label_extend_field_translated'] =			'Tercüme Edilebilir';
$lang['ionize_label_extend_field_context'] =			'Bağlam';

$lang['ionize_message_extend_field_name_exists'] =		'Aynı isimde zaten bir genişletilmiş alan mevcut';
$lang['ionize_message_extend_field_saved'] =			'Genişletilmiş Alan Kaydedildi';
$lang['ionize_message_extend_field_not_saved'] =		'Hata : Lütfen Alan Adını Girin!';
$lang['ionize_message_extend_field_deleted'] =			'Genişletilmiş Alan Silindi';
$lang['ionize_message_extend_field_not_deleted'] =		'Genişletilmiş Alan Silinemedi';
$lang['ionize_message_field_must_have_a_name'] = 'Alanın bir adı olmalı';
$lang['ionize_message_varchar_int_must_have_length'] = 'VARCHAR yada INT alanının uzunluğu olmak zorunda';
$lang['ionize_message_field_name_sql_reserved'] = 'Seçmiş olduğunuz alan ismi SQL ayrılmış bir kelimedir. Lütfen değiştirin.';

$lang['ionize_help_ef_parent'] = 'Bu genişletilmiş alanı alt öğe gibi limitleyin yada limitlemeyin';
$lang['ionize_help_ef_global'] = 'Genişletilmiş alan her bir element için görünebilirmi ?';
$lang['ionize_help_ef_name'] = 'Anahtarı alan içerisinde kullanma etiketi. Örnek : &#8249;ion:field name=&#34;field-name&#34; /&#8250;';
$lang['ionize_help_ef_values'] =				'anahtar:değer, satır sonu ile ayrılmış';
$lang['ionize_help_ef_default_value'] =			'Eğer select, radio veya checkbox sa, anahtar koyun';
$lang['ionize_help_ef_description'] = 'Bu alan için kullanıcı için gösterilecek yardım, bu baloncuz gibi';
$lang['ionize_help_label_label'] = 'Ionizede alan için gösterilecek etiket';
$lang['ionize_help_field_length'] = 'Alan uzunluğu';



/*
|--------------------------------------------------------------------------
| Admin : Translations
|--------------------------------------------------------------------------
*/
$lang['ionize_message_language_files_saved'] =			'Çeviriler Kaydedildi';
$lang['ionize_message_language_dir_creation_fail'] =	'Klasör oluşturmak imkansız';
$lang['ionize_message_language_file_creation_fail'] =	'Dosyaya Yazmak imkansız';
$lang['ionize_label_expand_all'] =						'Tümünü Genişlet';
$lang['ionize_label_collapse_all'] =					'Tümünü Daralt';
$lang['ionize_label_add_translation'] = 'Çevrilmiş öğe ekle';
$lang['ionize_message_delete_translation'] = 'Bu çeviri öğesi silinsin mi?';


/*
|--------------------------------------------------------------------------
| Admin : Media Manager
|--------------------------------------------------------------------------
*/
$lang['ionize_image_manager'] =	'Resimler';
$lang['ionize_file_manager'] =	'Dosyalar';


/*
|--------------------------------------------------------------------------
| Admin : Modules
|--------------------------------------------------------------------------
*/
$lang['ionize_title_modules_list'] =		'Modül Listesi';
$lang['ionize_label_module_name'] =			'Adı';
$lang['ionize_label_module_uri'] =			'URI';
$lang['ionize_label_module_install'] =		'Yükle';
$lang['ionize_label_module_uninstall'] =	'Kaldır';
$lang['ionize_label_database_tables'] = 'Tablolar';

$lang['ionize_message_module_install_error_no_config'] =		'Hata : Module without config.xml';
$lang['ionize_message_module_install_error_config_write'] =		'Yazma Hatası : config/modules.php';
$lang['ionize_message_module_page_conflict'] =					'Hata : Var Olan Bir URI';
$lang['ionize_message_module_install_database_error'] =			'Hata : Mödül Tablolarını Yüklemek İmkansız';
$lang['ionize_message_module_saved'] =							'Modül Yüklendi';
$lang['ionize_message_module_uninstalled'] =					'Modül Kaldırıldı';
$lang['ionize_message_module_not_installed'] =					'Modül Yüklenemedi !';


/*
|--------------------------------------------------------------------------
| Admin : System Check
|--------------------------------------------------------------------------
*/
$lang['ionize_title_system_check'] = 'Sistem Denetim Aracı';
$lang['ionize_menu_sitemap'] = 'Site Haritası';
$lang['ionize_menu_system_check'] = 'Sistemi Kontrol Et';
$lang['ionize_text_system_check'] = 'Bu araç Ionize bütünlüğünü ve onarımını kontrol etmek için kullanılır. Bu özelliği kullanarak hiçbirşeyi bozamazsınız.';

$lang['ionize_label_start_system_check'] = 'Kontrolü Başlat';
$lang['ionize_button_start_system_check'] = 'Kontrolü Başlat';
$lang['ionize_title_check_lang'] = 'Dilleri Kontrol Et';
$lang['ionize_title_check_page_level'] = 'Sayfa düzeylerini kontrol et';
$lang['ionize_title_check_article_context'] = 'Makale bağlamlarını kontrol et';

$lang['ionize_message_check_corrected'] = ' düzeltildi.';
$lang['ionize_message_check_ok'] = 'Herşey normal.';