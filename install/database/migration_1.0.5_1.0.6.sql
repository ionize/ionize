--
-- Ionize 1.0.6 database migration
-- From 1.0.5 to 1.0.6
-- 


update setting set content='1.0.6' where name = 'ionize_version';
    

INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (220,NULL,'admin/item','create,edit,delete,add','Static Items','Static Items');
INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (221,220,'admin/item/definition','edit','Definition','Static Items > Definition');

INSERT IGNORE INTO rule (id_role, resource, actions, permission) VALUES (2,'admin/item','create,edit,delete,add',1);
INSERT IGNORE INTO rule (id_role, resource, actions, permission) VALUES (3,'admin/item','create,edit,delete,add',1);

INSERT IGNORE INTO resource (id_resource, id_parent, resource, actions, title, description) VALUES (222,220,'admin/item/media','link,unlink,edit','Media','Static Items > Media');
INSERT IGNORE INTO rule (id_role, resource, actions, permission) VALUES (2,'admin/item/media','link,unlink,edit',1);
INSERT IGNORE INTO rule (id_role, resource, actions, permission) VALUES (3,'admin/item/media','link,unlink,edit',1);

ALTER TABLE extend_field add id_parent int(11) unsigned NULL;
UPDATE extend_field set parent='element', id_parent=id_element_definition where id_element_definition != 0;
ALTER TABLE extend_field add main smallint(1) unsigned NOT NULL DEFAULT 0;

delete from rule where resource='admin/page/media' and actions='';
delete from rule where resource='admin/article/media' and actions='';
ALTER TABLE rule DROP PRIMARY KEY;
ALTER TABLE rule add id_user int(1) unsigned NOT NULL DEFAULT 0;
ALTER TABLE rule ADD PRIMARY KEY(id_role,id_user,resource);

update resource set actions='link,unlink,edit' where resource='admin/page/media';
update resource set actions='link,unlink,edit' where resource='admin/article/media';
update rule set actions='link,unlink,edit' where resource='admin/page/media';
update rule set actions='link,unlink,edit' where resource='admin/article/media';

delete from resource where resource ='admin/page/media/file';
delete from resource where resource ='admin/page/media/music';
delete from resource where resource ='admin/page/media/video';
delete from resource where resource ='admin/page/media/picture';
delete from resource where resource ='admin/article/media/file';
delete from resource where resource ='admin/article/media/music';
delete from resource where resource ='admin/article/media/video';
delete from resource where resource ='admin/article/media/picture';

delete from rule where resource ='admin/page/media/file';
delete from rule where resource ='admin/page/media/music';
delete from rule where resource ='admin/page/media/video';
delete from rule where resource ='admin/page/media/picture';
delete from rule where resource ='admin/article/media/file';
delete from rule where resource ='admin/article/media/music';
delete from rule where resource ='admin/article/media/video';
delete from rule where resource ='admin/article/media/picture';

update extend_field set id_parent = id_element_definition where parent='element';
update extend_fields set id_parent = id_element where id_element != 0;
update extend_fields set parent = 'element' where id_element != 0;
alter table extend_fields drop id_element;
alter table extend_field drop id_element_definition;
alter table extend_field drop parents;
alter table extend_field drop block;
alter table extend_field change type type int(3);
alter table extend_field add copy_in varchar(50) NULL;
alter table extend_field add copy_in_pk varchar(50) NULL;

alter table article_lang change lang lang varchar(8);
alter table article_media change lang_display lang_display varchar(8);
alter table captcha change lang lang varchar(8);
alter table element_definition_lang change lang lang varchar(8);
alter table extend_field_lang change lang lang varchar(8);
alter table extend_fields change lang lang varchar(8);
alter table item_definition_lang change lang lang varchar(8);
alter table item_lang change lang lang varchar(8);
alter table lang change lang lang varchar(8);
alter table media_lang change lang lang varchar(8);
alter table module_setting change lang lang varchar(8);
alter table page_lang change lang lang varchar(8);
alter table page_media change lang_display lang_display varchar(8);
alter table setting change lang lang varchar(8);
alter table url change lang lang varchar(8);


