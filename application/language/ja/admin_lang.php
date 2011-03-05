<?php

/*
|--------------------------------------------------------------------------
| Ionize Language file
| Language : Japanese
| Translater : Senri, 5th Oct 2010
|
| Usage :
| Form labels :				ionize_label_*
| Form buttons :			ionize_button_*
| Menu items :				ionize_menu_*
| Page titles, titles :		ionize_title_*
| Messages :				ionize_message_*
|							ionize_*_message_*
| Notification :			ionize_notify_*
|							ionize_*_notify_*
| Help (inline) :			ionize_help_*
|
| Notes : 	Modules translation items should begin with the prefix 'module_name'
|			Example :
|			$lang['module_fancyupload_label_folder'] =			'Destination folder';
|
| Label documentation :	Each label should have a "title" attribute set
| 						The title attribute value helps the user to understand what use he can made from a field
|						Example : 
|						Label : $lang['ionize_label_appears'] = 'Appears in nav';
|						Label title : $lang['ionize_help_appears'] = 'The item will be visible in the navigation';
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Admin login panel
|--------------------------------------------------------------------------
*/
$lang['ionize_home'] = 'ホーム';
$lang['ionize_login'] = 'ログイン';
$lang['ionize_logout'] = 'ログアウト';
$lang['ionize_goback'] = 'サイトへ戻る';
$lang['ionize_website'] = 'サイトの確認';
$lang['ionize_logged_as'] = 'ログイン中';
$lang['ionize_login_name'] = 'ユーザ名';
$lang['ionize_login_password'] = 'パスワード';
$lang['ionize_login_remember'] = 'リマインダー';
$lang['ionize_login'] = 'ログイン';
$lang['ionize_forgot_password'] = 'パスワードを忘れましたか?';


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
$lang['ionize_dashboard_icon_add_page'] = '新規ページ';
$lang['ionize_dashboard_icon_mediamanager'] = 'メディア';
$lang['ionize_dashboard_icon_translation'] = '翻訳';
$lang['ionize_dashboard_icon_google_analytics'] = '分析';

$lang['ionize_dashboard_title_content'] = 'コンテンツ';
$lang['ionize_dashboard_title_tools'] =	'ツール';
$lang['ionize_dashboard_title_settings'] = '設定';

$lang['ionize_dashboard_title_last_modified_articles'] = '最近更新された記事';
$lang['ionize_dashboard_title_last_connected_users'] =	'最終接続ユーザ';
$lang['ionize_dashboard_title_orphan_pages'] = 'リンクのないページ';
$lang['ionize_dashboard_title_orphan_articles'] = 'リンクのない記事';


/*
|--------------------------------------------------------------------------
| Structure
|--------------------------------------------------------------------------
*/
$lang['ionize_structure_main_menu'] = 'メインメニュー';
$lang['ionize_structure_system_menu'] = 'システム機能';


/*
|--------------------------------------------------------------------------
| Admin navigation menu
|--------------------------------------------------------------------------
*/
$lang['ionize_menu_content'] = 'コンテンツ';
$lang['ionize_menu_articles'] = 'Articles';
$lang['ionize_menu_translation'] = '翻訳';
$lang['ionize_menu_modules'] = 'モジュール';
$lang['ionize_menu_tools'] = 'ツール';
$lang['ionize_menu_settings'] = '設定';
$lang['ionize_menu_help'] = 'ヘルプ';

$lang['ionize_menu_menu'] = 'メニュー管理...';
$lang['ionize_menu_page'] = 'ページ作成...';
$lang['ionize_menu_article'] = '記事作成...';
$lang['ionize_menu_media_manager'] = 'メディア管理';

$lang['ionize_menu_modules_admin'] = 'システム管理...';

$lang['ionize_menu_site_settings'] = 'サイト設定...';
$lang['ionize_menu_global'] = '全体設定...';
$lang['ionize_menu_theme'] = 'テーマ...';
$lang['ionize_menu_technical_settings'] = '高度な設定...';
$lang['ionize_menu_translations'] = '固定部分の翻訳';

$lang['ionize_menu_site_settings_global'] = 'サイト設定';
$lang['ionize_menu_site_settings_translations'] = '固定部分の翻訳';
$lang['ionize_menu_site_settings_technical'] = '高度な設定';

$lang['ionize_menu_users'] = 'ユーザ...';
$lang['ionize_menu_languages'] = '言語...';

$lang['ionize_menu_about'] = '概要';
$lang['ionize_menu_documentation'] = 'ドキュメント';
$lang['ionize_menu_extend_fields'] = '拡張フィールド';


