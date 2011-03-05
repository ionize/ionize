/*
---
description: FileManager.Gallery
longdescription: Adds functionality to create a gallery out of a list of images

authors:
  - Christoph Pojer

requires:
  core/1.2.4: '*'

provides:
  - filemanager.gallery

license:
  MIT-style license
...
*/

(function(){

FileManager.Gallery = new Class({
	
	Extends: FileManager,
	
	initialize: function(options){
		this.offsets = {y: -72};
		this.parent(options);
		
		var show = function(){
			this.galleryContainer.setStyles({
				opacity: 0,
				display: 'block'
			});

			var size = this.el.getSize(),
				pos = this.el.getPosition();

			this.galleryContainer.setStyles({
				top: pos.y + size.y - 1,
				left: pos.x + (size.x - this.galleryContainer.getWidth()) / 2,
				opacity: 1
			});

			this.hideClone();
			this.wrapper.setStyle('display', 'none');

			if (this.howto){
				var self = this;
				(function(){
					if (self.howto && self.howto.fade) self.howto.fade(0).get('tween').chain(function(){
						this.element.destroy();
						self.howto = null;
					});
				}).delay(2000);
			}
		};

		this.addEvents({

			scroll: show,
			show: (function(){
				show.apply(this);
				this.populate();
			}),

			hide: function(){
				this.gallery.empty();

				this.captions = {};
				this.files = [];

				this.hideClone();
				this.wrapper.setStyle('display', 'none');
			},

			modify: function(file){
				var name = this.normalize(file.dir + '/' + file.name);
				var el = (this.gallery.getElements('li').filter(function(el){
					var f = el.retrieve('file');
					return name == this.normalize(f.dir + '/' + f.name);
				}, this) || null)[0];

				if (el) this.erasePicture(name, el);
			}
		});

		this.addMenuButton('serialize');
		this.galleryContainer = new Element('div', {'class': 'filemanager-gallery'}).inject(this.container);
		this.gallery = new Element('ul').inject(this.galleryContainer);

		var timer, removeClone = this.removeClone.bind(this);

		this.input = new Element('input', {name: 'imgCaption'}).addEvent('keyup', function(e){
			if (e.key == 'enter') removeClone(e);
		});
		this.wrapper = new Element('div', {
			'class': 'filemanager-wrapper',
			tween: {duration: 200},
			opacity: 0,
			events: {
				mouseenter: function(){
					$clear(timer);
				},
				mouseleave: function(e){
					timer = (function(){
						removeClone(e);
					}).delay(500);
				}
			}
		}).adopt(
			new Element('span', {text: this.language.gallery.text}),
			this.input,
			new Element('div', {'class': 'img'}),
			new Element('button', {text: this.language.gallery.save}).addEvent('click', removeClone)
		).inject(document.body);
		
		this.droppables.push(this.gallery);
		
		this.captions = {};
		this.files = [];
		this.animation = {};

		this.howto = new Element('div', {'class': 'howto', text: this.language.gallery.drag}).inject(this.galleryContainer);
		this.switchButton();
	},
	
	onDragComplete: function(el, droppable){
		if (!droppable || droppable != this.gallery) return false;
		
		var file;
		if ($type(el) == 'string'){
			var part = el.split('/');
			file = {
				name: part.pop(),
				dir: part.join('/')
			};
		}else{
			el.setStyles({left: '', top: ''});
			file = el.retrieve('file');
		}
		
		var	self = this, name = this.normalize(file.dir + '/' + file.name);
		
		if (this.files.contains(name)) return true;
		this.files.push(name);

		var img = new Asset.image(this.options.assetBasePath + 'destroy.png').set({
			'class': 'filemanager-remove',
			title: this.language.gallery.remove,
			events: {click: this.removePicture}
		}).store('gallery', this);

		var li = new Element('li').store('file', file).adopt(
			img,
			new Asset.image(this.options.baseURL + name, {
				onload: function(){
					var el = this;
					li.setStyle('background', 'none').addEvent('click', function(e){
						if (e) e.stop();
						
						var pos = el.getCoordinates();
						pos.left += el.getStyle('paddingLeft').toInt();
						pos.top += el.getStyle('paddingTop').toInt();

						self.hideClone();
						self.animation = {
							from: {
								width: 75,
								height: 56,
								left: pos.left,
								top: pos.top
							},
							to: {
								width: 200,
								height: 150,
								left: pos.left - 75,
								top: pos.top + pos.height - 150
							}
						};

						self.hideClone();
						self.input.removeEvents('blur').addEvent('blur', function(){
							self.captions[name] = this.get('value') || '';
						});

						li.set('opacity', 0);
						self.clone = el.clone();
						self.clone.store('file', file).store('parent', li).addClass('filemanager-clone').setStyles(self.animation.from).set({
							morph: {link: 'chain'},
							styles: {
								position: 'absolute',
								zIndex: 1100
							},
							events: {
								click: function(e){
									self.fireEvent('preview', [self.options.baseURL + name, self.captions[name], li]);
								}
							}
						}).inject(document.body).morph(self.animation.to).get('morph').chain(function(){
							self.input.set('value', self.captions[name] || '');
							self.wrapper.setStyles({
								opacity: 0,
								display: 'block',
								left: self.animation.to.left - 12,
								top: self.animation.to.top - 53
							}).fade(1).get('tween').chain(function(){
								self.input.focus();
							});
						});
					});
				}
			})
		).inject(this.gallery);
		
		this.tips.attach(img.appearOn(li));
		this.switchButton();
		
		return true;
	},
	
	removeClone: function(e){
		if (!this.clone || (e.relatedTarget && ([this.clone, this.wrapper].contains(e.relatedTarget) || this.wrapper.hasChild(e.relatedTarget)))) return;
		if (this.clone.get('morph').timer) return;
		
		var file = this.clone.retrieve('file');
		if (!file) return;
		
		this.captions[this.normalize(file.dir + '/' + file.name)] = this.input.get('value') || '';
		
		this.clone.morph(this.animation.from).get('morph').clearChain().chain((function(){
			this.clone.retrieve('parent').set('opacity', 1);
			this.clone.destroy();
		}).bind(this));
		
		this.wrapper.fade(0).get('tween').chain(function(){
			this.element.setStyle('display', 'none');
		});
	},

	hideClone: function(){
		if (!this.clone) return;

		this.clone.get('morph').cancel();
		var parent = this.clone.retrieve('parent');
		if (parent) parent.set('opacity', 1);
		this.clone.destroy();
		this.wrapper.setStyles({
			opacity: 0,
			display: 'none'
		});
	},
	
	removePicture: function(e){
		if(e) e.stop();

		var self = this.retrieve('gallery'),
			parent = this.getParent('li'),
			file = parent.retrieve('file'),
			name = self.normalize(file.dir + '/' + file.name);
		
		self.erasePicture(name, parent);
	},

	erasePicture: function(name, element){
		this.captions[name] = '';
		this.files.erase(name);
		this.tips.hide();

		var self = this;
		element.set('tween', {duration: 250}).removeEvents('click').fade(0).get('tween').chain(function(){
			this.element.destroy();
			self.switchButton();
		});
	},
	
	switchButton: function(){
		var chk = !!this.gallery.getChildren().length;
		
		this.menu.getElement('button.filemanager-serialize').set('disabled', !chk)[(chk ? 'remove' : 'add') + 'Class']('disabled');
	},

	populate: function(data){
		Hash.each(data || {}, function(v, i){
			this.captions[i] = v;

			this.onDragComplete(i, this.gallery);
		}, this);
	},
	
	serialize: function(e){
		if(e) e.stop();
		
		var serialized = {};
		this.files.each(function(v){
			serialized[v] = this.captions[v] || '';
		}, this);
		
		this.hide();
		this.fireEvent('complete', [serialized]);
	}
	
});

})();