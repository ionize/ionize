/*
---
description: FileManager.TinyMCE
longdescription: MooTools FileManager for integration with [TinyMCE](http://tinymce.moxiecode.com/)

authors:
  - Christoph Pojer

requires:
  core/1.2.4: '*'

provides:
  - filemanager.tinymce

license:
  MIT-style license

Usage:
  - Pass this to the "file_browser_callback"-option of TinyMCE: FileManager.TinyMCE(function(){ return {FileManagerOptions}; });
  - See the Demo for an example.
...
*/

FileManager.TinyMCE = function(options)
{
	return function(field, url, type, win)
	{
		var manager = new FileManager($extend(
		{
			onComplete: function(path)
			{
				if (!win.document) return;
				win.document.getElementById(field).value = path;
				if (win.ImageDialog) win.ImageDialog.showPreviewImage(path, 1);
				this.container.destroy();
			}
		}, options(type)));
		manager.dragZIndex = 400002;
		manager.SwiffZIndex = 400003;
		manager.el.setStyle('zIndex', 400001);
		manager.overlay.el.setStyle('zIndex', 400000);
		document.id(manager.tips).setStyle('zIndex', 400010);
		manager.show();
		return manager;
	};
};

FileManager.implement('SwiffZIndex', 400003);
var Dialog = new Class({
	
	Extends: Dialog,
	
	initialize: function(text, options){
		this.parent(text, options);
		this.el.setStyle('zIndex', 400010);
		this.overlay.el.setStyle('zIndex', 400009);
	}
	
});