/*
|--------------------------------------------------------------------------
| Global titles
|--------------------------------------------------------------------------
*/
$lang['ionize_title_welcome'] = 'Ionizeへようこそ';
$lang['ionize_title_structure'] = '構成';
$lang['ionize_title_options'] =	'オプション';
$lang['ionize_title_advanced'] = '高度なオプション';
$lang['ionize_title_dates'] = '日付';
$lang['ionize_title_informations'] = 'お知らせ';
$lang['ionize_title_authorization'] = 'アクセス権限';
$lang['ionize_title_metas'] = 'メタデータ';
$lang['ionize_title_modules'] = 'モジュール管理';
$lang['ionize_title_menu'] = 'メニュー管理';

$lang['title_delete_installer'] = 'installフォルダの削除';
$lang['title_ionize_installation'] = 'Ionizeインストール';


/*
|--------------------------------------------------------------------------
| Modal windows
|--------------------------------------------------------------------------
*/
$lang['ionize_modal_confirmation_title'] = '確認しましたか ?';


/*
|--------------------------------------------------------------------------
| Menus 
|--------------------------------------------------------------------------
*/
$lang['ionize_title_add_menu'] = 'メニュー追加';
$lang['ionize_button_save_new_menu'] = 'メニュー保存';

$lang['ionize_title_existing_menu'] = '既存のメニュー';
$lang['ionize_message_menu_saved'] = 'メニューは保存されました';
$lang['ionize_message_menu_not_saved'] = 'メニューは保存されませんでした';
$lang['ionize_message_menu_already_exists'] = 'このメニューは既に存在します';
$lang['ionize_message_menu_updated'] = 'メニューを更新しました';
$lang['ionize_message_menu_ordered'] = 'メニューを移動しました';
$lang['ionize_message_menu_deleted'] = 'メニューは削除されました';
$lang['ionize_message_menu_not_deleted'] = 'メニューは削除されませんでした';


/*
|--------------------------------------------------------------------------
| Global forms labels & help
| Labels are also commonly used for table head column titles
|--------------------------------------------------------------------------
*/

$lang['ionize_label_online'] = 'オンライン';
$lang['ionize_label_offline'] = 'オフライン';
$lang['ionize_label_set_offline'] = 'オフラインへ';
$lang['ionize_label_set_online'] = 'オンラインへ';
$lang['ionize_label_edit'] = '編集';
$lang['ionize_label_delete'] = '削除';
$lang['ionize_label_status'] = '状態';
$lang['ionize_label_max_upload_size'] =	'アップロード最大容量';
$lang['ionize_label_file_uploads'] = 'アップロードしますか ?';
$lang['ionize_label_site_email'] = 'メールサイト';
$lang['ionize_label_linkto'] = 'リンク先...';
$lang['ionize_label_remove_link'] = 'リンクの削除';
$lang['ionize_label_url'] = 'URL';
$lang['ionize_label_see_online'] = 'オンラインの確認';

$lang['ionize_label_page'] = 'ページ';
$lang['ionize_label_article'] = '記事';

$lang['ionize_label_name'] = '名称';
$lang['ionize_label_id'] = 'ID';
$lang['ionize_label_parent'] = '親';
$lang['ionize_label_permanent_url'] = '固定URL';
$lang['ionize_label_template'] = '参照';
$lang['ionize_label_article_template'] = '記事参照';

$lang['ionize_label_title'] = 'タイトル';
$lang['ionize_label_subtitle'] = 'サブタイトル';
$lang['ionize_label_meta_title'] = 'ウィンドウタイトル';
$lang['ionize_label_text'] = 'テキスト';
$lang['ionize_label_content'] = 'コンテンツ';
$lang['ionize_label_category'] = '分類';
$lang['ionize_label_ordering'] = '並び替え';
$lang['ionize_label_pictures'] = '画像';
$lang['ionize_label_music'] = '音楽';
$lang['ionize_label_videos'] = 'ビデオ';
$lang['ionize_label_files'] = 'ファイル';
$lang['ionize_label_default'] = 'デフォルト';
$lang['ionize_label_code'] = 'コード';
$lang['ionize_label_toggle_editor'] = '表示 / 隠す HTML';

$lang['ionize_label_in_menu'] = 'メニューへの表示';
$lang['ionize_label_parent'] = '親';
$lang['ionize_label_meta_keywords'] = 'キーワード';
$lang['ionize_label_meta_description']= '備考';
$lang['ionize_label_created'] = '作成日';
$lang['ionize_label_updated'] = '更新日';
$lang['ionize_label_publish_on'] = '発行';
$lang['ionize_label_publish_off'] = '発行取りやめ';
$lang['ionize_label_permanent_url'] = '固定URL';
$lang['ionize_label_add_media'] = 'メディアの追加';
$lang['ionize_label_author'] = '著者';
$lang['ionize_label_updater'] = '更新者';

$lang['ionize_label_groups'] = 'グループ';

