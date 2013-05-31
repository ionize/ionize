--
-- Ionize 1.0.0 database migration
-- From 0.9.9 to 1.0.0
--

update setting set content='1.0.0' where name = 'ionize_version';

ALTER TABLE lang ADD direction smallint(1) NOT NULL DEFAULT 1;


CREATE TABLE api_key (
    id int(11) NOT NULL AUTO_INCREMENT,
    key varchar(40) NOT NULL,
    level int(2) NOT NULL,
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



CREATE TABLE event_log (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    status varchar(50) DEFAULT NULL,
    message text,
    id_user int(11) DEFAULT NULL,
    email varchar(255) DEFAULT NULL,
    date_log datetime DEFAULT NULL,
    ip_address varchar(45) DEFAULT NULL,
    seen tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DELETE FROM setting WHERE name='media_upload_mode';
INSERT IGNORE INTO setting VALUES ('', 'upload_autostart', '1', '');
INSERT IGNORE INTO setting VALUES ('', 'resize_on_upload', '1', '');
INSERT IGNORE INTO setting VALUES ('', 'picture_max_width', '1200', '');
INSERT IGNORE INTO setting VALUES ('', 'picture_max_height', '1200', '');
INSERT IGNORE INTO setting VALUES ('', 'upload_mode', '', '');
INSERT IGNORE INTO setting VALUES ('', 'display_dashboard_shortcuts', '1', '');
INSERT IGNORE INTO setting VALUES ('', 'display_dashboard_modules', '1', '');
INSERT IGNORE INTO setting VALUES ('', 'display_dashboard_users', '1', '');
INSERT IGNORE INTO setting VALUES ('', 'display_dashboard_content', '1', '');


-- Page table
alter table page add deny_code varchar(3) NULL;
alter table page drop id_group;

-- Tag table
drop table if exists tag;

CREATE TABLE IF NOT EXISTS tag (
id_tag int(11) UNSIGNED NOT NULL auto_increment,
tag_name varchar(50) default NULL,
PRIMARY KEY  (id_tag)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;


-- User table
drop table if exists user;
create table if not exists user as select * from users;
alter table user change id_group id_role int(11) unsigned not null;
alter table user add primary key(id_user);
alter table user modify id_user int(11) unsigned auto_increment;
drop table users;

-- Role table
create table if not exists role as select * from user_groups;
alter table role change id_group id_role int(11) not null AUTO_INCREMENT PRIMARY KEY;
alter table role change level role_level int(11);
alter table role change slug role_code varchar(50);
alter table role change group_name role_name varchar(100);
alter table role change description role_description tinytext;
drop table user_groups;

update role set role_name='super-admin' where role_name='super-admins';
update role set role_name='admin' where role_name='admins';
update role set role_name='editor' where role_name='editors';
update role set role_name='user' where role_name='users';
update role set role_name='guest' where role_name='guests';


-- Resource table
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

INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description)
VALUES
	(1,NULL,'admin','','Backend login','Connect to ionize backend'),
	(10,NULL,'admin/menu','create,edit,delete','Menu','Menus'),
	(11,10,'admin/menu/permissions/backend','','Permissions','Menu > Backend Permissions'),
	(20,NULL,'admin/translations','','Translations','Translations'),
	(30,NULL,'admin/filemanager','upload,rename,delete,move','Filemanager','FileManager'),
	(35,NULL,'admin/medialist','','MediaList','MediaList'),
	(40,NULL,'admin/page','create,edit,delete','Page','Page'),
	(41,40,'admin/page/article','add','Article','Page > Article'),
	(42,40,'admin/page/element','add','Content Element','Page > Content Element'),
	(50,40,'admin/page/media','','Media','Page > Media'),
	(51,50,'admin/page/media/picture','link,unlink, edit','Pictures','Page > Media > Pictures'),
	(52,50,'admin/page/media/video','link,unlink, edit','Videos','Page > Media > Videos'),
	(53,50,'admin/page/media/music','link,unlink, edit','Music','Page > Media > Music'),
	(54,50,'admin/page/media/file','link,unlink, edit','Files','Page > Media > Files'),
	(60,40,'admin/page/permissions','','Permission','Page > Permission'),
	(61,60,'admin/page/permissions/backend','','Backend','Page > Permission > Backend'),
	(62,60,'admin/page/permissions/frontend','','Frontend','Page > Permission > Frontend'),
	(70,NULL,'admin/article','create,edit,delete,move,copy,duplicate','Article','Article'),
	(80,70,'admin/article/media','','Media','Article > Media'),
	(81,80,'admin/article/media/picture','link,unlink, edit','Pictures','Article > Media > Pictures'),
	(82,80,'admin/article/media/video','link,unlink,edit','Videos','Article > Media > Videos'),
	(83,80,'admin/article/media/music','link,unlink,edit','Music','Article > Media > Music'),
	(84,80,'admin/article/media/file','link,unlink,edit','Files','Article > Media > Files'),
	(85,70,'admin/article/element','add','Content Element','Article > Content Element'),
	(86,70,'admin/article/category','','Manage categories','Article > Categories'),
	(93,70,'admin/article/tag','','Manage tags','Article > Tags'),
	(90,70,'admin/article/permissions','','Permission','Article > Permission'),
	(91,90,'admin/article/permissions/backend','','Backend','Article > Permission > Backend'),
	(92,90,'admin/article/permissions/frontend','','Frontend','Article > Permission > Frontend'),
	(100,NULL,'admin/tree','','Tree',''),
	(101,100,'admin/tree/menu','add_page,edit','Menu','Tree > Menus'),
	(102,100,'admin/tree/page','status,add_page,add_article,order','Page','Tree > Pages'),
	(103,100,'admin/tree/article','unlink,status,move,copy,order','Article','Tree > Articles'),
	(120,NULL,'admin/article/type','create,edit,delete','Article Type','Article types'),
	(150,NULL,'admin/modules','install','Modules','Modules'),
	(151,150,'admin/modules/permissions','','Set Permissions','Modules > Permissions'),
	(180,NULL,'admin/element','create,edit,delete','Content Element','Content Elements'),
	(210,NULL,'admin/extend','create,edit,delete','Extend Fields','Extend Fields'),
	(240,NULL,'admin/tools','','Tools','Tools'),
	(241,240,'admin/tools/google_analytics','','Google Analytics','Tools > Google Analytics'),
	(250,240,'admin/tools/system','','System Diagnosis','Tools > System'),
	(251,250,'admin/tools/system/information','','Information','Tools > System > Information'),
	(252,250,'admin/tools/system/repair','','Repair tools','Tools > System > Repair'),
	(253,250,'admin/tools/system/report','','Reports','Tools > System > Reports'),
	(270,NULL,'admin/settings','','Settings','Settings'),
	(271,270,'admin/settings/ionize','','Ionize UI','Settings > UI Settings'),
	(272,270,'admin/settings/languages','','Languages Management','Settings > Languages'),
	(273,270,'admin/settings/themes','edit','Themes','Settings > Themes'),
	(274,270,'admin/settings/website','','Website settings','Settings > Website'),
	(275,270,'admin/settings/technical','','Technical settings','Settings > Technical'),
	(300,NULL,'admin/users_roles','','Users & Roles','Users & Roles'),
	(301,300,'admin/user','create,edit,delete','Users','Users'),
	(302,300,'admin/role','create,edit,delete','Roles','Roles'),
	(303,302,'admin/role/permissions','','Set Permissions','See Role\'s permissions');

-- Rule table
CREATE TABLE if not exists rule (
  id_role int(11) NOT NULL,
  resource varchar(100) NOT NULL DEFAULT '',
  actions varchar(100) NOT NULL DEFAULT '',
  permission smallint(1) DEFAULT NULL,
  id_element int(11) unsigned,
  PRIMARY KEY (id_role,resource,actions)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  (2,'admin/article/tag','',1,NULL),
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
  (3,'admin/article/tag','',1,NULL),
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


INSERT INTO rule (id_role, resource, actions, permission)
VALUES
     (1,'all','',1);

-- Tag table
alter table tag change tag tag_name varchar(50) not null;


-- ALTER database ionize_099 default CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE article CONVERT TO CHARACTER SET utf8;
ALTER TABLE article_comment CONVERT TO CHARACTER SET utf8;
ALTER TABLE article_lang CONVERT TO CHARACTER SET utf8;
ALTER TABLE article_media CONVERT TO CHARACTER SET utf8;
ALTER TABLE article_type CONVERT TO CHARACTER SET utf8;
ALTER TABLE captcha CONVERT TO CHARACTER SET utf8;
ALTER TABLE category CONVERT TO CHARACTER SET utf8;
ALTER TABLE category_lang CONVERT TO CHARACTER SET utf8;
ALTER TABLE element CONVERT TO CHARACTER SET utf8;
ALTER TABLE element CONVERT TO CHARACTER SET utf8;
ALTER TABLE element_definition CONVERT TO CHARACTER SET utf8;
ALTER TABLE element_definition_lang CONVERT TO CHARACTER SET utf8;
ALTER TABLE extend_field CONVERT TO CHARACTER SET utf8;
ALTER TABLE extend_field_lang CONVERT TO CHARACTER SET utf8;
ALTER TABLE extend_fields CONVERT TO CHARACTER SET utf8;
ALTER TABLE ion_sessions CONVERT TO CHARACTER SET utf8;
ALTER TABLE lang CONVERT TO CHARACTER SET utf8;
ALTER TABLE login_tracker CONVERT TO CHARACTER SET utf8;
ALTER TABLE media CONVERT TO CHARACTER SET utf8;
ALTER TABLE media_lang CONVERT TO CHARACTER SET utf8;
ALTER TABLE menu CONVERT TO CHARACTER SET utf8;
ALTER TABLE module CONVERT TO CHARACTER SET utf8;
ALTER TABLE module_setting CONVERT TO CHARACTER SET utf8;
ALTER TABLE note CONVERT TO CHARACTER SET utf8;
ALTER TABLE page CONVERT TO CHARACTER SET utf8;
ALTER TABLE page_article CONVERT TO CHARACTER SET utf8;
ALTER TABLE page_lang CONVERT TO CHARACTER SET utf8;
ALTER TABLE page_media CONVERT TO CHARACTER SET utf8;
ALTER TABLE setting CONVERT TO CHARACTER SET utf8;
ALTER TABLE tag CONVERT TO CHARACTER SET utf8;
ALTER TABLE tracker CONVERT TO CHARACTER SET utf8;
ALTER TABLE type CONVERT TO CHARACTER SET utf8;
ALTER TABLE url CONVERT TO CHARACTER SET utf8;

