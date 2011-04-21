/*
Script: Language.br.js
	MooTools FileManager - Language Strings in Brazilian Portuguese

Translation:
	[Fabiana Pires](http://twitter.com/nervosinha)
*/

FileManager.Language.br = {
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
	
	information: 'Informação',
	type: 'Tipo:',
	size: 'Tamanho:',
	dir: 'Caminho:',
	modified: 'Última modificação:',
	preview: 'Pré-visualização',
	close: 'Fechar',
	destroy: 'Apagar',
	destroyfile: 'Tem certeza que deseja apagar este arquivo?',
	
	rename: 'Renomear',
	renamefile: 'Por favor especifique o novo nome do arquivo:',
	
	download: 'Download',
	nopreview: '<i>Pré-visualização indisponível</i>',
	
	title: 'Título:',
	artist: 'Artista:',
	album: 'Album:',
	length: 'Tamanho:',
	bitrate: 'Bitrate:',
	
	deselect: 'Desfazer',
	
	nodestroy: 'Apagar arquivos está desabilitado neste servidor.',
	
	'backend.disabled': 'Não é permitido enviar arquivos neste servidor.',
	'backend.authorized': 'Você não está autenticado para enviar arquivos neste servidor.',
	'backend.path': 'A pasta especificada não existe. Por favor contate o administrador do site.',
	'backend.exists': 'A pasta especificada já existe. Por favor contate o administrador do site.',
	'backend.mime': 'O tipo de arquivo especificado não é permitido.',
	'backend.extension': 'O arquivo enviado é de tipo desconhecido ou proibido.',
	'backend.size': 'O tamanho do arquivo enviado é muito grande para ser processado neste servidor. Por favor, envie um arquivo menor.',
	'backend.partial': 'O arquivo enviado foi corrompido, por favor envie o arquivo novamente.',
	'backend.nofile': 'Não existe arquivo especificado para ser enviado.',
	'backend.default': 'Erro no envio do arquivo.',
	
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
		unknown: 'Erro desconhecido',
		sizeLimitMin: 'Não é permitido anexar "<em>${name}</em>" (${size}), o tamanho mínimo para o arquivo é de <strong>${size_min}</strong>!',
		sizeLimitMax: 'Não é permitido anexar "<em>${name}</em>" (${size}), o tamanho máximo para o arquivo é de <strong>${size_max}</strong>!'
	},
	
	flash: {
		hidden: 'Para habilitar o uploader, desbloqueie a função em seu browser e recarregue a página (veja Adblock).',
		disabled: 'Para habilitar o uploader, habilite o arquivo Flash e recarregue a página (veja Flashblock).',
		flash: 'Para enviar arquivos é necessário instalar o <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe Flash Player</a>.'
	},
	
	resizeImages: 'Redimensionar imagens grandes ao enviar'
};