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
	
	video_codec: 'Codec:',
	
	deselect: 'Poista valinta',
	
	nodestroy: 'Tiedostojen poisto otettu käytöstä.',
	
	notwritable: 'Pohja media dir ei ole kirjoitettavissa. Tarkista käyttöoikeudet.',
	
	'upload.disabled': 'Tiedostojen lähetys otettu käytöstä.',
	'upload.authenticated': 'Sinulla ei ole oikeuksia tiedostojen lähettämiseen.',
	'upload.path': 'Määritettyä kansiota ei löydy. Ole hyvä ja ota yhteyttä sivuston ylläpitäjään.',
	'upload.exists': 'Tiedosto on jo olemassa - siirto peruttu. Ole hyvä ja ota yhteyttä sivuston ylläpitäjään.',
	'upload.mime': 'Tiedostotyyppi ei ole sallittujen listalla - siirto peruttu.',
	'upload.extension': 'Tiedostopääte tuntematon, tai ei sallittujen listalla - siirto peruttu.',
	'upload.size': 'Tiedostokoko liian suuri palvelimelle. Ole hyvä ja siirrä pienempiä tiedostoja.',
	'upload.partial': 'Tiedonsiirto onnistui vain osittain - siirto epäonnistui. Ole hyvä ja siirrä tiedosto uudestaan',
	'upload.nofile': 'Tiedostoa ei määritetty.',
	'upload.default': 'Tiedonsiirto epäonnistui tunnistamattomasta syystä.',
	
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
