/*
---

description: FileManager, Ionize version.

authors: Christoph Pojer (@cpojer), Fabian Vogelsteller (@frozeman)

license: MIT-style license

requires:
  core/1.3.1: '*'
  more/1.3.1.1: [Array.Extras, String.QueryString, Hash, Element.Delegation, Element.Measure, Fx.Scroll, Fx.SmoothScroll, Drag, Drag.Move, Assets, Tips ]

provides: Filemanager

...
*/

var MooFileManagerUniqueID = 1;

/**

Class: FileManager
	Creates a Filemanager.
	
Syntax:
	(start code)
	new Filemanager(options);
	(end)	

Arguments:
	options

Options:
	directory - (string)
    url - (string). Default to null,
    assetBasePath - (string). Default to null,
    language - (String). Default to 'en',
    selectable - (boolean). Default to false,
    destroy - (boolean). Default to true,
    rename - (boolean). Default to true,
    download - (boolean). Default to false,
    createFolders - (boolean). Default to false,
    filter - (string). Default to '',
    hideOnClick - (boolean). Default to false,
    hideClose - (boolean). Default to false. If the 'standalone' option is set to true, this option will automatically set to true.
    hideOverlay - (boolean). Default to false. If the 'standalone' option is set to true, this option will automatically set to true.
	hideQonDelete - (boolean). Default to false,
	standalone - (boolean). Default to true. If set to false, returns the Filemanager without enclosing window / overlay.
	parentContainer - (string). ID of the parent container. If not set, FM will consider its first container parent for fitSizes();
	advancedEffects - (boolean). Default to true. If set to false, it disables the fading effects.
	hideOnSelect - (boolean). Default to true. If set to false, it leavers the FM open after a picture select.

*/
var FileManager = new Class({

  Implements: [Options, Events],

  Request: null,
  Directory: null,
  Current: null,
  ID: null,

  options: {
    /*onComplete: function(){},
    onModify: function(){},
    onShow: function(){},
    onHide: function(){},
    onPreview: function(){}*/
    directory: '',
    url: null,
    assetBasePath: null,
    language: 'en',
    selectable: false,
    destroy: true,
    rename: true,
    download: false,
    createFolders: true,
    filter: '',
    hideOnClick: false,
    hideClose: false,
    hideOverlay: false,
	hideQonDelete: false,
	hideonSelect: false,
	standalone: false,
	parentContainer: null,
	advancedEffects: false,
	thumbSize: 120
  },

  hooks: {
    show: {},
    cleanup: {}
  },

  initialize: function(options)
  {
    this.setOptions(options);
    this.ID = MooFileManagerUniqueID++;
    this.dragZIndex = 1300;
    this.droppables = [];
    this.assetBasePath = this.options.assetBasePath.replace(/(\/|\\)*$/, '/');
    this.Directory = this.options.directory;
    this.listType = 'list';
    this.dialogOpen = false;
    this.usingHistory = false;
    this.fmShown = false;

    this.language = Object.clone(FileManager.Language.en);
    if(this.options.language != 'en') this.language = Object.merge(this.language, FileManager.Language[this.options.language]);

// Added by Partikule
	if (this.options.standalone == false)
	{
		this.options.hideOverlay = true;
		this.options.hideClose = true;
	}

    this.container = new Element('div', {'class': 'filemanager-container' + (Browser.opera ? ' filemanager-engine-presto' : '') + (Browser.ie ? ' filemanager-engine-trident' : '') + (Browser.ie8 ? '4' : '') + (Browser.ie9 ? '5' : '')});
    this.filemanager = new Element('div', {'class': 'filemanager'}).inject(this.container);
	this.header = new Element('div', {'class': 'filemanager-header'}).inject(this.filemanager);
	this.menu = new Element('div', {'class': 'filemanager-menu'}).inject(this.filemanager);
    this.loader = new Element('div', {'class': 'loader', opacity: 0, tween: {duration: 300}}).inject(this.header);
    this.previewLoader = new Element('div', {'class': 'loader', opacity: 0, tween: {duration: 300}});
    this.browserLoader = new Element('div', {'class': 'loader', opacity: 0, tween: {duration: 300}});
    
    // switch the path, from clickable to input text
    this.clickablePath = new Element('span', {'class': 'filemanager-dir'});
    this.selectablePath = new Element('input',{'type':'text','class': 'filemanager-dir','readonly':'readonly'});
    this.pathTitle = new Element('a', {href:'#','class': 'filemanager-dir-title',text: this.language.dir}).addEvent('click',(function(e){
      e.stop();
      if(this.header.getElement('span.filemanager-dir')!= null) {
        this.selectablePath.setStyle('width',(this.header.getSize().x - this.pathTitle.getSize().x - 55));
        this.selectablePath.replaces(this.clickablePath);
      } else
        this.clickablePath.replaces(this.selectablePath);
    }).bind(this));
    this.header.adopt(this.pathTitle,this.clickablePath);

// Altered by Partikule
// Because the header is positionned -30px before the container, we hide it for the moment if th FM isn't standalone.
// Need to think about a better integration
	if (this.options.standalone == false)
	{
		this.header.hide();
		this.filemanager.setStyle('width','100%');
	}

    var self = this;

    // -> catch a click on an element in the file/folder browser
    this.relayClick = function(e)
    {
      if(e) e.stop();
      self.storeHistory = true;

      var file = this.retrieve('file');
      if (this.retrieve('edit')) {
        this.eliminate('edit');
        return;
      }
      if (file.mime == 'text/directory'){
        this.addClass('selected');
        self.load(self.Directory + file.name);
        return;
      }

      self.fillInfo(file);
      if (self.Current) self.Current.removeClass('selected');
      self.Current = this.addClass('selected');

      self.switchButton();
    };

// Partikule
	// Cacth a double click click on thumb list in the preview thumb list
	this.relayDblClick = function(e)
	{
		if(e) e.stop();
		
		var file = this.retrieve('file');

		if (self.Current) self.Current.removeClass('selected');
		self.Current = this.addClass('selected');

		self.CurrentFile = file;

		self.open();
	};
// /Partikule

    this.toggleList = function(e) {
      if(e) e.stop();
      $$('.filemanager-browserheader a').set('opacity',0.5);
      if(!this.browserMenu_thumb.retrieve('set',false)) {
        this.browserMenu_list.store('set',false);
        this.browserMenu_thumb.store('set',true).set('opacity',1);
        this.listType = 'thumb';
        if(typeof jsGET != 'undefined') jsGET.set('fmListType=thumb');
      } else {
        this.browserMenu_thumb.store('set',false);
        this.browserMenu_list.store('set',true).set('opacity',1);
        this.listType = 'list';
        if(typeof jsGET != 'undefined') jsGET.set('fmListType=list');
      }
      this.load(this.Directory);
    };

    this.browsercontainer = new Element('div',{'class': 'filemanager-browsercontainer'}).inject(this.filemanager);
    this.browserheader = new Element('div',{'class': 'filemanager-browserheader'}).inject(this.browsercontainer);
    this.browserheader.adopt(this.browserLoader);
    this.browserScroll = new Element('div', {'class': 'filemanager-browserscroll'}).inject(this.browsercontainer).addEvent('mouseover',(function(){
      this.browser.getElements('span.fi.hover').each(function(span){ span.removeClass('hover'); });
    }).bind(this));
    
// Partikule : Add a scroller to scroll the browser list when moving a file
	this.scroller = new Scroller(this.browserScroll);
// /Partikule


    this.browserMenu_thumb = new Element('a',{
        'id':'togggle_side_boxes',
        'class':'listType'
      }).set('opacity',0.5).addEvents({
        click: this.toggleList.bind(this)
      });
    this.browserMenu_list = new Element('a',{
        'id':'togggle_side_list',
        'class':'listType'
      }).set('opacity',1).addEvents({
        click: this.toggleList.bind(this)
      });
// Partikule : Thumb list in preview panel
    this.browserMenu_thumbList = new Element('a',{
        'id':'togggle_side_thumbs'
      }).set('opacity',1).addEvent('click', function(){
		if (typeof jsGET !== 'undefined')
			jsGET.clear();
        self.load(self.Directory, true);
   		return self.deselect();
      });
// /Partikule
    this.browserheader.adopt([this.browserMenu_thumbList, this.browserMenu_thumb,this.browserMenu_list]);

    this.browser = new Element('ul', {'class': 'filemanager-browser'}).inject(this.browserScroll);

    if(this.options.createFolders) this.addMenuButton('create');
    if(this.options.download) this.addMenuButton('download');
    if(this.options.selectable) this.addMenuButton('open');

    this.info = new Element('div', {'class': 'filemanager-infos', opacity: 0}).inject(this.filemanager);

    var head = new Element('div', {'class': 'filemanager-head'}).adopt([
      new Element('img', {'class': 'filemanager-icon'}),
      new Element('h1')
    ]);
	this.info.adopt(head);

// Partikule. Removed :
// 1. To gain more vertical space for preview
// 2. Because the user now this is info about the file
// 3. Less is more :-)
//    this.info.adopt([head, new Element('h2', {text: this.language.information})]);

    new Element('dl').adopt([
      new Element('dt', {text: this.language.modified}),
      new Element('dd', {'class': 'filemanager-modified'}),
      new Element('dt', {text: this.language.type}),
      new Element('dd', {'class': 'filemanager-type'}),
      new Element('dt', {text: this.language.size}),
      new Element('dd', {'class': 'filemanager-size'})
    ]).inject(this.info);

    this.preview = new Element('div', {'class': 'filemanager-preview'}).addEvent('click:relay(img.preview)', function(){
      self.fireEvent('preview', [this.get('src')]);
    });

// Partikule. Removed :
// 1. To gain more vertical space for preview
// 2. Because the user now this is info about the file
/*
    this.info.adopt([
      new Element('h2', {'class': 'filemanager-headline', text: this.language.more}),
      this.preview
    ]);
*/
	this.info.adopt(this.preview);


    if(!this.options.hideClose) {
      this.closeIcon = new Element('a', {
        'class': 'filemanager-close',
        opacity: 0.5,
        title: this.language.close,
        events: {click: this.hide.bind(this)}
      }).inject(this.filemanager).addEvent('mouseover',function(){this.fade(1);}).addEvent('mouseout',function(){this.fade(0.5);});
    }

    this.tips = new Tips({
      className: 'tip-filebrowser',
      offsets: {x: 15, y: 0},
      text: null,
      showDelay: 50,
      hideDelay: 50,
      onShow: function()
      {
// Partikule
// Certainly a better way to optimize this....
        if (self.advancedEffects == true)
        	this.tip.set('tween', {duration: 250}).setStyle('display', 'block').fade(1);
        else
         	this.tip.setStyle('display', 'block');
      },
      onHide: function()
      {
        if (self.advancedEffects == true)
        {
        	this.tip.fade(0).get('tween').chain(function()
        	{
				this.element.setStyle('display', 'none');
		    });
		}
		else
		{
			this.tip.setStyle('display', 'none');		
		}
      }
    });
    if(!this.options.hideClose)
      this.tips.attach(this.closeIcon);

    this.imageadd = new Asset.image(this.assetBasePath + 'Images/add.png', {
      'class': 'browser-add'
    }).set('opacity', 0).set('tween',{duration:300}).inject(this.container);

// Move to bottom by Partikule
//    this.container.inject(document.body);

    if(!this.options.hideOverlay) {
      this.overlay = new Overlay(this.options.hideOnClick ? {
        events: {click: this.hide.bind(this)}
      } : null);
    }

    this.bound = {
      keydown: (function(e){
        if (e.control || e.meta) this.imageadd.fade(1);
      }).bind(this),
      keyup: (function(){
        this.imageadd.fade(0);
      }).bind(this),
      toggleList: (function(e){
        if(this.dialogOpen) return;
        if(e.key=='tab') {
          e.preventDefault();
          this.toggleList();
        }
      }).bind(this),
      keyesc:( function(e) {
        if(this.dialogOpen) return;

        if (e.key=='esc') this.hide();
      }).bind(this),
      keyboardInput: (function(e) {
        if(this.dialogOpen) return;
        if (e.key=='up') {
          e.preventDefault();
          this.browserSelection('up');
        }
        if (e.key=='down') {
          e.preventDefault();
          this.browserSelection('down');
        }
        if (e.key=='enter') {
          e.preventDefault();
          this.browserSelection('enter');
        }
      }).bind(this),
      scroll: (function(){
        this.fireEvent('scroll');
        this.fitSizes();
      }).bind(this)
    };

    this.fitSizes = function()
    {
// Partikule : Add the standalone check
		if (this.standalone == true)
		{
			this.filemanager.center(this.offsets);
		}
		else
		{
			parent = (this.options.parentContainer != null) ? $(this.options.parentContainer) : this.container.getParent();
			if (parent)
			{
				parentSize = parent.getSize();
				this.filemanager.setStyle('height',parentSize.y);
			}
		}
		
		containerSize = this.filemanager.getSize();
		headerSize = this.browserheader.getSize();
		menuSize = this.menu.getSize();
		this.browserScroll.setStyle('height',containerSize.y - headerSize.y);
		this.info.setStyle('height',containerSize.y - menuSize.y);
    };

    // ->> autostart filemanager when set
    if(!this.galleryPlugin) {
      if(typeof jsGET != 'undefined' && jsGET.get('fmID') == this.ID)
          this.show();
      else {
        window.addEvent('jsGETloaded',(function(){
          if(typeof jsGET != 'undefined' && jsGET.get('fmID') == this.ID)
            this.show();
        }).bind(this));
      }
    }
    
// Added by Partikule
	if (this.options.standalone == true)
	{
    	this.container.inject(document.body);
    }
    else
    {
    	this.options.hideOverlay = true;
    	return this;
    }
  },

  hashHistory: function(vars) { // get called from the jsGET listener
    this.storeHistory = false;
    //console.log(vars);
    if(vars.changed['fmPath'] == '')
      vars.changed['fmPath'] = '/';

    Object.each(vars.changed,function(value,key) {
        if(key == 'fmPath') {
          this.load(value);
        }

        if(key == 'fmFile') {
          this.browser.getElements('span.fi span').each((function(current) {
            current.getParent('span.fi').removeClass('hover');
            if(current.get('title') == value) {
              this.deselect();
              this.Current = current.getParent('span.fi');
              new Fx.Scroll(this.browserScroll,{duration: 250,offset:{x:0,y:-(this.browserScroll.getSize().y/4)}}).toElement(current.getParent('span.fi'));
              current.getParent('span.fi').addClass('selected');
              this.fillInfo(current.getParent('span.fi').retrieve('file'));
            }
          }).bind(this));
        }
    },this);
  },

  show: function(e)
  {
    if(e) e.stop();
    if(this.fmShown) return;
    this.fmShown = true;
    this.onShow = false;
    
    // get and set history
    if(typeof jsGET != 'undefined')
    {
      if(jsGET.get('fmFile') != null) this.onShow = true;
      
      if(jsGET.get('fmListType') != null) {
        $$('.filemanager-browserheader a').set('opacity',0.5);
        this.listType = jsGET.get('fmListType');
        if(this.listType == 'thumb')
          this.browserMenu_thumb.store('set',true).set('opacity',1);
        else
          this.browserMenu_list.store('set',true).set('opacity',1);
      }
      if(jsGET.get('fmPath') != null) this.Directory = jsGET.get('fmPath');
      jsGET.set({'fmID':this.ID,'fmPath':this.Directory});
      this.hashListenerId = jsGET.addListener(this.hashHistory,false,this);
    }

    this.load(this.Directory);
    
    if( ! this.options.hideOverlay)
      this.overlay.show();

    this.info.set('opacity', 0);
    this.container.set('opacity', 0);

    this.container.setStyles({
        opacity: 0,
        display: 'block'
      });

    window.addEvents({
      'scroll': this.bound.scroll,
      'resize': this.bound.scroll
    });
    
    
    // add keyboard navigation
    document.addEvent('keydown', this.bound.toggleList);
    window.addEvent('keydown', this.bound.keyesc);
    if((Browser.Engine && (Browser.Engine.trident || Browser.Engine.webkit)) || (Browser.ie || Browser.chrome || Browser.safari))
		document.addEvent('keydown', this.bound.keyboardInput);
    else
		document.addEvent('keypress', this.bound.keyboardInput);

// Partikule : Advanced effect ?   
    if (this.options.advancedEffects)
	    this.container.tween('opacity',1);
	else
	    this.container.set('opacity',1);

    this.fitSizes();
    this.fireEvent('show');
    this.fireHooks('show');
    
// Added by Partikule
   	if (this.options.standalone == false)
	{
    	return this.container;
    }

    
  },

  hide: function(e){
    if (e) e.stop();
    if(!this.fmShown) return;
    this.fmShown = false;

    // stop hashListener
    if(typeof jsGET != 'undefined') {
      jsGET.removeListener(this.hashListenerId);
      jsGET.remove(['fmID','fmPath','fmFile','fmListType']);
    }

    if(!this.options.hideOverlay)
      this.overlay.hide();
    this.tips.hide();
    this.browser.empty();
    this.container.setStyle('display', 'none');

    // add keyboard navigation
    window.removeEvent('scroll', this.bound.scroll).removeEvent('resize', this.bound.scroll);
    document.removeEvent('keydown', this.bound.toggleList);
    window.removeEvent('keydown', this.bound.keyesc);
    if((Browser.Engine && (Browser.Engine.trident || Browser.Engine.webkit)) || (Browser.ie || Browser.chrome || Browser.safari))
     document.removeEvent('keydown', this.bound.keyboardInput);
    else
     document.removeEvent('keypress', this.bound.keyboardInput);

    this.fireHooks('cleanup');
    this.fireEvent('hide');
  },

  open: function(e)
  {
    if (e) e.stop();
    
    if (!this.Current) return;
    
    this.fireEvent('complete', 
    [
      this.normalize(this.Current.retrieve('file').path),
      this.Current.retrieve('file')
    ]);

// Partikule
// Why Hide ? For some usage, it can be useful to keep it open (more than 3 files select and it will be really annoying to re-open the FM for each file select)
	if (this.options.hideOnSelect == true)
		this.hide();
  },

  download: function(e) {
    e.stop();
    if (!this.Current) return;
    window.open(this.options.url + '?event=download&file='+this.normalize(this.Current.retrieve('file').path));
  },

  create: function(e) {
    e.stop();
    var input = new Element('input', {'class': 'createDirectory','autofocus':'autofocus'});

    var self = this;
    new Dialog(this.language.createdir, {
      language: {
        confirm: this.language.create,
        decline: this.language.cancel
      },
      content: [
        input
      ],
      onOpen: this.onDialogOpen.bind(this),
      onClose: this.onDialogClose.bind(this),
      onShow: function(){
        input.addEvent('keyup', function(e){
          if (e.key == 'enter') e.target.getParent('div.dialog').getElement('button.dialog-confirm').fireEvent('click');
        }).focus();
      },
      onConfirm: function() {
        new FileManager.Request({
          url: self.options.url + '?event=create',
          onRequest: self.browserLoader.set('opacity', 1),
          onSuccess: self.fill.bind(self),
          onComplete: self.browserLoader.fade(0),
          onError: (function(xmlHttpRequest) {
            this.showError(xmlHttpRequest);
            this.browserLoader.fade(0);
          }).bind(self),
          data: {
            file: input.get('value'),
            directory: self.Directory,
            type: self.listType
          }
        }).send();
      }
    });
  },

  deselect: function(el) {
    if (el && this.Current != el) return;
    if (el) this.fillInfo();
    if (this.Current) this.Current.removeClass('selected');
    this.Current = null;
    this.switchButton();
  },

  load: function(dir, nofade) {

    this.deselect();
// Partikule : Effects
    if (!nofade && this.options.advancedEffects == true) this.info.fade(0);
    else if (!nofade) this.info.set('opacity', 0);
// /Partikule

    if (this.Request) this.Request.cancel();

    this.Request = new FileManager.Request({
      url: this.options.url,
      onRequest: (function(){
        this.browserLoader.set('opacity', 1);
      }).bind(this),
      onSuccess: (function(j) {
        this.fill(j, nofade);
      }).bind(this),
      onComplete: (function() {
        this.fitSizes();
        this.browserLoader.fade(0);
      }).bind(this),
      onError: (function(xmlHttpRequest) {
        this.showError(xmlHttpRequest);
        this.browserLoader.fade(0);
      }).bind(this),
      data: {
        directory: dir,
        type: this.listType,
        filter: this.options.filter
      }
    }, this).send();
  },

  destroy_noQasked: function(file) {
    var self = this;
	new FileManager.Request({
	  url: self.options.url + '?event=destroy',
	  data: {
		file: file.name,
		directory: self.Directory,
		filter: self.options.filter
	  },
	  onRequest: self.browserLoader.set('opacity', 1),
	  onSuccess: function(j){
		if (!j || j.content!='destroyed'){
		  new Dialog(self.language.nodestroy, {language: {confirm: self.language.ok}, buttons: ['confirm']});
		  return;
		}

		self.fireEvent('modify', [Object.clone(file)]);
		file.element.getParent().fade(0).get('tween').chain(function(){
		  self.deselect(file.element);
		  this.element.destroy();
		});
	  },
	  onComplete: self.browserLoader.fade(0),
	  onError: (function(xmlHttpRequest) {
		this.showError(xmlHttpRequest);
		this.browserLoader.fade(0);
	  }).bind(self)
	}).send();
  },

  destroy: function(file){
    var self = this;
	if (self.options.hideQonDelete) {
	  self.destroy_noQasked(file);
	}
	else {
		new Dialog(this.language.destroyfile, {
		  language: {
			confirm: this.language.destroy,
			decline: this.language.cancel
		  },
		  onOpen: this.onDialogOpen.bind(this),
		  onClose: this.onDialogClose.bind(this),
		  onConfirm: function() {
			self.destroy_noQasked(file);
		  }
		});
	}
  },

  rename: function(file) {
    var self = this;
    var name = file.name;
    var input = new Element('input', {'class': 'rename', value: name,'autofocus':'autofocus'});

    if (file.mime != 'text/directory') name = name.replace(/\..*$/, '');

    new Dialog(this.language.renamefile, {
      language: {
        confirm: this.language.rename,
        decline: this.language.cancel
      },
      content: [
        input
      ],
      onOpen: this.onDialogOpen.bind(this),
      onClose: this.onDialogClose.bind(this),
      onShow: function(){
        input.addEvent('keyup', function(e){
          if (e.key=='enter') e.target.getParent('div.dialog').getElement('button.dialog-confirm').fireEvent('click');
        }).focus();
      },
      onConfirm: (function(){
        new FileManager.Request({
          url: self.options.url + '?event=move',
          onRequest: self.browserLoader.set('opacity', 1),
          onSuccess: (function(j){
            if (!j || !j.name) return;
            self.fireEvent('modify', [Object.clone(file)]);
            file.element.getElement('span').set('text', j.name);
            file.element.addClass('selected');
            file.name = j.name;
            self.fillInfo(file);
          }).bind(this),
          onComplete: self.browserLoader.fade(0),
          onError: (function(xmlHttpRequest) {
            this.showError(xmlHttpRequest);
            this.browserLoader.fade(0);
          }).bind(self),
          data: {
            file: file.name,
            name: input.get('value'),
            directory: self.Directory,
            filter: this.options.filter
          }
        }, self).send();
      }).bind(this)
    });
  },

  browserSelection: function(direction) {
    if(this.browser.getElement('li') == null) return;

    // none is selected
    if(this.browser.getElement('span.fi.hover') == null && this.browser.getElement('span.fi.selected') == null) {
      // select first folder
      this.browser.getFirst('li').getElement('span.fi').addClass('hover');
      new Fx.Scroll(this.browserScroll,{duration: 250}).toElement(this.browser.getFirst('li').getElement('span.fi'));
    } else {
      // select the current file/folder or the one with hover
      var current = null;
      if(this.browser.getElement('span.fi.hover') == null && this.browser.getElement('span.fi.selected') != null)
        current = this.browser.getElement('span.fi.selected');
      else if(this.browser.getElement('span.fi.hover') != null)
        current = this.browser.getElement('span.fi.hover');
      var browserScrollFx = new Fx.Scroll(this.browserScroll,{duration: 150}); //offset: {x:0,y:-(this.browserScroll.getSize().y / 4)},

      // go down
      if(direction == 'down') {
        if(current.getParent('li').getNext('li') != null) {
          current.removeClass('hover');
          var next = current.getParent('li').getNext('li').getElement('span.fi');
          next.addClass('hover');
          if((current.getPosition(this.browserScroll).y + (current.getSize().y*2)) >= this.browserScroll.getSize().y)
            browserScrollFx.toElement(current);
        }
      // go up
      } else if(direction == 'up') {
        if(current.getParent('li').getPrevious('li') != null) {
          current.removeClass('hover');
          var previous = current.getParent('li').getPrevious('li').getElement('span.fi');
          previous.addClass('hover');
          if((current.getPosition(this.browserScroll).y) <= current.getSize().y) {
            browserScrollFx.start(current.getPosition(this.browserScroll).x,(this.browserScroll.getScroll().y - this.browserScroll.getSize().y + (current.getSize().y*2)));
          }
        }

      // select
      } else if(direction == 'enter') {
        this.storeHistory = true;
        this.Current = current;
        if(this.browser.getElement('span.fi.selected') != null) // remove old selected one
          this.browser.getElement('span.fi.selected').removeClass('selected');
        current.addClass('selected');
        var currentFile = current.retrieve('file');
        if(currentFile.mime == 'text/directory')
          this.load(currentFile.path.replace(this.root,''));
        else {
          this.fillInfo(currentFile);
        }
      }
    }
  },


// Partikule : Start making the code more readable
//
	
	fill: function(j, nofade)
	{
		this.Directory = j.path;
		this.CurrentDir = j.dir;
		
		// Fills the folder info
//		if (!nofade && !this.onShow) this.fillInfo(j.dir);
		this.fillInfo(j.dir)

		// Empty the browser's files list
		this.browser.empty();
		
		this.root = j.root;
		var self = this;
// Partikule
// define timer, for dblclick event.
		var timer;
		
		// set history
		if(typeof jsGET != 'undefined' && this.storeHistory && j.dir.mime == 'text/directory')
			jsGET.set({'fmPath':j.path});
		
		// Path to the files
		this.CurrentPath = this.root + this.Directory;
		var text = [], pre = [];
		
		// On error reported by backend, there WON'T be a JSON 'root' element at all times:
		// TODO: how to handle that error condition correctly?
		if (!j.root)
		{
			new Dialog(('${error}: ' + j.error).substitute(this.language, /\\?\$\{([^{}]+)\}/g) , {language: {confirm: this.language.ok}, buttons: ['confirm']});
			return;
		}
		
		// Website's rootpath
		var rootPath = j.root.slice(0,-1).split('/');
		rootPath.pop();

		this.CurrentPath.split('/').each(function(folderName)
		{
			if (!folderName) return;
			
			pre.push(folderName);
			var path = ('/'+pre.join('/')+'/').replace(j.root,'');
			
			// add non-clickable path
			if(rootPath.contains(folderName))
			{
				text.push(new Element('span', {'class': 'icon',text: folderName}));
				// add clickable path
			} 
			else
			{
				text.push(new Element('a',
				{
					'class': 'icon',
					href: '#',
					text: folderName
				}).addEvent('click', function(e)
				{
					e.stop();
					self.load(path);
				}));
			}
			text.push(new Element('span', {text: ' / '}));
		});
		
		text.pop();
		text[text.length-1].addClass('selected').removeEvents('click').addEvent('click', function(e){e.stop();});
		this.selectablePath.set('value','/'+this.CurrentPath);
		this.clickablePath.empty().adopt(new Element('span', {text: '/ '}), text);
		
		if (!j.files) return;

// Partikule
// Add of the thumbnail list in the preview panel
// Reasons : 
// - One doubleclick on one thumb in this list will select the file : quicker select
// - One click displays the preview, but with the file in bigger format : less clicks to see the picture wider.

		// Thumbs list container (in preview panel)
		var filelist = new Element('div', {'class': 'filemanager-filelist'});
		this.preview.adopt(filelist);

		
		// Generate browser list
		var els = [[], []];

	    Array.each(j.files, function(file)
	    {
			file.dir = j.path;
			var largeDir = '';
			
			// generate unique id
			var newDate = new Date;
			uniqueId = newDate.getTime();

			var icon = (this.listType == 'thumb') ? new Asset.image(file.thumbnail+'?'+uniqueId,{'class':this.listType}) : new Asset.image(file.icon);
			
			var el = file.element = new Element('span', {'class': 'fi ' + this.listType, href: '#'}).adopt(
				icon,
				new Element('span', {text: file.name, title:file.name})
			).store('file', file);
			
			// add click event, only to directories, files use the revert function (to enable drag n drop)
			if(file.mime == 'text/directory')
				el.addEvent('click',this.relayClick);
			
			// -> add icons
			var icons = [];
			
			// download icon
			if(file.mime != 'text/directory' && this.options.download)
			{
				icons.push(new Asset.image(this.assetBasePath + 'Images/disk.png', {title: this.language.download}).addClass('browser-icon').addEvent('mouseup', (function(e)
				{
					e.preventDefault();
					el.store('edit',true);
					window.open(this.options.url + '?event=download&file=' + this.normalize(file.path));
				}).bind(this)).inject(el, 'top'));
			}
			
			// rename, delete icon
			if(file.name != '..')
			{
				var editButtons = new Array();
				if(this.options.rename) editButtons.push('rename');
				if(this.options.destroy) editButtons.push('destroy');
				
				editButtons.each(function(v)
				{
					icons.push(new Asset.image(this.assetBasePath + 'Images/' + v + '.png', {title: this.language[v]}).addClass('browser-icon').addEvent('mouseup', (function(e)
					{
						e.preventDefault();
						el.store('edit',true);
						this.tips.hide();
						this[v](file);
					}).bind(this)).inject(el,'top'));
				}, this);				
			}

// Partikule : Thumbs list
			
			// Add the file in the preview thumbs list if not directory
			if (file.mime != 'text/directory')
			{
				// Thumb container
				var thumb = new Element('div', {'class': 'thumb ' + file.icon}).setStyles({
					'width': self.options.thumbSize + 'px',
					'height': self.options.thumbSize + 'px'
				});

				// Thumb background : Picture thumb or large icon of the file type
				thumb.setStyle('background-image', 'url(' + file.thumbnail + '?'+ uniqueId +')');

				var domfile = new Element('div', {'class': 'file'}).adopt([
					thumb,
					new Element('div', {'class': 'name'}).setStyle('width', self.options.thumbSize).set('text', file.name)
				]).store('file', file).addEvents(
				{
					'click': function(){
						clearTimeout(timer);
				        timer = self.relayClick.delay(500, domfile); 
					},
					'dblclick': function(){
						clearTimeout(timer);
						timer = self.relayDblClick.delay(0, domfile);
					}
				});
				
				domfile.inject(filelist);
			}
// / Partikule
			
			els[file.mime == 'text/directory' ? 1 : 0].push(el);
			
			//if (file.name == '..') el.set('opacity', 0.7);
			
			// Browser's file list
			el.inject(new Element('li',{'class':this.listType}).inject(this.browser)).store('parent', el.getParent());
			
			icons = $$(icons.map((function(icon)
			{
				this.showFunctions(icon,icon,0.5,1);
				this.showFunctions(icon,el.getParent('li'),1);
			}).bind(this)));
			
			// ->> LOAD the FILE/IMAGE from history when PAGE gets REFRESHED (only directly after refresh)
			if(this.onShow && typeof jsGET != 'undefined' && jsGET.get('fmFile') != null && file.name == jsGET.get('fmFile'))
			{
				this.deselect();
				this.Current = file.element;
				new Fx.Scroll(this.browserScroll,{duration: 250,offset:{x:0,y:-(this.browserScroll.getSize().y/4)}}).toElement(file.element);
				file.element.addClass('selected');
				this.fillInfo(file);
			}
			else if(this.onShow && jsGET.get('fmFile') == null)
				this.onShow = false;
			
		}, this);

		// -> cancel dragging
		var revert = function(el)
		{
			el.set('opacity', 1).removeClass('drag').removeClass('move').setStyles({
				opacity: 1,
				'z-index': 'auto',
				position: 'relative',
				width: 'auto',
				left: 0,
				top: 0
			}).inject(el.retrieve('parent'));
		
			document.removeEvent('keydown', self.bound.keydown).removeEvent('keyup', self.bound.keyup);
			self.imageadd.fade(0);

// Partikule
			// Stop the scroller
			self.scroller.stop();
// Partikule
	
			self.relayClick.apply(el);
		};

// Partikule : Stop making the code readable


    // -> make dragable
    $$(els[0]).makeDraggable(
    {
      droppables: $$(this.droppables.combine(els[1])),
      //stopPropagation: true,

      onDrag: function(el, e){

// Partikule
// Change the drag because of container : The container can be moved, the pos needs to be relative to the container.
// Not perfect...
// 
		var cpos = el.retrieve('cpos');
		el.setStyles({
			display: 'block',
			left: e.page.x - cpos.x + 12,
			top: e.page.y - cpos.y + 10
		});
	
		self.imageadd.setStyles({
			'left': e.page.x - cpos.x,
			'top': e.page.y - cpos.y + 12
		});
// / Partikule
/*
        self.imageadd.setStyles({
          'left': e.page.x + 25,
          'top': e.page.y + 25
        });
        self.imageadd.fade('in');
*/
      },

      onBeforeStart: function(el){
        self.deselect();
        self.tips.hide();
        var position = el.getPosition();
		
// Partikule : Store the container position and start the scroller

		// start the scroller				
		self.scroller.start();

		el.store('cpos', self.container.getPosition());

// / Partikule
/*
        el.addClass('drag').setStyles({
          'z-index': self.dragZIndex,
          'position': 'absolute',
          'width': el.getWidth() - el.getStyle('paddingLeft').toInt() - el.getStyle('paddingRight').toInt(),
          'left': position.x,
          'top': position.y
        }).inject(self.container);
*/
      },

      onCancel: revert,

      onStart: function(el, e)
      {
// Partikule : Add
		var position = el.getPosition();

		var cpos = el.retrieve('cpos');

		el.addClass('drag').setStyles({
			'z-index': self.dragZIndex,
			'position': 'absolute',
			'width': el.getWidth() - el.getStyle('paddingLeft').toInt(),
			'display': 'none',
			'left': e.page.x - cpos.x + 10,
			'top': e.page.y - cpos.y + 10

		}).inject(self.container);
// / Partikule

        el.set('opacity', 0.7).addClass('move');
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

      onDrop: function(el, droppable, e){
        revert(el);

        if (e.control || e.meta || !droppable) el.setStyles({left: 0, top: 0});
        if (!droppable && !e.control && !e.meta) return;

        var dir;
        if (droppable){
          droppable.addClass('selected').removeClass('droppable');
          (function(){droppable.removeClass('selected');}).delay(300);
          if (self.onDragComplete(el, droppable)) return;

          dir = droppable.retrieve('file');
        }
        var file = el.retrieve('file');

        new FileManager.Request({
//          url: self.options.url + '?event=move',
          url: self.options.url + '/move',
          data: {
            file: file.name,
            filter: self.options.filter,
            directory: self.Directory,
            newDirectory: dir ? (dir.dir ? dir.dir + '/' : '') + dir.name : self.Directory,
            copy: e.control || e.meta ? 1 : 0
          },
          onSuccess: function(){
            if (!dir) self.load(self.Directory);
          },
          onError: (function(xmlHttpRequest) {
            this.showError(xmlHttpRequest);
            this.browserLoader.fade(0);
          }).bind(self)
        }, self).send();

        self.fireEvent('modify', [Object.clone(file)]);

        if (!e.control && !e.meta)
          el.fade(0).get('tween').chain(function(){
            self.deselect(el);
            el.getParent().destroy();
          });
      }
    });

    $$(els[0].combine(els[1])).setStyles({'left': 0, 'top': 0});

    this.tips.attach(this.browser.getElements('img.browser-icon'));
  },

  fillInfo: function(file) {
    if (!file) file = this.CurrentDir;
    if (!file) return;

    // set file history
    //console.log(this.storeHistory);
    if(typeof jsGET != 'undefined' && this.storeHistory) {
      if(file.mime != 'text/directory')
        jsGET.set({'fmFile':file.name});
      else
        jsGET.set({'fmFile':''});
    }

    var size = this.size(file.size);
    var icon = file.icon;

    this.switchButton();

			
    this.info.getElement('img').set({
      src: icon,
      alt: file.mime
    });

// Partikule
// Again with effects... could be better.....
	if(this.options.advancedEffects == true )
		this.info.fade(1);
	else
	    this.info.set('opacity', 1).setStyle('display', 'block');

// / Partikule

    this.fireHooks('cleanup');
    this.preview.empty();

    this.info.getElement('h1').set('text', file.name);
    this.info.getElement('h1').set('title', file.name);
    this.info.getElement('dd.filemanager-modified').set('text', file.date);
    this.info.getElement('dd.filemanager-type').set('text', file.mime);
    this.info.getElement('dd.filemanager-size').set('text', !size[0] && size[1] == 'Bytes' ? '-' : (size.join(' ') + (size[1] != 'Bytes' ? ' (' + file.size + ' Bytes)' : '')));
//    this.info.getElement('h2.filemanager-headline').setStyle('display', file.mime == 'text/directory' ? 'none' : 'block');

    if (file.mime=='text/directory') return;

    if (this.Request) this.Request.cancel();

    this.Request = new FileManager.Request({
//      url: this.options.url + '?event=detail',
	  url: this.options.url + '/detail',
      onRequest: (function() {
        this.previewLoader.inject(this.preview);
        this.previewLoader.set('opacity', 1);
      }).bind(this),
      onSuccess: (function(j) {

        this.previewLoader.fade(0).get('tween').chain((function() {
          this.previewLoader.dispose();

		  if (0)   // debugging only:
		    if (j && !j.status)
		    {
		      new Dialog(('' + j.error).substitute(this.language, /\\?\$\{([^{}]+)\}/g) , {language: {confirm: this.language.ok}, buttons: ['confirm']});
		    }

          var prev = this.preview.removeClass('filemanager-loading').set('html', j && j.content ? j.content.substitute(this.language, /\\?\$\{([^{}]+)\}/g) : '').getElement('img.preview');
          if (prev) prev.addEvent('load', function(){
            this.setStyle('background', 'none');
          });

          var els = this.preview.getElements('button');
          if (els) els.addEvent('click', function(e){
            e.stop();
            window.open(this.get('value'));
          });

          if(typeof milkbox != 'undefined')
            milkbox.reloadPageGalleries();

        }).bind(this));
      }).bind(this),
      onError: (function(xmlHttpRequest) {
        this.previewLoader.dispose();
        this.showError(xmlHttpRequest);
      }).bind(this),
      data: {
        directory: this.Directory,
        file: file.name,
        filter: this.options.filter
      }
    }, this).send();

  },

  showFunctions: function(icon,appearOn,opacityBefore,opacityAfter) {
    var opacity = [opacityBefore || 1, opacityAfter || 0];
    icon.set({
      opacity: opacity[1]
    });

    $(appearOn).addEvents({
      mouseenter: (function(){this.setStyle('opacity',opacity[0]);}).bind(icon),
      mouseleave: (function(){this.setStyle('opacity',opacity[1]);}).bind(icon)
    });
    return icon;
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

  switchButton: function() {
    var chk = !!this.Current;
    var els = new Array();
    els.push(this.menu.getElement('button.filemanager-open'));
    els.push(this.menu.getElement('button.filemanager-download'));
    els.each(function(el){
      if (el) el.set('disabled', !chk)[(chk ? 'remove' : 'add') + 'Class']('disabled');
    });
  },

  // adds buttons to the file main menu, which onClick start a method with the same name
  addMenuButton: function(name) {
    var el = new Element('button', {
      'class': 'filemanager-' + name,
      text: this.language[name]
    }).inject(this.menu, 'top');
    if (this[name]) el.addEvent('click', this[name].bind(this));
    return el;
  },

  fireHooks: function(hook){
    var args = Array.slice(arguments, 1);
    for(var key in this.hooks[hook]) this.hooks[hook][key].apply(this, args);
    return this;
  },

  showError: function(text) {
    var errorText = text;
    var self = this;

    if(errorText.indexOf('{') != -1)
      errorText = errorText.substring(0,errorText.indexOf('{'));

    new Dialog(this.language.error, {
      buttons: ['confirm'],
      language: {
        confirm: this.language.ok
      },
      content: [
        errorText
      ],
      onOpen: this.onDialogOpen.bind(this),
      onClose: this.onDialogClose.bind(this)
    });
  },

  onRequest: function(){this.loader.set('opacity', 1);},
  onComplete: function(){this.loader.fade(0);},
  onError: function(){this.loader.fade(0);},
  onDialogOpen: function(){this.dialogOpen = true; this.onDialogOpenWhenUpload.apply(this);},
  onDialogClose: function(){this.dialogOpen = false; this.onDialogCloseWhenUpload.apply(this);},
  onDialogOpenWhenUpload: function(){},
  onDialogCloseWhenUpload: function(){},
  onDragComplete: Function.from(false)
});

FileManager.Request = new Class({
  Extends: Request.JSON,
  secure: true,

  initialize: function(options, filebrowser){
    this.parent(options);

    if (filebrowser) this.addEvents({
      request: filebrowser.onRequest.bind(filebrowser),
      complete: filebrowser.onComplete.bind(filebrowser),
      error: filebrowser.onError.bind(filebrowser)
    });
  }
});

FileManager.Language = {};

(function(){

// ->> load DEPENCIES
var __DIR__ = (function() {
    var scripts = document.getElementsByTagName('script');
    var script = scripts[scripts.length - 1].src;
    var host = window.location.href.replace(window.location.pathname+window.location.hash,'');
    return script.substring(0, script.lastIndexOf('/')).replace(host,'') + '/';
})();
Asset.javascript(__DIR__+'../Assets/js/milkbox/milkbox.js');
Asset.css(__DIR__+'../Assets/js/milkbox/css/milkbox.css');
Asset.css(__DIR__+'../Assets/Css/FileManager.css');
Asset.css(__DIR__+'../Assets/Css/Additions.css');
Asset.javascript(__DIR__+'../Assets/js/jsGET.js', { events: {load: (function(){ window.fireEvent('jsGETloaded'); }).bind(this)}});

Element.implement({

  center: function(offsets) {
    var scroll = document.getScroll(),
      offset = document.getSize(),
      size = this.getSize(),
      values = {x: 'left', y: 'top'};

    if(!offsets) offsets = {};

    for (var z in values){
      var style = scroll[z] + (offset[z] - size[z]) / 2 + (offsets[z] || 0);
      this.setStyle(values[z], (z == 'y' && style < 30) ? 30 : style);
    }
    return this;
  }

});

this.Dialog = new Class({

  Implements: [Options, Events],

  options: {
    /*onShow: function(){},
    onOpen: function(){},
    onConfirm: function(){},
    onDecline: function(){},
    onClose: function(){},*/
    request: null,
    buttons: ['confirm', 'decline'],
    language: {}
  },

  initialize: function(text, options){
    this.setOptions(options);
    this.dialogOpen = false;

    this.el = new Element('div', {
      'class': 'dialog' + (Browser.ie ? ' dialog-engine-trident' : '') + (Browser.ie ? ' dialog-engine-trident' : '') + (Browser.ie8 ? '4' : '') + (Browser.ie9 ? '5' : ''),
      opacity: 0,
      tween: {duration: 250}
    }).adopt([
      typeOf(text) == 'string' ? new Element('div', {text: text}) : text
    ]);

    if(typeof this.options.content != 'undefined') {
      this.options.content.each((function(content){
        if(content && typeOf(content) == 'element') this.el.getElement('div').adopt(content);
        else if(content) this.el.getElement('div').set('html',this.el.getElement('div').get('html')+'<br>'+content);
      }).bind(this));
    }

    Array.each(this.options.buttons, function(v){
      new Element('button', {'class': 'dialog-' + v, text: this.options.language[v]}).addEvent('click', (function(e){
        if (e) e.stop();
        this.fireEvent(v).fireEvent('close');
        //if(!this.options.hideOverlay)
        this.overlay.hide();
        this.destroy();
      }).bind(this)).inject(this.el);
    }, this);

    this.overlay = new Overlay({
      'class': 'overlay overlay-dialog',
      events: {click: this.fireEvent.pass('close',this)},
      tween: {duration: 250}
    });

    this.bound = {
      scroll: (function(){
        if (!this.el) this.destroy();
        else this.el.center();
      }).bind(this),
      keyesc: (function(e){
        if (e.key == 'esc') {
          e.stopPropagation();
          this.fireEvent('close').destroy();
        };
      }).bind(this)
    };

    this.show();
  },

  show: function(){
    if(!this.options.hideOverlay)
      this.overlay.show();
    var self = this.fireEvent('open');
    this.el.setStyle('display', 'block').inject(document.body).center().fade(1).get('tween').chain(function(){
      var button = this.element.getElement('button.dialog-confirm') || this.element.getElement('button');
      if (button) button.focus();
      self.fireEvent('show');
    });

    document.addEvents({
      'scroll': this.bound.scroll,
      'resize': this.bound.scroll,
      'keyup': this.bound.keyesc
    });
  },

  destroy: function() {
    if (this.el)
      this.el.fade(0).get('tween').chain((function(){
        if(!this.options.hideOverlay)
          this.overlay.destroy();
        this.el.destroy();
      }).bind(this));

      document.removeEvent('scroll', this.bound.scroll).removeEvent('resize', this.bound.scroll).removeEvent('keyup', this.bound.keyesc);
  }

});

this.Overlay = new Class({

  initialize: function(options){
    this.el = new Element('div', Object.append({
      'class': 'overlay'
    }, options)).inject(document.body);
  },

  show: function(){
    this.objects = $$('object, select, embed').filter(function(el){
      return el.id == 'SwiffFileManagerUpload' || el.style.visibility == 'hidden' ? false : !!(el.style.visibility = 'hidden');
    });

    this.resize = (function(){
      if (!this.el) this.destroy();
      else this.el.setStyles({
        width: document.getScrollWidth(),
        height: document.getScrollHeight()
      });
    }).bind(this);

    this.resize();

    this.el.setStyles({
      opacity: 0,
      display: 'block'
    }).get('tween').pause().start('opacity', 0.5);

    window.addEvent('resize', this.resize);

    return this;
  },

  hide: function(){
    if(!Browser.ie) {
      this.el.fade(0).get('tween').chain((function(){
        this.revertObjects();
        this.el.setStyle('display', 'none');
      }).bind(this));
    } else {
      this.revertObjects();
      this.el.setStyle('display', 'none');
    }

    window.removeEvent('resize', this.resize);

    return this;
  },

  destroy: function(){
    this.revertObjects().el.destroy();
  },

  revertObjects: function(){
    if (this.objects && this.objects.length)
      this.objects.each(function(el){
        el.style.visibility = 'visible';
      });

    return this;
  }

});

})();