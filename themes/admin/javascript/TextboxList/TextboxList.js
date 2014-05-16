/*
---
description: TextboxList

authors:
  - Guillermo Rauch

requires:
  core/1.3: '*'

provides:
  - TextboxList
...
*/

var $chk = function(obj) {
    return !!(obj || obj === 0);
};

var TextboxList = new Class({

	Implements: [Options, Events],

	plugins: [],
	options: {
		/** events
		onFocus: function(){},
		onBlur: function(){},
		onBitFocus: function(){},
		onBitBlur: function(){},
		onBitAdd: function(){},
		onBitRemove: function(){},
		onBitBoxFocus: function(){},
		onBitBoxBlur: function(){},
		onBitBoxAdd: function(){},
		onBitBoxRemove: function(){},
		onBitEditableFocus: function(){},
		onBitEditableBlue: function(){},
		onBitEditableAdd: function(){},
		onBitEditableRemove: function(){},
		**/
		bitsOptions: {editable: {}, box: {}},
		check: function(s) {
			return s.clean().replace(/,/g, '') !== '';
		},
		decode: function(o) {
			return o.split(',');
		},
		encode: function(o) {
				return o.map(function(v) {
				v = ($chk(v[0]) ? v[0] : v[1]);
				return $chk(v) ? v : null;
			}).clean().join(',');
		},
		endEditableBit: true,
		hideEditableBits: true,
		inBetweenEditableBits: true,
		keys: {previous: 'left', next: 'right'},
		max: null,
		plugins: {},
		prefix: 'textboxlist',
		startEditableBit: true,
		unique: false,
		uniqueInsensitive: true
	},

	add: function(plain, id, html, afterEl) {
		var box = this.create('box', [id, plain, html]);
		if (box) {
			if ( ! afterEl) {
				afterEl = this.list.getLast('.'+this.options.prefix+'-bit-box');
			}
			box.inject(afterEl || this.list, afterEl ? 'after' : 'top');
		}
		return this;
	},

	afterInit: function(){
		if (this.options.unique) {
			this.index = [];
		}
		if (this.options.endEditableBit) {
			this.create('editable', null, {tabIndex: this.original.tabIndex}).inject(this.list);
		}
		var update = this.update.bind(this);
		this.addEvent('bitAdd', update, true).addEvent('bitRemove', update, true);
		document.addEvents({
			click: function(e) {
				if ( ! this.focused) return;
				if (e.target.className.contains(this.options.prefix)){
					if (e.target == this.container) return;
					var parent = e.target.getParent('.'+this.options.prefix);
					if (parent == this.container) return;
				}
				this.blur();
			}.bind(this),
			keydown: function(ev) {
				if ( ! this.focused || ! this.current) return;
				var caret = this.current.is('editable') ? this.current.getCaret() : null;
				var value = this.current.getValue()[1];
				var special = ['shift', 'alt', 'meta', 'ctrl'].some(function(e) {
					return ev[e];
				});
				var custom = special || (this.current.is('editable') && this.current.isSelected());
				switch (ev.key) {
					case 'enter':
						if (this.current.is('box') || ((caret === 0 || !value.length) && ! custom)) {
							ev.stop();
							this.current.editBit();
							break;
						}
					case 'backspace':
						if (this.current.is('box')) {
							ev.stop();
							return this.current.remove();
						}
					case this.options.keys.previous:
						if (this.current.is('box') || ((caret === 0 || !value.length) && ! custom)) {
							ev.stop();
							this.focusRelative('previous');
						}
						break;
					case 'delete':
						if (this.current.is('box')) {
							ev.stop();
							return this.current.remove();
						}
					case this.options.keys.next:
						if (this.current.is('box') || (caret == value.length && ! custom)) {
							ev.stop();
							this.focusRelative('next');
						}
				}
			}.bind(this)
		});
		this.setValues(this.options.decode(this.original.get('value')));
	},

	blur: function() {
		if ( ! this.focused) return this;
		if (this.current) {
			this.current.blur();
		}
		this.focused = false;
		return this.fireEvent('blur');
	},

	create: function(klass, value, options) {
		if (klass == 'box') {
			if (( ! value[0] && ! value[1]) || (value[1] !== null && ! this.options.check(value[1]))) return false;
			if (this.options.max !== null && this.list.getChildren('.'+this.options.prefix+'-bit-box').length + 1 > this.options.max) return false;
			if (this.options.unique && this.index.contains(this.uniqueValue(value))) return false;
		}
		return new TextboxListBit[klass.capitalize()](value, this, Object.merge(this.options.bitsOptions[klass], options));
	},

	enablePlugin: function(name, options) {
		this.plugins[name] = new TextboxList[name.camelCase().capitalize()](this, options);
	},

	focusLast: function() {
		var lastElement = this.list.getLast();
		if (lastElement) {
			this.getBit(lastElement).focus();
		}
		return this;
	},

	focusRelative: function(dir, to) {
		var bit = false;
		if(typeOf(document.id([to, this.current].pick())) != 'null')
			bit = this.getBit(document.id([to, this.current].pick())['get'+dir.capitalize()]());
		if (bit) {
			bit.focus();
		}
		return this;
	},

	getBit: function(obj) {
		return (typeOf(obj) == 'element') ? obj.retrieve('textboxlist:bit') : obj;
	},

	getValues: function() {
		return this.list.getChildren().map(function(el) {
			var bit = this.getBit(el);
			if (bit.is('editable')) return null;
			return bit.getValue();
		}, this).clean();
	},

	initialize: function(element, options) {
		this.setOptions(options);
		this.original = document.id(element).setStyle('display', 'none').set('autocomplete', 'off').addEvent('focus', this.focusLast.bind(this));
		this.container = new Element('div.'+this.options.prefix).inject(element, 'after');
		this.container.addEvent('click', function(e) {
			if ((e.target == this.list || e.target == this.container) && ( ! this.focused || document.id(this.current) != this.list.getLast())) {
				this.focusLast();
			}
		}.bind(this));
		this.list = new Element('ul.'+this.options.prefix+'-bits').inject(this.container);
		for (var name in this.options.plugins) {
			this.enablePlugin(name, this.options.plugins[name]);
		}
		['check', 'encode', 'decode'].each(function(i) {
			this.options[i] = this.options[i].bind(this);
		}, this);
		this.afterInit();
	},

	onAdd: function(bit) {
		if (this.options.unique && bit.is('box')) {
			this.index.push(this.uniqueValue(bit.value));
		}
		if (bit.is('box')) {
			var prior = this.getBit(document.id(bit).getPrevious());
			if ((prior && prior.is('box') && this.options.inBetweenEditableBits) || ( ! prior && this.options.startEditableBit)) {
				var b = this.create('editable').inject(prior || this.list, prior ? 'after' : 'top');
				if (this.options.hideEditableBits) {
					b.hide();
				}
			}
		}
	},

	onBlur: function(bit, all) {
		this.current = null;
		this.container.removeClass(this.options.prefix+'-focus');
		this.blurtimer = this.blur.delay(all ? 0 : 200, this);
	},

	onFocus: function(bit) {
		if (this.current) this.current.blur();
		clearTimeout(this.blurtimer);
		this.current = bit;
		this.container.addClass(this.options.prefix+'-focus');
		if ( ! this.focused){
			this.focused = true;
			this.fireEvent('focus', bit);
		}
	},

	onRemove: function(bit) {
		// if ( ! this.focused) return;
		if (this.options.unique && bit.is('box')) {
			this.index.erase(this.uniqueValue(bit.value));
		}
		var prior = this.getBit(document.id(bit).getPrevious());
		if (prior && prior.is('editable')) {
			prior.remove();
		}
		this.focusRelative('next', bit);
	},

	setValues: function(values) {
		if ( ! values) return;
		values.each(function(value) {
			if (value) {
				this.add.apply(this, typeOf(value) == 'array' ? [value[1], value[0], value[2]] : [value]);
			}
		}, this);
	},

	uniqueValue: function(value) {
		return $chk(value[0]) ? value[0] : (this.options.uniqueInsensitive ? value[1].toLowerCase() : value[1]);
	},

	update: function(){
		this.original.set('value', this.options.encode(this.getValues()));
	}

});