$lang['ionize_label_installed'] = 'インストール完了';
$lang['ionize_label_hide_options'] = 'オプションを隠す';
$lang['ionize_label_show_options'] = 'オプションを表示';

$lang['ionize_help_status'] = '管理者でログインしているとき、オンラインでないサイトの要素も見ることができます。';
$lang['ionize_help_publish_on'] = '指定の日付にアイテム発行およびアイテムの表示日付を置き換えます。';
$lang['ionize_help_publish_off'] = '指定の日付にアイテム発行を取りやめます。';
$lang['ionize_help_online_lang'] = 'この言語でアイテムをオンラインにしますか';
$lang['ionize_help_url'] = '要素URL';

/*
|--------------------------------------------------------------------------
| Global forms buttons
|--------------------------------------------------------------------------
*/
$lang['ionize_button_save'] = '保存';
$lang['ionize_button_save_close'] = '保存して閉じる';
$lang['ionize_button_send'] = '送信';
$lang['ionize_button_next'] = '次へ';
$lang['ionize_button_delete'] = '削除';
$lang['ionize_button_new'] = '新規';
$lang['ionize_button_close'] = '閉じる';
$lang['ionize_button_yes'] = 'はい';
$lang['ionize_button_no'] = 'いいえ';
$lang['ionize_button_confirm'] = '確認';
$lang['ionize_button_cancel'] = '中止';
$lang['ionize_button_add_page'] = 'ページの追加';
$lang['ionize_button_switch_online'] = 'オンライン/オフライン';

$lang['ionize_button_save_new_lang'] = 'この言語を追加';
$lang['ionize_button_save_page'] = 'ページ保存';
$lang['ionize_button_save_article'] = '記事保存';
$lang['ionize_button_save_module_settings'] = '設定保存';
$lang['ionize_button_save_views'] = 'ビューの保存';
$lang['ionize_button_save_themes'] = 'このテーマを使う';
$lang['ionize_button_save_settings'] = '設定保存';


/*
|--------------------------------------------------------------------------
| Global Messages
|--------------------------------------------------------------------------
*/

$lang['ionize_confirm_element_delete'] = 'この要素を本当に削除しますか';
$lang['ionize_message_missing_params'] = '設定に誤りがあります';
$lang['ionize_message_operation_ok'] = '操作完了';
$lang['ionize_message_operation_nok'] = '操作失敗';
$lang['ionize_message_delete_installer'] = '重要 : <br/>セキュリティ上、<b>"/install"</b>を直ちに削除してください。Ionize はこのフォルダが削除されるまで実行できません';
$lang['ionize_message_no_circular_link'] = '循環リンクは許可されません';
$lang['button_delete_installer_done_admin'] = '完了! 管理画面へ';
$lang['button_delete_installer_done_site'] = '完了! Webサイトへ';

/*
|--------------------------------------------------------------------------
| Admin : Language
|--------------------------------------------------------------------------
*/
$lang['ionize_title_language'] = '言語管理';
$lang['ionize_title_existing_languages'] = '既存の言語';
$lang['ionize_title_add_language'] = '言語の追加';
$lang['ionize_title_advanced_language'] = '高度な機能';
$lang['ionize_message_no_languages'] = '<strong>言語が存在しません</strong>. <br/> 一つの言語を作成してください';
$lang['ionize_message_lang_saved'] = '言語は保存されました';
$lang['ionize_message_lang_not_saved'] = '言語は保存されませんでした';
$lang['ionize_message_lang_file_not_saved'] = 'エラー : application/config/language.phpファイルを書き込めません';
$lang['ionize_message_lang_code_already_exists'] = 'この言語コードは既に存在しています';
$lang['ionize_message_lang_not_deleted'] = '言語は削除できませんでした';
$lang['ionize_message_lang_deleted'] = '言語を削除しました';
$lang['ionize_message_lang_ordered'] = '言語を並べ替えました';
$lang['ionize_message_lang_not_ordered'] = '言語は並べ替えできませんでした';
$lang['ionize_message_lang_updated'] = '言語を更新しました';
$lang['ionize_notify_advanced_language'] = 'この機能についてご存知の場合のみ、機能を利用してください';
$lang['ionize_button_clean_lang_tables'] = '言語テーブルのクリーンアップ';
$lang['ionize_help_clean_lang_tables'] = '存在しない言語を取り除いてlangテーブルをクリーンにします。';
$lang['ionize_confirmation_clean_lang'] = '定義されていないすべての言語をlangテーブルから削除します';
$lang['ionize_message_lang_tables_cleaned'] = 'コンテンツテーブルをクリーンアップしました';


