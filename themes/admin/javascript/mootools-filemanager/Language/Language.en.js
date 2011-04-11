/*
Script: Language.en.js
	MooTools FileManager - Language Strings in English

Translation:
	[Christoph Pojer](http://cpojer.net)
*/

FileManager.Language.en = {
	more: 'Details',
	width: 'Width:',
	height: 'Height:',
	
	ok: 'Ok',
	open: 'Select file',
	upload: 'Upload',
	create: 'Create folder',
	createdir: 'Please specify a folder name:',
	cancel: 'Cancel',
	uploadUrl: 'Upload from URL',
	addUrl: 'Add URL',
	
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
	
	download: 'Download',
	nopreview: '<i>No preview available</i>',
	
	title: 'Title:',
	artist: 'Artist:',
	album: 'Album:',
	length: 'Length:',
	bitrate: 'Bitrate:',

	video_codec: 'Codec:',
	
	deselect: 'Deselect',
	
	nodestroy: 'Deleting files has been disabled on this Server.',
	
	notwritable: 'The base media dir is not writable. Check the permissions.',
	
	'upload.disabled': 'Uploading has been disabled on this Server.',
	'upload.authenticated': 'You are not authenticated to upload files.',
	'upload.path': 'The specified Upload-Folder does not exist. Please contact the administrator of this Website.',
	'upload.exists': 'The specified Upload-Location does already exist. Please contact the administrator of this Website.',
	'upload.mime': 'The specified file-type is not allowed.',
	'upload.extension': 'The uploaded file has an unknown or forbidden file extension.',
	'upload.size': 'The size of the file you uploaded is too big to be processed on this server. Please upload a smaller file.',
	'upload.partial': 'The file you uploaded was only partially uploaded, please upload the file again.',
	'upload.nofile': 'There was no file specified to be uploaded.',
	'upload.default': 'Something went wrong with the File-Upload.',
	
	/* FU */
	uploader: {
		unknown: 'Unknown Error',
		sizeLimitMin: 'You can not attach "<em>${name}</em>" (${size}), the file size minimum is <strong>${size_min}</strong>!',
		sizeLimitMax: 'You can not attach "<em>${name}</em>" (${size}), the file size limit is <strong>${size_max}</strong>!'
	},
	
	flash: {
		hidden: 'To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).',
		disabled: 'To enable the embedded uploader, enable the blocked Flash movie  and refresh (see Flashblock).',
		flash: 'In order to upload files you need to install <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe Flash</a>.'
	},
	
	resizeImages: 'Resize big images on upload',

	serialize: 'Save gallery',
	gallery: {
		text: 'Image caption',
		save: 'Save',
		remove: 'Remove from gallery',
		drag: 'Drag items here to create a gallery...'
	}
};