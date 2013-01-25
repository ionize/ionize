/*
Script: Language.br.js
	MooTools Filemanager - Language Strings in Brazilian Portuguese

Translation:
	[Fabiana Pires](http://twitter.com/nervosinha)
*/

Filemanager.Language.br = {
	more: 'Detalhes',
	width: 'Largura:',
	height: 'Altura:',

	ok: 'Ok',
	open: 'Selecione o arquivo',
	upload: 'Upload',
	create: 'Criar pasta',
	createdir: 'Por favor especifique o nome da pasta:',
	cancel: 'Cancelar',
	error: 'Erro',

	information: 'Informa��o',
	type: 'Tipo:',
	size: 'Tamanho:',
	dir: 'Caminho:',
	modified: '�ltima modifica��o:',
	preview: 'Pr�-visualiza��o',
	close: 'Fechar',
	destroy: 'Apagar',
	destroyfile: 'Tem certeza que deseja apagar este arquivo?',

	rename: 'Renomear',
	renamefile: 'Por favor especifique o novo nome do arquivo:',
	rn_mv_cp: 'Rename/Move/Copy',

	download: 'Download',
	nopreview: '<i>Pr�-visualiza��o indispon�vel</i>',

	title: 'T�tulo:',
	artist: 'Artista:',
	album: 'Album:',
	length: 'Tamanho:',
	bitrate: 'Bitrate:',

	deselect: 'Desfazer',

	nodestroy: 'Apagar arquivos est� desabilitado neste servidor.',

	toggle_side_boxes: 'Thumbnail view',
	toggle_side_list: 'List view',
	show_dir_thumb_gallery: 'Show thumbnails of the files in the preview pane',
	drag_n_drop: 'Drag & drop has been enabled for this directory',
	drag_n_drop_disabled: 'Drag & drop has been temporarily disabled for this directory',
	goto_page: 'Go to page',

	'backend.disabled': 'N�o � permitido enviar arquivos neste servidor.',
	'backend.authorized': 'Voc� n�o est� autenticado para enviar arquivos neste servidor.',
	'backend.path': 'A pasta especificada n�o existe. Por favor contate o administrador do site.',
	'backend.exists': 'A pasta especificada j� existe. Por favor contate o administrador do site.',
	'backend.mime': 'O tipo de arquivo especificado n�o � permitido.',
	'backend.extension': 'O arquivo enviado � de tipo desconhecido ou proibido.',
	'backend.size': 'O tamanho do arquivo enviado � muito grande para ser processado neste servidor. Por favor, envie um arquivo menor.',
	'backend.partial': 'O arquivo enviado foi corrompido, por favor envie o arquivo novamente.',
	'backend.nofile': 'N�o existe arquivo especificado para ser enviado.',
	'backend.default': 'Erro no envio do arquivo.',
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
	'backend.img_will_not_fit': 'Server error: image does not fit in available RAM; minimum required (estimate): ', /* XXX MBytes */
	'backend.unsupported_imgfmt': 'unsupported image format: ',    /* jpeg/png/gif/... */

	/* FU */
	uploader: {
		unknown: 'Erro desconhecido',
		sizeLimitMin: 'N�o � permitido anexar "<em>${name}</em>" (${size}), o tamanho m�nimo para o arquivo � de <strong>${size_min}</strong>!',
		sizeLimitMax: 'N�o � permitido anexar "<em>${name}</em>" (${size}), o tamanho m�ximo para o arquivo � de <strong>${size_max}</strong>!',
		mod_security: 'No response was given from the uploader, this may mean that "mod_security" is active on the server and one of the rules in mod_security has cancelled this request.  If you can not disable mod_security, you may need to use the NoFlash Uploader.'
	},

	flash: {
		hidden: 'Para habilitar o uploader, desbloqueie a fun��o em seu browser e recarregue a p�gina (veja Adblock).',
		disabled: 'Para habilitar o uploader, habilite o arquivo Flash e recarregue a p�gina (veja Flashblock).',
		flash: 'Para enviar arquivos � necess�rio instalar o <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe Flash Player</a>.'
	},

	resizeImages: 'Redimensionar imagens grandes ao enviar',

	serialize: 'Save gallery',
	gallery: {
		text: 'Image caption',
		save: 'Save',
		remove: 'Remove from gallery',
		drag: 'Drag items here to create a gallery...'
	}
};