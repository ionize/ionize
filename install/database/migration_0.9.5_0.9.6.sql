--
-- Ionize 0.9.6 database migration
-- From 0.9.5 to 0.9.6
--

CREATE TABLE IF NOT EXISTS page_article (
	id_page INT(11) UNSIGNED NOT NULL,
	id_article INT(11) UNSIGNED NOT NULL,
	online tinyint(1) UNSIGNED NOT NULL default 0,
	view varchar(50) default NULL,
  	ordering int(11) default 0,
	id_type int(11) UNSIGNED NULL,
	PRIMARY KEY  (id_page, id_article)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO page_article (id_page, id_article, online, view, ordering, id_type)
	SELECT 
		id_page, 
		id_article,
		online,
		`view`, 
		ordering ,
		id_type
	from article
);

ALTER TABLE `article` ADD `flag` SMALLINT NOT NULL;

ALTER TABLE `article`
  DROP `online`,
  DROP `ordering`,
  DROP `id_page`,
  DROP `view`;


UPDATE setting SET content='mootools-filemamager' where name='filemanager';

UPDATE setting SET content='0.9.6' where name='ionize_version';

DELETE FROM setting WHERE name='media_thumb_size';
INSERT INTO setting VALUES ('', 'media_thumb_size', '120', '');

DELETE FROM setting WHERE name='display_connected_label';
INSERT INTO setting VALUES ('', 'display_connected_label', '1', '');

DELETE FROM setting WHERE name='tinybuttons1';
INSERT INTO setting VALUES ('', 'tinybuttons1', 'pdw_toggle,|,bold,italic,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,|,bullist,numlist,|,link,unlink,image', '');

DELETE FROM setting WHERE name='tinybuttons2';
INSERT INTO setting VALUES ('', 'tinybuttons2', 'fullscreen, undo,redo,|,pastetext,selectall,removeformat,|,media,charmap,hr,blockquote,|,template,|,codemirror', '');

DELETE FROM setting WHERE name='tinybuttons3';
INSERT INTO setting VALUES ('', 'tinybuttons3', 'tablecontrols', '');

DELETE FROM setting WHERE name='displayed_admin_languages';
INSERT INTO setting VALUES('', 'displayed_admin_languages', 'en,fr', NULL);

INSERT INTO setting VALUES('', 'date_format', '%Y.%m.%d', NULL);


