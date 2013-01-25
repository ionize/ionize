/*
Script: Language.fr.js
	MooTools Filemanager - Language Strings in French

Translation:
	[Samuel Sanchez](http://www.kromack.com)
*/

Filemanager.Language.fr = {
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
	rn_mv_cp: 'Renommer/Déplacer/Copier',

	download: 'Télécharger',
	nopreview: '<i>Aucun aperçu disponible</i>',

	title: 'Titre :',
	artist: 'Artiste :',
	album: 'Album :',
	length: 'Durée :',
	bitrate: 'Débit :',

	deselect: 'Désélectionner',

	nodestroy: 'La suppression de fichier a été désactivée sur ce serveur.',

	toggle_side_boxes: 'Vue miniatures',
	toggle_side_list: 'Vue liste',
	show_dir_thumb_gallery: 'Voir les miniatures dans le panneau de prévisualisation',
	drag_n_drop: 'Le glisser / déposé est actif pour ce dossier',
	drag_n_drop_disabled: 'Le glisser / déposé est temporairement inactif pour ce dossier',
	goto_page: 'Aller à la page',

	'backend.disabled': 'L\'upload de fichier a été désactivé sur ce serveur.',
	'backend.authorized': 'Vous n\'êtes pas authentifié et ne pouvez pas téléverser de fichier.',
	'backend.path': 'Le répertoire d\'upload spécifié n\'existe pas. Merci de contacter l\'administrateur de ce site Internet.',
	'backend.exists': 'Le chemin de d\'upload spécifié existe déjà. Merci de contacter l\'administrateur de ce site Internet.',
	'backend.mime': 'Le type de fichier spécifié n\'est pas autorisé.',
	'backend.extension': 'Le fichier uploadé a une extension inconnue ou interdite.',
	'backend.size': 'La taille de votre fichier est trop grande pour être uploadée sur ce serveur. Merci de sélectionner un fichier moins lourd.',
	'backend.partial': 'Le fichier a été partiellement uploadé, merci de recommencer l\'opération.',
	'backend.nofile': 'Aucun fichier n\'a été spécifié.',
	'backend.default': 'Une erreur s\'est produite.',
	'backend.path_not_writable': 'Problème de droit d\'écriture sur le dossier.',
	'backend.filename_maybe_too_large': 'Le chemin ou nom de fichier est probablement trop long. Réessayez avec un nom plus court.',
	'backend.fmt_not_allowed': 'Le format ou nom de fichier que vous souhaitez uploader n\'est pas autorisé.',
	'backend.read_error': 'Cannot read / download the specified file.',
	'backend.unidentified_error': 'Une erreur inconnue est survenue lors de la communication avec le serveur.',

	'backend.nonewfile': 'Le nouveau nom de fichier est manquant.',
	'backend.corrupt_img': 'Ce fichier est corrompu ou n\'est pas une image : ', // path
	'backend.resize_inerr': 'Ce fichier ne peut être redimensionné (erreur interne).',
	'backend.copy_failed': 'Une erreur est survenue lors de la copie de ce fichier / dossier : ', // oldlocalpath : newlocalpath
	'backend.delete_cache_entries_failed': 'Une erreur est survenue lors de la suppression du cache (thumbnails, metadata)',
	'backend.mkdir_failed': 'Une erreur est survenue lors de la création du dossier : ', // path
	'backend.move_failed': 'Une erreur est survenue lors du déplacement / renommage du fichier / dossier : ', // oldlocalpath : newlocalpath
	'backend.path_tampering': 'Altération de chemin détectée.',
	'backend.realpath_failed': 'Impossible de traduire le chemin de ce fichier en emplacement valide : ', // $path
	'backend.unlink_failed': 'Une erreur est survenue lors de la suppression du fichier / dossier : ',  // path

	// Image.class.php:
	'backend.process_nofile': 'Le chemin du fichier à traiter n\'est pas valide.',
	'backend.imagecreatetruecolor_failed': 'Le traitement de l\'image a échoué : GD imagecreatetruecolor().',
	'backend.imagealphablending_failed': 'Le traitement de l\'image a échoué : Blending alpha impossible.',
	'backend.imageallocalpha50pctgrey_failed': 'Le traitement de l\'image a échoué : Mémoire insuffisante pour alpha channel et fond 50%.',
	'backend.imagecolorallocatealpha_failed': 'Le traitement de l\'image a échoué : Mémoire insuffisante pour alpha channel.',
	'backend.imagerotate_failed': 'Le traitement de l\'image a échoué : GD imagerotate().',
	'backend.imagecopyresampled_failed': 'Le traitement de l\'image a échoué : GD imagecopyresampled(). Résolution image : ', /* x * y */
	'backend.imagecopy_failed': 'Le traitement de l\'image a échoué : GD imagecopy().',
	'backend.imageflip_failed': 'Le traitement de l\'image a échoué : Flip impossible.',
	'backend.imagejpeg_failed': 'Le traitement de l\'image a échoué : GD imagejpeg().',
	'backend.imagepng_failed': 'Le traitement de l\'image a échoué : GD imagepng().',
	'backend.imagegif_failed': 'Le traitement de l\'image a échoué : GD imagegif().',
	'backend.imagecreate_failed': 'Le traitement de l\'image a échoué : GD imagecreate().',
	'backend.cvt2truecolor_failed': 'conversion to True Color failed. Résolution image : ', /* x * y */
	'backend.no_imageinfo': 'Image corrompu ou fichier non image.',
	'backend.img_will_not_fit': 'Erreur serveur: Mémoire insuffisante; Minimum requis : ', /* XXX MBytes */
	'backend.unsupported_imgfmt': 'Format non supporté : ',    /* jpeg/png/gif/... */

	/* FU */
	uploader: {
		unknown: 'Erreur inconnue',
		duplicate: 'Vous ne pouvez pas ajouter "<em>${name}</em>" (${size}), car l\'élément est déjà ajoutée !',
		sizeLimitMin: 'Vous ne pouvez pas ajouter "<em>${name}</em>" (${size}), la taille minimale des fichiers est de <strong>${size_min}</strong>!',
		sizeLimitMax: 'Vous ne pouvez pas ajouter "<em>${name}</em>" (${size}), la taille maximale des fichiers est de <strong>${size_max}</strong>!',
		mod_security: 'Aucune réponse de l\'uploadeur. "mod_security" est peut-être actif sur le serveur et un des rôles a annulé cette requête.  Si vous ne pouvez désactiver mod_security, il vous faut utiliser NoFlash Uploader.'
	},

	flash: {
		hidden: null,
		disabled: null,
		flash: 'Dans le but d\'uploader des fichiers, vous devez installer <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe Flash</a>.'
	},

	resizeImages: 'Redimen. après upload',

	serialize: 'Sauver la galerie',
	gallery: {
		text: 'Texte de l\'image',
		save: 'Sauver',
		remove: 'Retirer de la galerie',
		drag: 'Glissez des images ici pour créer une galerie...'
	}
};