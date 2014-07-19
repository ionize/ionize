
/**
 * Button Toolbar Class
 *
 * @type {Class}
 */
ION.ButtonToolbar = new Class({

	Implements: [Events, Options],

	buttons: [],
	toolbar: [],

	options:
	{
		btnToolbarClass: 'btn-toolbar m0',
		'class': null
	},

	initialize: function(element, options)
	{
		var self = this;
		this.setOptions(options);

		this.container = $(element);

		this.toolbar = new Element('div',{'class': this.options.btnToolbarClass}).inject(this.container);

		// Additional CSS classes
		if (typeOf(options['class']) != 'null') this.toolbar.addClass(options['class']);

		Array.each(options.buttons, function(btn)
		{
			self.addButton(btn);
		});

		this.fireEvent('onLoaded', this);

		return this;
	},

	/**
	 * Each button is a button group
	 *
	 * @param options
	 */
	addButton: function(options)
	{
		options = Object.merge(
			options,
			{
				'container': this.toolbar
			}
		);

		var button = new ION.Button(options);

		this.buttons.push(button);

		return this;
	},

	/**
	 * Return true if the Toolbar has already the asked button id.
	 * The button must have an ID
	 * @param id
	 * @returns {boolean}
	 */
	hasButton: function(id)
	{
		if (this.getButtonById(id) != null)
			return true;

		return false;
	},

	adopt:function(element)
	{
		var pos = arguments[1];
		if ( ! pos) pos = 'bottom';

		if (element.button)
		{
			element.button.inject(this.toolbar, pos);
		}
	},

	remove: function(id)
	{
		var btn = this.getButtonById(id);

		if (btn != null)
			btn.destroy();
	},

	activateButton: function(id)
	{
		var btn = this.getButtonById(id);
		if (btn) btn.activate();
	},

	deactivateButton: function(id)
	{
		var btn = this.getButtonById(id);
		if (btn) btn.deactivate();
	},

	enableButton: function(id)
	{
		var btn = this.getButtonById(id);
		if (btn) btn.enable();
	},

	disableButton: function(id)
	{
		var btn = this.getButtonById(id);
		if (btn) btn.disable();
	},

	getButtonById: function(id)
	{
		var found = null;
		Array.each(this.buttons, function(btn)
		{
			if (btn.getElement().id && btn.getElement().id == id)
				found = btn;
		});
		return found;
	}
});


