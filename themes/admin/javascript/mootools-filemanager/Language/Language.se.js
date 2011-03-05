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
	
	video_codec: 'Codec:',
	
	deselect: 'Avmarkera',
	
	nodestroy: 'Funktionen ta bort filer är avstängd på denna server.',
	
	notwritable: 'Basen media dir är inte skrivbar. Kontrollera behörigheterna.',
	
	'upload.disabled': 'Uppladdning är avstängt på denna server.',
	'upload.authenticated': 'Du har inte behörighet att ladda upp filer.',
	'upload.path': 'Den angivna uppladdnings-mappen existerar inte. Vänligen kontakta serveradministratören.',
	'upload.exists': 'Den angivna uppladdnings-mappen existerar redan. Vänligen kontakta serveradministratören.',
	'upload.mime': 'Denna filtyp accepteras inte på denna server.',
	'upload.extension': 'Den uppladdade filen har en okänd eller förbjuden filändelse.',
	'upload.size': 'Filen är för stor för denna server. Vänligen ladda upp en mindre fil.',
	'upload.partial': 'Ett fel uppstod och hela filen kunde inte laddas upp. Vänligen försök igen.',
	'upload.nofile': 'Du måste välja en fil att ladda upp.',
	'upload.default': 'Ett fel inträffade under uppladdningen.',
	
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
