/*
Script: Language.da.js
	MooTools FileManager - Language Strings in Danish

Translation:
	Jan Ebsen
*/

FileManager.Language.da = {
	more: 'Detaljer',
	width: 'Bredde:',
	height: 'Højde:',
	
	ok: 'Ok',
	open: 'Vælg fil',
	upload: 'Upload',
	create: 'Opret mappe',
	createdir: 'Angiv venligst mappe navn:',
	cancel: 'Anuller',
	error: 'Fejl',
	
	information: 'Information',
	type: 'Type:',
	size: 'Størrelse:',
	dir: 'Sti:',
	modified: 'Sidst ændret:',
	preview: 'Miniature',
	close: 'Luk',
	destroy: 'Slet',
	destroyfile: 'Er du sikker på du vil slette denne fil?',
	
	rename: 'Omdøb',
	renamefile: 'Skriv nyt filnavn:',
	
	download: 'Download',
	nopreview: '<i>Ingen miniature tilgængelig</i>',
	
	title: 'Titel:',
	artist: 'Kunstner:',
	album: 'Album:',
	length: 'Længde:',
	bitrate: 'Bitrate:',
	
	deselect: 'Fravælg',
	
	nodestroy: 'Det er ikke muligt at slette filer på serveren.',
	
	'backend.disabled': 'Det er ikke muligt at uploade filer på serveren.',
	'backend.authorized': 'Du har ikke rettigheder til at uploade filer.',
	'backend.path': 'Upload mappen findes ikke. Kontakt venligst sidens administrator.',
	'backend.exists': 'Upload mappen findes allerede. Kontakt venligst sidens administrator.',
	'backend.mime': 'Fil-typen er ikke tilladt.',
	'backend.extension': 'Filen er af en ukendt, eller ulovlig type.',
	'backend.size': 'Filen er for stor, upload venligst en mindre fil.',
	'backend.partial': 'Filen blev kun delvist uploaded, prøv venligst igen.',
	'backend.nofile': 'Der er ikke angivet nogen fil til upload.',
	'backend.default': 'Noget gik galt med fil-uploaderen.',
	
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
		unknown: 'Ukendt fejl',
		duplicate: 'Du kan ikke tilføje "<em>${name}</em>" (${size}), den er allerede tilføjet!',
		sizeLimitMin: 'Du kan ikke tilføje "<em>${name}</em>" (${size}), mindst tilladte filstørrelse er <strong>${size_min}</strong>!',
		sizeLimitMax: 'Du kan ikke tilføje "<em>${name}</em>" (${size}), højst tilladte filstørrelse er <strong>${size_max}</strong>!'
	},
	
	flash: {
		hidden: null,
		disabled: null,
		flash: 'For at uploade filer skal du installere <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe Flash</a>.'
	},
	
	resizeImages: 'Scaler store billeder ved upload'
};