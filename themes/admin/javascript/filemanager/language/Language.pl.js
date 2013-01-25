/*
Script: Language.pl.js
	MooTools Filemanager - Language Strings in Polish

Translation:
	[Marek Kalucki](http://www.webdeco.pl)
    [Grzegorz Nakonieczny](http://naki.info/)
*/

Filemanager.Language.pl = {
	more: 'Szczegóły',
	width: 'Szerokość:',
	height: 'Wysokość:',

	ok: 'Ok',
	open: 'Wybierz plik',
	upload: 'Wyślij',
	create: 'Utwórz folder',
	createdir: 'Podaj nazwę folderu:',
	cancel: 'Anuluj',
	error: 'Błąd',

	information: 'Informacje',
	type: 'Typ:',
	size: 'Rozmiar:',
	dir: 'Ścieżka:',
	modified: 'Ost. modyfikacja:',
	preview: 'Podgląd',
	close: 'Zamknij',
	destroy: 'Usuń',
	destroyfile: 'Czy na pewno chcesz usunąć ten plik?',

	rename: 'Zmień nazwę',
	renamefile: 'Podaj nową nazwę pliku:',
	rn_mv_cp: 'Rename/Move/Copy',

	download: 'Pobierz',
	nopreview: '<i>Podgląd niedostępny</i>',

	title: 'Tytuł:',
	artist: 'Wykonawca:',
	album: 'Płyta:',
	length: 'Długość:',
	bitrate: 'Przepływność:',

	deselect: 'Odznacz',

	nodestroy: 'Usuwanie plików z serwera zostało wyłączone.',

	toggle_side_boxes: 'Thumbnail view',
	toggle_side_list: 'List view',
	show_dir_thumb_gallery: 'Show thumbnails of the files in the preview pane',
	drag_n_drop: 'Drag & drop has been enabled for this directory',
	drag_n_drop_disabled: 'Drag & drop has been temporarily disabled for this directory',
	goto_page: 'Go to page',

	'backend.disabled': 'Wysyłanie plików na serwer zostało wyłączone.',
	'backend.authorized': 'Nie jesteś upoważniony do wysyłania plików na serwer.',
	'backend.path': 'Folder do wysyłania plików nie istnieje. Skontaktuj się z administratorem.',
	'backend.exists': 'Folder do wysyłania plików istnieje. Skontaktuj się z administratorem.',
	'backend.mime': 'Typ wybranego pliku jest niedozwolony.',
	'backend.extension': 'Wysyłany plik ma nieznane lub niedozwolone rozszerzenie.',
	'backend.size': 'Rozmiar wysyłanego pliku jest zbyt duży. Wyślij mniejszy plik (jeśli wysyłasz obrazy-zmniejsz obraz na swoim komputerze i ponów wysyłanie).',
	'backend.partial': 'Plik nie został wysłany w całości. Ponów próbę wysyłki pliku.',
	'backend.nofile': 'Nie wybrano pliku do wysyłki.',
	'backend.default': 'Wystąpił błąd w trakcie wysyłki.',
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
		unknown: 'Wystąpił nieznany błąd.',
		sizeLimitMin: 'Nie można wybrać "<em>${name}</em>" (${size}), minimalny rozmiar pliku to <strong>${size_min}</strong>!',
		sizeLimitMax: 'Nie można wybrać "<em>${name}</em>" (${size}), maksymalny rozmiar pliku to <strong>${size_max}</strong>!',
		mod_security: 'No response was given from the uploader, this may mean that "mod_security" is active on the server and one of the rules in mod_security has cancelled this request.  If you can not disable mod_security, you may need to use the NoFlash Uploader.'
	},

	flash: {
		hidden: 'Aby włączyć wysyłanie plików, odblokuj go w swojej przeglądarce i odśwież stronę (prawdopodobnie wysyłanie plików jest blokowane przez wtyczkę Adblock).',
		disabled: 'Aby włączyć wysyłanie plików, odblokuj obiekt Flash i odśwież stronę (prawdopodobnie wysyłanie plików blokowane jest przez wtyczkę Flashblock).',
		flash: 'Aby wysyłać pliki na serwer, należy zainstalować w przeglądarce wtyczkę <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe Flash</a>.'
	},

	resizeImages: 'Zmniejsz duże obrazy w trakcie wysyłania',

    serialize: 'Zapisz galerię',
    gallery: {
        text: 'Podpis',
        save: 'Zapisz',
        remove: 'Usuń element z galerii',
        drag: 'Przeciągnij tutaj elementy aby utworzyć galerię...'
    }
};