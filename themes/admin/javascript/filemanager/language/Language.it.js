/*
Script: Language.it.js
	MooTools Filemanager - Language Strings in English

Translation:
	Moreno Monga
*/

Filemanager.Language.it = {
	more: 'Dettagli',
	width: 'Larghezza:',
	height: 'Altezza:',

	ok: 'Ok',
	open: 'Seleziona file',
	upload: 'Upload',
	create: 'Crea cartella',
	createdir: 'Specifica il nome della cartella:',
	cancel: 'Annulla',
	error: 'Errore',

	information: 'Informazioni',
	type: 'Tipo:',
	size: 'Dimensione:',
	dir: 'Percorso:',
	modified: 'Ultima modifica:',
	preview: 'Anteprima',
	close: 'Chiudi',
	destroy: 'Cancella',
	destroyfile: 'Sei sicuro di voler cancellare questo file?',

	rename: 'Rinomina',
	renamefile: 'Scrivi un nuovo nome per il file:',
	rn_mv_cp: 'Rename/Move/Copy',

	download: 'Download',
	nopreview: '<i>Non sono disponibili anteprime</i>',

	title: 'Titolo:',
	artist: 'Artista:',
	album: 'Album:',
	length: 'Lunghezza:',
	bitrate: 'Bitrate:',

	deselect: 'Deseleziona',

	nodestroy: 'La cancellazioni dei file è disabilitata.',

	toggle_side_boxes: 'Thumbnail view',
	toggle_side_list: 'List view',
	show_dir_thumb_gallery: 'Show thumbnails of the files in the preview pane',
	drag_n_drop: 'Drag & drop has been enabled for this directory',
	drag_n_drop_disabled: 'Drag & drop has been temporarily disabled for this directory',
	goto_page: 'Go to page',

	'backend.disabled': 'L Upload dei file è disabilitato.',
	'backend.authorized': 'Non sei autorizzato a fare l upload dei file.',
	'backend.path': 'La cartella degli upload non esiste. Contattare il webmaster.',
	'backend.exists': 'La cartella specificata per gli upload esiste già. Contattare il webmaster.',
	'backend.mime': 'Il tipo del file specificato non è consentito.',
	'backend.extension': 'Il tipo di file che si vuole caricare non è consentito o è sconosciuto.',
	'backend.size': 'La dimensione del file è troppo grande per essere processato. Ricarica un file con dimensioni ridotte.',
	'backend.partial': 'Il file è stato parzialmente caricato. Per favore, prova a ricaricarlo.',
	'backend.nofile': 'Non è stato specificato alcun file da caricare.',
	'backend.default': 'Mi spiace, l operazione non è andata a buon fine.',
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
		unknown: 'Errore sconosciuto',
		sizeLimitMin: 'Non puoi caricare "<em>${name}</em>" (${size}), la dimensione minima del file è <strong>${size_min}</strong>!',
		sizeLimitMax: 'Non puoi caricare "<em>${name}</em>" (${size}), la dimensione massima del file è <strong>${size_max}</strong>!',
		mod_security: 'No response was given from the uploader, this may mean that "mod_security" is active on the server and one of the rules in mod_security has cancelled this request.  If you can not disable mod_security, you may need to use the NoFlash Uploader.'
	},

	flash: {
		hidden: 'To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).',
		disabled: 'To enable the embedded uploader, enable the blocked Flash movie  and refresh (see Flashblock).',
		flash: 'In order to upload files you need to install <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe Flash</a>.'
	},

	resizeImages: 'Ridimensiona immagini grandi',

	serialize: 'Salva galleria',
	gallery: {
		text: 'Titolo immagine',
		save: 'Salva',
		remove: 'Rimuovi dalla galleria',
		drag: 'Sposta gli oggetti qui per creare una galleria...'
	}
};