var TextboxListBit = new Class({

	Implements: Options,

	blur: function() {
		if ( ! this.focused) return this;
		this.focused = false;
		this.textboxlist.onBlur(this);
		this.bit.removeClass(this.prefix+'-focus').removeClass(this.prefix+'-'+this.type+'-focus');
		return this.fireBitEvent('blur');
	},

	fireBitEvent: function(type) {
		type = type.capitalize();
		this.textboxlist.fireEvent('bit'+type, this).fireEvent('bit'+this.name+type, this);
		return this;
	},

	focus: function() {
		if (this.focused) return this;
		this.show();
		this.focused = true;
		this.textboxlist.onFocus(this);
		this.bit.addClass(this.prefix+'-focus').addClass(this.prefix+'-'+this.type+'-focus');
		return this.fireBitEvent('focus');
	},

	getValue: function() {
		return this.value;
	},

	hide: function() {
		this.bit.setStyle('display', 'none');
		return this;
	},

	editBit: function(e) {
		if(!e || (e.key == 'enter' && this.focused)) {
			this.blur();
			var editable = this.textboxlist.create('editable');
			var editableInput = editable.bit.getElement('.textboxlist-bit-editable-input');
			if (this.value && typeOf(this.value[1]) != 'null')
				editableInput.setProperty('value',this.value[1]);
			editable.focus();
			editableInput.addEvent('toBox',function(){
					editable.bit.destroy();
				});
			editable.inject(this, 'after');
			editableInput.retrieve('growing').resize();
			this.remove();
		}
	},

	initialize: function(value, textboxlist, options){
		this.name = this.type.capitalize();
		this.value = value;
		this.textboxlist = textboxlist;
		this.setOptions(options);
		this.prefix = this.textboxlist.options.prefix+'-bit';
		this.typeprefix = this.prefix+'-'+this.type;
		this.bit = new Element('li.'+this.prefix+'.'+this.typeprefix).store('textboxlist:bit', this);
		this.bit.addEvents({
			mouseenter: function() {
				this.bit.addClass(this.prefix+'-hover').addClass(this.typeprefix+'-hover');
			}.bind(this),
			mouseleave: function() {
				this.bit.removeClass(this.prefix+'-hover').removeClass(this.typeprefix+'-hover');
			}.bind(this),
			'dblclick': function(){
				this.editBit();
			}.bind(this)
		});
	},

	inject: function(element, where) {
		this.bit.inject(element, where);
		this.textboxlist.onAdd(this);
		return this.fireBitEvent('add');
	},

	is: function(type) {
		return this.type == type;
	},

	remove: function(event) {
		if(event)
			event.preventDefault();
		this.blur();
		this.textboxlist.onRemove(this);
		this.bit.destroy();
		return this.fireBitEvent('remove');
	},

	setValue: function(value) {
		this.value = value;
		return this;
	},

	show: function() {
		this.bit.setStyle('display', 'block');
		return this;
	},

	toElement: function() {
		return this.bit;
	}

});

