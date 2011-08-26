<?php

$lang = array();

/* General */
$lang['title_ionize_installation'] = 		'インストール';

$lang['title_system_check'] = 		'システムチェック結果';
$lang['title_database_settings'] = 	'データベース設定';
$lang['title_user_account'] = 	'管理者アカウント';
$lang['title_default_language'] = 	'デフォルト言語';
$lang['title_sample_data'] = 	'サンプルのサイトをインストールしますか';

$lang['button_next_step'] = 		'次のステップへ';
$lang['button_skip_next_step'] = 	'次のステップへスキップ';
$lang['button_save_next_step'] = 	'保存して次のステップへ';
$lang['button_install_test_data'] = 	"テストデータのインストール";
$lang['button_start_migrate'] = 		'データベース移行開始';

$lang['nav_check'] = 'システムチェック';
$lang['nav_db'] = 'データベース';
$lang['nav_settings'] = '設定';
$lang['nav_end'] = '終了';
$lang['nav_data'] = 'Demo data';


/* System check */
$lang['php_version'] = 			'PHP >= 5';
$lang['php_version_found'] = 	'PHPバージョン';
$lang['mysql_support'] = 		'MySQLサポート';
$lang['mysql_version_found'] = 	'MySQLバージョン';
$lang['file_uploads'] = 		'ファイルアップロード';
$lang['mcrypt'] = 				'PHP Mcrypt Lib';
$lang['gd_lib'] = 				'PHP GD Lib';
$lang['write_config_dir'] = 	'<b>/application/config/</b>';
$lang['write_files'] = 			'<b>/files/</b>';
$lang['write_themes'] = 		'<b>/themes/*</b>';
$lang['config_check_errors'] = 	'いくつかの基本機能がOKになりませんでした。<br/>インストールを続けるにはこれらを解決してください';
$lang['welcome_text'] = 		"<p>Ionizeへようこそ!<br/>このステップはIonizeインストールに役立つでしょう。</p>";
$lang['write_check_text'] = 	"<p>次のフォルダおよびファイルは書き込み可能にしてください...</p>";
$lang['system_check_text'] = 	"<p>Ionizeはこれらの設定をすべてOKにする必要があります。</p>";
 

/* Database */
$lang['database_driver'] = 			'ドライバ';
$lang['database_hostname'] = 		'ホスト名';
$lang['database_name'] = 			'データベース';
$lang['database_username'] = 		'ユーザ';
$lang['database_password'] = 		'パスワード';
$lang['database_create'] = 			'データベース作成';
$lang['title_database_create'] = 	'データベースの作成';
$lang['db_create_text'] = 			"<p>Ionizeはデータベースのインストールもしくは移行します:</p><p><b class=\"highlight\">新規インストール</b> : データベースおよびテーブルが作成されます<br/><b class=\"highlight2\">更新</b> : アップデートが必要な場合は次のステップでチェックされます</p>";
$lang['db_create_prerequisite'] = 			"ユーザはデータベース作成の権限を持っている必要があります。<br/>データベースが作成済みの場合はチェックされません";
$lang['database_error_missing_settings'] = 	'いくつかの情報に誤りがあります。<br/>すべての項目を入力してください';
$lang['database_success_install'] = 		'<b class="ex">データベースは正常に作成されました。</b>';
$lang['database_success_install_no_settings_needed'] = 		'<b class="ex">データベースはOKです。</b><br/>データベースが既に存在する場合は、ウェブサイト設定ステップは省略されます。';
$lang['database_success_migrate'] = 		'<b class="ex">データベースのアップグレードに成功しました。</b>';
$lang['database_error_coud_not_connect'] = 		'指定の設定ではデータベース接続に失敗しました。';
$lang['database_error_database_dont_exists'] = 		"データベースが存在しません。";
$lang['database_error_writing_config_file'] = 		"<b>エラー :</b><br/><b style=\"color:#000;\">/application/config/database.php</b>ファイルが書き込めません。<br/>権限をチェックしてください。";
$lang['database_error_coud_not_write_database'] = 		"<b>エラー :</b><br/> データベースにデータを書き込めません。<br/>DB権限をチェックしてください。";
$lang['database_error_coud_not_create_database'] = "インストーラはデータベースを作成できません。データベース権限をチェックしてください。";
$lang['database_error_no_ionize_tables'] = 			"指定されたデータベースはIonize用データベースではありません。再度確認してください。";
$lang['database_error_no_users_to_migrate'] = 		"ユーザアカウントを移行しました";

