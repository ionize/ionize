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
    uploadAuthData: {}
  },

  hooks: {
    show: {
      upload: function() {
        this.startUpload();
      }
    },

    cleanup: {
      upload: function(){
        if (!this.options.upload  || !this.upload) return;

        if (this.upload.uploader) this.upload.uploader.set('opacity', 0).dispose();
      }
    }
  },

  onDialogOpenWhenUpload: function(){
    if (this.swf && this.swf.box) this.swf.box.setStyle('visibility', 'hidden');
  },

  onDialogCloseWhenUpload: function(){
    if (this.swf && this.swf.box) this.swf.box.setStyle('visibility', 'visible');
  },

  startUpload: function(){

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

      initialize: function(base, data){

        this.parent(base, data);

        this.setOptions({
          url: self.options.url + (self.options.url.indexOf('?') == -1 ? '?' : '&') + Object.toQueryString(Object.merge({}, self.options.uploadAuthData, {
            event: 'upload',
            directory: self.normalize(self.Directory),
            resize: self.options.resizeImages && resizer.hasClass('checkboxChecked') ? 1 : 0
          }))
        });
      },

      render: function(){
        if (this.invalid){
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
        this.ui.icon = new Asset.image(self.assetBasePath+'Images/Icons/' + this.extension + '.png', {
          'class': 'icon',
          onerror: function(){ new Asset.image(self.assetBasePath + 'Images/Icons/default.png').replaces(this); }
        });
        this.ui.element = new Element('li', {'class': 'file', id: 'file-' + this.id});
        this.ui.title = new Element('span', {'class': 'file-title', text: this.name});
        this.ui.size = new Element('span', {'class': 'file-size', text: Swiff.Uploader.formatUnit(this.size, 'b')});

        var file = this;
        this.ui.cancel = new Asset.image(self.assetBasePath+'Images/cancel.png', {'class': 'file-cancel', title: self.language.cancel}).addEvent('click', function(){
          file.remove();
          self.tips.hide();
          self.tips.detach(this);
        });
        self.tips.attach(this.ui.cancel);

        var progress = new Element('img', {'class': 'file-progress', src: self.assetBasePath+'Images/bar.gif'});

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

      onComplete: function(){
        this.ui.progress = this.ui.progress.cancel().element.destroy();
        this.ui.cancel = this.ui.cancel.destroy();

        var response = JSON.decode(this.response.text);
        if (!response.status)
          new Dialog(('' + response.error).substitute(self.language, /\\?\$\{([^{}]+)\}/g) , {language: {confirm: self.language.ok}, buttons: ['confirm']});

        this.ui.element.set('tween', {duration: 2000}).highlight(response.status ? '#e6efc2' : '#f0c2c2');
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
      }
    });

    this.getFileTypes = function() {
      var fileTypes = {};
      if(this.options.filter == 'image')
        fileTypes = {'Images (*.jpg, *.gif, *.png)': '*.jpg; *.jpeg; *.bmp; *.gif; *.png'};
      if(this.options.filter == 'video')
        fileTypes = {'Videos (*.avi, *.flv, *.mov, *.mpeg, *.mpg, *.wmv, *.mp4)': '*.avi; *.flv; *.fli; *.movie; *.mpe; *.qt; *.viv; *.mkv; *.vivo; *.mov; *.mpeg; *.mpg; *.wmv; *.mp4'};
      if(this.options.filter == 'audio')
        fileTypes = {'Audio (*.aif, *.mid, *.mp3, *.mpga, *.rm, *.wav)': '*.aif; *.aifc; *.aiff; *.aif; *.au; *.mka; *.kar; *.mid; *.midi; *.mp2; *.mp3; *.mpga; *.ra; *.ram; *.rm; *.rpm; *.snd; *.wav; *.tsi'};
      if(this.options.filter == 'text')
        fileTypes = {'Text (*.txt, *.rtf, *.rtx, *.html, *.htm, *.css, *.as, *.xml, *.tpl)': '*.txt; *.rtf; *.rtx; *.html; *.htm; *.css; *.as; *.xml; *.tpl'};
      if(this.options.filter == 'application')
        fileTypes = {'Application (*.bin, *.doc, *.exe, *.iso, *.js,*.odt, *.pdf, *.php, *.ppt, *.swf, *.rar, *.zip)': '*.ai; *.bin; *.ccad; *.class; *.cpt; *.dir; *.dms; *.drw; *.doc; *.dvi; *.dwg; *.eps; *.exe; *.gtar; *.gz; *.js; *.latex; *.lnk; *.lnk; *.oda; *.odt; *.ods; *.odp; *.odg; *.odc; *.odf; *.odb; *.odi; *.odm; *.ott; *.ots; *.otp; *.otg; *.pdf; *.php; *.pot; *.pps; *.ppt; *.ppz; *.pre; *.ps; *.rar; *.set; *.sh; *.skd; *.skm; *.smi; *.smil; *.spl; *.src; *.stl; *.swf; *.tar; *.tex; *.texi; *.texinfo; *.tsp; *.unv; *.vcd; *.vda; *.xlc; *.xll; *.xlm; *.xls; *.xlw; *.zip'};

  		return fileTypes;
    };

    this.swf = new Swiff.Uploader({
      id: 'SwiffFileManagerUpload',
      path: this.assetBasePath + 'Swiff.Uploader.swf',
      queued: false,
      target: this.upload.button,
      allowDuplicates: true,
      instantStart: true,
      fileClass: File,
      timeLimit: 260,
      fileSizeMax: 2600 * 2600 * 25,
      typeFilter: this.getFileTypes(),
      zIndex: this.SwiffZIndex || 9999,
      onSelectSuccess: function(){
        self.fillInfo();
        self.info.getElement('h2.filemanager-headline').setStyle('display', 'none');
        self.preview.adopt(self.upload.uploader);
        self.upload.uploader.fade(1);
      },
      onComplete: function(){
        self.load(self.Directory, true);
      },
      onFail: function(error) {
        if(error != 'empty') {
          $$(self.upload.button, self.upload.label).dispose();
          new Dialog(new Element('div', {html: self.language.flash[error] || self.language.flash.flash}), {language: {confirm: self.language.ok}, buttons: ['confirm']});
        }
      }
    });
  }

});