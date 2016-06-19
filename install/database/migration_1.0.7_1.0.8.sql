--
-- Ionize 1.0.8 database migration
-- From 1.0.7 to 1.0.8
-- 


UPDATE setting set content='1.0.8' where name = 'ionize_version';

INSERT IGNORE INTO extend_field_type (id_extend_field_type, type_name, default_values, `values`, translated, active, validate, html_element, html_element_type, html_element_class, html_element_pattern)
VALUES (9,'Color',0,0,0,1,NULL,'input','color','color w120 inputtext',NULL);

INSERT IGNORE INTO extend_field_type (id_extend_field_type, type_name, default_values, `values`, translated, active, validate, html_element, html_element_type, html_element_class, html_element_pattern)
VALUES (10,'Date & Time',0,0,0,1,NULL,'input','datetime','w140 inputtext', NULL);

CREATE TABLE IF NOT EXISTS page_category (
  id_page INT(11) UNSIGNED NOT NULL ,
  id_category INT(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS page_tag (
  id_page int(11) UNSIGNED NOT NULL,
  id_tag int(11) UNSIGNED NOT NULL,
  PRIMARY KEY  (id_page, id_tag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE page_lang change subtitle subtitle text;
ALTER TABLE article_lang change subtitle subtitle text;


ALTER TABLE page_lang change subtitle subtitle text;
ALTER TABLE article_lang change subtitle subtitle text;
ALTER TABLE page change publish_on publish_on datetime default '0000-00-00 00:00:00';
ALTER TABLE page change publish_off publish_off datetime default '0000-00-00 00:00:00';
ALTER TABLE page change updated updated datetime default '0000-00-00 00:00:00';
ALTER TABLE page change id_type id_type SMALLINT(2) default NULL;
ALTER TABLE page change link_id link_id varchar(20) default NULL;
ALTER TABLE article change publish_on publish_on datetime default '0000-00-00 00:00:00';
ALTER TABLE article change publish_off publish_off datetime default '0000-00-00 00:00:00';
ALTER TABLE article change updated updated datetime default '0000-00-00 00:00:00';
ALTER TABLE article change logical_date logical_date datetime default '0000-00-00 00:00:00';
ALTER TABLE media change date date datetime default '0000-00-00 00:00:00';
    