/*
|--------------------------------------------------------------------------
| Admin : Users & groups
|--------------------------------------------------------------------------
*/
$lang['ionize_title_users'] = 'ユーザ管理';
$lang['ionize_title_user_edit'] = 'ユーザ編集';
$lang['ionize_title_existing_users'] = '既存のユーザ';
$lang['ionize_title_existing_groups'] = '既存のグループ';
$lang['ionize_title_group_edit'] = 'グループ編集';
$lang['ionize_title_add_user'] = 'ユーザ追加';
$lang['ionize_title_add_group'] = 'グループ追加';
$lang['ionize_title_change_password'] = 'パスワードの変更';
$lang['ionize_title_users_export'] = 'ユーザ出力';
$lang['ionize_title_user_meta'] = 'ユーザメタデータ';

$lang['ionize_label_username'] = 'ID (ログイン名)';
$lang['ionize_label_screen_name'] = '正式名';
$lang['ionize_label_email'] = 'メール';
$lang['ionize_label_group'] = 'グループ';
$lang['ionize_label_password'] = 'パスワード';
$lang['ionize_label_password2'] = '確認';
$lang['ionize_label_group_name'] = '名前';
$lang['ionize_label_group_title'] = '役割';
$lang['ionize_label_group_level'] = 'レベル';
$lang['ionize_label_group_description'] = '備考';
$lang['ionize_label_export_meta'] = '出力へ';
$lang['ionize_label_export_format'] = 'フォーマット';
$lang['ionize_label_last_visit'] = '最終訪問日';

$lang['ionize_message_user_updated'] = '更新しました';
$lang['ionize_message_user_not_saved'] = '更新出来ませんでした';
$lang['ionize_message_user_saved'] = '保存しました';
$lang['ionize_message_user_exists'] = '既にデータベースに存在しています!';
$lang['ionize_message_user_deleted'] = '削除しました';
$lang['ionize_message_user_cannot_delete_yourself'] = '自分自身は削除できません';
$lang['ionize_message_group_updated'] = '更新しました';
$lang['ionize_message_group_not_saved'] = '更新できませんでした';
$lang['ionize_message_group_saved'] = '保存しました';
$lang['ionize_message_group_deleted'] = '削除しました';
$lang['ionize_message_users_exported'] = '出力しました';
$lang['ionize_message_users_not_exported'] = '出力できませんでした';

$lang['ionize_button_export'] = '出力';

/*
|--------------------------------------------------------------------------
| Admin : Settings
|--------------------------------------------------------------------------
*/

$lang['ionize_label_site_title'] = 'サイト名';
$lang['ionize_message_settings_saved'] = '設定を保存しました';
$lang['ionize_title_admin_ui_options'] = '管理画面';
$lang['ionize_label_show_help_tips'] = 'フィールド上にヘルプを表示';

/*
|--------------------------------------------------------------------------
| Admin : Technical Settings
|--------------------------------------------------------------------------
*/
$lang['ionize_title_themes'] = 'テーマ';
$lang['ionize_title_theme'] = 'テーマ';
$lang['ionize_title_translation'] = '静的な翻訳';
$lang['ionize_title_database'] = 'データベース';
$lang['ionize_title_mail_send'] = 'メール設定';
$lang['ionize_title_media_management'] = 'メディア管理';
$lang['ionize_title_google_analytics'] = '統計';
$lang['ionize_title_thumb_new'] = '新規写真サムネイル';
$lang['ionize_title_thumbs'] = 'サムネイル';
$lang['ionize_title_thumbs_system'] = 'Ionizeシステムサムネイル';

$lang['ionize_label_files_path'] = 'メディアの基本フォルダ';
$lang['ionize_label_media_type_picture'] = 'イメージ拡張子';
$lang['ionize_label_media_type_video'] = 'ビデオ拡張子';
$lang['ionize_label_media_type_music'] = '音楽拡張子';
$lang['ionize_label_media_type_file'] = 'ファイル拡張子';
$lang['ionize_label_filemanager'] = 'ファイル管理';
$lang['ionize_label_theme'] = 'テーマ';
$lang['ionize_label_theme_admin'] = '管理者テーマ';
$lang['ionize_label_db_driver'] = 'ドライバー';
$lang['ionize_label_db_host'] = 'ホスト';
$lang['ionize_label_db_name'] = 'データベース名';
$lang['ionize_label_db_user'] = 'ユーザ';
$lang['ionize_label_db_pass'] = 'パスワード';
$lang['ionize_label_google_analytics'] = 'Google Analytics';

$lang['ionize_label_smtp_protocol'] = 'プロトコル';
$lang['ionize_label_smtp_host'] = 'ホスト';
$lang['ionize_label_smtp_user'] = 'ユーザ';
$lang['ionize_label_smtp_pass'] = 'パスワード';
$lang['ionize_label_smtp_port'] = 'ポート';
$lang['ionize_label_email_charset'] = 'キャラクタセット';
$lang['ionize_label_email_mailtype'] = 'フォーマット';
$lang['ionize_label_mailpath'] = 'メインパス';

