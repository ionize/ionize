/*
Script: Language.se.js
	MooTools FileManager - Language Strings in Swedish

Translation:
	[Marcus *xintron* Carlsson](http://xintron.se)
*/

FileManager.Language.se = {
	more: 'Detaljer',
	width: 'Bredd:',
	height: 'Höjd:',
	
	ok: 'Ok',
	open: 'Välj fil',
	upload: 'Ladda upp',
	create: 'Skapa mapp',
	createdir: 'Vänligen ange ett mapp-namn:',
	cancel: 'Avbryt',
	error: 'Fel',
	
	information: 'Information',
	type: 'Typ:',
	size: 'Storlek:',
	dir: 'Sökväg:',
	modified: 'Senast ändad:',
	preview: 'Förhandsgranska',
	close: 'Stäng',
	destroy: 'Ta bort',
	destroyfile: 'Är du säker på att du vill ta bort filen?',
	
	rename: 'Döp om',
	renamefile: 'Vänligen ange ett nytt filnamn:',
	
	download: 'Ladda ner',
	nopreview: '<i>Ingen förhandsgranskning tillgänglig</i>',
	
	title: 'Titel:',
	artist: 'Artist:',
	album: 'Album:',
	length: 'Längd:',
	bitrate: 'Bitrate:',
	
	deselect: 'Avmarkera',
	
	nodestroy: 'Funktionen ta bort filer är avstängd på denna server.',
	
	'backend.disabled': 'Uppladdning är avstängt på denna server.',
	'backend.authorized': 'Du har inte behörighet att ladda upp filer.',
	'backend.path': 'Den angivna uppladdnings-mappen existerar inte. Vänligen kontakta serveradministratören.',
	'backend.exists': 'Den angivna uppladdnings-mappen existerar redan. Vänligen kontakta serveradministratören.',
	'backend.mime': 'Denna filtyp accepteras inte på denna server.',
	'backend.extension': 'Den uppladdade filen har en okänd eller förbjuden filändelse.',
	'backend.size': 'Filen är för stor för denna server. Vänligen ladda upp en mindre fil.',
	'backend.partial': 'Ett fel uppstod och hela filen kunde inte laddas upp. Vänligen försök igen.',
	'backend.nofile': 'Du måste välja en fil att ladda upp.',
	'backend.default': 'Ett fel inträffade under uppladdningen.',
	
	'backend.nonewfile': 'A new name for the file to be moved / copied is missing.',
	'backend.corrupt_img': 'This file is a not a image or a corrupt file: ', // path
	'backend.copy_failed': 'An error occurred while copying the file / directory: ', // oldlocalpath : newlocalpath
	'backend.delete_thumbnail_failed': 'An error occurred when attempting to delete the image thumbnail',
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
	'backend.imagecopyresampled_failed': 'The image processing unit failed: GD imagecopyresampled() failed.',
	'backend.imagecopy_failed': 'The image processing unit failed: GD imagecopy() failed.',
	'backend.imageflip_failed': 'The image processing unit failed: cannot flip the image.',
	'backend.imagejpeg_failed': 'The image processing unit failed: GD imagejpeg() failed.',
	'backend.imagepng_failed': 'The image processing unit failed: GD imagepng() failed.',
	'backend.imagegif_failed': 'The image processing unit failed: GD imagegif() failed.',
	'backend.imagecreate_failed': 'The image processing unit failed: GD imagecreate() failed.',
	'backend.cvt2truecolor_failed': 'conversion to True Color failed. Image resolution: ', /* x * y */
	'backend.no_imageinfo': 'Corrupt image or not an image file at all.',
	'backend.img_will_not_fit': 'image does not fit in available RAM; minimum required (estimate): ', /* XXX MBytes */
	'backend.unsupported_imgfmt': 'unsupported image format: ',    /* jpeg/png/gif/... */
	
	/* FU */
	uploader: {
		unknown: 'Okänt fel',
		duplicate: 'Du kan inte ladda upp "<em>${name}</em>" (${size}), filen existerar redan!',
		sizeLimitMin: 'Du kan inte ladda upp "<em>${name}</em>" (${size}), minsta storlek som accepteras är <strong>${size_min}</strong>!',
		sizeLimitMax: 'Du kan inte ladda upp "<em>${name}</em>" (${size}), filens storlek får inte överstiga <strong>${size_max}</strong>!'
	},
	
	flash: {
		hidden: null,
		disabled: null,
		flash: 'För att kunna ladda upp filer behöver du ha <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe Flash</a> installerat.'
	},
	
	resizeImages: 'Ändra storleken på bilden under uppladdningen'
};