TextboxListBit.Editable = new Class({

	Extends: TextboxListBit,

	options: {
		tabIndex: null,
		growing: true,
		growingOptions: {},
		stopEnter: true,
		addOnBlur: false,
		addKeys: 'enter'
	},

	type: 'editable',

	blur: function(noReal) {
		this.parent();
		if ( ! noReal) {
			this.element.blur();
		}
		if (this.hidden && ! this.element.value.length) {
			this.hide();
		}
		return this;
	},

	initialize: function(value, textboxlist, options) {
		this.parent(value, textboxlist, options);
		var self = this;
		this.element = new Element('input.'+this.typeprefix+'-input[value="'+(this.value ? this.value[1] : '')+'"][type=text][autocomplete=off]').inject(this.bit);
		if ($chk(this.options.tabIndex)) {
			this.element.tabIndex = this.options.tabIndex;
		}
		if (this.options.growing) {
			new GrowingInput(this.element, this.options.growingOptions);
		}
		this.element.addEvents({
			focus: function() {
				self.focus(true);
			},
			blur: function() {
				self.blur(true);
				if (self.options.addOnBlur) {
					self.toBox();
				}
			}
		});
		if (this.options.addKeys || this.options.stopEnter) {
			var keys = Array.from(self.options.addKeys);
			this.element.addEvent('keyup', function(ev) {
				if (!self.focused) return;
				if (self.options.stopEnter && ev.key === 'enter') {
					ev.stop();
				}
				if (keys.contains(ev.key) || keys.contains(ev.code)){
					ev.stop();
					self.toBox();
				}
			});
		}
	},

	focus: function(noReal) {
		this.parent();
		if ( ! noReal) {
			this.element.focus();
		}
		this.element.retrieve('growing').resize();

		return this;
	},

	getCaret: function() {
		if (this.element.createTextRange) {
			var range = document.selection.createRange().duplicate();
			range.moveEnd('character', this.element.value.length);
			if (range.text === '') return this.element.value.length;
			return this.element.value.lastIndexOf(range.text);
		}
		else {
			return this.element.selectionStart;
		}
	},

	getCaretEnd: function() {
		if (this.element.createTextRange) {
			var range = document.selection.createRange().duplicate();
			range.moveStart('character', -this.element.value.length);
			return range.text.length;
		} else return this.element.selectionEnd;
	},

	getValue: function() {
		return [null, this.element.value, null];
	},

	hide: function() {
		this.parent();
		this.hidden = true;
		return this;
	},

	isSelected: function() {
		return this.focused && (this.getCaret() !== this.getCaretEnd());
	},

	setValue: function(val) {

		this.element.value = $chk(val[0]) ? val[0] : val[1];
		if (this.options.growing) {
			this.element.retrieve('growing').resize();
		}
		return this;
	},

	toBox: function() {
		var value = this.getValue();
		var box = this.textboxlist.create('box', value);
		if (box) {
			box.inject(this.bit, 'before');
			this.setValue([null, '', null]);
			this.element.fireEvent('toBox');
			return box;
		}
		this.textboxlist.focusRelative('next');
		// this.textboxlist.focusRelative('previous');
		this.element.fireEvent('toBox');
		return null;
	}

});

