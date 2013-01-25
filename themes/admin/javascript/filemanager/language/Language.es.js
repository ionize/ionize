/*
Script: Language.es.js
	MooTools Filemanager - Language Strings in Spanish

Translation:
	[Sergio Rubio](http://rubiojr.netcorex.org)
*/

Filemanager.Language.es = {
	more: 'Detalles',
	width: 'Anchura:',
	height: 'Altura:',

	ok: 'Ok',
	open: 'Seleccionar Fichero',
	upload: 'Subir ficheros',
	create: 'Crear carpeta',
	createdir: 'Especifica el nombre de la carpeta:',
	cancel: 'Cancelar',
	error: 'Error',

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
	rn_mv_cp: 'Rename/Move/Copy',

	download: 'Descargar',
	nopreview: '<i>No hay previsualizacion disponible</i>',

	title: 'Título:',
	artist: 'Artista:',
	album: 'Album:',
	length: 'Duración:',
	bitrate: 'Bitrate:',

	deselect: 'Desmarcar',

	nodestroy: 'El borrado de ficheros ha sido deshabilitado.',

	toggle_side_boxes: 'Thumbnail view',
	toggle_side_list: 'List view',
	show_dir_thumb_gallery: 'Show thumbnails of the files in the preview pane',
	drag_n_drop: 'Drag & drop has been enabled for this directory',
	drag_n_drop_disabled: 'Drag & drop has been temporarily disabled for this directory',
	goto_page: 'Go to page',

	'backend.disabled': 'La carga de archivos ha sido deshabilitada.',
	'backend.authorized': 'Necesitas autorización para subir ficheros.',
	'backend.path': 'La carpeta destino especificada no existe. Contacta con el administrador del sitio web.',
	'backend.exists': 'El la ruta destino ya existe. Por favor, contacta con el administrador del sitio web.',
	'backend.mime': 'No se permite subir el tipo de fichero especificado.',
	'backend.extension': 'El fichero subido tienen una extensión no permitida o desconocida.',
	'backend.size': 'El tamaño del fichero que intentas subir es demasiado grande para ser procesado por el servidor. Por favor, sube un fichero mas pequeño.',
	'backend.partial': 'El fichero se ha subido parcialmente, por favor, sube el fichero de nuevo.',
	'backend.nofile': 'No se especificó el fichero a subir.',
	'backend.default': 'Algo falló durante la carga del fichero.',
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
	'backend.img_will_not_fit': 'image does not fit in available RAM; minimum required (estimate): ', /* XXX MBytes */
	'backend.unsupported_imgfmt': 'unsupported image format: ',    /* jpeg/png/gif/... */

	/* FU */
	uploader: {
		unknown: 'Error desconocido',
		duplicate: 'No se puede subir "<em>${name}</em>" (${size}), ya ha sido añadido!',
		sizeLimitMin: 'No se puede subir "<em>${name}</em>" (${size}), el tamaño mínimo de fichero es <strong>${size_min}</strong>!',
		sizeLimitMax: 'No se puede subir "<em>${name}</em>" (${size}), el tamaño máximo de fichero es <strong>${size_max}</strong>!',
		mod_security: 'No response was given from the uploader, this may mean that "mod_security" is active on the server and one of the rules in mod_security has cancelled this request.  If you can not disable mod_security, you may need to use the NoFlash Uploader.'
	},

	flash: {
		hidden: null,
		disabled: null,
		flash: 'Para poder subir ficheros necesitas instalar <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe Flash</a>.'
	},

	resizeImages: 'Redimensionar las imágenes grandes al subirlas',

	serialize: 'Save gallery',
	gallery: {
		text: 'Image caption',
		save: 'Save',
		remove: 'Remove from gallery',
		drag: 'Drag items here to create a gallery...'
	}
};
