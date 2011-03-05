/*
---
description: FileManager Uploader
longdescription: Implements Upload functionality into the FileManager based on [FancyUpload](http://digitarald.de)

authors:
  - Christoph Pojer

requires:
  core/1.2.4: '*'

provides:
  - filemanager.uploader

license:
  MIT-style license

options:
  - upload: (boolean, defaults to *true*) 
  - uploadAuthData: (object) Data to be send with the GET-Request of an Upload as Flash ignores authenticated clients
  - resizeImages: (boolean, defaults to *true*) Whether to show the option to resize big images or not
...
*/

FileManager.implement({
	
	options: {
		resizeImages: true,
		upload: true,
		uploadAuthData: ''
	},
	
	hooks: {
		show: {
			upload: function(){
				this.startUpload();
			}
		},
		
		cleanup: {
			upload: function(){
				if (!this.options.upload || !this.upload) return;
				if (this.upload.uploader) this.upload.uploader.set('opacity', 0).dispose();
			}
		},
		
		hide: {
			upload: function()
			{
				$$('.swiff-uploader-box').destroy();			
			}
		}
	},
	
	onDialogOpen: function(){
		if (this.swf && this.swf.box) this.swf.box.setStyle('visibility', 'hidden');
	},
	
	onDialogClose: function(){
		if (this.swf && this.swf.box) this.swf.box.setStyle('visibility', 'visible');
	},
	
	startUpload: function()
	{
		if (!this.options.upload || this.swf) return;
		
		var self = this;
		this.upload = {
			button: this.addMenuButton('upload').inject(this.menu, 'bottom').addEvents({
				click: function(){
					return false;
				},
				mouseenter: function(){
					this.addClass('hover');
				},
				mouseleave: function(){
					this.removeClass('hover');
					this.blur();
				},
				mousedown: function(){
					this.focus();
				}
			}),
			list: new Element('ul', {'class': 'filemanager-uploader-list'}),
			uploader: new Element('div', {opacity: 0}).adopt(
				new Element('h2', {text: this.language.upload}),
				new Element('div', {'class': 'filemanager-uploader'})
			)
		};
		this.upload.uploader.getElement('div').adopt(this.upload.list);
		
		if (this.options.resizeImages){
			var resizer = new Element('div', {'class': 'checkbox'}),
				check = (function(){ this.toggleClass('checkboxChecked'); }).bind(resizer);
			check();
			this.upload.label = new Element('label').adopt(
				resizer, new Element('span', {text: this.language.resizeImages})
			).addEvent('click', check).inject(this.menu);
		}
		
		var File = new Class({

			Extends: Swiff.Uploader.File,
			
			initialize: function(base, data)
			{
				this.parent(base, data);

				// Set the URL and add data to POST data, because of CI.
				this.setOptions(
				{
					url: self.options.url + '/upload',
					data: {
						directory: self.normalize(self.Directory),													// Dir to upload the file
						uploadAuthData: self.options.uploadAuthData,												// Authentication data
						resizeImages: (self.options.resizeImages && resizer.hasClass('checkboxChecked') ? 1 : 0)	// Resize ? Not used for the moment.
					}
				});
			},
			
			render: function()
			{
				if (this.invalid)
				{
					var message = self.language.uploader.unknown, sub = {
						name: this.name,
						size: Swiff.Uploader.formatUnit(this.size, 'b')
					};
					
					if (self.language.uploader[this.validationError])
						message = self.language.uploader[this.validationError];
					
					if (this.validationError == 'sizeLimitMin')
						sub.size_min = Swiff.Uploader.formatUnit(this.base.options.fileSizeMin, 'b');
					else if (this.validationError == 'sizeLimitMax')
						sub.size_max = Swiff.Uploader.formatUnit(this.base.options.fileSizeMax, 'b');
					
					new Dialog(new Element('div', {html: message.substitute(sub, /\\?\$\{([^{}]+)\}/g)}) , {language: {confirm: self.language.ok}, buttons: ['confirm']});
					return this;
				}
				
				this.addEvents({
					open: this.onOpen,
					remove: this.onRemove,
					requeue: this.onRequeue,
					progress: this.onProgress,
					stop: this.onStop,
					complete: this.onComplete
				});
				
				this.ui = {};
				this.ui.icon = new Asset.image(self.options.assetBasePath+'Icons/' + this.extension + '.png', {
					onerror: function(){ new Asset.image(self.options.assetBasePath + 'Icons/default.png').replaces(this); }
				});
				this.ui.element = new Element('li', {'class': 'file', id: 'file-' + this.id});
				this.ui.title = new Element('span', {'class': 'file-title', text: this.name});
				this.ui.size = new Element('span', {'class': 'file-size', text: Swiff.Uploader.formatUnit(this.size, 'b')});
				
				var file = this;
				this.ui.cancel = new Asset.image(self.options.assetBasePath+'cancel.png', {'class': 'file-cancel', title: self.language.cancel}).addEvent('click', function(){
					file.remove();
					self.tips.hide();
					self.tips.detach(this);
				});
				self.tips.attach(this.ui.cancel);

				var progress = new Element('img', {'class': 'file-progress', src: self.options.assetBasePath+'bar.gif'});

				this.ui.element.adopt(
					this.ui.cancel,
					progress,
					this.ui.icon,
					this.ui.title,
					this.ui.size
				).inject(self.upload.list).highlight();
				
				this.ui.progress = new Fx.ProgressBar(progress).set(0);
							
				this.base.reposition();

				return this.parent();
			},

			onOpen: function(){
				this.ui.element.addClass('file-running');
			},

			onRemove: function(){
				this.ui = this.ui.element.destroy();
			},

			onProgress: function(){
				this.ui.progress.start(this.progress.percentLoaded);
			},

			onStop: function(){
				this.remove();
			},

			onComplete: function()
			{
				this.ui.progress = this.ui.progress.cancel().element.destroy();
				this.ui.cancel = this.ui.cancel.destroy();
				
				var response = JSON.decode(this.response.text);
			
				// Display the upload error
				if (!response.status)
					new Dialog(('' + response.error).substitute(self.language, /\\?\$\{([^{}]+)\}/g) , {language: {confirm: self.language.ok}, buttons: ['confirm']});
				
				this.ui.element.set('tween', {duration: 2000}).highlight(response.status ? '#e6efc2' : '#f0c2c2');
				
				// Hide the upload box (file list) and reload the info
/*
				(function(){
					this.ui.element.setStyle('overflow', 'hidden').morph({
						opacity: 0,
						height: 0
					}).get('morph').chain(function(){
						this.element.destroy();
						if (!self.upload.list.getElements('li').length)
							self.upload.uploader.fade(0).get('tween').chain(function(){
								self.fillInfo();
							});
					});
				}).delay(5000, this);
*/

			}
		});

		this.swf = new Swiff.Uploader({
			id: 'SwiffFileManagerUpload',
			container: this.container,
			path: this.options.assetBasePath + 'Swiff.Uploader.swf',
			queued: false,
			target: this.upload.button,
			allowDuplicates: true,
			instantStart: true,
			fileClass: File,
			fileSizeMax: 25 * 1024 * 1024,
			zIndex: this.SwiffZIndex || 9999,
			onSelectSuccess: function(){
				self.fillInfo();
				self.preview.adopt(self.upload.uploader);
				self.upload.uploader.fade(1);
			},
			onComplete: function()
			{
				self.load(self.Directory, true);
			},
			onFail: function(error)
			{
				$$(self.upload.button, self.upload.label).dispose();
				new Dialog(new Element('div', {html: self.language.flash[error] || self.language.flash.flash}), {language: {confirm: self.language.ok}, buttons: ['confirm']});
			}
		});
	}
	
});