$lang['database_migration_from'] = 			'データベースのアップグレードが必要です。<br/>アップグレードバージョン : ';

$lang['database_migration_text'] = 		"<p class=\"error\"><b>注意 :</b><br/> データベースはアップグレードされませんでした。<b><br/>アップグレードの前にデータベースのバックアップをしてください。</p>";


/* Settings */
$lang['lang_code'] = 		'コード (2文字)';
$lang['lang_name'] = 		'ラベル';
$lang['settings_default_lang_title'] = 		'デフォルト言語';
$lang['settings_default_lang_text'] = 		'ウェブサイトではデフォルト言語が必要です。<br/>言語コードについての詳しい情報は <a target="_blank" href="http://en.wikipedia.org/wiki/ISO_639-1">WikipediaのISO 639-1ページ</a> を参照してください。';
$lang['settings_error_missing_lang_code'] = "言語コードは必須です。";
$lang['settings_error_missing_lang_name'] = "言語コードは必須です。";
$lang['settings_error_lang_code_2_chars'] = "言語コードは2文字でなくてはなりません。例 : \"en\"";
$lang['settings_error_write_rights'] = "<b>/application/config/laguage.php</b>の書き込み権限がありません。PHPが書き込み権限を持っているか確認してください。";
$lang['settings_error_write_rights_config'] = "<b>/application/config/config.php</b>の書き込み権限がありません。PHPが書き込み根源を持っているか確認してください。";


/* User */
$lang['user_introduction'] = 	'このログイン名で管理パネルに接続できます。';
$lang['username'] = 			'ログイン名(最小4文字)';
$lang['screen_name'] = 			'正式な名前';
$lang['email'] = 				'メール';
$lang['password'] = 			'パスワード(最小4文字)';
$lang['password2'] = 			'パスワードの確認';
$lang['user_error_missing_settings'] = 			'すべての項目に入力してください。';
$lang['user_error_not_enough_char'] = 			'ログイン名およびパスワードは最低でも4文字以上が必要です。';
$lang['user_error_email_not_valid'] = 			'メールアドレスが正しくありません。確認してください。';
$lang['user_error_passwords_not_equal'] = 		'パスワードが一致しません。';
$lang['user_info_admin_exists'] = 		'この管理者は既に存在します。<br/>管理者アカウントを作成または更新する必要がなければスキップできます。';
$lang['encryption_key'] = 			'暗号化キー';
$lang['encryption_key_text'] = 		"Ionizeは128バイとの暗号化キーを必要とします。<br />
									このキーはユーザアカウントとすべての極秘データを暗号化するでしょう。<br/>
									これは<b>/application/config/config.php</b>ファイルに書き込まれます。";
$lang['no_encryption_key_found'] = 	"ユーザアカウントは暗号化キーは見つけられませんでした。移行しませんでした。<b>新しい管理者ユーザを作成する必要があります。</b>";


/* Example data */
$lang['data_install_intro'] = 	"あなたが初めてIonizeを使用する場合は、サンプルデータをインストールすることを強く勧めます。<br/>
								これは次のデータを含みます : ";
$lang['data_install_list'] = 	"<li>Ionizeをテストするための完全なデータベースデータ,</li>
								<li>1個のサンプルテーマ</li>";
$lang['title_skip_this_step'] = 	"このステップをスキップする";


/* Finish screen */
$lang['title_finish'] = 		'インストール完了';
$lang['finish_text'] = 			'<b>重要</b>: <br/>"<b>/install</b>"フォルダを手動で削除してからウェブサイトまたは管理画面へアクセスしてください。';
$lang['button_go_to_admin'] = 	'管理画面へ';
$lang['button_go_to_site'] = 	'ウェブサイトへ';
