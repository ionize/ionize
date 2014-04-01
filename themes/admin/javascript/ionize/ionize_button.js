
/**
 * Button Toolbar Class
 *
 * @type {Class}
 */
ION.ButtonToolbar = new Class({

	Implements: [Events, Options],

	buttons: [],
	group: [],

	options:
	{
		btnToolbarClass: 'btn-toolbar m0',
		btnGroupClass: 'btn-group',
		btnClass: 'button'
	},

	initialize: function(element, options)
	{
		var self = this;
		this.setOptions(options);

		this.container = $(element);

		this.group = new Element(
			'div',
			{'class': this.options.btnToolbarClass}
		).inject(this.container);

		Array.each(options.buttons, function(btn)
		{
			self.addButtonGroup(btn);
		});

		return this;
	},

	/**
	 * Each button is a button group
	 *
	 * @param options
	 */
	addButtonGroup: function(options)
	{
		var self = this;

		var btnGroup = new Element('div', {'class': this.options.btnGroupClass}).inject(this.group, 'bottom');

		var button = new Element('button', {
			'class': this.options.btnClass,
			text: options.title
		}).inject(btnGroup);

		if (options.id) button.setProperty('id', options.id);
		if (options.class) button.addClass(options.class);

		if (options.icon) new Element('i', {'class':options.icon}).inject(button, 'top');

		// Dropdown Button
		if (typeOf(options.elements) != 'null' && (options.elements).length > 0)
		{
			this._addButtonCaret(button);

			this.addButtonGroupElements(options.elements, btnGroup);

			btnGroup.addEvent('click', function(e)
			{
				e.stop();
				if (this.hasClass('open'))
				{
					this.removeClass('open');
				}
				else
				{
					$$('.' + self.options.btnGroupClass).removeClass('open');
					this.addClass('open');
				}
			});
		}
		else
		{
			if (typeOf(options.onClick) == 'function')
			{
				button.addEvent('click', options.onClick);
			}
		}

		if (typeOf(options.onLoaded) == 'function')
			options.onLoaded(button);

		this.buttons.push(button);

		return btnGroup;
	},

	_addButtonCaret: function(button)
	{
		var caret = new Element('span', {'class':'caret'}).inject(button);
	},

	addButtonGroupElements: function(elements, btnGroup)
	{
		var ul = btnGroup.getElement('ul.dropdown-menu');

		if ( ! ul)
			ul = new Element('ul', {'class':'dropdown-menu'}).inject(btnGroup);

		Array.each(elements, function(el)
		{
			var li = new Element('li').inject(ul);
			var a = new Element('a', {text: el.title}).inject(li);

			if (typeOf(el.onClick) == 'function')
			{
				a.addEvent('click', el.onClick);
			}
		});
	},

	adopt:function(element)
	{
		var pos = arguments[1];
		if ( ! pos) pos = 'bottom';

		if (element.button)
		{
			element.button.inject(this.group, pos);
		}
	},

	getButtonById: function(id)
	{
		var found = null;
		Array.each(this.buttons, function(btn)
		{
			if (btn.id && btn.id == id)
				found = btn;
		});
		return found;
	},

	addListElementsToButton: function(id_button, elements)
	{
		var button = this.getButtonById(id_button);

		if (button != null)
		{
			var group = button.getParent('.btn-group');

			if (group)
			{
				this.addButtonGroupElements(elements, group);
			}
		}
	}
});


ION.Button = new Class({

	Implements: [Events, Options],

	isEnabled: true,

	options:
	{
		baseClass: 'button'
	},

	initialize: function(options)
	{
		var self = this;
		this.setOptions(options);

		this.button = new Element('a', {
			'class': this.options.baseClass + ' ' + this.options.class
		});

		this.buttonTitle = new Element('span', {
			'text':this.options.title
		}).inject(this.button);

		if (options.icon)
			var i = new Element('i', {'class': options.icon}).inject(this.button, 'top');

		if (typeOf(options.onClick) == 'function')
		{
			this.options.onClick = options.onClick;
			this.button.addEvent('click', this.options.onClick);
		}

		if (options.parent)
			$(options.parent).adopt(this.button);

		return this;
	},

	setTitle: function(title)
	{
		this.buttonTitle.set('text', title);
	},

	enable: function()
	{
		if ( ! this.isEnabled)
		{
			this.button.removeProperty('disabled');
			this.button.removeClass('disabled');
			this.button.addEvent('click', this.options.onClick);
		}
	},

	disable: function()
	{
		this.button.setProperty('disabled', 'disabled');
		this.button.addClass('disabled');
		this.button.removeEvents();
		this.isEnabled = false;
	},

	hide: function()
	{
		this.button.hide();
	},

	show: function()
	{
		this.button.show();
	}
});



/**
 * Button Class
 *
 * @type {*}
 */