$lang['ionize_label_thumb_dir'] = 'フォルダ';
$lang['ionize_label_thumb_size'] = 'サイズ';
$lang['ionize_label_thumb_sizeref'] = '参照';
$lang['ionize_label_thumb_sizeref_width'] = '幅';
$lang['ionize_label_thumb_sizeref_height'] = '高さ';
$lang['ionize_label_thumb_square'] = '区画';
$lang['ionize_label_thumb_unsharp'] = 'アンシャープフィルタ';
$lang['ionize_label_thumb_list'] = '画像一覧';
$lang['ionize_label_thumb_edition'] = '画像編集';
$lang['ionize_label_thumbs_system'] = '画像 一覧 / 編集';

$lang['ionize_title_db_version'] = 'データベース';
$lang['ionize_title_php_version'] = 'PHP';

$lang['ionize_message_database_not_saved'] = 'データベース設定が不正です';
$lang['ionize_message_database_not_exist'] = '存在しないデータベースが選択されました';
$lang['ionize_message_database_connection_error'] = 'データベースへの接続ができません';
$lang['ionize_message_database_saved'] = 'データベース設定の保存が完了しました';

$lang['ionize_message_smtp_not_saved'] = 'メール設定の誤りまたは不正です';
$lang['ionize_message_smtp_saved'] = 'メール設定を保存しました';

$lang['ionize_message_thumb_saved'] = 'サムネイルを保存しました';
$lang['ionize_message_thumb_not_saved'] = 'サムネイルは保存できませんでした';
$lang['ionize_message_thumb_deleted'] = 'サムネイルを削除しました';
$lang['ionize_message_thumb_not_deleted'] = 'サムネイルは削除できませんでした';

$lang['ionize_message_error_writing_file'] = 'ファイル書き込みエラー';
$lang['ionize_message_error_writing_medias_file'] = 'application/config/medias.phpへの書き込みができません';
$lang['ionize_message_error_writing_database_file'] = 'application/config/database.phpへの書き込みができません';
$lang['ionize_message_error_writing_email_file'] = 'application/config/email.phpへの書き込みができません';

$lang['ionize_help_setting_google_analytics'] = '、Google AnalyticsウェブサイトからコピーするなどしたGoogleスクリプトです。';
$lang['ionize_help_setting_files_path'] = '基本メディアフォルダを示します。物理的なフォルダ名は変更しません。';
$lang['ionize_help_setting_system_thumb_list'] = 'イメージリストにあるionizeによるサムネイル表示および写真編集ウィンドウ';
$lang['ionize_help_setting_media_type_picture'] = 'コンマで分離されたファイル拡張子';
$lang['ionize_help_setting_media_type_music'] = 'コンマで分離されたファイル拡張子';
$lang['ionize_help_setting_media_type_video'] = 'コンマで分離されたファイル拡張子';
$lang['ionize_help_setting_media_type_file'] = 'コンマで分離されたファイル拡張子';

/*
|--------------------------------------------------------------------------
| Admin : Themes
|--------------------------------------------------------------------------
*/
$lang['ionize_title_views_list'] = '選択中テーマのビュー一覧';
$lang['ionize_title_view_edit'] = 'エディション';
$lang['ionize_title_views_translations'] = '選択中テーマの静的要素の翻訳';
$lang['ionize_label_view_filename'] = 'ファイル';
$lang['ionize_label_view_folder'] = 'フォルダ';
$lang['ionize_label_view_name'] = '論理名';
$lang['ionize_label_view_type'] = '型';
$lang['ionize_label_current_theme'] = '選択中テーマ';

$lang['ionize_select_no_type'] = '-- 未選択 --';
$lang['ionize_message_views_saved'] = 'ビューの設定を保存しました';

$lang['ionize_message_view_saved'] = 'ビューを保存しました';


/*
|--------------------------------------------------------------------------
| Admin : Page
|--------------------------------------------------------------------------
*/
$lang['ionize_label_articles'] = '記事';
$lang['ionize_label_add_article'] = '記事の追加';
$lang['ionize_label_appears'] = 'メニューへ表示';
$lang['ionize_label_link'] = 'リンク';
$lang['ionize_label_page_extendfields'] = '格調フィールド';
$lang['ionize_label_add_extendfield'] = 'フィールド追加';
$lang['ionize_label_pagination_nb'] = '記事 / ページ';
$lang['ionize_label_article_list_template'] = 'ビュー一覧';
$lang['ionize_label_page_delete_date'] = 'ページ削除日付';
$lang['ionize_label_menu'] = 'メニュー';
$lang['ionize_label_home_page'] = 'ホームページ';

