/*
---
description: Ionize FileManager

Adapted from: Filemanager, by Christoph Pojer

authors:
  - Christoph Pojer

requires:
  core/1.2.4: '*'
  more/1.2.4.2: [Drag, Drag.Move, Tips, Assets, Element.Delegation]

provides:
  - filemanager

license:
  MIT-style license

version:
  1.1

todo:
  - Add Scroller.js (optional) for Drag&Drop in the Filelist

inspiration:
  - Loosely based on a Script by [Yannick Croissant](http://dev.k1der.net/dev/brooser-un-browser-de-fichier-pour-mootools/)

options:
  - url: (string) The base url to the Backend FileManager, without QueryString
  - baseURL: (string) Absolute URL to the FileManager files
  - assetBasePath: (string) The path to all images and swf files
  - selectable: (boolean, defaults to *false*) If true, provides a button to select a file
  - language: (string, defaults to *en*) The language used for the FileManager
  - hideOnClick: (boolean, defaults to *false*) When true, hides the FileManager when the area outside of it is clicked
  - directory: (string) Can be used to load a subfolder instead of the base folder

events:
  - onComplete(path, file): fired when a file gets selected via the "Select file" button
  - onModify(file): fired when a file gets renamed/deleted or modified in another way
  - onShow: fired when the FileManager opens
  - onHide: event fired when FileManager closes
  - onPreview: event fired when the user clicks an image in the preview
...
*/

