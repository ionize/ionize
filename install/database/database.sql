--
-- Ionize 1.0.0 SQL creation tables
--

CREATE TABLE api_key (
    id int(11) NOT NULL AUTO_INCREMENT,
    `key` varchar(40) NOT NULL,
    `level` int(2) NOT NULL,
    ignore_limits tinyint(1) NOT NULL DEFAULT '0',
    is_private tinyint(1) NOT NULL DEFAULT '0',
    ip_addresses text,
    date_created datetime,
    PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE api_log (
    id int(11) NOT NULL AUTO_INCREMENT,
    uri varchar(255) NOT NULL,
    method varchar(6) NOT NULL,
    params text,
    api_key varchar(40) NOT NULL,
    date_log datetime DEFAULT NULL,
    ip_address varchar(45) NOT NULL,
    authorized tinyint(1) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS article (
  id_article int(11) UNSIGNED NOT NULL auto_increment,
  name varchar(255) DEFAULT NULL,
  author varchar(55) DEFAULT NULL,
  updater varchar(55) DEFAULT NULL,
  created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  publish_on datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  publish_off datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updated datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  logical_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  indexed tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  id_category int(11) UNSIGNED DEFAULT NULL,
  comment_allow char(1) DEFAULT '0',
  comment_autovalid char(1) DEFAULT '0',
  comment_expire datetime,
  flag smallint(1) DEFAULT 0,
  has_url tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY  (id_article)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS article_category (
	id_article INT(11) UNSIGNED NOT NULL ,
	id_category INT(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS article_lang (
  id_article int(11) UNSIGNED NOT NULL default 0,
  lang varchar(3) NOT NULL default '',
  url VARCHAR( 100 ) NOT NULL default '',
  title varchar(255) default NULL,
  subtitle varchar(255) default NULL,
  meta_title varchar(255) default NULL,
  content longtext,
  meta_keywords varchar(255) default NULL,
  meta_description varchar(255) default NULL,
  online tinyint(1) UNSIGNED NOT NULL default 1,
  PRIMARY KEY  (id_article,lang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS article_media (
  id_article int(11) UNSIGNED NOT NULL default 0,
  id_media int(11) UNSIGNED NOT NULL default 0,
  online tinyint(1) UNSIGNED NOT NULL default 1,
  ordering int(11) UNSIGNED default 9999,
  url varchar(255) default NULL,
  lang_display varchar(3) DEFAULT NULL,
  PRIMARY KEY  (id_article,id_media)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS article_comment (
	id_article_comment int(11) UNSIGNED NOT NULL auto_increment,
	id_article int(11) UNSIGNED NOT NULL default 0,
	author varchar(255) default NULL,
	email varchar(255) default NULL,
	site varchar(255) default NULL,
	content text,
	ip varchar(40) default NULL,
	status tinyint UNSIGNED default NULL,
	created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	updated datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	admin tinyint UNSIGNED NOT NULL	default 0 COMMENT 'If comment comes from admin, set to 1',
	PRIMARY KEY (id_article_comment)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS article_tag (
	id_article int(11) UNSIGNED NOT NULL,
	id_tag int(11) UNSIGNED NOT NULL,
	PRIMARY KEY  (id_article, id_tag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS article_type (
  id_type int(11) unsigned NOT NULL auto_increment,
  type varchar(50) NOT NULL,
  ordering int(11) DEFAULT 0,
  description text DEFAULT NULL,
  type_flag TINYINT( 1 ) NOT NULL default 0,
  PRIMARY KEY  (id_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS captcha (
  id_captcha int(11) UNSIGNED NOT NULL auto_increment,
  question varchar(255) NOT NULL default '',
  answer varchar(255) NOT NULL default '',
  lang varchar(3) NOT NULL default '',
  hash varchar(32) NOT NULL default '',
  PRIMARY KEY  (id_captcha)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS category (
  id_category int(11) UNSIGNED NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  ordering int(11) default 0,
  PRIMARY KEY  (id_category)
)  ENGINE=InnoDB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS category_lang (
  id_category int(11) UNSIGNED NOT NULL default '0',
  lang char(3) NOT NULL,
  title varchar(255) NOT NULL default '',
  subtitle VARCHAR( 255 ) NOT NULL default '',
  description text NOT NULL,
	  PRIMARY KEY  (id_category, lang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS element (
  id_element int(11) unsigned NOT NULL auto_increment,
  id_element_definition int(11) unsigned NOT NULL,
  parent varchar(50) NOT NULL,
  id_parent int(11) NOT NULL,
  ordering int(11) NOT NULL default '0',
  PRIMARY KEY  (id_element),
  KEY idx_element_id_element_definition (id_element_definition),
  KEY idx_element_id_parent (id_parent),
  KEY idx_element_parent (parent)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS element_definition (
  id_element_definition int(11) unsigned NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  description text,
  ordering int(11) not null default 0,
  PRIMARY KEY  (id_element_definition)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS element_definition_lang (
  id_element_definition int(11) unsigned NOT NULL,
  lang varchar(3) NOT NULL,
  title varchar(255) NOT NULL default '',
  PRIMARY KEY  (id_element_definition, lang)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE event_log (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    status varchar(50) DEFAULT NULL,
    message text,
    id_user int(11) DEFAULT NULL,
    email varchar(255) DEFAULT NULL,
    date datetime DEFAULT NULL,
    ip_address varchar(45) DEFAULT NULL,
    seen tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS extend_field (
	id_extend_field INT(11) UNSIGNED NOT NULL auto_increment,
	name varchar(255) NOT NULL,
	type varchar(1) NOT NULL,
	description varchar(255) DEFAULT '',
	parent varchar(50) NOT NULL,
  	ordering int(11) default 0,
	translated char(1) default '0',
	value text NULL,
	default_value varchar(255) NULL,
	global tinyint(1) UNSIGNED NOT NULL default '0',
	parents varchar(300) NOT NULL default '',
	id_element_definition INT( 11 ) UNSIGNED NOT NULL DEFAULT '0',
	block VARCHAR( 50 ) NOT NULL DEFAULT '',
	PRIMARY KEY  (id_extend_field),
	KEY idx_extend_field_parent (parent),
    KEY idx_extend_field_id_element_definition (id_element_definition) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8   AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS extend_field_lang (
    id_extend_field int(11) unsigned NOT NULL,
    lang char(3) NOT NULL,
    label varchar(255),
    PRIMARY KEY  (id_extend_field, lang)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS extend_fields (
	id_extend_fields INT(11) UNSIGNED NOT NULL auto_increment,
	id_extend_field INT(11) UNSIGNED NOT NULL,
	parent varchar(50) NOT NULL default '',
	id_parent int(11) UNSIGNED NOT NULL,
	lang char(3) NOT NULL default '',
	content text,
	ordering INT( 11 ) UNSIGNED NOT NULL DEFAULT '0',
	id_element INT( 11 ) UNSIGNED NOT NULL,
	PRIMARY KEY  (id_extend_fields),
	KEY idx_extend_fields_id_parent (id_parent),
	KEY idx_extend_fields_lang (lang),
    KEY idx_extend_fields_id_extend_field (id_extend_field) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8   AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS ion_sessions (
  session_id varchar(40) NOT NULL default '0',
  ip_address varchar(16) NOT NULL default '0',
  user_agent varchar(50) NULL,
  last_activity int(10) unsigned NOT NULL default '0',
  user_data text NULL,
  PRIMARY KEY  (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS lang (
  lang varchar(3) NOT NULL default '',
  name varchar(40) default NULL,
  online char(1) default '0',
  def char(1) default '0',
  ordering int(11),
  direction smallint(1) NOT NULL default 1,
  PRIMARY KEY  (lang)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS login_tracker (
  ip_address varchar(32) NOT NULL,
  first_time int(11) unsigned NOT NULL,
  failures tinyint(2) unsigned default NULL,
  PRIMARY KEY  (ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS media (
	id_media int(11) UNSIGNED NOT NULL auto_increment,
	type varchar(10) NOT NULL DEFAULT '',
	file_name varchar(255) NOT NULL default '' COMMENT 'file_name',
	path varchar(500) NOT NULL COMMENT 'Complete path to the medium, including media file name, excluding host name',
	base_path varchar(500) NOT NULL COMMENT 'medium folder base path, excluding host name',
	copyright varchar(128) default NULL,
	provider varchar(255) default NULL,
	date datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Medium date',
	link varchar(255) default NULL COMMENT 'Link to a resource, attached to this medium',
	square_crop enum('tl','m','br') NOT NULL DEFAULT 'm',
--	path_hash varchar(100) NOT NULL DEFAULT  '',
	PRIMARY KEY  (id_media)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS media_lang (
  lang varchar(3) NOT NULL default '',
  id_media int(11) UNSIGNED NOT NULL default 0,
  title varchar(255) default NULL,
  alt varchar(500) default NULL,
  description longtext,
  PRIMARY KEY  (id_media, lang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS menu (
  id_menu int(11) NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  title varchar(50) NOT NULL,
  ordering int(11),
  PRIMARY KEY  (id_menu),
  UNIQUE KEY name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS module (
	id_module int(11) UNSIGNED NOT NULL auto_increment,
	name varchar(100) NOT NULL default '',
	with_admin tinyint UNSIGNED  NOT NULL default 0,
	version varchar(10) NOT NULL default '',
	status tinyint UNSIGNED NOT NULL default 0,
	ordering tinyint UNSIGNED NOT NULL default 0,
	info text NOT NULL,
	description text NOT NULL,
	PRIMARY KEY  (id_module),
	KEY i_module_name (name)
)  ENGINE=InnoDB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS module_setting (
  id_module_setting int(11) NOT NULL auto_increment,
  id_module int(11) NOT NULL,
  name varchar(50) NOT NULL					COMMENT 'Setting name',
  content varchar(255) NOT NULL				COMMENT 'Setting content',	
  lang varchar(2) default NULL,
  PRIMARY KEY  (id_module_setting) 
)   ENGINE=InnoDB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS note (
  id_note INT( 11 ) NOT NULL AUTO_INCREMENT,
  id_user INT( 11 ) NOT NULL ,
  date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  type VARCHAR( 10 ) NOT NULL,
  content TEXT NOT NULL ,
  PRIMARY KEY  (id_note)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS page (
  id_page int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  id_parent int(11) UNSIGNED NOT NULL default 0,
  id_menu int(11) UNSIGNED NOT NULL default 0,
  id_type smallint(2) NOT NULL default 0,
  id_subnav int(11) UNSIGNED NOT NULL default 0, 
  name varchar(255) default NULL,
  ordering int(11) UNSIGNED default 0,
  level int(11) UNSIGNED NOT NULL default 0,
  online tinyint(1) UNSIGNED NOT NULL default 0,
  home tinyint( 1 ) NOT NULL DEFAULT '0',
  author varchar(55) default NULL,
  updater varchar(55) default NULL,
  created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  publish_on datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  publish_off datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updated datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  logical_date datetime NOT NULL default '0000-00-00 00:00:00',
  appears tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  has_url tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  view varchar(50) default NULL										COMMENT 'Page view',
  view_single varchar(50) default NULL								COMMENT 'Single Article Page view',
  article_list_view VARCHAR(50) default NULL						COMMENT 'Article list view for each article linked to this page',
  article_view varchar(50) default NULL								COMMENT 'Article detail view for each article linked to this page',
  article_order VARCHAR(50) NOT NULL DEFAULT 'ordering'				COMMENT 'Article order in this page. Can be "ordering", "date"',
  article_order_direction VARCHAR(50) NOT NULL DEFAULT 'ASC',	
  link varchar(255) default ''										COMMENT 'Link to internal / external resource',
  link_type varchar(25) default NULL COMMENT '''page'', ''article'' or NULL',
  link_id varchar(20) NOT NULL default '',
  pagination tinyint(1) UNSIGNED NOT NULL DEFAULT 0						COMMENT 'Pagination use ?',
  pagination_nb tinyint(1) UNSIGNED NOT NULL DEFAULT 5						COMMENT 'Article number per page',
  priority int(1) unsigned NOT NULL DEFAULT '5' COMMENT 'Page priority',
  used_by_module tinyint(1) unsigned NULL,
  deny_code varchar(3) NULL,
  PRIMARY KEY  (id_page),
  KEY idx_page_id_parent (id_parent),
  KEY idx_page_level (level),
  KEY idx_page_menu (id_menu) 
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS page_article (
	id_page INT(11) UNSIGNED NOT NULL,
	id_article INT(11) UNSIGNED NOT NULL,
	online tinyint(1) UNSIGNED NOT NULL default 0,
	view varchar(50) default NULL,
  	ordering int(11) default 0,
	id_type int(11) UNSIGNED NULL,
	link_type VARCHAR( 25 ) NOT NULL DEFAULT '',
	link_id varchar(20) NOT NULL DEFAULT '',
	link VARCHAR( 255 ) NOT NULL DEFAULT '',
	main_parent TINYINT(1) NOT NULL DEFAULT 0,
	PRIMARY KEY  (id_page, id_article),
    KEY idx_page_article_id_type (id_type) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS page_category (
	id_page INT(11) UNSIGNED NOT NULL ,
	id_category INT(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS page_role (
  id_page int(11) UNSIGNED NOT NULL default 0,
  ig_group smallint(4) UNSIGNED NOT NULL default 0,
  PRIMARY KEY  (id_page,ig_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS page_lang (
  lang varchar(3) NOT NULL default '',
  id_page int(11) UNSIGNED NOT NULL default 0,
  url VARCHAR( 100 ) NOT NULL default '',
  link VARCHAR( 255 ) NOT NULL default '',
  title varchar(255) default NULL,
  subtitle varchar(255) default NULL,
  nav_title VARCHAR( 255 ) NOT NULL DEFAULT  '',
  subnav_title VARCHAR( 255 ) NOT NULL DEFAULT '',
  meta_title varchar(255) default NULL,
  meta_description varchar(255) default NULL,
  meta_keywords varchar(255) default NULL,
  online tinyint(1) UNSIGNED NOT NULL default 1,
  PRIMARY KEY  (id_page,lang)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS page_media (
  id_page int(11) UNSIGNED NOT NULL default 0,
  id_media int(11) UNSIGNED NOT NULL default 0,
  online tinyint(1) UNSIGNED NOT NULL default 1,
  ordering int(11) UNSIGNED default 9999,
  lang_display varchar(3) DEFAULT NULL,
  PRIMARY KEY  (id_page,id_media)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS page_tag (
	id_page int(11) UNSIGNED NOT NULL,
	id_tag int(11) UNSIGNED NOT NULL,
	PRIMARY KEY  (id_article, id_tag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE if not exists resource (
  id_resource int(11) NOT NULL AUTO_INCREMENT,
  id_parent int(11) unsigned DEFAULT '0',
  resource varchar(255) NOT NULL DEFAULT '',
  actions varchar(500) DEFAULT '',
  title varchar(255) DEFAULT '',
  description varchar(1000) DEFAULT '',
  PRIMARY KEY (id_resource),
  UNIQUE KEY resource_key (resource)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS role (
  id_role smallint(4) UNSIGNED NOT NULL auto_increment,
  role_level int(11) default NULL,
  role_code varchar(25) NOT NULL,
  role_name varchar(100) NOT NULL,
  role_description tinytext,
  PRIMARY KEY (id_role),
  UNIQUE KEY role_code (role_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

CREATE TABLE if not exists rule (
  id_role int(11) NOT NULL,
  resource varchar(150) NOT NULL DEFAULT '',
  actions varchar(150) NOT NULL DEFAULT '',
  permission smallint(1) DEFAULT NULL,
  id_element int(11) unsigned,
  PRIMARY KEY (id_role,resource,actions)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS setting (
  id_setting int(11) UNSIGNED NOT NULL auto_increment,
  name varchar(255) NOT NULL,
  content text not null,
  lang varchar(3),
  PRIMARY KEY (id_setting)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS tag (
	id_tag int(11) UNSIGNED NOT NULL auto_increment,
	tag_name varchar(50) default NULL,
	PRIMARY KEY  (id_tag)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

CREATE TABLE tracker (
  id_user int(11) unsigned NOT NULL,
  ip_address varchar(32) DEFAULT NULL,
  element varchar(50) DEFAULT NULL,
  id_element int(11) DEFAULT NULL,
  last_time datetime DEFAULT NULL,
  elements varchar(3000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS type (
  id_type int(11) unsigned NOT NULL AUTO_INCREMENT,
  code varchar(50) NOT NULL,
  parent char(20) NOT NULL,
  title varchar(255) NOT NULL,
  description varchar(3000) DEFAULT NULL,
  ordering smallint(6) NOT NULL,
  view varchar(50) DEFAULT NULL,
  flag tinyint(1) NOT NULL,
  PRIMARY KEY (id_type)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS url (
  id_url int(11) unsigned NOT NULL AUTO_INCREMENT,
  id_entity int(11) unsigned NOT NULL,
  type varchar(10) NOT NULL,
  canonical smallint(1) NOT NULL DEFAULT '0',
  active smallint(1) NOT NULL DEFAULT '0',
  lang varchar(3) NOT NULL,
  path varchar(255) NOT NULL DEFAULT '',
  path_ids varchar(50),
  full_path_ids varchar(50),
  creation_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id_url),
  KEY idx_url_type (type),
  KEY idx_url_active (active),
  KEY idx_url_lang (lang)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS user (
  id_user int(11) unsigned NOT NULL auto_increment,
  id_role smallint(4) unsigned NOT NULL,
  join_date timestamp NULL default NULL,
  last_visit timestamp NULL default NULL,
  username varchar(50) NOT NULL,
  screen_name varchar(50) default NULL,
  firstname varchar(100) DEFAULT NULL,
  lastname varchar(100) DEFAULT NULL,
  birthdate datetime DEFAULT NULL,
  gender smallint(1) DEFAULT NULL COMMENT '1: Male, 2 : Female',
  password varchar(255) NOT NULL,
  email varchar(120) NOT NULL,
  salt varchar(50) NULL,
  PRIMARY KEY  (id_user),
  UNIQUE KEY username (username),
  KEY id_role (id_role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;


TRUNCATE TABLE login_tracker;

INSERT IGNORE INTO role VALUES (1, 10000, 'super-admin', 'Super Admin', NULL);
INSERT IGNORE INTO role VALUES (2, 5000, 'admin', 'Admin', NULL);
INSERT IGNORE INTO role VALUES (3, 1000, 'editor', 'Editor', NULL);
INSERT IGNORE INTO role VALUES (4, 100, 'user', 'User', NULL);
INSERT IGNORE INTO role VALUES (5, 50, 'pending', 'Pending', NULL);
INSERT IGNORE INTO role VALUES (6, 10, 'guest', 'Guest', NULL);
INSERT IGNORE INTO role VALUES (7, -10, 'banned', 'Banned', NULL);
INSERT IGNORE INTO role VALUES (8, -100, 'deactivated', 'Deactivated', NULL);

INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (1,NULL,'admin','','Backend login','Connect to ionize backend');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (10,NULL,'admin/menu','create,edit,delete','Menu','Menus');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (11,10,'admin/menu/permissions/backend','','Permissions','Menu > Backend Permissions');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (20,NULL,'admin/translations','','Translations','Translations');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (30,NULL,'admin/filemanager','upload,rename,delete,move','Filemanager','FileManager');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (35,NULL,'admin/medialist','','MediaList','MediaList');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (40,NULL,'admin/page','create,edit,delete','Page','Page');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (41,40,'admin/page/article','add','Article','Page > Article');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (42,40,'admin/page/element','add','Content Element','Page > Content Element');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (50,40,'admin/page/media','','Media','Page > Media');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (51,50,'admin/page/media/picture','link,unlink, edit','Pictures','Page > Media > Pictures');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (52,50,'admin/page/media/video','link,unlink, edit','Videos','Page > Media > Videos');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (53,50,'admin/page/media/music','link,unlink, edit','Music','Page > Media > Music');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (54,50,'admin/page/media/file','link,unlink, edit','Files','Page > Media > Files');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (60,40,'admin/page/permissions','','Permission','Page > Permission');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (61,60,'admin/page/permissions/backend','','Backend','Page > Permission > Backend');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (62,60,'admin/page/permissions/frontend','','Frontend','Page > Permission > Frontend');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (70,NULL,'admin/article','create,edit,delete,move,copy,duplicate','Article','Article');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (80,70,'admin/article/media','','Media','Article > Media');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (81,80,'admin/article/media/picture','link,unlink, edit','Pictures','Article > Media > Pictures');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (82,80,'admin/article/media/video','link,unlink,edit','Videos','Article > Media > Videos');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (83,80,'admin/article/media/music','link,unlink,edit','Music','Article > Media > Music');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (84,80,'admin/article/media/file','link,unlink,edit','Files','Article > Media > Files');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (85,70,'admin/article/element','add','Content Element','Article > Content Element');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (86,70,'admin/article/category','','Manage categories','Article > Categories');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (90,70,'admin/article/permissions','','Permission','Article > Permission');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (91,90,'admin/article/permissions/backend','','Backend','Article > Permission > Backend');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (92,90,'admin/article/permissions/frontend','','Frontend','Article > Permission > Frontend');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (100,NULL,'admin/tree','','Tree','');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (101,100,'admin/tree/menu','add_page,edit','Menus','');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (102,100,'admin/tree/page','status,add_page,add_article,order','Pages','');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (103,100,'admin/tree/article','unlink,status,move,copy,order','Articles','');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (120,NULL,'admin/article/type','create,edit,delete','Article Type','Article types');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (150,NULL,'admin/modules','install','Modules','Modules');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (151,150,'admin/modules/permissions','','Set Permissions','Modules > Permissions');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (180,NULL,'admin/element','create,edit,delete','Content Element','Content Elements');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (210,NULL,'admin/extend','create,edit,delete','Extend Fields','Extend Fields');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (240,NULL,'admin/tools','','Tools','Tools');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (241,240,'admin/tools/google_analytics','','Google Analytics','Tools > Google Analytics');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (250,240,'admin/tools/system','','System Diagnosis','Tools > System');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (251,250,'admin/tools/system/information','','Information','Tools > System > Information');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (252,250,'admin/tools/system/repair','','Repair tools','Tools > System > Repair');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (253,250,'admin/tools/system/report','','Reports','Tools > System > Reports');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (270,NULL,'admin/settings','','Settings','Settings');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (271,270,'admin/settings/ionize','','Ionize UI','Settings > UI Settings');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (272,270,'admin/settings/languages','','Languages Management','Settings > Languages');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (273,270,'admin/settings/themes','edit','Themes','Settings > Themes');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (274,270,'admin/settings/website','','Website settings','Settings > Website');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (275,270,'admin/settings/technical','','Technical settings','Settings > Technical');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (300,NULL,'admin/users_roles','','Users / Roles','Users / Roles');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (301,300,'admin/user','create,edit,delete','Users','Users');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (302,300,'admin/role','create,edit,delete','Roles','Roles');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (303,302,'admin/role/permissions','','Set Permissions','See Role\'s permissions');

INSERT IGNORE INTO rule (id_role, resource, actions, permission, id_element)
  VALUES
  (1,'all','',1,NULL),
  (2,'admin','',1,NULL),
  (2,'admin/article','create,edit,delete,move,copy,duplicate',1,NULL),
  (2,'admin/article/category','',1,NULL),
  (2,'admin/article/element','add',1,NULL),
  (2,'admin/article/media','',1,NULL),
  (2,'admin/article/media/file','link,unlink,edit',1,NULL),
  (2,'admin/article/media/music','link,unlink,edit',1,NULL),
  (2,'admin/article/media/picture','link,unlink,edit',1,NULL),
  (2,'admin/article/media/video','link,unlink,edit',1,NULL),
  (2,'admin/article/permissions','',1,NULL),
  (2,'admin/article/permissions/backend','',1,NULL),
  (2,'admin/article/permissions/frontend','',1,NULL),
  (2,'admin/article/type','create,edit,delete',1,NULL),
  (2,'admin/element','create,edit,delete',1,NULL),
  (2,'admin/extend','create,edit,delete',1,NULL),
  (2,'admin/filemanager','upload,rename,delete,move',1,NULL),
  (2,'admin/menu','create,edit,delete',1,NULL),
  (2,'admin/modules','install',1,NULL),
  (2,'admin/modules/permissions','',1,NULL),
  (2,'admin/page','create,edit,delete',1,NULL),
  (2,'admin/page/article','add',1,NULL),
  (2,'admin/page/element','add',1,NULL),
  (2,'admin/page/media','',1,NULL),
  (2,'admin/page/media/file','link,unlink,edit',1,NULL),
  (2,'admin/page/media/music','link,unlink,edit',1,NULL),
  (2,'admin/page/media/picture','link,unlink,edit',1,NULL),
  (2,'admin/page/media/video','link,unlink,edit',1,NULL),
  (2,'admin/page/permissions','',1,NULL),
  (2,'admin/page/permissions/backend','',1,NULL),
  (2,'admin/page/permissions/frontend','',1,NULL),
  (2,'admin/role','create,edit,delete',1,NULL),
  (2,'admin/role/permissions','',1,NULL),
  (2,'admin/settings','',1,NULL),
  (2,'admin/settings/ionize','',1,NULL),
  (2,'admin/settings/languages','',1,NULL),
  (2,'admin/settings/website','',1,NULL),
  (2,'admin/tools','',1,NULL),
  (2,'admin/tools/google_analytics','',1,NULL),
  (2,'admin/tools/system','',1,NULL),
  (2,'admin/tools/system/information','',1,NULL),
  (2,'admin/tools/system/repair','',1,NULL),
  (2,'admin/tools/system/report','',1,NULL),
  (2,'admin/translations','',1,NULL),
  (2,'admin/tree','',1,NULL),
  (2,'admin/tree/article','unlink,status,move,copy,order',1,NULL),
  (2,'admin/tree/menu','add_page,edit',1,NULL),
  (2,'admin/tree/page','status,add_page,add_article,order',1,NULL),
  (2,'admin/user','create,edit,delete',1,NULL),
  (2,'admin/users_roles','',1,NULL),
  (3,'admin','',1,NULL),
  (3,'admin/article','create,edit,delete,move,copy,duplicate',1,NULL),
  (3,'admin/article/category','',1,NULL),
  (3,'admin/article/element','add',1,NULL),
  (3,'admin/article/media','',1,NULL),
  (3,'admin/article/media/picture','unlink',1,NULL),
  (3,'admin/article/media/video','unlink,edit',1,NULL),
  (3,'admin/article/permissions','',1,NULL),
  (3,'admin/article/permissions/backend','',1,NULL),
  (3,'admin/article/permissions/frontend','',1,NULL),
  (3,'admin/filemanager','upload,rename,delete,move',1,NULL),
  (3,'admin/menu','create,edit,delete',1,NULL),
  (3,'admin/modules','',1,NULL),
  (3,'admin/page','create,edit,delete',1,NULL),
  (3,'admin/page/article','add',1,NULL),
  (3,'admin/page/element','add',1,NULL),
  (3,'admin/page/media','',1,NULL),
  (3,'admin/page/media/file','link,unlink,edit',1,NULL),
  (3,'admin/page/media/music','link,unlink,edit',1,NULL),
  (3,'admin/page/media/picture','link,unlink,edit',1,NULL),
  (3,'admin/page/media/video','link,unlink,edit',1,NULL),
  (3,'admin/page/permissions','',1,NULL),
  (3,'admin/page/permissions/backend','',1,NULL),
  (3,'admin/page/permissions/frontend','',1,NULL),
  (3,'admin/settings','',1,NULL),
  (3,'admin/settings/ionize','',1,NULL),
  (3,'admin/settings/languages','',1,NULL),
  (3,'admin/settings/website','',1,NULL),
  (3,'admin/tools','',1,NULL),
  (3,'admin/tools/google_analytics','',1,NULL),
  (3,'admin/tools/system','',1,NULL),
  (3,'admin/tools/system/information','',1,NULL),
  (3,'admin/tools/system/report','',1,NULL),
  (3,'admin/translations','',1,NULL),
  (3,'admin/tree','',1,NULL),
  (3,'admin/tree/article','unlink,status,move,copy,order',1,NULL),
  (3,'admin/tree/menu','add_page,edit',1,NULL),
  (3,'admin/tree/page','status,add_page,add_article,order',1,NULL),
  (3,'admin/user','create,edit,delete',1,NULL),
  (3,'admin/users_roles','',1,NULL);


DELETE FROM setting WHERE name='cache';
DELETE FROM setting WHERE name='cache_time';
DELETE FROM setting WHERE name='create_dir_use_ftp';
DELETE FROM setting WHERE name='ftp_host';
DELETE FROM setting WHERE name='ftp_user';
DELETE FROM setting WHERE name='ftp_password';
DELETE FROM setting WHERE name='picture_copyright';


INSERT IGNORE INTO setting VALUES ('', 'website_email', '', '');
INSERT IGNORE INTO setting VALUES ('', 'files_path', 'files', '');
INSERT IGNORE INTO setting VALUES ('', 'cache', '0', '');
INSERT IGNORE INTO setting VALUES ('', 'cache_time', '150', '');
INSERT IGNORE INTO setting VALUES ('', 'theme', 'default', '');
INSERT IGNORE INTO setting VALUES ('', 'theme_admin', 'admin', '');
INSERT IGNORE INTO setting VALUES ('', 'google_analytics', '', '');
INSERT IGNORE INTO setting VALUES ('', 'filemanager', 'mootools-filemanager', '');
INSERT IGNORE INTO setting VALUES ('', 'show_help_tips', '1', '');

INSERT IGNORE INTO setting VALUES ('', 'display_connected_label', '1', '');
INSERT IGNORE INTO setting VALUES ('', 'display_dashboard_shortcuts', '1', '');
INSERT IGNORE INTO setting VALUES ('', 'display_dashboard_modules', '1', '');
INSERT IGNORE INTO setting VALUES ('', 'display_dashboard_users', '1', '');
INSERT IGNORE INTO setting VALUES ('', 'display_dashboard_content', '1', '');

INSERT IGNORE INTO setting VALUES ('', 'texteditor', 'tinymce', '');
INSERT IGNORE INTO setting VALUES ('', 'media_thumb_size', '120', '');

INSERT IGNORE INTO setting VALUES ('', 'tinybuttons1', 'pdw_toggle,|,bold,italic,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,|,bullist,numlist,|,link,unlink,image,|,spellchecker', '');
INSERT IGNORE INTO setting VALUES ('', 'tinybuttons2', 'fullscreen, undo,redo,|,pastetext,selectall,removeformat,|,media,charmap,hr,blockquote,|,template,|,codemirror', '');
INSERT IGNORE INTO setting VALUES ('', 'tinybuttons3', 'tablecontrols', '');
INSERT IGNORE INTO setting VALUES ('', 'smalltinybuttons1', 'bold,italic,|,bullist,numlist,|,link,unlink,image,|,nonbreaking', '');
INSERT IGNORE INTO setting VALUES ('', 'smalltinybuttons2', '', '');
INSERT IGNORE INTO setting VALUES ('', 'smalltinybuttons3', '', '');

INSERT IGNORE INTO setting VALUES ('', 'displayed_admin_languages', 'en', '');
INSERT IGNORE INTO setting VALUES ('', 'date_format', '%Y.%m.%d', '');
INSERT IGNORE INTO setting VALUES ('', 'force_lang_urls', '0', '');
INSERT IGNORE INTO setting VALUES ('', 'tinyblockformats', 'p,h2,h3,h4,h5,pre,div', '');
INSERT IGNORE INTO setting VALUES ('', 'filemanager_file_types','gif,jpe,jpeg,jpg,png,flv,mpg,mp3,doc,pdf,rtf','');
INSERT IGNORE INTO setting VALUES ('', 'article_allowed_tags','h1,h2,h3,h4,h5,h6,em,img,table,div,span,dl,pre,code,thead,tbody,tfoot,tr,th,td,caption,dt,dd,map,area,p,a,ul,ol,li,br,b,strong','');
INSERT IGNORE INTO setting VALUES ('', 'no_source_picture','default.png','');
INSERT IGNORE INTO setting VALUES ('', 'enable_backend_tracker','0', '');
INSERT IGNORE INTO setting VALUES ('', 'backend_ui_style','original', '');


DELETE FROM setting WHERE name='default_admin_lang';
INSERT INTO setting VALUES ('', 'default_admin_lang', 'en', '');

DELETE FROM setting WHERE name='ionize_version';
INSERT INTO setting VALUES ('', 'ionize_version', '1.0.0', '');

INSERT IGNORE INTO setting VALUES ('', 'upload_autostart', '1', '');
INSERT IGNORE INTO setting VALUES ('', 'resize_on_upload', '1', '');
INSERT IGNORE INTO setting VALUES ('', 'picture_max_width', '1200', '');
INSERT IGNORE INTO setting VALUES ('', 'picture_max_height', '1200', '');
INSERT IGNORE INTO setting VALUES ('', 'upload_mode', '', '');



INSERT IGNORE INTO menu (id_menu, name, title) VALUES
	(1 , 'main', 'Main menu'),
	(2 , 'system', 'System menu');
		