$lang['ionize_select_default_view'] = '-- デフォルトビュー --';
$lang['ionize_select_everyone'] = '-- すべて --';

$lang['ionize_message_page_name_exists'] = 'このページは既に存在します';
$lang['ionize_message_page_url_exists'] = '同じURLのページが既に存在します';
$lang['ionize_message_page_saved'] = 'ページを保存しました';
$lang['ionize_message_page_not_saved'] = 'ページは保存されませんでした';
$lang['ionize_message_page_not_exist'] = 'ページが存在しません';
$lang['ionize_message_page_ordered'] = 'ページを並べ替えました';
$lang['ionize_message_page_needs_url_or_title'] = 'デフォルト言語でタイトルまたはURLを入力してください';

$lang['ionize_help_page_link'] = '内部または外部サイトのリンク。デフォルトページのリンクへ置き換えます';
$lang['ionize_help_pagination'] = '0より大きい場合、記事のページングが有効です';
$lang['ionize_help_article_list_template'] = 'ページに１つ以上の記事があれば、各記事の表示のためにこのビューを使用します';
$lang['ionize_help_appears'] = 'メニューにこのページを表示しますか?';
$lang['ionize_help_page_meta'] = '空でない場合はサイト全体のメタデータに置き換えます';
$lang['ionize_help_page_window_title'] = 'ウィンドウタイトルの参照';
$lang['ionize_help_home_page'] = 'このページをサイトのホームページにしますか?';



/*
|--------------------------------------------------------------------------
| Admin : Media
|--------------------------------------------------------------------------
*/
$lang['ionize_title_medias'] =  'メディア';
$lang['ionize_title_thumbs_status'] = 'サムネイルの状態';
$lang['ionize_title_informations'] = '情報';

$lang['ionize_label_file_size'] = 'ファイルサイズ';
$lang['ionize_label_reload_picture_list'] = 'リロード一覧';
$lang['ionize_message_no_picture'] = '画像なし';
$lang['ionize_message_no_music'] = 'オーディオファイルなし';
$lang['ionize_message_no_video'] = 'ビデオファイルなし';
$lang['ionize_message_no_file'] = 'ファイルなし';

$lang['ionize_label_init_thumb'] = 'サムネイルの初期化';
$lang['ionize_label_attach_media'] = 'メディアの追加';
$lang['ionize_label_detach_media'] = 'メディアのリンク解除';
$lang['ionize_label_detach_all_pictures'] = '全画像のリンク解除';
$lang['ionize_label_detach_all_videos'] = '全ビデオのリンク解除';
$lang['ionize_label_detach_all_musics'] = '全音楽ファイルのリンク解除';
$lang['ionize_label_detach_all_files'] = '全ファイルのリンク解除';
$lang['ionize_label_init_all_thumbs'] = '全サムネイルの初期化';
$lang['ionize_label_copyright'] = '著作権';
$lang['ionize_label_date'] = '日付';
$lang['ionize_label_alt'] = '代替テキスト';
$lang['ionize_label_link'] = 'リンク';
$lang['ionize_label_description'] = '備考';
$lang['ionize_label_reload_media_list'] = 'メディア一覧の再読込み';

$lang['ionize_message_please_save_first'] = 'メディア追加の前に保存を行って下さい';
$lang['ionize_message_media_not_authorized'] = '許可されていないメディアタイプです';
$lang['ionize_message_media_attached'] = 'メディアがリンクされました';
$lang['ionize_message_media_detached'] = 'メディアがリンク解除されました';
$lang['ionize_message_no_media_to_detach'] = 'リンク解除すべき要素がありません';
$lang['ionize_message_no_picture'] = 'リンク済み画像なし';
$lang['ionize_message_no_video'] = 'リンク済みビデオなし';
$lang['ionize_message_no_music'] = 'リンク済み音楽ファイルなし';
$lang['ionize_message_no_file'] = 'リンク済みファイルなし';
$lang['ionize_message_media_not_detached'] = 'メディアはリンク解除されませんでした';
$lang['ionize_message_media_already_attached'] = 'メディアはリンク済みです';
$lang['ionize_message_media_data_saved'] = 'メディアデータを保存しました';
$lang['ionize_message_media_data_not_saved'] = 'メディアデータは保存されませんでした';
$lang['ionize_message_thumb_initialized'] = 'サムネイルを再生成しました';

$lang['ionize_message_media_reordered'] = 'メディアを並べ替えました';

