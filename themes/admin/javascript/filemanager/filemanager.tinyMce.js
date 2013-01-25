/*
---

description: MooTools FileManager for integration with [TinyMCE](http://tinymce.moxiecode.com/)

authors: Christoph Pojer (@cpojer)

license: MIT-style license.

requires: [Core/*]

provides: FileManager.TinyMCE

Usage:
  - Pass this to the "file_browser_callback"-option of TinyMCE: FileManager.TinyMCE(function(){ return {FileManagerOptions}; });
  - See the Demo for an example.
...
*/

FileManager.TinyMCE = function(options){
	/*
	 * field: Id of the element to set value in.
	 * url: value currently stored in the indicated element
	 * type: Type of browser to open image/file/flash: 'file' ~ page links, 'image' ~ insert picture, 'media' ~ insert media/movie
	 * win: window object reference
	 */
	return function(field, url, type, win){
		var manager;                // jsLint warning fix
		manager = new FileManager(Object.append({
			onComplete: function(path, file, mgr) {
				if (!win.document)
					return;
				//path = manager.escapeRFC3986(path);
				win.document.getElementById(field).value = path;
				if (win.ImageDialog) {
					win.ImageDialog.showPreviewImage(path, 1);
				}
				this.container.destroy();
			}
		}, options(type),
		{
			zIndex: 400000,
			styles: {
				'width': '90%',
				'height': '90%'
			}
		}));
		var src = win.document.getElementById(field).value;

		src = decodeURI(src);

		if (src.length > 0)
		{
			src = this.documentBaseURI.toAbsolute(src);
		}
		if (src.match(/^[a-z]+:/i))
		{
			// strip off scheme + authority sections:
			src = src.replace(/^[a-z]+:(\/\/?)[^\/]*/i, '');
		}

		// pass full path to 'preselect': backend will take care of it for us
		manager.show(null, null, (src.length > 0 ? src : null));
		return manager;
	};
};

