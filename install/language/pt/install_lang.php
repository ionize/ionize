<?php

$lang = array();

/*
|--------------------------------------------------------------------------
| General
|--------------------------------------------------------------------------
*/
$lang['title_ionize_installation'] = 'Instalação de Ionize';
$lang['title_welcome'] = 'Bem-vindo ao Ionize !';

$lang['title_system_check'] = 		'Verificação do sistema';
$lang['title_database_settings'] = 	'Definições de base de dados';
$lang['title_user_account'] = 	'Conta de administrador';
$lang['title_default_language'] = 	'Idioma por omissão';
$lang['title_sample_data'] = 	'Instalar demonstração?';

$lang['button_next_step'] = 		'Continuar';
$lang['button_skip_next_step'] = 	'Saltar este passo';
$lang['button_save_next_step'] = 	'Guardar & continuar';
$lang['button_install_test_data'] = 	"Instalar demonstração";
$lang['button_start_migrate'] = 		'Iniciar migração da base de dados';

$lang['nav_check'] = 'Verificação do sistema';
$lang['nav_db'] = 'Base de dados';
$lang['nav_settings'] = 'Definições';
$lang['nav_end'] = 'Finalizar';
$lang['nav_data'] = 'Demonstração';


/*
|--------------------------------------------------------------------------
| System check
|--------------------------------------------------------------------------
*/
$lang['php_version'] = 			'PHP >= 5';
$lang['php_version_found'] = 	'Versão PHP';
$lang['mysql_support'] = 		'Suporte a MySQL';
$lang['mysql_version_found'] = 	'Versão MySQL';
$lang['file_uploads'] = 		'Carregamento de ficheiros (Upload)';
$lang['mcrypt'] = 				'Extensão Mcrypt';
$lang['gd_lib'] = 				'Extensão GD';
$lang['write_config_dir'] = 	'<b>/application/config/</b>';
$lang['write_files'] = 			'<b>/files/*</b>';
$lang['write_themes'] = 		'<b>/themes/*</b>';
$lang['config_check_errors'] = 	'Alguns requerimentos estão em falta.<br/>Por favor corrija para continuar a instalação.';
$lang['welcome_text'] = 		"<p>Os seguintes passos irão ajudar a instalar o Ionize.</p><p>Seguem os resultados dos requerimentos.<br/>Se um requerimento não estiver satisfeito, por favor corrija e recarregue a página.</p>";
$lang['write_check_text'] = 	"<p>As seguintes pastas e ficheiros devem ter permissão de escrita...</p>";
$lang['title_folder_check'] = 	"Estas pastas devem ter permissão de escrita";
$lang['title_files_check'] = 	"Estes ficheiros devem ter permissão de escrita";

/*
|--------------------------------------------------------------------------
| Database
|--------------------------------------------------------------------------
*/
$lang['database_driver'] = 			'Driver';
$lang['database_hostname'] = 		'Servidor';
$lang['database_name'] = 			'Base de dados';
$lang['database_username'] = 		'Utilizador';
$lang['database_password'] = 		'Palavra-passe';
$lang['database_create'] = 			'Create the database';
$lang['title_database_create'] = 	'Criação da base de dados';
$lang['db_create_text'] = 			"<p>Por favor preencha as definições de ligação à base de dados.<br/>Em caso de upgrade, Ionize irá detetar a versão atual e irá correr o upgrade da BD.</p><p><strong>Importante:</strong> Se estiver a fazer upgrade, por favor guarde antes um backup da BD.</p>";
$lang['db_create_prerequisite'] = 			"O utilizador deve ter permissões para criar a BD.<br/>Não marque se a base de dados já existir.";
$lang['database_error_missing_settings'] = 	'Alguma informação está em falta.<br/>Por favor preencha todos os campos!';
$lang['database_success_install'] = 		'<b class="ex">A BD foi instalada com sucesso.</b>';
$lang['database_success_install_no_settings_needed'] = 		'<b class="ex">Base de dados OK.</b><br/>Como a BD já existe o passo para configurar o site não será executado.';
$lang['database_success_migrate'] = 		'<b class="ex">O upgrade à BD foi realizado com sucesso.</b>';
$lang['database_error_coud_not_connect'] = 		'A ligação à BD falhou usando as suas definições.';
$lang['database_error_database_dont_exists'] = 		"A base de dados não existe!";
$lang['database_error_writing_config_file'] = 		"<b>Erro :</b><br/>O ficheiro <b style=\"color:#000;\">/application/config/database.php</b> não pode ser escrito!<br/>Verifique as permissões.";
$lang['database_error_coud_not_write_database'] = 		"<b>Erro :</b><br/> Foi impossivel escrever na BD.<br/>Verifique as permissões do utilizador.";
$lang['database_error_coud_not_create_database'] = "A instalaão não consegui criar a base de dados. Verifique as permissões do utilizador";
$lang['database_error_no_ionize_tables'] = 			"A BD indicada não parece ser uma BD Ionize. Por favor verifique novamente.";
$lang['database_error_no_users_to_migrate'] = 		"A conta de utilizador não foi alvo de upgrade";
$lang['database_migration_from'] = 			'A base de dados indicada precisa de upgrade.<br/>Versão de upgrade: ';
$lang['database_migration_text'] = 		"<p class=\"error\"><b>NOTA :</b><br/> Será agora realizado um upgrade à BD.<b><br/>Por favor certifique-se de que faz agora um backup antes de avançar.</p>";