// Exceptions
$lang['ionize_exception_folder_creation'] = 'フォルダ作成中にエラーが発生しました';
$lang['ionize_exception_no_thumbs_settings'] = '設定 : サムネイルの定義がありません';
$lang['ionize_exception_getimagesize'] = 'PHP : getimagesize関数が存在しません';
$lang['ionize_exception_getimagesize_get'] = '画像サイズの取得ができませんでした';
$lang['ionize_exception_chmod'] = 'PHP : CHMODができませんでした';
$lang['ionize_exception_unlink'] = 'PHP : ファイルの削除ができませんでした';
$lang['ionize_exception_image_resize'] = 'イメージライブラリ : 写真のリサイズができませんでした';
$lang['ionize_exception_image_crop'] = 'イメージライブラリ : 写真の切り取りができませんでした';
$lang['ionize_exception_copy'] = 'PHP : 写真のコピーができませんでした';
$lang['ionize_exception_no_source_file'] = '元ファイルがみつかりません';
$lang['ionize_exception_memory_limit'] = 'PHPのメモリ限界に達しました';
$lang['ionize_exception_image_lib'] = 'イメージライブラリのエラーです';


/*
|--------------------------------------------------------------------------
| Admin : Article
|--------------------------------------------------------------------------
*/
$lang['ionize_title_articles'] = '記事';
$lang['ionize_select_no_category'] = '-- なし --';

$lang['ionize_title_create_article'] = '新規記事';
$lang['ionize_title_edit_article'] = '記事の編集';
$lang['ionize_title_comments'] = 'コメント';
$lang['ionize_title_duplicate_article'] = '記事のコピー';

$lang['ionize_label_indexed'] = 'インデックス';
$lang['ionize_label_categories'] = 'カテゴリ';
$lang['ionize_label_edit_categories'] = 'カテゴリの編集';
$lang['ionize_label_new_category'] = 'カテゴリ作成';
$lang['ionize_label_comment_allow'] = '許可';
$lang['ionize_label_comment_autovalid'] = '自動チェック';
$lang['ionize_label_comment_expire'] = '期限日';
$lang['ionize_label_tags'] = 'タグ';
$lang['ionize_label_existing_tags'] = '既存のタグ';
$lang['ionize_label_ordering_first'] = '先頭';
$lang['ionize_label_ordering_last'] = '末尾';
$lang['ionize_label_ordering_after'] = '直後...';

$lang['ionize_message_article_not_saved'] = '記事は保存されませんでした : データの消失';
$lang['ionize_message_article_saved'] = '記事を保存しました';
$lang['ionize_message_article_name_exists'] = '同名の記事が既に存在します';
$lang['ionize_message_article_url_exists'] = '同じURLの記事が既に存在します';
$lang['ionize_message_article_ordered'] = '記事を並べ替えました';
$lang['ionize_message_article_duplicate_no_name_change'] = 'コピーした記事には別名が必要です';
$lang['ionize_message_article_duplicated'] = '記事をコピーしました';
$lang['ionize_message_article_not_duplicated'] = '記事はコピーできませんでした';
$lang['ionize_message_article_needs_url_or_title'] = 'デフォルト言語用のタイトルまたはURLを入力してください';

// Inline help
$lang['ionize_help_indexed'] =  'チェックした場合、記事内容は全文が索引になります（サーチエンジン用)';
$lang['ionize_help_article_link'] = 'HTTPリンク。リンクタグと共に使用できます。';
$lang['ionize_help_articles_types'] = '記事のタイプ。記事参照においての分類に役立ちます';
$lang['ionize_help_article_window_title'] = '参照ウィンドウのタイトル';

$lang['ionize_button_duplicate_article'] = '記事のコピー';

/*
|--------------------------------------------------------------------------
| Admin : Categories, Types & extend fields
|--------------------------------------------------------------------------
*/
$lang['ionize_title_categories'] = 'カテゴリ';
$lang['ionize_title_category_new'] = '新規カテゴリ';
$lang['ionize_title_category_edit'] = 'カテゴリ : 編集';
$lang['ionize_title_category_exist'] = '既存のカテゴリ';

$lang['ionize_title_types'] = 'タイプ';
$lang['ionize_title_article_type_new'] = '新規タイプ';
$lang['ionize_title_article_type_edit'] = '記事タイプの編集';

$lang['ionize_label_categories'] = 'カテゴリ';
$lang['ionize_label_category'] = 'カテゴリ';
$lang['ionize_label_edit_categories'] = 'カテゴリの編集';
$lang['ionize_label_edit_category'] = 'カテゴリ編集';
$lang['ionize_label_new_category'] = 'カテゴリの作成';

$lang['ionize_label_type'] = 'タイプ';
$lang['ionize_label_label'] = 'ラベル';
$lang['ionize_label_edit_types'] = 'タイプの編集';
$lang['ionize_label_new_type'] = '新規タイプ';
$lang['ionize_label_values'] = '値';
$lang['ionize_label_default_value'] = 'デフォルト値';

