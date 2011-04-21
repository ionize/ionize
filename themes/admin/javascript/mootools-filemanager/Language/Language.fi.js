/*
Script: Language.fi.js
	MooTools FileManager - Language Strings in Finnish

Translation:
	[Jabis Sevón](http://pumppumedia.com)
*/

FileManager.Language.fi = {
	more: 'Lisätiedot',
	width: 'Leveys:',
	height: 'Korkeus:',
	
	ok: 'Ok',
	open: 'Valitse tiedosto',
	upload: 'Lähetä',
	create: 'Luo kansio',
	createdir: 'Kansion nimi:',
	cancel: 'Peruuta',
	error: 'Virhe',
	
	information: 'Tiedot',
	type: 'Tyyppi:',
	size: 'Koko:',
	dir: 'Polku:',
	modified: 'Viimeksi muokattu:',
	preview: 'Esikatselu',
	close: 'Sulje',
	destroy: 'Poista',
	destroyfile: 'Haluatko varmasti poistaa tiedoston?',
	
	rename: 'Nimeä uudelleen',
	renamefile: 'Syötä tiedoston uusi nimi:',
	
	download: 'Lataa',
	nopreview: '<i>Esikatselua ei voida näyttää</i>',
	
	title: 'Kappaleen nimi:',
	artist: 'Artisti:',
	album: 'Albumi:',
	length: 'Pituus:',
	bitrate: 'Bitrate:',
	
	deselect: 'Poista valinta',
	
	nodestroy: 'Tiedostojen poisto otettu käytöstä.',
	
	'backend.disabled': 'Tiedostojen lähetys otettu käytöstä.',
	'backend.authorized': 'Sinulla ei ole oikeuksia tiedostojen lähettämiseen.',
	'backend.path': 'Määritettyä kansiota ei löydy. Ole hyvä ja ota yhteyttä sivuston ylläpitäjään.',
	'backend.exists': 'Tiedosto on jo olemassa - siirto peruttu. Ole hyvä ja ota yhteyttä sivuston ylläpitäjään.',
	'backend.mime': 'Tiedostotyyppi ei ole sallittujen listalla - siirto peruttu.',
	'backend.extension': 'Tiedostopääte tuntematon, tai ei sallittujen listalla - siirto peruttu.',
	'backend.size': 'Tiedostokoko liian suuri palvelimelle. Ole hyvä ja siirrä pienempiä tiedostoja.',
	'backend.partial': 'Tiedonsiirto onnistui vain osittain - siirto epäonnistui. Ole hyvä ja siirrä tiedosto uudestaan',
	'backend.nofile': 'Tiedostoa ei määritetty.',
	'backend.default': 'Tiedonsiirto epäonnistui tunnistamattomasta syystä.',
	
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
		unknown: 'Tunnistamaton virhe',
		duplicate: 'Et voi lisätä seuraavaa tiedostoa: "<em>${name}</em>" (${size}), koska se on jo siirtolistalla!',
		sizeLimitMin: 'Et voi lisätä seuraavaa tiedostoa: "<em>${name}</em>" (${size}). Tiedostojen minimikoko on <strong>${size_min}</strong>!',
		sizeLimitMax: 'Et voi lisätä seuraavaa tiedostoa: "<em>${name}</em>" (${size}). Tiedostojen maksimikoko on <strong>${size_max}</strong>!'
	},
	
	flash: {
		hidden: null,
		disabled: null,
		flash: 'Käyttääksesi FileManageria, tarvitset Adobe Flash Playerin. <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Lataa tästä</a>.'
	},
	
	resizeImages: 'Pienennä liian suuret kuvat automaattisesti siirron yhteydessä'
};