var FileManager = new Class({

	Implements: [Options, Events],

	Request: null,
	Directory: null,
	Current: null,

	options: {
		/*onComplete: $empty,
		onModify: $empty,
		onShow: $empty,
		onHide: $empty,
		onPreview: $empty*/
		directory: '',
		url: null,
		baseURL: '',
		assetBasePath: null,
		selectable: false,
		hideOnClick: false,
		language: 'en',
		thumbSize: 120
	},
	
	hooks: {
		show: {},
		cleanup: {},
		hide: {
			tips: function()
			{
				$$('.tip-filebrowser').destroy();			
			}
		}
	},

	initialize: function(options)
	{
		this.setOptions(options);
		this.options.assetBasePath = this.options.assetBasePath.replace(/(\/|\\)*$/, '/');
		this.droppables = [];

		// Not set through JS. Set through Filemanager.php. See /controllers/media.php. For JS usage, see Filemanager.js->fill()
		this.Directory = this.options.directory;

		this.language = $unlink(FileManager.Language.en);
		if (this.options.language != 'en') this.language = $merge(this.language, FileManager.Language[this.options.language]);
		
		this.container = new Element('div', {'class': 'filemanager-container filemanager-engine-' + Browser.Engine.name + (Browser.Engine.trident ? Browser.Engine.version : '')});
		this.el = new Element('div', {'class': 'filemanager'}).inject(this.container);
		this.menu = new Element('div', {'class': 'filemanager-menu'}).inject(this.el);
		this.loader = new Element('div', {'class': 'loader', opacity: 0, tween: {duration: 200}}).inject(this.menu);

		var self = this;

		// Window size
		if (Cookie.read('fm'))
		{
			var fm = new Hash.Cookie('fm', {duration: 365});
			
			this.wSize = {
				'width': fm.get('width'),
				'height': fm.get('height'),
				'y': fm.get('top'),
				'x': fm.get('left')
			}
		}
		else
		{
			this.wSize = {
				'width': 810,
				'height': 420,
				'x': 80,
				'y': null
			}
		}
		
		
		// Executed by each click on folder in the browser or on file / thumb
		this.relayClick = function(e)
		{
			if(e) e.stop();

			var file = this.retrieve('file');

			if (this.retrieve('block') && !Browser.Engine.trident)
			{
				this.eliminate('block');
				return;
			}
			
			// Folder ?
			if (file.mime == 'text/directory')
			{
				this.addClass('selected');
				self.load(self.Directory + '/' + file.name);
				return;
			}
			
			// File
			self.fillInfo(file);
			if (self.Current) self.Current.removeClass('selected');
			self.Current = this.addClass('selected');

			self.CurrentFile = file;

			self.switchButton();
		};

		// Double click on thumb
		this.relayDblClick = function(e)
		{
			if(e) e.stop();
			
			var file = this.retrieve('file');

			if (self.Current) self.Current.removeClass('selected');
			self.Current = this.addClass('selected');

			self.CurrentFile = file;

			self.open();
		};

		
		// Files and folder browser (left panel)
		this.browser = new Element('ul', {'class': 'filemanager-browser'}).addEvents(
		{
			'click': (function(e)
			{
				e.stop();
				self.load(self.Directory, true);
				return self.deselect();
			})
		}).inject(this.el);
		
		this.scroller = new Scroller(this.browser);
		
//		this.addMenuButton('uploadUrl');

		this.addMenuButton('create');
		if (this.options.selectable) this.addMenuButton('open');
		
		
		// Information panel about one folder / file
		this.info = new Element('div', {'class': 'filemanager-infos'}).inject(this.el);

		var head = new Element('div', {'class': 'filemanager-head'}).adopt([
			new Element('img', {'class': 'filemanager-icon'}),
			new Element('h1')
		]);

		this.info.adopt(head);

		// Details panel : Contains thumbs or file infos
		this.details = new Element('div', {'class': 'filemanager-details'}).inject(this.info);

		this.initFileInfo();

		// Tips init
		$$('tip-filebrowser').dispose();
		this.tips = new Tips({
			className: 'tip-filebrowser',
			offsets: {x: 15, y: 0},
			text: null,
			showDelay: 50,
			hideDelay: 50,
			onShow: function(){
				this.tip.set('tween', {duration: 250}).setStyle('display', 'block').fade(1);
			},
			onHide: function(){
				this.tip.fade(0).get('tween').chain(function(){
					this.element.setStyle('display', 'none');
				});
			}
		});
		
		this.imageadd = new Asset.image(this.options.assetBasePath + 'add.png', {
			'class': 'browser-add'
		}).set('opacity', 0).inject(this.container);
		
		// Bound object !
		this.bound = {
			keydown: (function(e){
				if (e.control || e.meta) this.imageadd.fade(1);
			}).bind(this),
			keyup: (function(){
				this.imageadd.fade(0);
			}).bind(this),
			keyesc: (function(e){
				if (e.key=='esc') this.hide();
			}).bind(this),
			scroll: (function(){
				this.el.center(this.offsets);
				this.fireEvent('scroll');
			}).bind(this)
		};
	},
	
	
	/**
	 * Displays the filemanager in a MUI window
	 *
	 */
	show: function(e)
	{
		if (e) e.stop();

		// Call the directory content load
		this.load(this.Directory, true);
		
		(function()
		{
			var self = this;
			
			var options  = 
			{
				id: 'filemanagerWindow',
				title: 'Filemanager',
				loadMethod: 'html',
				content: this.container,
				evalResponse: true,
				width: self.wSize.width,
				height: self.wSize.height,
				y: 35,
				padding: { top: 0, right: 0, bottom: 0, left: 0 },
				maximizable: false,
				contentBgColor: '#fff',
				onClose: function()
				{
					self.hide();
				},
				onResize: function()
				{
					var fm = new Hash.Cookie('fm', {duration: 365});
					fm.erase();
					fm.extend(this.windowEl.getCoordinates());
				}
			};
			
			// Correct windows levels : Get the current highest level.			
			MUI.getWindowWithHighestZindex();							// stores the highest level in MUI.highestZindex
			var zidx = (MUI.highestZindex).toInt();
			
			if (this.options.indexLevel && MUI.highestZindex < this.options.indexLevel)
			{
				zidx = this.options.indexLevel;
			}
			
			MUI.Windows.indexLevel = zidx + 100;						// Mocha window z-index
			this.SwiffZIndex = zidx + 200;								// Uploader index
			document.id(this.tips).setStyle('zIndex', zidx + 500);		// Tips
			
			// Window creation
			this.window = new MUI.Window(options);
			
			this.container.setStyles({
				display: 'block'
			});
		
			this.fireEvent('show');
			this.fireHooks('show');

		}).delay(100, this);
	},


	/**
	 * Displays the filemanager in a container
	 *
	 */
	showIn: function(el)
	{
		// Call the directory content load
		this.load(this.Directory, true);
		
		if ($(el))
		{
			(function()
			{
				this.container.setStyles({
					display: 'block'
				});
				
				document.id(this.tips).setStyle('zIndex', 100000);			// Tips. 100000 so were're cool.
				
				this.fireEvent('show');
				this.fireHooks('show');
				
				$(el).adopt(this.container);
				
			}).delay(100, this);
		}
	},

	/**
	 * Closes the Mocha UI window
	 * The window has an onClose Event which calls hide(), so the filemanager will be detroyed properly
	 *
	 */
	close: function()
	{
		this.window.close();
	},

	hide: function(e)
	{
		if (e) e.stop();

		this.tips.hide();
		this.browser.empty();
		this.container.setStyle('display', 'none');
		
		this.container.destroy();
		
		this.fireHooks('cleanup').fireHooks('hide').fireEvent('hide');
		window.removeEvent('scroll', this.bound.scroll).removeEvent('resize', this.bound.scroll).removeEvent('keyup', this.bound.keyesc);
	},

	open: function(e)
	{
		if (e) e.stop();

		if (!this.Current) return false;

		this.fireEvent('complete', [
			this.normalize(this.Directory + '/' + this.CurrentFile.name),
			this.CurrentFile
		]);

		// Why hide ??? Keep it opened !
		// this.hide();
	},

	create: function(e)
	{
		e.stop();

		var self = this;
		
		new Dialog(this.language.createdir, {
			container: self.container,
			language: {
				confirm: this.language.create,
				decline: this.language.cancel
			},
			content: [
				new Element('input', {'class': 'createDirectory'})
			],
			onOpen: this.onDialogOpen.bind(this),
			onClose: this.onDialogClose.bind(this),
			onShow: function()
			{
				var self = this;
				this.el.getElement('input').addEvent('keyup', function(e){
					if (e.key == 'enter') self.el.getElement('button-confirm').fireEvent('click');
				}).focus();
			},
			onConfirm: function()
			{
				new FileManager.Request(
				{
					url: self.options.url + '/create',
					onSuccess: self.fill.bind(self),
					data: {
						file: this.el.getElement('input').get('value'),
						directory: self.Directory
					}
				}, self).post();
			}
		});
	},
	
	
	/**
	 * Upload form URL : Show upload form
	 *
	 */
	uploadUrl: function(e)
	{
		e.stop();

		this.deselect();
		this.details.empty();

		var self = this;

		this.info.getElement('img').set({
			src: this.options.assetBasePath + 'icon_16_up.png'
		});
		this.info.getElement('h1').set('text', this.language['uploadUrl']);

		// Main URL upload panel
		this.body = new Element('div', {'class': 'filemanager-body'});
		this.formUploadUrl = new Element('form', {name:'uploadUrl', method:'post'});
		var submit = new Element('button', {type:'button', 'class':'clear ml0'}).set('text', this.language['upload']) ;
		var addUrl = new Element('button', {type:'button', 'class':'clear ml0'}).set('text', this.language['addUrl']) ;
		
		submit.addEvent('click', function(item){ self.postUploadUrl(); }, this);
		addUrl.addEvent('click', function(item){ self.addUploadUrlField(); }, this);

		this.formUploadUrl.inject(this.body, 'top');
		addUrl.inject(this.body, 'top');
		submit.inject(this.body, 'bottom');

		// Add panel to Details
		this.details.adopt(this.body);
		
		this.addUploadUrlField();
		
	},
	
	
	postUploadUrl: function(e)
	{
		var self = this;

		var data = {};

		(this.formUploadUrl.getElements('input')).each(function(item)
		{
			console.log(item.name);
			
			data[item.name] = item.value;
		});

		
		if (this.Request) this.Request.cancel();

		this.Request = new FileManager.Request(
		{
			url: this.options.url + '/uploadUrl',
			onSuccess: (function(j)
			{
// Here
				
			}).bind(this),
			data: data
		}, this).post();
	},

	addUploadUrlField: function()
	{
		var id = (this.formUploadUrl.getElements('div')).length;
		var div = new Element('div', {'class':'clear mt10 h20'});
		div.adopt(
			new Element('input', {type:'text', style:'width:70%', 'class':'inputtext left mr5', name:'url' + id}),
			new Element('input', {type:'text', style:'width:25%', 'class':'inputtext left', name:'name' + id})
		);
		div.inject(this.formUploadUrl, 'bottom');
	},

	/**
	 * 
	 *
	 */
	deselect: function(el)
	{
		if (el && this.Current != el) return;

		if (el) this.fillInfo();
		if (this.Current) this.Current.removeClass('selected');
		this.Current = null;

		this.switchButton();
	},

	load: function(dir, nofade)
	{
		this.deselect();
		
		if (this.Request) this.Request.cancel();

		this.Request = new FileManager.Request(
		{
			url: this.options.url,
			onSuccess: (function(j)
			{
				// Error dialog if a problem occurs during Filemanager init.
				if (j && j.error)
				{
					new Dialog((this.language[j.error]) , {language: {confirm: this.language.ok}, buttons: ['confirm']});
					return;
				}

				this.fill(j, nofade);
				
			}).bind(this),
			data: {
				directory: dir
			}
			
		}, this).post();
	},

	destroy: function(e, file)
	{
		e.stop();
		
		this.tips.hide();
		
		var self = this;
		new Dialog(this.language.destroyfile, {
			language: {
				confirm: this.language.destroy,
				decline: this.language.cancel
			},
			onOpen: this.onDialogOpen.bind(this),
			onClose: this.onDialogClose.bind(this),
			onConfirm: function(){
				new FileManager.Request(
				{
					url: self.options.url + '/destroy',
					data: {
						file: file.name,
						directory: self.Directory
					},
					onSuccess: function(j)
					{
						if (!j || j.content!='destroyed')
						{
							new Dialog(self.language.nodestroy, {language: {confirm: self.language.ok}, buttons: ['confirm']});
							return;
						}
						
						self.fireEvent('modify', [$unlink(file)]);
						
						file.element.getParent().fade(0).get('tween').chain(function()
						{
							self.deselect(file.element);
							this.element.destroy();
							self.load(self.Directory);
						});
					}
				}, self).post();
			}
		});

	},

	rename: function(e, file)
	{
		e.stop();
		
		this.tips.hide();
		
		var name = file.name;
		if (file.mime != 'text/directory') name = name.replace(/\..*$/, '');

		var self = this;
		
		new Dialog(this.language.renamefile, {
			language: {
				confirm: this.language.rename,
				decline: this.language.cancel
			},
			content: [
				new Element('input', {'class': 'rename', value: name})
			],
			onOpen: this.onDialogOpen.bind(this),
			onClose: this.onDialogClose.bind(this),
			onShow: function()
			{
				var self = this;
				this.el.getElement('input').addEvent('keyup', function(e){
					if (e.key=='enter') self.el.getElement('button-confirm').fireEvent('click');
				}).focus();
			},
			onConfirm: function()
			{
				new FileManager.Request(
				{
					url: self.options.url + '/move',
					onSuccess: (function(j){
						if (!j || !j.name) return;

						self.fireEvent('modify', [$unlink(file)]);

						file.element.getElement('span').set('text', j.name);
						file.name = j.name;
						self.fillInfo(file);
					}).bind(this),
					data: {
						file: file.name,
						name: this.el.getElement('input').get('value'),
						directory: self.Directory
					}
				}, self).post();
			}
		});
	},
	

	/**
	 * Fills the file browser
	 * @param	JSON object		Files / folders JSON object
	 *
	 */
	fill: function(j, nofade)
	{
		this.Directory = j.path;
		this.CurrentDir = j.dir;

		// Fills the folder info
		this.fillInfo(j.dir);

		this.browser.empty();

		if (!j.files) return;

		var els = [[], []];
		
		// Thumbs container
		var filelist = new Element('div', {'class': 'filemanager-filelist'});
		this.details.adopt(filelist);

		var self = this;
		var timer;

		$each(j.files, function(file)
		{
			file.dir = j.path;

			var el = file.element = new Element('span', {'class': 'fi', href: '#'}).adopt(
				new Asset.image(this.options.assetBasePath + 'Icons/' + file.icon + '.png'),
				new Element('span', {text: file.name})
			).store('file', file).addEvents(
			{
				'click': function(e){
					e.stop();
					$clear(timer);
					timer = self.relayClick.delay(0, el);
				}
			});
			
			var bIcons = new Element('span', {'class': 'browser-icons'});
			var icons = [];
			
			// Files
			if (file.mime != 'text/directory')
			{
				// File download icon
				icons.push(new Asset.image(this.options.assetBasePath + 'disk.png', {title: this.language.download}).addClass('browser-icon').addEvent('click', (function(e){
					this.tips.hide();
					e.stop();
					window.open(this.options.baseURL + this.normalize(this.Directory + '/' + file.name));
				}).bind(this)).inject(bIcons, 'top'));
				
				// Get thumbnails
				var thumb = new Element('div', {'class': 'thumb ' + file.icon}).setStyles({
					'width': self.options.thumbSize + 'px',
					'height': self.options.thumbSize + 'px'
				});

				// Get the pictures thumbnails
				if (file.mime && (file.mime).substring(0,5) == 'image')
				{
					this.Request = new Request.JSON({
					    url: self.options.url + '/thumb',
					    async: true,
					    link:'chain',
						onSuccess: (function(j)
						{
							var d = new Date();
							thumb.setStyle('backgroundImage', 'url(' + j.url + '?'+ d.getTime() +')');
						}),
						data: {
							path: file.path
						}
					}).post();
				}
				
				var domfile = new Element('div', {'class': 'file'}).adopt([
					thumb,
					new Element('div', {'class': 'name'}).setStyle('width', self.options.thumbSize).set('text', file.name)
				]).store('file', file).addEvents(
				{
					'click': function(){
						$clear(timer);
				        timer = self.relayClick.delay(500, domfile); 
					},
					'dblclick': function(){
						$clear(timer);
						timer = self.relayDblClick.delay(0, domfile);
					}
				});
				
				domfile.inject(filelist);
			}
			
			// Rename / destroy icons
			if (file.name != '..')
				['rename', 'destroy'].each(function(v){
					icons.push(new Asset.image(this.options.assetBasePath + v + '.png', {title: this.language[v]}).addClass('browser-icon').addEvent('click', this[v].bindWithEvent(this, [file])).injectTop(bIcons));
				}, this);

			// Put the DOM browserlist element to els
			els[file.mime == 'text/directory' ? 1 : 0].push(el);
			if (file.name == '..') el.setOpacity(0.7);
//			el.injectTop(new Element('li').adopt(bIcons).inject(this.browser)).store('parent', el.getParent());
			bIcons.inject(el, 'top');
			el.injectTop(new Element('li').inject(this.browser)).store('parent', el.getParent());

			icons = $$(icons).appearOn(el.getParent('li'), 1);
		}, this);


		var revert = function(el)
		{
			el.setOpacity(1).store('block', true).removeClass('drag').removeClass('move').setStyles({
				zIndex: '',
				position: 'relative',
				width: 'auto',
				left: 0,
				top: 0
			}).inject(el.retrieve('parent'));

			el.getElements('img.browser-icon').setOpacity(0);
			
			document.removeEvents('keydown', self.bound.keydown).removeEvents('keyup', self.bound.keydown);
			self.imageadd.fade(0);

			// Stop the scroller
			self.scroller.stop();
		
			self.relayClick.apply(el);
		};
		
		// Make files draggable
		$$(els[0]).makeDraggable(
		{
			droppables: $$(this.droppables, els[1]),

			onDrag: function(el, e)
			{
				var cpos = el.retrieve('cpos');
				
				el.setStyles({
					display: 'block',
					left: e.page.x - cpos.x + 12,
					top: e.page.y - cpos.y + 10
				});
			
				self.imageadd.setStyles({
					left: e.page.x - cpos.x,
					top: e.page.y - cpos.y + 12
				});
			},

			onBeforeStart: function(el)
			{
				self.deselect();
				self.tips.hide();

				// start the scroller				
				self.scroller.start();

				el.store('cpos', self.container.getPosition());
			},

			onCancel: revert,

			onStart: function(el, e)
			{	
				var position = el.getPosition();

				var cpos = el.retrieve('cpos');

				el.addClass('drag').setStyles({
					zIndex: 100000,
					position: 'absolute',
					width: el.getWidth() - el.getStyle('paddingLeft').toInt(),
					display: 'none',
					left: e.page.x - cpos.x + 10,
					top: e.page.y - cpos.y + 10

				}).inject(self.container);
				
				el.setOpacity(0.7).addClass('move');
				document.addEvents({
					keydown: self.bound.keydown,
					keyup: self.bound.keyup
				});
			},

			onEnter: function(el, droppable){
				droppable.addClass('droppable');
			},

			onLeave: function(el, droppable){
				droppable.removeClass('droppable');
			},

			onDrop: function(el, droppable, e)
			{
				revert(el);

				if (e.control || e.meta || !droppable) el.setStyles({left: 0, top: 0});
				if (!droppable && !e.control && !e.meta) return;
				
				var dir;
				if (droppable){
					droppable.addClass('selected').removeClass('droppable');
					(function(){ droppable.removeClass('selected'); }).delay(300);
					if (self.onDragComplete(el, droppable)) return;

					dir = droppable.retrieve('file');
				}
				var file = el.retrieve('file');

				new FileManager.Request({
					url: self.options.url + '/move',
					data: {
						file: file.name,
						directory: self.Directory,
						newDirectory: dir ? dir.dir + '/' + dir.name : self.Directory,
						copy: e.control || e.meta ? 1 : 0
					},
					onSuccess: function(){
						if (!dir) self.load(self.Directory);
					}
				}, self).post();

				self.fireEvent('modify', [$unlink(file)]);

				if (!e.control && !e.meta)
					el.fade(0).get('tween').chain(function(){
						self.deselect(el);
						el.getParent().destroy();
					});
			}
		});

		$$(els).setStyles({left: 0, top: 0});

		this.tips.attach(this.browser.getElements('img.browser-icon'));
	},
	
	
	/**
	 * Displays Infos about the file or directory
	 * Calls an XHR Request to update these infos
	 *
	 */
	fillInfo: function(file, path)
	{
		if (!file) file = this.CurrentDir;
		if (!path) path = this.Directory;
		if (!file) return;
		var size = this.size(file.size);

		this.info.getElement('img').set({
			src: this.options.assetBasePath + 'Icons/' + file.icon + '.png',
			alt: file.mime
		});
		
		this.initFileInfo();
		
		this.fireHooks('cleanup');

		this.info.getElement('h1').set('text', file.name);
		this.info.getElement('dd.filemanager-modified').set('text', file.date);
		this.info.getElement('dd.filemanager-type').set('text', file.mime);
		this.info.getElement('dd.filemanager-size').set('text', !size[0] && size[1] == 'Bytes' ? '-' : (size.join(' ') + (size[1] != 'Bytes' ? ' (' + file.size + ' Bytes)' : '')));

		var text = [], pre = [];

		path.split('/').each(function(v){
			if (!v) return;

			pre.push(v);
			text.push(new Element('a', {
					'class': 'icon',
					href: '#',
					text: v
				}).addEvent('click', (function(e, dir){
					e.stop();
					this.load(dir);
				}).bindWithEvent(this, [pre.join('/')]))
			);
			text.push(new Element('span', {text: ' / '}));
		}, this);

		text.pop();
		text[text.length-1].addClass('selected').removeEvents('click').addEvent('click', function(e){ e.stop(); });

		this.info.getElement('dd.filemanager-dir').empty().adopt(new Element('span', {text: '/ '}), text);

		// Exit if directory
		if (file.mime=='text/directory') return;

		if (this.Request) this.Request.cancel();

		this.Request = new FileManager.Request(
		{
			url: this.options.url + '/detail',
			onSuccess: (function(j)
			{
				var prev = this.preview.removeClass('filemanager-loading').set('html', j && j.content ? j.content.substitute(this.language, /\\?\$\{([^{}]+)\}/g) : '').getElement('img.prev');
				if (prev) prev.addEvent('load', function()
				{
					this.setStyle('background', 'none');
				});

				var els = this.preview.getElements('button');
				if (els) els.addEvent('click', function(e)
				{
					e.stop();
					window.open(this.get('value'));
				});
			}).bind(this),
			data: {
				directory: this.Directory,
				file: file.name
			}
		}, this).post();
	},

	size: function(size){
		var tab = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
		for(var i = 0; size > 1024; i++)
			size = size/1024;

		return [Math.round(size), tab[i]];
	},

	normalize: function(str){
		return str.replace(/\/+/g, '/');
	},


	/**
	 * Enable / Disable "Select file" button
	 *
	 */
	switchButton: function()
	{
		var chk = !!this.Current;
		var el = this.menu.getElement('button.filemanager-open');
		if (el) el.set('disabled', !chk)[(chk ? 'remove' : 'add') + 'Class']('disabled');
	},

	addMenuButton: function(name){
		var el = new Element('button', {
			'class': 'filemanager-' + name,
			text: this.language[name]
		}).inject(this.menu, 'top');
		if (this[name]) el.addEvent('click', this[name].bind(this));
		return el;
	},
	
	/**
	 * Adds file-info detail containers
	 *
	 */
	initFileInfo: function ()
	{
		this.details.empty();
	
		new Element('dl').adopt([
			new Element('dt', {text: this.language.modified}),
			new Element('dd', {'class': 'filemanager-modified'}),
			new Element('dt', {text: this.language.type}),
			new Element('dd', {'class': 'filemanager-type'}),
			new Element('dt', {text: this.language.size}),
			new Element('dd', {'class': 'filemanager-size'}),
			new Element('dt', {text: this.language.dir}),
			new Element('dd', {'class': 'filemanager-dir'})
		]).inject(this.details);

		// Information file preview
		this.preview = new Element('div', {'class': 'filemanager-preview'}).addEvent('click:relay(img.preview)', function(){
			self.fireEvent('preview', [this.get('src')]);
		});
		this.details.adopt(this.preview);
	},
	
	fireHooks: function(hook){
		var args = Array.slice(arguments, 1);
		for(var key in this.hooks[hook]) this.hooks[hook][key].apply(this, args);
		return this;
	},
	
	onRequest: function(){ this.loader.set('opacity', 1); },
	onComplete: function(){ this.loader.fade(0); },
	onDialogOpen: $empty,
	onDialogClose: $empty,
	onDragComplete: $lambda(false)	
});

FileManager.Request = new Class({
	
	Extends: Request.JSON,
	
	initialize: function(options, filebrowser){
		this.parent(options);
		
		if (filebrowser) this.addEvents({
			request: filebrowser.onRequest.bind(filebrowser),
			complete: filebrowser.onComplete.bind(filebrowser)
		});
	}
	
});

FileManager.Language = {};