ION.Button = new Class({

	Implements: [Events, Options],


	isEnabled: 	true,
	isActive: 	false,
	button: 	null,
	btnGroup: 	null,
	w: 			null,

	options:
	{
		baseClass: 		'button',
		'class': 		'',				// Additional CSS class
		title: 			'',				// Button title
		icon:			null,			// Icon class
		iconClass:		'',				// Additional icon CSS class
		parent:			null,			// Parent DOM Element
		btnGroupClass: 	'btn-group',
		enabled: 		true,

		// onClick: function(ION.Button, DomElement)
		// onActivate: function(ION.Button, DomElement)
		// onDeactivate: function(ION.Button, DomElement)
	},

	initialize: function(o)
	{
		var self = this;

		this.setOptions(o);

		this.container = typeOf(o.container) != 'null' ? o.container :
						 (typeOf(o.parent) !='null' ? o.parent : null);

		this.container = typeOf(this.container) == 'string' ? $(this.container) : this.container;

		var cl = typeOf(o['class'] != 'null') ? this.options.baseClass + ' ' + o['class'] : this.options.baseClass;

		this.button = new Element('a', {'class': cl});
		this.button.store('instance', this);

		if (o.id) this.button.setProperty('id', o.id);

		if (o.w) this.w = o.w;

		this.buttonTitle = new Element('span', {'html': o.title}).inject(this.button);

		if (this.options.iconClass) this.options.iconClass = ' ' + this.options.iconClass;
		if (o.icon) new Element('i', {'class': o.icon + this.options.iconClass}).inject(this.button, 'top');

		// List button
		if (typeOf(o.elements) != 'null' && (o.elements).length > 0)
		{
			this.addCaret();

			this.btnGroup = new Element('div', {'class': o.btnGroupClass});
			this.button.inject(this.btnGroup);

			this.addListElements(o.elements);

			// Store the event
			this.options.onClick = function()
			{
				if (self.btnGroup.hasClass('open'))
				{
					self.btnGroup.removeClass('open');
				}
				else
				{
					$$('.' + self.options.btnGroupClass).removeClass('open');
					self.btnGroup.addClass('open');
					self.correctBtnGroupPosition();
				}
			}

			this.btnGroup.addEvent('click', this.options.onClick);

			if (this.container)	this.btnGroup.inject(this.container);
		}
		// Simple Button
		else
		{
			if (typeOf(o.onClick) == 'function')
			{
				// Store the event
				this.options.onClick = function()
				{
					o.onClick(self, self.button);
				};
				this.button.addEvent('click', this.options.onClick);
			}

			if (this.container) this.button.inject(this.container);
		}

		if (o.enabled == false)
			this.disable();

		this.fireEvent('onLoaded', this.button);

		return this;
	},

	correctBtnGroupPosition: function()
	{
		var ul = this.btnGroup.getElement('ul.dropdown-menu');

		var dim = ul.getCoordinates(),
			docDim = document.getCoordinates();

		if ((dim.left + dim.width) > docDim.width)
			ul.setStyles({'right': 0, left:'auto'});
	},

	addListElements: function(elements)
	{
		var ul = this.btnGroup.getElement('ul.dropdown-menu');

		if ( ! ul) ul = new Element('ul', {'class':'dropdown-menu'}).inject(this.btnGroup);

		Array.each(elements, function(el)
		{
			var li = new Element('li').inject(ul);
			var a = new Element('a', {text: el.title}).inject(li);

			if (typeOf(el.onClick) == 'function')
			{
				a.addEvent('click', el.onClick);
			}
		});

		// Calculate list position in case the button is on the right
	},

	setTitle: function(title)
	{
		this.buttonTitle.set('text', title);
	},


	/**
	 * Activates one button
	 * (make it selected)
	 *
	 * @param args		String or Array of IDs. Partners to unactivate
	 */
	activate: function()
	{
		var partners = arguments[0];

		if (typeOf(partners) != 'null')
		{
			if (typeOf(partners) == 'array')
			{
				Array.each(partners, function(id){
					$(id).removeClass('active');
				});
			}
			else
				$(partners).removeClass('active');
		}

		this.button.addClass('active');
		this.fireEvent('onActivate', [this, this.button]);
	},

	deactivate: function()
	{
		this.button.removeClass('active');
		this.fireEvent('onDeactivate', [this, this.button]);
	},

	isActivated: function()
	{
		return this.button.hasClass('active');
	},

	toggleActivate: function()
	{
		if( ! this.isActivated())
			this.activate();
		else
			this.deactivate();
	},

	enable: function()
	{
		if ( ! this.isEnabled)
		{
			this.button.removeProperty('disabled');
			this.button.removeClass('disabled');

			if (typeOf(this.options.onClick) == 'function')
			{
				if (this.btnGroup != null)
					this.btnGroup.addEvent('click', this.options.onClick);
				else
					this.button.addEvent('click', this.options.onClick);
			}
		}
	},

	disable: function()
	{
		this.button.setProperty('disabled', 'disabled');
		this.button.addClass('disabled');
		if (this.btnGroup != null)
		{
			this.btnGroup.removeEvents();
			this.btnGroup.removeClass('open');
		}
		else
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
	},

	destroy: function()
	{
		if (this.btnGroup != null)
			this.btnGroup.destroy();
		else
			this.button.destroy();
	},

	getElement: function()
	{
		return this.button;
	},

	getWindow: function()
	{
		return this.w;
	},

	addCaret: function()
	{
		new Element('span', {'class':'caret'}).inject(this.button);
	}
});
