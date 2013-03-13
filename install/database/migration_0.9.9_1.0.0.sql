--
-- Ionize 1.0.0 database migration
-- From 0.9.9 to 1.0.0
--

update setting set content='1.0.0' where name = 'ionize_version';

ALTER TABLE media ADD provider varchar(50) NOT NULL DEFAULT  '';


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


DELETE FROM setting WHERE name='media_upload_mode';</query>
INSERT IGNORE INTO setting VALUES ('', 'upload_autostart', '1', '');</query>
INSERT IGNORE INTO setting VALUES ('', 'resize_on_upload', '1', '');</query>
INSERT IGNORE INTO setting VALUES ('', 'picture_max_width', '1200', '');</query>
INSERT IGNORE INTO setting VALUES ('', 'picture_max_height', '1200', '');</query>
INSERT IGNORE INTO setting VALUES ('', 'upload_mode', '', '');</query>



-- User table
create table if not exists user as select * from users;
alter table user change id_group id_role int(11) unsigned not null;
alter table user add primary key(id_user);
alter table user modify id_user int(11) unsigned auto_increment;

-- Role table
create table if not exists role as select * from user_groups;
alter table role change id_group id_role int(11) not null AUTO_INCREMENT PRIMARY KEY;
alter table role change level role_level int(11);
alter table role change slug role_code varchar(50);
alter table role change group_name role_name varchar(100);
alter table role change description role_description tinytext;


-- Resource table
CREATE TABLE if not exists resource (
  id_resource int(11) NOT NULL AUTO_INCREMENT,
  id_parent int(11) unsigned DEFAULT '0',
  resource varchar(255) NOT NULL DEFAULT '',
  actions varchar(1000) DEFAULT '',
  title varchar(255) DEFAULT '',
  description varchar(1000) DEFAULT '',
  PRIMARY KEY (id_resource),
  UNIQUE KEY resource_key (resource)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO resource (id_parent, resource, actions, title, description)
VALUES
     (0,'admin','','Backend login','Connect to ionize backend'),
     (0,'admin/settings','','Settings',''),
     (2,'admin/settings/ionize','','Ionize UI',''),
     (2,'admin/settings/languages','','Languages Management',''),
     (2,'admin/settings/users','create,edit,delete','Users',''),
     (2,'admin/settings/themes','edit','Themes',''),
     (2,'admin/settings/website','','Website settings',''),
     (2,'admin/settings/technical','','Technical settings',''),
     (0,'admin/menu','create,edit,delete','Menu',''),
     (0,'admin/page','create,edit,delete','Page',''),
     (10,'admin/page/media','link,unlink','Media',''),
     (10,'admin/page/element','add','Content Element',''),
     (10,'admin/page/article','add','Article',''),
     (0,'admin/article','create,edit,delete,move,copy,duplicate','Article',''),
     (14,'admin/article/media','link, unlink','Media',''),
     (14,'admin/article/element','add','Content Element',''),
     (0,'admin/modules','install','Modules',''),
     (0,'admin/translations','','Translations',''),
     (0,'admin/filemanager','upload,rename,delete,move','Filemanager',''),
     (0,'admin/article/type','create,edit,delete','Article Type',''),
     (0,'admin/element','create,edit,delete','Content Element',''),
     (0,'admin/extend','create,edit,delete','Extend Fields',''),
     (0,'admin/system','','System',''),
     (23,'admin/system/diagnosis/info','','Diagnosis Informations',''),
     (23,'admin/system/diagnosis/tools','','Diagnosis Tools',''),
     (23,'admin/system/diagnosis/reports','','Diagnosis Reports',''),
     (14,'admin/article/category','','Manage categories',''),
     (2,'admin/settings/roles','create,edit,delete','Roles',''),
     (2,'admin/settings/roles/permissions','','Roles Permissions','See Role\'s permissions');

-- Rule table
CREATE TABLE if not exists rule (
  id_role int(11) NOT NULL,
  resource varchar(255) NOT NULL DEFAULT '',
  actions varchar(25) NOT NULL DEFAULT '',
  permission smallint(1) DEFAULT NULL,
  PRIMARY KEY (id_role,resource,actions)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO rule (id_role, resource, actions, permission)
VALUES
     (1,'all','',1);


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
ALTER TABLE user_groups CONVERT TO CHARACTER SET utf8;
ALTER TABLE users CONVERT TO CHARACTER SET utf8;


-- Migration from article to "content"

