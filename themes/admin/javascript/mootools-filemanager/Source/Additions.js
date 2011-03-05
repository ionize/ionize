/*
---
description: FileManager Additions

authors:
  - Christoph Pojer

requires:
  core/1.2.4: '*'

provides:
  - filemanager.additions

license:
  MIT-style license

contains:
  - Element.appearOn: Can be used to show an element when another one is hovered: $(myElement).appearOn(myWrapper)
  - Element.center: Centers an element
  - Dialog, Overlay: Classes used by the FileManager
...
*/

//(function(){

Element.implement({
	
	appearOn: function(el) {
		
		var params = Array.link($A(arguments).erase(arguments[0]), {options: Object.type, opacity: $defined}),
			opacity = $type(params.opacity) == 'array' ? [params.opacity[0] || 1, params.opacity[1] || 0] : [params.opacity || 1, 0];

		this.set({
			opacity: opacity[1],
			tween: params.options || {duration: 200}
		});
		
		$$(el).addEvents({
			mouseenter: this.set.bind(this, {opacity: opacity[0]}),
			mouseleave: this.set.bind(this, {opacity: opacity[1]})
		});
			
		return this;
	},
	
	center: function(offsets){
		var scroll = document.getScroll(),
			offset = document.getSize(),
			size = this.getSize(),
			values = {x: 'left', y: 'top'};
		
		if (!offsets) offsets = {};
		
		for (var z in values){
			var style = scroll[z] + (offset[z] - size[z]) / 2 + (offsets[z] || 0);
			this.setStyle(values[z], style < 10 ? 10 : style);
		}
		
		return this;
	}
});

// this.Dialog = new Class({
Dialog = new Class({
	
	Implements: [Options, Events],
	
	options: {
		/*onShow: $empty,
		onOpen: $empty,
		onConfirm: $empty,
		onDecline: $empty,
		onClose: $empty,*/
		request: null,
		buttons: ['confirm', 'decline'],
		language: {}
	},
	
	initialize: function(text, options){
		this.setOptions(options);
		
		this.el = new Element('div', {
			'class': 'dialog dialog-engine-' + Browser.Engine.name + ' dialog-engine-' + Browser.Engine.name + (Browser.Engine.trident ? Browser.Engine.version : ''),
			opacity: 0,
			tween: {duration: 220}
		}).adopt([
			$type(text) == 'string' ? new Element('div', {text: text}) : text
		]);
		
		if (this.options.content) this.el.getElement('div').adopt(this.options.content);
		
		Array.each(this.options.buttons, function(v){
			new Element('button', {'class': 'dialog-' + v, text: this.options.language[v]}).addEvent('click', (function(e){
				if (e) e.stop();
				this.fireEvent(v).fireEvent('close');
				this.overlay.hide();
				this.destroy();
			}).bind(this)).inject(this.el);
		}, this);
		
		this.overlay = new Overlay({
			'class': 'overlay overlay-dialog',
			events: {click: this.fireEvent.bind(this, ['close'])},
			tween: {duration: 200}
		});
		
		this.bound = {
			scroll: (function(){
				if (!this.el) this.destroy();
				else this.el.center();
			}).bind(this),
			keyesc: (function(e){
				if (e.key == 'esc') this.fireEvent('close').destroy();
			}).bind(this)
		};
		
		this.show();
	},
	
	show: function(){
		this.overlay.show();
		var self = this.fireEvent('open');
		
//		var container = (this.options.container) ? $(this.options.container) : document.body ;
		var container = document.body ;
		
		this.el.setStyle('display', 'block').inject(container).center().fade(1).get('tween').chain(function(){
			var button = this.element.getElement('button.dialog-confirm') || this.element.getElement('button');
			if (button) button.focus();
			self.fireEvent('show');
		});
		
		window.addEvents({
			scroll: this.bound.scroll,
			resize: this.bound.scroll,
			keyup: this.bound.keyesc
		});
	},
	
	destroy: function()
	{
		if (this.el)
		{
			this.overlay.destroy();
			this.el.destroy();
		}
		
		window.removeEvent('scroll', this.bound.scroll).removeEvent('resize', this.bound.scroll).removeEvent('keyup', this.bound.keyesc);
	}
});

//this.Overlay = new Class({
Overlay = new Class({
	
	initialize: function(options){
		this.el = new Element('div', $extend({
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
			opacity: 0.5,
			display: 'block'
		});
		
		window.addEvent('resize', this.resize);
		
		return this;
	},
	
	hide: function()
	{
		this.revertObjects();
		this.el.setStyle('display', 'none');
		
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

// })();