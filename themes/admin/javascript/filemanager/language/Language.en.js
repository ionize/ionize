/*
Script: Language.en.js
	MooTools Filemanager - Language Strings in English

Translation:
	[Christoph Pojer](http://cpojer.net)
*/

Filemanager.Language.en = {
	more: 'Details',
	width: 'Width:',
	height: 'Height:',

	ok: 'Ok',
	open: 'Select file',
	upload: 'Upload',
	create: 'Create folder',
	createdir: 'Please specify a folder name:',
	cancel: 'Cancel',
	error: 'Error',

	information: 'Information',
	type: 'Type:',
	size: 'Size:',
	dir: 'Path:',
	modified: 'Last modified:',
	preview: 'Preview',
	close: 'Close',
	destroy: 'Delete',
	destroyfile: 'Are you sure to delete this file?',

	rename: 'Rename',
	renamefile: 'Please enter a new file name:',
	rn_mv_cp: 'Rename/Move/Copy',

	download: 'Download',
	nopreview: '<i>No preview available</i>',

	title: 'Title:',
	artist: 'Artist:',
	album: 'Album:',
	length: 'Length:',
	bitrate: 'Bitrate:',

	deselect: 'Deselect',

	nodestroy: 'Deleting files has been disabled on this Server.',

	toggle_side_boxes: 'Thumbnail view',
	toggle_side_list: 'List view',
	show_dir_thumb_gallery: 'Show thumbnails of the files in the preview pane',
	drag_n_drop: 'Drag & drop has been enabled for this directory',
	drag_n_drop_disabled: 'Drag & drop has been temporarily disabled for this directory',
	goto_page: 'Go to page',

	'backend.disabled': 'This operation has been disabled on this Server.',
	'backend.authorized': 'You are not authorized to perform this operation.',
	'backend.path': 'The specified Folder does not exist.',
	'backend.exists': 'The specified Location does already exist.',
	'backend.mime': 'The specified file-type is not allowed.',
	'backend.extension': 'The uploaded file has an unknown or forbidden file extension.',
	'backend.size': 'The size of the file you uploaded is too big. Please upload a smaller file.',
	'backend.partial': 'The file you uploaded was only partially uploaded, please upload the file again.',
	'backend.nofile': 'There was no file specified or the file does not exist.',
	'backend.default': 'Something went wrong with the File-Upload.',
	'backend.path_not_writable': 'You do not have write/upload permissions for this directory.',
	'backend.filename_maybe_too_large': 'The filename/path is too long for the server. Please retry with a shorter file name.',
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
		unknown: 'Unknown Error',
		sizeLimitMin: 'You can not attach "<em>${name}</em>" (${size}), the file size minimum is <strong>${size_min}</strong>!',
		sizeLimitMax: 'You can not attach "<em>${name}</em>" (${size}), the file size limit is <strong>${size_max}</strong>!',
		mod_security: 'No response was given from the uploader, this may mean that "mod_security" is active on the server and one of the rules in mod_security has cancelled this request.  If you can not disable mod_security, you may need to use the NoFlash Uploader.'
	},

	flash: {
		hidden: 'To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).',
		disabled: 'To enable the embedded uploader, enable the blocked Flash movie  and refresh (see Flashblock).',
		flash: 'In order to upload files you need to install <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe Flash</a>.'
	},

	resizeImages: 'Resize on upload',

	serialize: 'Save gallery',
	gallery: {
		text: 'Image caption',
		save: 'Save',
		remove: 'Remove from gallery',
		drag: 'Drag items here to create a gallery...'
	}
};