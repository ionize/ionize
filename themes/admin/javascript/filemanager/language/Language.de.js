/*
Script: Language.de.js
	MooTools Filemanager - Language Strings in German

Translation:
	[Christoph Pojer](http://cpojer.net)
*/

Filemanager.Language.de = {
	more: 'Details',
	width: 'Breite:',
	height: 'Höhe:',

	ok: 'Ok',
	open: 'Datei wählen',
	upload: 'Datei hochladen',
	create: 'Ordner erstellen',
	createdir: 'Bitte gib einen Ordnernamen ein:',
	cancel: 'Abbrechen',
	error: 'Fehler',

	information: 'Information',
	type: 'Typ:',
	size: 'Größe:',
	dir: 'Verzeichnis:',
	modified: 'Zuletzt bearbeitet:',
	preview: 'Vorschau',
	close: 'Schließen',
	destroy: 'Löschen',
	destroyfile: 'Bist du sicher, dass du diese Datei löschen möchtest?',

	rename: 'Umbenennen',
	renamefile: 'Gib einen neuen Dateinamen ein:',
	rn_mv_cp: 'Rename/Move/Copy',

	download: 'Download',
	nopreview: '<i>Keine Vorschau verfügbar</i>',

	title: 'Titel:',
	artist: 'Artist:',
	album: 'Album:',
	length: 'Länge:',
	bitrate: 'Bitrate:',

	deselect: 'Entfernen',

	nodestroy: 'Dateien löschen wurde auf diesem Server deaktiviert.',

	toggle_side_boxes: 'Thumbnail view',
	toggle_side_list: 'List view',
	show_dir_thumb_gallery: 'Show thumbnails of the files in the preview pane',
	drag_n_drop: 'Drag & drop has been enabled for this directory',
	drag_n_drop_disabled: 'Drag & drop has been temporarily disabled for this directory',
	goto_page: 'Go to page',

	'backend.disabled': 'Dieser Funktion wurde auf diesem Server deaktiviert.',
	'backend.authorized': 'Akt fehlgeschlagen: Du hast keine Genehmigung.',
	'backend.path': 'Der angegebene Ordner oder Datei existiert nicht. Bitte kontaktiere den Administrator dieser Website.',
	'backend.exists': 'Der angegebene Speicherort existiert bereits. Bitte kontaktiere den Administrator dieser Website.',
	'backend.mime': 'Der angegebene Dateityp ist nicht erlaubt.',
	'backend.extension': 'Die Datei hat eine unbekannte oder unerlaubte Datei-Erweiterung.',
	'backend.size': 'Die Datei, die du hochgeladen hast, ist zu groß um sie auf diesem Server zu verarbeiten. Bitte lade eine kleinere Datei hoch.',
	'backend.partial': 'Die Datei wurde nur teilweise hochgeladen. Bitte lade sie erneut hoch.',
	'backend.nofile': 'Es wurde keine Datei angezeigt/hochgeladen oder der Datei konnte nicht gefunden werden.',
	'backend.default': 'Der Datei-Upload ist fehlgeschlagen.',
	'backend.path_not_writable': 'You do not have write/upload permissions for this directory.',
	'backend.filename_maybe_too_large': 'The filename/path is probably too long for the server filesystem. Please retry with a shorter file name.',
	'backend.fmt_not_allowed': 'You are not allowed to upload this file format/name.',
	'backend.read_error': 'Cannot read / download the specified file.',
	'backend.unidentified_error': 'An unindentified error occurred while communicating with the backend (web server).',

	'backend.nonewfile': 'A new name for the file to be moved / copied is missing.',
	'backend.corrupt_img': 'This file is a not a image or a corrupt file: ', // path
	'backend.resize_inerr': 'This file could not be resized due to an internal error.',
	'backend.copy_failed': 'An error occurred while copying the file / directory: ', // oldlocalpath : newlocalpath
	'backend.delete_cache_entries_failed': 'An error occurred when attempting to delete the item cache (thumbnails, metadata)',
	'backend.mkdir_failed': 'An error occurred when attempting to create the directory: ', // path
	'backend.move_failed': 'An error occurred while moving / renaming the file / directory: ', // oldlocalpath : newlocalpath
	'backend.path_tampering': 'Path tampering detected.',
	'backend.realpath_failed': 'Cannot translate the given file specification to a valid storage location: ', // $path
	'backend.unlink_failed': 'An error occurred when attempting to delete the file / directory: ',  // path

	// Image.class.php:
	'backend.process_nofile': 'The image processing unit did not receive a valid file location to work on.',
	'backend.imagecreatetruecolor_failed': 'The image processing unit failed: GD imagecreatetruecolor() failed.',
	'backend.imagealphablending_failed': 'The image processing unit failed: cannot perform the required image alpha blending.',
	'backend.imageallocalpha50pctgrey_failed': 'The image processing unit failed: cannot allocate space for the alpha channel and the 50% background.',
	'backend.imagecolorallocatealpha_failed': 'The image processing unit failed: cannot allocate space for the alpha channel for this color image.',
	'backend.imagerotate_failed': 'The image processing unit failed: GD imagerotate() failed.',
	'backend.imagecopyresampled_failed': 'The image processing unit failed: GD imagecopyresampled() failed. Image resolution: ', /* x * y */
	'backend.imagecopy_failed': 'The image processing unit failed: GD imagecopy() failed.',
	'backend.imageflip_failed': 'The image processing unit failed: cannot flip the image.',
	'backend.imagejpeg_failed': 'The image processing unit failed: GD imagejpeg() failed.',
	'backend.imagepng_failed': 'The image processing unit failed: GD imagepng() failed.',
	'backend.imagegif_failed': 'The image processing unit failed: GD imagegif() failed.',
	'backend.imagecreate_failed': 'The image processing unit failed: GD imagecreate() failed.',
	'backend.cvt2truecolor_failed': 'conversion to True Color failed. Image resolution: ', /* x * y */
	'backend.no_imageinfo': 'Corrupt image or not an image file at all.',
	'backend.img_will_not_fit': 'Server error: image does not fit in available RAM; minimum required (estimate): ', /* XXX MBytes */
	'backend.unsupported_imgfmt': 'unsupported image format: ',    /* jpeg/png/gif/... */

	/* FU */
	uploader: {
		unknown: 'Unbekannter Fehler',
		sizeLimitMin: 'Die Datei "<em>${name}</em>" (${size}), ist zu klein. Minimaldateigröße: <strong>${size_min}</strong>!',
		sizeLimitMax: 'Die Datei "<em>${name}</em>" (${size}), ist zu groß. Dateigrößen-Limit: <strong>${size_max}</strong>!',
		mod_security: 'No response was given from the uploader, this may mean that "mod_security" is active on the server and one of the rules in mod_security has cancelled this request.  If you can not disable mod_security, you may need to use the NoFlash Uploader.'
	},

	flash: {
		hidden: 'Um den Uploader benutzen zu können, muss er im Browser freigegeben werden und die Seite muss neu geladen werden (Adblock).',
		disabled: 'Um den Uploader benutzen zu können, muss die geblockte Flash Datei freigegeben werden und die Seite muss neu geladen werden (Flashblock).',
		flash: 'Um Dateien hochzuladen muss <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe Flash</a> installiert werden.'
	},

	resizeImages: 'Große Bilder bei Upload verkleinern',

	serialize: 'Galerie speichern',
	gallery: {
		text: 'Bildtext',
		save: 'Speichern',
		remove: 'Entfernen',
		drag: 'Verschiebe Bilder in diesen Bereich um eine Galerie zu erstellen...'
	}
};