/*
ION.Button = new Class({
})
// Static methods
.extend(
{
	styles:[],

	options: {
		'method': 'darker',         // Can be 'darker', 'lighter'
		'bg_color': '34629e',       // Default bg color
		'color': 'ffffff',           // Default text color
		'text_bg_colors':
		{
			'yellow':'e3c600',
			'orange':'e38000',
			'blue':'33619D',
			'anthracite':'27374c',
			'green':'7ca637',
			'red':'921111',
			'gray':'B2B6B7',
			'black':'0d191e'
		}
	},

	init:function(element, options)
	{
		if ( ! options) options = {};
		if ( ! options.bg_color) options.bg_color = ION.Button.options.bg_color;
		if ( ! options.color) options.color = ION.Button.options.color;

		if (ION.Button.options.text_bg_colors[options.bg_color] != undefined)
			options.bg_color = ION.Button.options.text_bg_colors[options.bg_color];

		element = $(element);
		if ( ! element)
		{
			console.log('ION.Button > init() : Element not found');
			return {};
		}

		var styleSelector = ION.Button.insertStyle(options.bg_color, options.color);

		element.addClass(styleSelector);

		return element;
	},


	insertStyle:function(bg_color, color)
	{
		var styleSelector = ION.Button.getStyleSelectorName(bg_color, color);

		if ( ! ION.Button.styles.contains(styleSelector))
		{
			ION.Button.styles.push(styleSelector);

			var bg_shade_color = ION.Button.shadeColor(bg_color, -10);

			// Normal Style
			var styleString = ''
				+ 'background: #' + bg_color + ' !important;'
				+ 'border-color: #' + bg_shade_color + ' !important;'
				+ 'color: #' + color + ' !important;'
				+ '';

			ION.Button.injectDocumentStyle('.' + styleSelector, styleString );

			// Hover style
			styleString = ''
				+ 'background: #' + bg_shade_color + ' !important;'
				+ 'color: #' + color + ' !important;'
				+ '';

			ION.Button.injectDocumentStyle('.' + styleSelector + ':hover', styleString );
		}
		return styleSelector;
	},


	getStyleSelectorName:function(bg_color, color)
	{
		return '_button_' + bg_color + '_' + color;
	},


	shadeColor:function(color, percent) {
		var num = parseInt(color,16),
			amt = Math.round(2.55 * percent),
			R = (num >> 16) + amt,
			B = (num >> 8 & 0x00FF) + amt,
			G = (num & 0x0000FF) + amt;
		return (0x1000000 + (R<255?R<1?0:R:255)*0x10000 + (B<255?B<1?0:B:255)*0x100 + (G<255?G<1?0:G:255)).toString(16).slice(1);
	},


	injectDocumentStyle:function(selector, style)
	{
		if ( ! document.styleSheets) {
			return;
		}

		if (document.getElementsByTagName("head").length == 0) {
			return;
		}

		var styleSheet;
		var mediaType;
		if (document.styleSheets.length > 0)
		{
			for (i = 0; i < document.styleSheets.length; i++) {
				if (document.styleSheets[i].disabled) {
					continue;
				}
				var media = document.styleSheets[i].media;
				mediaType = typeof media;

				if (mediaType == "string") {
					if (media == "" || (media.indexOf("screen") != -1)) {
						styleSheet = document.styleSheets[i];
					}
				} else if (mediaType == "object") {
					if (media.mediaText == "" || (media.mediaText.indexOf("screen") != -1)) {
						styleSheet = document.styleSheets[i];
					}
				}

				if (typeof styleSheet != "undefined") {
					break;
				}
			}
		}

		if (typeof(styleSheet) == "null")
		{
			var styleSheetElement = document.createElement("style");
			styleSheetElement.type = "text/css";

			document.getElementsByTagName("head")[0].appendChild(styleSheetElement);

			for (i = 0; i < document.styleSheets.length; i++) {
				if (document.styleSheets[i].disabled) {
					continue;
				}
				styleSheet = document.styleSheets[i];
			}

			var media = styleSheet.media;
			mediaType = typeof media;
		}

		if (mediaType == "string")
		{
			for (i = 0; i < styleSheet.rules.length; i++) {
				if (styleSheet.rules[i].selectorText.toLowerCase() == selector.toLowerCase()) {
					styleSheet.rules[i].style.cssText = style;
					return;
				}
			}
			styleSheet.addRule(selector, style);
		}
		else if (mediaType == "object")
		{
			for (i = 0; i < styleSheet.cssRules.length; i++)
			{
				if (styleSheet.cssRules[i].selectorText.toLowerCase() == selector.toLowerCase()) {
					styleSheet.cssRules[i].style.cssText = style;
					return;
				}
			}

			if(styleSheet.insertRule)
				styleSheet.insertRule(selector+'{'+style+'}', 0);
			else
				styleSheet.addRule(selector, style, -1);
		}
	},

	getBgColors:function()
	{
		return ION.Button.options.text_bg_colors;
	}
});
*/