$lang['ionize_message_category_name_exists'] = '同名のカテゴリが既に存在します。';
$lang['ionize_message_category_saved'] = 'カテゴリを保存しました';
$lang['ionize_message_category_not_saved'] = 'カテゴリは保存できませんでした';
$lang['ionize_message_category_deleted'] = 'カテゴリを削除しました';
$lang['ionize_message_category_not_deleted'] = 'カテゴリは削除できませんでした';
$lang['ionize_message_category_name_exists'] = 'このカテゴリは既に存在しています';

$lang['ionize_message_article_type_saved'] = '記事タイプを保存しました';
$lang['ionize_message_article_type_not_saved'] = '記事タイプは保存されませんでした';
$lang['ionize_message_article_type_deleted'] = 'タイプを削除しました';
$lang['ionize_message_article_type_not_deleted'] = 'タイプは削除できませんでした';
$lang['ionize_message_type_exists'] = 'この記事タイプは既に存在しています';

$lang['ionize_label_type_text'] = 'テキスト入力';
$lang['ionize_label_type_textarea'] = 'テキストエリア';
$lang['ionize_label_type_editor'] = 'テキストエリア + エディタ';
$lang['ionize_label_type_checkbox'] = 'チェックボックス';
$lang['ionize_label_type_radio'] = 'ラジオボタン';
$lang['ionize_label_type_select'] = '選択';
$lang['ionize_label_type_datetime'] = '日付 & 時刻';

$lang['ionize_title_extend_field_new'] = '新規拡張フィールド';
$lang['ionize_title_extend_new_page_extend'] = 'ページフィールド';
$lang['ionize_title_extend_new_article_extend'] = '記事フィールド';
$lang['ionize_title_extend_new_media_extend'] = 'メディアフィールド';
$lang['ionize_title_extend_fields'] = '拡張フィールド';
$lang['ionize_title_extend_field'] = '拡張フィールド';

$lang['ionize_label_extend_fields_activate'] = '活性化';
$lang['ionize_label_extend_field_type'] = 'タイプ';
$lang['ionize_label_extend_field_translated'] = '翻訳可能です。';
$lang['ionize_label_extend_field_context'] = 'コンテキスト';

$lang['ionize_message_extend_field_name_exists'] = '同名の拡張が既に存在しています';
$lang['ionize_message_extend_field_saved'] = '拡張フィールドを保存しました';
$lang['ionize_message_extend_field_not_saved'] = 'エラー : フィールド名を入力して下さい';
$lang['ionize_message_extend_field_deleted'] = '拡張フィールドを削除しました';
$lang['ionize_message_extend_field_not_deleted'] = '拡張フィールドは削除できませんでした';

$lang['ionize_help_ef_name'] = 'フィールドタグとともに使用するキーです';
$lang['ionize_help_ef_values'] = 'キー:値、コロンで分割します';
$lang['ionize_help_ef_default_value'] = 'ラジオボタンまたはチェックボックスが選択された場合にキーを設置します。';
$lang['ionize_help_ef_description'] = 'このフィールドの概要をユーザヘルプとして表示';


/*
|--------------------------------------------------------------------------
| Admin : Translations
|--------------------------------------------------------------------------
*/
$lang['ionize_message_language_files_saved'] = '翻訳を保存しました';
$lang['ionize_message_language_dir_creation_fail'] = 'フォルダの作成ができません';
$lang['ionize_message_language_file_creation_fail'] = 'ファイルの書き込みができません';
$lang['ionize_label_expand_all'] = 'すべて展開';
$lang['ionize_label_collapse_all'] = 'すべて収縮';


/*
|--------------------------------------------------------------------------
| Admin : Media Manager
|--------------------------------------------------------------------------
*/
$lang['ionize_image_manager'] =	'写真';
$lang['ionize_file_manager'] = 'ファイル';


/*
|--------------------------------------------------------------------------
| Admin : Modules
|--------------------------------------------------------------------------
*/
$lang['ionize_title_modules_list'] = 'モジュール一覧';
$lang['ionize_label_module_name'] = '名称';
$lang['ionize_label_module_uri'] = 'URI';
$lang['ionize_label_module_install'] = 'インストール';
$lang['ionize_label_module_uninstall'] = 'アンインストール';

$lang['ionize_message_module_install_error_no_config'] = 'エラー : config.xmlがありません';
$lang['ionize_message_module_install_error_config_write'] = '書込みエラー : application/config/modules.php';
$lang['ionize_message_module_page_conflict'] = 'エラー : 既存ページのURIと矛盾が発生';
$lang['ionize_message_module_install_database_error'] = 'エラー : モジュールテーブルのインストールができません';
$lang['ionize_message_module_saved'] = 'モジュールをインストールしました';
$lang['ionize_message_module_uninstalled'] = 'モジュールを削除しました';
$lang['ionize_message_module_not_installed'] = 'モジュールはインストールできませんでした';