TextboxListBit.Box = new Class({

	Extends: TextboxListBit,

	options: {
		deleteButton: true
	},

	type: 'box',

	initialize: function(value, textboxlist, options) {
		this.parent(value, textboxlist, options);
		this.bit.set('html', $chk(this.value[2]) ? this.value[2] : this.value[1]);
		this.bit.addEvent('click', this.focus.bind(this));
		if (this.options.deleteButton) {
			this.bit.addClass(this.typeprefix+'-deletable');
			this.close = new Element('a.'+this.typeprefix+'-deletebutton[href=#]', {events: {click: this.remove.bind(this)}}).inject(this.bit);
		}
		this.bit.getChildren().addEvent('click', function(e) {
			e.stop();
		});
	}

});

if (window.GrowingInput == null) { (function() {

GrowingInput = new Class({

	Implements: [Options, Events],

	options: {
		min: 0,
		max: null,
		startWidth: 2,
		correction: 15
	},

	calculate: function(chars) {
		this.calc.set('html', chars);
		var width = this.calc.getStyle('width').toInt();
		return (width ? width : this.options.startWidth) + this.options.correction;
	},

	initialize: function(element, options) {
		this.setOptions(options);
		this.element = $(element).store('growing', this).set('autocomplete', 'off');
		this.calc = new Element('span', {
			'styles': {
				'float': 'left',
				'display': 'inline-block',
				'position': 'absolute',
				'left': -1000
			}
		}).inject(this.element, 'after');
		['font-size', 'font-family', 'padding-left', 'padding-top', 'padding-bottom',
		 'padding-right', 'border-left', 'border-right', 'border-top', 'border-bottom',
		 'word-spacing', 'letter-spacing', 'text-indent', 'text-transform'].each(function(property) {
			this.calc.setStyle(property, this.element.getStyle(property));
		}, this);
		this.resize();
		var resize = this.resize.bind(this);
		this.element.addEvents({blur: resize, keyup: resize, keydown: resize, keypress: resize});
	},

	resize: function() {
		this.lastvalue = this.value;
		this.value = this.element.value;
		var value = this.value;
		if ($chk(this.options.min) && this.value.length < this.options.min) {
			if ($chk(this.lastvalue) && (this.lastvalue.length <= this.options.min)) return this;
			value = str_pad(this.value, this.options.min, '-');
		}
		else if ($chk(this.options.max) && this.value.length > this.options.max) {
			if ($chk(this.lastvalue) && (this.lastvalue.length >= this.options.max)) return this;
			value = this.value.substr(0, this.options.max);
		}
		this.element.setStyle('width', this.calculate(value));
		return this;
	}

});

var str_pad = function(self, length, str, dir) {
	if (self.length >= length) return this;
	str = str || ' ';
	var pad = str_repeat(str, length - self.length).substr(0, length - self.length);
	if (!dir || dir == 'right') return self + pad;
	if (dir == 'left') return pad + self;
	return pad.substr(0, (pad.length / 2).floor()) + self + pad.substr(0, (pad.length / 2).ceil());
};

var str_repeat = function(str, times) {
	return new Array(times + 1).join(str);
};

})(); }