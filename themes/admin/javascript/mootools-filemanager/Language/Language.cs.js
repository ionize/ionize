/*
Script: Language.cs.js
	MooTools FileManager - Language Strings in Czech

Translation:
	[Matěj Grabovský](http://mgrabovsky.is-game.com)
*/

FileManager.Language.cs = {
	more: 'Podrobnosti',
	width: 'Šířka:',
	height: 'Výška:',
	
	ok: 'Ok',
	open: 'Vybrat soubor',
	upload: 'Nahrát',
	create: 'Vytvořit složku',
	createdir: 'Prosím zadejte název složky:',
	cancel: 'Storno',
	error: 'Chyba',
	
	information: 'Informace',
	type: 'Typ:',
	size: 'Velikost:',
	dir: 'Cesta:',
	modified: 'Naposledy změněno:',
	preview: 'Náhled',
	close: 'Zavřít',
	destroy: 'Smazat',
	destroyfile: 'Určitě chcete smazat tento soubor?',
	
	rename: 'Přejmenovat',
	renamefile: 'Prosím zadejte název nového souboru:',
	
	download: 'Stáhnout',
	nopreview: '<i>Náhled není dostupný</i>',
	
	title: 'Název:',
	artist: 'Umělec:',
	album: 'Album:',
	length: 'Délka:',
	bitrate: 'Přenosová rychlost:',
	
	deselect: 'Odstranit z výběru',
	
	nodestroy: 'Mazání souborů je na tomto serveru zakázáno.',
	
	'backend.disabled': 'Nahrávání souborů je na tomto serveru zakázáno.',
	'backend.authorized': 'Nemáte právo nahrávat soubory.',
	'backend.path': 'Specifikovaná složka pro nahrávání neexistuje. Prosím kontaktujte správce těchto stránek.',
	'backend.exists': 'Specifikovaný soubor již existuje. Prosím kontaktujte správce těchto stránek.',
	'backend.mime': 'Specifikovaný typ souboru není povolen.',
	'backend.extension': 'Nahrávaný soubor má neznámou nebo zakázanou příponu.',
	'backend.size': 'Velikost nahrávaného souboru je přílíš velká. Prosím nahrajte menší soubor.',
	'backend.partial': 'Nahrávaný soubor byl nahrán jen zčásti. Prosím nahrajte ho znovu.',
	'backend.nofile': 'Nebyl vybrán žádný soubor pro nahrání.',
	'backend.default': 'Něco se nepovedlo při nahrávání souboru.',
	
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
		unknown: 'Neznámá chyba',
		duplicate: 'Nelze přidat soubor „<em>${name}</em>“ (${size}), byl již přidán!',
		sizeLimitMin: 'Nelze přidat soubor „<em>${name}</em>“ (${size}), minimální povolená velikost souboru je <strong>${size_min}</strong>!',
		sizeLimitMax: 'Nelze přidat soubor „<em>${name}</em>“ (${size}), maximální povolená velikost souboru je <strong>${size_max}</strong>!'
	},
	
	flash: {
		hidden: null,
		disabled: null,
		flash: 'Pokud chcete nahrávat soubory, musíte mít nainstalovaný <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe Flash</a>'
	},
	
	resizeImages: 'Změnšit velké obrázky při nahrávání'
};