/*
|--------------------------------------------------------------------------
| Settings
|--------------------------------------------------------------------------
*/
$lang['lang_code'] = 		'Código (2 letras)';
$lang['lang_name'] = 		'Etiqueta';
$lang['settings_default_lang_title'] = 		'Idioma por omissão';
$lang['settings_default_lang_text'] = 		'O site necessita de ter um idioma por omissão definido.<br/>Visite <a target="_blank" href="http://en.wikipedia.org/wiki/ISO_639-1">the Wikipedia ISO 639-1 page</a> para mais informação sobre os códigos de idiomas standard.';
$lang['settings_error_missing_lang_code'] = "O código de idioma por omissão é obrigatório";
$lang['settings_error_missing_lang_name'] = "A etiqueta do idioma é obrigatória";
$lang['settings_error_lang_code_2_chars'] = "O código do idioma deve ter apenas 2 letras. Examplo : \"pt\"";
$lang['settings_error_write_rights'] = "Não existem permissões de escrita para <b>/application/config/language.php</b>. Por favor corrija.";
$lang['settings_error_write_rights_config'] = "Não existem permissões de escrita para <b>/application/config/config.php</b>. Por favor corrija.";
$lang['settings_error_admin_url'] = "A URL do painel de administração deve ser uma string alfanumérica, sem espaços ou símbolos";
$lang['settings_admin_url_title'] = 		'URL do painel de Administração';
$lang['settings_admin_url_text'] = 		'É fortemente recomendado alterar o valor por omissão.';
$lang['admin_url'] = 'URL do painel de administração';

/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
*/
$lang['user_introduction'] = 	'Este é o nome de utilizador para entrar no painel de administração.';
$lang['username'] = 			'Nome de utilizador (min. 4 caracteres)';
$lang['screen_name'] = 			'Nome completo';
$lang['email'] = 				'Endereço de e-mail';
$lang['password'] = 			'Palavra-passe (min. 4 caracteres)';
$lang['password2'] = 			'Confirmar palavra-passe';
$lang['user_error_missing_settings'] = 			'Por favor preencha todos os campos!';
$lang['user_error_not_enough_char'] = 			'O nome e palavra-passe deve ter pelo menos 4 caracteres de comprimento!';
$lang['user_error_email_not_valid'] = 			'O endereço de e-mail não é válido. Por favor corrija.';
$lang['user_error_passwords_not_equal'] = 		'A confirmação da palavra-passe não coicide.	';
$lang['user_info_admin_exists'] = 		'Um administrador já existe na BD.<br/>Este passo pode ser omitido se pretender não alterar o administrador existente.<br/><br/>IMPORTANTE : Deve efetuar uma cópia da chave de encriptação do antigo site para poder iniciar sessão:<br/>Ver: /application/config/config.php -> $config[\'encryption_key\']';
$lang['encryption_key'] = 			'Chave de encriptação';
$lang['encryption_key_text'] = 		"Ionize necessita de uma chave de encriptação de 128 bits.<br />Esta chave irá encriptar as contas de utilizador e todos os dados sensíveis.<br/>A chave será escrita em <b>/application/config/config.php</b>.";
$lang['no_encryption_key_found'] = 	"A chave de encriptação não foi encontrada. A conta de utilizador não foi migrada. <b>Deve criar uma nova conta de administração</b>.";


/*
|--------------------------------------------------------------------------
| Data
|--------------------------------------------------------------------------
*/
$lang['data_install_intro'] = 	"<p>Se esta é a primeira vez que usa o Ionize, é fortemente recomendada a instalação da demonstração.<br/>Esta demosntração inclui: </p><ul><li>Um conjunto completo de dados, útil para testar as potencialidades do Ionize,</li><li>Um tema de exemplo</li></ul>";
$lang['title_skip_this_step'] = "Omitir este passo";

$lang['title_finish'] = 		'Instalação completa';
$lang['finish_text'] = 			'<b>IMPORTANTE</b>: <br/>Deve apagar a pasta "<b>/install</b>" manualmente antes de visualizar o site ou iniciar sessão no painel de administração.';
$lang['button_go_to_admin'] = 	'Ir para administração';
$lang['button_go_to_site'] = 	'Ir para o site';
