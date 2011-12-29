/*
---

description: Implements Upload functionality into the FileManager based on [FancyUpload](http://digitarald.de)

authors: Christoph Pojer (@cpojer)

license: MIT-style license.

requires: [Core/*]

provides: Filemanager.Uploader

...
*/

FileManager.implement({

	options: {
		resizeImages: true,
		upload: true,
		uploadAuthData: {},            // deprecated; use FileManager.propagateData instead!
		uploadTimeLimit: 300,
		uploadFileSizeMax: 2600 * 2600 * 25
	},

	hooks: {
		show: {
			upload: function() {
				this.startUpload();
			}
		},

		cleanup: {
			upload: function() {

				if (!this.options.upload || !this.upload) return;

				if (this.upload.uploader) this.upload.uploader.dispose();
				if (this.upload.button)	this.upload.button.dispose();
				if (this.upload.list)	this.upload.list.dispose();
				if (this.swf.box) this.swf.box.dispose();
			}
		}
	},

	onDialogOpenWhenUpload: function() {
		if (this.swf && this.swf.box) this.swf.box.setStyle('visibility', 'hidden');
	},

	onDialogCloseWhenUpload: function() {
		if (this.swf && this.swf.box) this.swf.box.setStyle('visibility', 'visible');
	},

	startUpload: function() {

		if (!this.options.upload || this.swf) return;

		var self = this;
		this.upload = {
			button: this.addMenuButton('upload').inject(this.menu, 'bottom').addEvents({
				click: function() {
					return false;
				},
				mouseenter: function() {
					this.addClass('hover');
				},
				mouseleave: function() {
					this.removeClass('hover');
					this.blur();
				},
				mousedown: function() {
					this.focus();
				}
			}),
			list: new Element('ul', {'class': 'filemanager-uploader-list'}),
			uploader: new Element('div', {opacity: 0, 'class': 'filemanager-uploader-area'}).adopt(
				new Element('h2', {text: this.language.upload}),
				new Element('div', {'class': 'filemanager-uploader'})
			),
			lastFileUploaded: null,  // name of the last successfully uploaded file; will be preselected in the list view
			error_count: 0
		};
		this.upload.uploader.getElement('div').adopt(this.upload.list);

		if (this.options.resizeImages) {
			var resizer = new Element('div', {'class': 'checkbox'});
			var check = (function() {
					this.toggleClass('checkboxChecked');
				}).bind(resizer);
			check();
			this.upload.label = new Element('label').adopt(
				resizer,
				new Element('span', {text: this.language.resizeImages})
			).addEvent('click', check).inject(this.menu);
		}

		var File = new Class({

			Extends: Swiff.Uploader.File,

			initialize: function(base, data) {

				this.parent(base, data);
				this.has_completed = false;

				var tx_cfg = self.options.mkServerRequestURL(self, 'upload', {
								directory: self.CurrentDir.path,
								filter: self.options.filter,
								resize: (self.options.resizeImages && resizer.hasClass('checkboxChecked')) ? 1 : 0
							});

				self.diag.log('Uploader: setOptions', tx_cfg);

				this.setOptions(tx_cfg);
			},

			render: function() {
				if (this.invalid) {
					var message = self.language.uploader.unknown;
					var sub = {
						name: this.name,
						size: Swiff.Uploader.formatUnit(this.size, 'b')
					};

					if (self.language.uploader[this.validationError]) {
						message = self.language.uploader[this.validationError];
					}

					if (this.validationError === 'sizeLimitMin')
						sub.size_min = Swiff.Uploader.formatUnit(this.base.options.fileSizeMin, 'b');
					else if (this.validationError === 'sizeLimitMax')
						sub.size_max = Swiff.Uploader.formatUnit(this.base.options.fileSizeMax, 'b');

					self.showError(message.substitute(sub, /\\?\$\{([^{}]+)\}/g));
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
				this.ui.icon = new Asset.image(self.URLpath4assets+'Images/Icons/' + this.extension + '.png', {
					'class': 'icon',
					onerror: function() {
						new Asset.image(self.URLpath4assets + 'Images/Icons/default.png').replaces(this);
					}
				});
				this.ui.element = new Element('li', {'class': 'file', id: 'file-' + this.id});
				// keep filename in display box at reasonable length:
				var laname = this.name;
				if (laname.length > 36) {
					laname = laname.substr(0, 36) + '...';
				}
				this.ui.title = new Element('span', {'class': 'file-title', text: laname, title: this.name});
				this.ui.size = new Element('span', {'class': 'file-size', text: Swiff.Uploader.formatUnit(this.size, 'b')});

				var file = this;
				this.ui.cancel = new Asset.image(self.URLpath4assets+'Images/cancel.png', {'class': 'file-cancel', title: self.language.cancel}).addEvent('click', function() {
					file.remove();
					self.tips.hide();
					self.tips.detach(this);
				});
				self.tips.attach(this.ui.cancel);

				var progress = new Element('img', {'class': 'file-progress', src: self.URLpath4assets+'Images/bar.gif'});

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

			onOpen: function() {
				this.ui.element.addClass('file-running');
			},

			onRemove: function() {
				this.ui = this.ui.element.destroy();

				// when all items in the list have been cancelled/removed, and the transmission of the files is done, i.e. after the onComplete has fired, destroy the list!
				var cnt = self.upload.list.getElements('li').length;

//				if (cnt == 0 && this.has_completed)
				if (cnt == 0 )
				{
					self.upload.uploader.setStyle('display', 'none');
				}
			},

			onProgress: function() {
				this.ui.progress.start(this.progress.percentLoaded);
			},

			onStop: function() {
				this.remove();
			},

			onComplete: function(file_obj)
			{
				self.diag.log('File-onComplete', arguments, ', fileList: ', self.swf.fileList);

				var response = null;
				var failure = true;

				this.has_completed = true;

				this.ui.progress = this.ui.progress.cancel().element.destroy();
				this.ui.cancel = this.ui.cancel.destroy();

				try
				{
					response = JSON.decode(this.response.text);
				}
				catch(e)
				{
					self.diag.log(this.response);
				}

				if (typeof response === 'undefined' || response == null)
				{
					if (this.response == null || !this.response.text)
					{
						// The 'mod_security' has shown to be one of the most unhelpful error messages ever; particularly when it happened on a lot on boxes which had a guaranteed utter lack of mod_security and friends.
						// So we restrict this report to the highly improbable case where we get to receive /nothing/ /at/ /all/.
						self.showError(self.language.uploader.mod_security);
					}
					else
					{
						self.showError(("Server response:\n" + this.response.text).substitute(self.language, /\\?\$\{([^{}]+)\}/g));
					}
				}
				else if (!response.status)
				{
					self.showError(('' + response.error).substitute(self.language, /\\?\$\{([^{}]+)\}/g));
				}
				else
				{
					failure = false;
				}

				this.ui.element.set('tween', {duration: 2000}).highlight(!failure ? '#e6efc2' : '#f0c2c2');
				(function() {
					this.ui.element.setStyle('overflow', 'hidden').morph({
						opacity: 0,
						height: 0
					}).get('morph').chain(function() {
						this.element.destroy();
						var cnt = self.upload.list.getElements('li').length;
						if (cnt == 0)
						{
							self.upload.uploader.fade(0).get('tween').chain(function() {
								self.upload.uploader.setStyle('display', 'none');
							});
						}
					});
				}).delay(!failure ? 1000 : 5000, this);

				if (failure)
				{
					self.upload.error_count++;
				}

				// don't wait for the cute delays to start updating the directory view!
				var cnt = self.upload.list.getElements('li').length;
				var fcnt = self.swf.fileList.length;
				self.diag.log('upload:onComplete for FILE', file_obj, cnt, fcnt);
			}
		});

		this.getFileTypes = function() {
			var fileTypes = {};
			if (this.options.filter == 'image')
				fileTypes = {'Images (*.jpg, *.gif, *.png)': '*.jpg; *.jpeg; *.bmp; *.gif; *.png'};
			if (this.options.filter == 'video')
				fileTypes = {'Videos (*.avi, *.flv, *.mov, *.mpeg, *.mpg, *.wmv, *.mp4)': '*.avi; *.flv; *.fli; *.movie; *.mpe; *.qt; *.viv; *.mkv; *.vivo; *.mov; *.mpeg; *.mpg; *.wmv; *.mp4'};
			if (this.options.filter == 'audio')
				fileTypes = {'Audio (*.aif, *.mid, *.mp3, *.mpga, *.rm, *.wav)': '*.aif; *.aifc; *.aiff; *.aif; *.au; *.mka; *.kar; *.mid; *.midi; *.mp2; *.mp3; *.mpga; *.ra; *.ram; *.rm; *.rpm; *.snd; *.wav; *.tsi'};
			if (this.options.filter == 'text')
				fileTypes = {'Text (*.txt, *.rtf, *.rtx, *.html, *.htm, *.css, *.as, *.xml, *.tpl)': '*.txt; *.rtf; *.rtx; *.html; *.htm; *.css; *.as; *.xml; *.tpl'};
			if (this.options.filter == 'application')
				fileTypes = {'Application (*.bin, *.doc, *.exe, *.iso, *.js, *.odt, *.pdf, *.php, *.ppt, *.swf, *.rar, *.zip)': '*.ai; *.bin; *.ccad; *.class; *.cpt; *.dir; *.dms; *.drw; *.doc; *.dvi; *.dwg; *.eps; *.exe; *.gtar; *.gz; *.js; *.latex; *.lnk; *.lnk; *.oda; *.odt; *.ods; *.odp; *.odg; *.odc; *.odf; *.odb; *.odi; *.odm; *.ott; *.ots; *.otp; *.otg; *.pdf; *.php; *.pot; *.pps; *.ppt; *.ppz; *.pre; *.ps; *.rar; *.set; *.sh; *.skd; *.skm; *.smi; *.smil; *.spl; *.src; *.stl; *.swf; *.tar; *.tex; *.texi; *.texinfo; *.tsp; *.unv; *.vcd; *.vda; *.xlc; *.xll; *.xlm; *.xls; *.xlw; *.zip'};

			return fileTypes;
		};

		this.diag.log('Uploader: SWF init');
		this.swf = new Swiff.Uploader({
			id: 'SwiffFileManagerUpload',
			path: this.URLpath4assets + 'Swiff.Uploader.swf',
			queued: false,
			target: this.upload.button,
			allowDuplicates: true,
			instantStart: true,
			appendCookieData: true, // pass along any session cookie data, etc. in the request section (PHP: $_GET[])
			verbose: this.options.verbose,
			data: Object.merge({},
				self.options.propagateData,
				(self.options.uploadAuthData || {})
			),
			fileClass: File,
			timeLimit: self.options.uploadTimeLimit,
			fileSizeMax: self.options.uploadFileSizeMax,
			typeFilter: this.getFileTypes(),
			zIndex: this.options.zIndex + 400000,
			onSelectSuccess: function() {
				self.diag.log('FlashUploader: onSelectSuccess', arguments, ', fileList: ', self.swf.fileList);
				//self.fillInfo();
				self.show_our_info_sections(false);
				//self.info.getElement('h2.filemanager-headline').setStyle('display', 'none');
				self.info.adopt(self.upload.uploader.setStyle('display', 'block'));
				self.upload.uploader.fade(1);
			},
			onComplete: function(info) {
				this.diag.log('FlashUploader: onComplete', arguments, ', fileList: ', self.swf.fileList);

				// don't wait for the cute delays to start updating the directory view!
				var cnt = this.upload.list.getElements('li').length;
				var fcnt = this.swf.fileList.length;
				this.diag.log('upload:onComplete', info, cnt, fcnt);
				// add a 5 second delay when there were upload errors:
				(function() {
					this.load(this.CurrentDir.path, this.upload.lastFileUploaded);
					// this.fillInfo();
				}).bind(this).delay(this.upload.error_count > 0 ? 5500 : 1);
			}.bind(this),
			onFileComplete: function(f) {
				self.diag.log('FlashUploader: onFileComplete', arguments, ', fileList: ', self.swf.fileList);
				self.upload.lastFileUploaded = f.name;
			},
			onFail: function(error) {
				self.diag.log('FlashUploader: onFail', arguments, ', swf: ', self.swf, ', fileList: ', (typeof self.swf !== 'undefined' ? self.swf : '---'));
				if (error !== 'empty') {
					$$(self.upload.button, self.upload.label).dispose();
					self.showError(self.language.flash[error] || self.language.flash.flash);
				}
			},

			onLoad: function() {
				self.diag.log('FlashUploader: onLoad', arguments, ', fileList: ', self.swf.fileList);
			},
			onStart: function() {
				self.diag.log('FlashUploader: onStart', arguments, ', fileList: ', self.swf.fileList);
			},
			onQueue: function() {
				self.diag.log('FlashUploader: onQueue', arguments, ', fileList: ', self.swf.fileList);
			},
			onBrowse: function() {
				self.diag.log('FlashUploader: onBrowse', arguments, ', fileList: ', self.swf.fileList);
			},
			onDisabledBrowse: function() {
				self.diag.log('FlashUploader: onDisabledBrowse', arguments, ', fileList: ', self.swf.fileList);
			},
			onCancel: function() {
				self.diag.log('FlashUploader: onCancel', arguments, ', fileList: ', self.swf.fileList);
			},
			onSelect: function() {
				self.diag.log('FlashUploader: onSelect', arguments, ', fileList: ', self.swf.fileList);
			},
			onSelectFail: function() {
				self.diag.log('FlashUploader: onSelectFail', arguments, ', fileList: ', self.swf.fileList);
			},

			onButtonEnter: function() {
				self.diag.log('FlashUploader: onButtonEnter', arguments, ', fileList: ', self.swf.fileList);
			},
			onButtonLeave: function() {
				self.diag.log('FlashUploader: onButtonLeave', arguments, ', fileList: ', self.swf.fileList);
			},
			onButtonDown: function() {
				self.diag.log('FlashUploader: onButtonDown', arguments, ', fileList: ', self.swf.fileList);
			},
			onButtonDisable: function() {
				self.diag.log('FlashUploader: onButtonDisable', arguments, ', fileList: ', self.swf.fileList);
			},

			onFileStart: function() {
				self.diag.log('FlashUploader: onFileStart', arguments, ', fileList: ', self.swf.fileList);
			},
			onFileStop: function() {
				self.diag.log('FlashUploader: onFileStop', arguments, ', fileList: ', self.swf.fileList);
			},
			onFileRequeue: function() {
				self.diag.log('FlashUploader: onFileRequeue', arguments, ', fileList: ', self.swf.fileList);
			},
			onFileOpen: function() {
				self.diag.log('FlashUploader: onFileOpen', arguments, ', fileList: ', self.swf.fileList);
			},
			onFileProgress: function() {
				self.diag.log('FlashUploader: onFileProgress', arguments, ', fileList: ', self.swf.fileList);
			},
			onFileRemove: function() {
				self.diag.log('FlashUploader: onFileRemove', arguments, ', fileList: ', self.swf.fileList);
			},

			onBeforeStart: function() {
				self.diag.log('FlashUploader: onBeforeStart', arguments, ', fileList: ', self.swf.fileList);
			},
			onBeforeStop: function() {
				self.diag.log('FlashUploader: onBeforeStop', arguments, ', fileList: ', self.swf.fileList);
			},
			onBeforeRemove: function() {
				self.diag.log('FlashUploader: onBeforeRemove', arguments, ', fileList: ', self.swf.fileList);
			}
		});
	}
});

