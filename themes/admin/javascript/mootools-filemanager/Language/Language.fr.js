/*
Script: Language.fr.js
	MooTools FileManager - Language Strings in French

Translation:
	[Samuel Sanchez](http://www.kromack.com)
*/

FileManager.Language.fr = {
	more: 'Détails',
	width: 'Largeur :',
	height: 'Hauteur :',

	ok: 'Ok',
	open: 'Sélectionner un fichier',
	upload: 'Téléverser',
	create: 'Créer un dossier',
	createdir: 'Merci de spécifier un nom de dossier :',
	cancel: 'Annuler',
	error: 'Erreur',

	information: 'Informations',
	type: 'Type :',
	size: 'Taille :',
	dir: 'Chemin :',
	modified: 'Modifié le :',
	preview: 'Aperçu',
	close: 'Fermer',
	destroy: 'Supprimer',
	destroyfile: 'Voulez-vous vraiment supprimer ce fichier ?',

	rename: 'Renommer',
	renamefile: 'Merci de spécifier un nouveau nom de fichier :',

	download: 'Télécharger',
	nopreview: '<i>Aucun aperçu disponible</i>',

	title: 'Titre :',
	artist: 'Artiste :',
	album: 'Album :',
	length: 'Durée :',
	bitrate: 'Débit :',

	deselect: 'Désélectionner',

	nodestroy: 'La suppression de fichier a été désactivée sur ce serveur.',

	toggle_side_boxes: 'Thumbnail view',
	toggle_side_list: 'List view',
	show_dir_thumb_gallery: 'Show thumbnails of the files in the preview pane',
	drag_n_drop: 'Drag & drop has been enabled for this directory',
	drag_n_drop_disabled: 'Drag & drop has been temporarily disabled for this directory',
	goto_page: 'Go to page',

	'backend.disabled': 'Le téléversement de fichier a été désactivé sur ce serveur.',
	'backend.authorized': 'Vous n\'êtes pas authentifié et ne pouvez pas téléverser de fichier.',
	'backend.path': 'Le répertoire de téléversement spécifié n\'existe pas. Merci de contacter l\'administrateur de ce site Internet.',
	'backend.exists': 'Le chemin de téléversement spécifié existe déjà. Merci de contacter l\'administrateur de ce site Internet.',
	'backend.mime': 'Le type de fichier spécifié n\'est pas autorisé.',
	'backend.extension': 'Le fichier téléversé a une extension inconnue ou interdite.',
	'backend.size': 'La taille de votre fichier est trop grande pour être téléversée sur ce serveur. Merci de sélectionner un fichier moins lourd.',
	'backend.partial': 'Le fichier a été partiellement téléversé, merci de recommencer l\'opération.',
	'backend.nofile': 'Aucun fichier n\'a été spécifié.',
	'backend.default': 'Une erreur s\'est produite.',
	'backend.path_not_writable': 'You do not have write/upload permissions for this directory.',
	'backend.filename_maybe_too_large': 'The filename/path is probably too long for the server filesystem. Please retry with a shorter file name.',
	'backend.fmt_not_allowed': 'You are not allowed to upload this file format/name.',
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
		unknown: 'Erreur inconnue',
		duplicate: 'Vous ne pouvez pas ajouter "<em>${name}</em>" (${size}), car l\'élément est déjà ajoutée !',
		sizeLimitMin: 'Vous ne pouvez pas ajouter "<em>${name}</em>" (${size}), la taille minimale des fichiers est de <strong>${size_min}</strong>!',
		sizeLimitMax: 'Vous ne pouvez pas ajouter "<em>${name}</em>" (${size}), la taille maximale des fichiers est de <strong>${size_max}</strong>!',
		mod_security: 'No response was given from the uploader, this may mean that "mod_security" is active on the server and one of the rules in mod_security has cancelled this request.  If you can not disable mod_security, you may need to use the NoFlash Uploader.'
	},

	flash: {
		hidden: null,
		disabled: null,
		flash: 'Dans le but de téléverser des fichiers, vous devez installer <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe Flash</a>.'
	},

	resizeImages: 'Redimensionner les images pendant le téléversement',

	serialize: 'Save gallery',
	gallery: {
		text: 'Image caption',
		save: 'Save',
		remove: 'Remove from gallery',
		drag: 'Drag items here to create a gallery...'
	}
};