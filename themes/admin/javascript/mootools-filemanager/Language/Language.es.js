/*
Script: Language.es.js
	MooTools FileManager - Language Strings in Spanish

Translation:
	[Sergio Rubio](http://rubiojr.netcorex.org)
*/

FileManager.Language.es = {
	more: 'Detalles',
	width: 'Anchura:',
	height: 'Altura:',
	
	ok: 'Ok',
	open: 'Seleccionar Fichero',
	upload: 'Subir ficheros',
	create: 'Crear carpeta',
	createdir: 'Especifica el nombre de la carpeta:',
	cancel: 'Cancelar',
	
	information: 'Información',
	type: 'Tipo:',
	size: 'Tamaño:',
	dir: 'Ruta:',
	modified: 'Última modificación:',
	preview: 'Previsualización',
	close: 'Cerrar',
	destroy: 'Borrar',
	destroyfile: '¿Seguro que deseas borrar el fichero?',
	
	rename: 'Renombrar',
	renamefile: 'Especifica un nuevo nombre para el fichero:',
	
	download: 'Descargar',
	nopreview: '<i>No hay previsualizacion disponible</i>',
	
	title: 'Título:',
	artist: 'Artista:',
	album: 'Album:',
	length: 'Duración:',
	bitrate: 'Bitrate:',
	
	video_codec: 'Codec:',
	
	deselect: 'Desmarcar',
	
	nodestroy: 'El borrado de ficheros ha sido deshabilitado.',
	
	notwritable: 'El directorio base de los medios de comunicación no se puede escribir. Compruebe los permisos.',
	
	'upload.disabled': 'La carga de archivos ha sido deshabilitada.',
	'upload.authenticated': 'Necesitas autorización para subir ficheros.',
	'upload.path': 'La carpeta destino especificada no existe. Contacta con el administrador del sitio web.',
	'upload.exists': 'El la ruta destino ya existe. Por favor, contacta con el administrador del sitio web.',
	'upload.mime': 'No se permite subir el tipo de fichero especificado.',
	'upload.extension': 'El fichero subido tienen una extensión no permitida o desconocida.',
	'upload.size': 'El tamaño del fichero que intentas subir es demasiado grande para ser procesado por el servidor. Por favor, sube un fichero mas pequeño.',
	'upload.partial': 'El fichero se ha subido parcialmente, por favor, sube el fichero de nuevo.',
	'upload.nofile': 'No se especificó el fichero a subir.',
	'upload.default': 'Algo falló durante la carga del fichero.',
	
	/* FU */
	uploader: {
		unknown: 'Error desconocido',
		duplicate: 'No se puede subir "<em>${name}</em>" (${size}), ya ha sido añadido!',
		sizeLimitMin: 'No se puede subir "<em>${name}</em>" (${size}), el tamaño mínimo de fichero es <strong>${size_min}</strong>!',
		sizeLimitMax: 'No se puede subir "<em>${name}</em>" (${size}), el tamaño máximo de fichero es <strong>${size_max}</strong>!'
	},
	
	flash: {
		hidden: null,
		disabled: null,
		flash: 'Para poder subir ficheros necesitas instalar <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe Flash</a>.'
	},
	
	resizeImages: 'Redimensionar las imágenes grandes al subirlas'
};
