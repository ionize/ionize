/**
 
Class: Notify
	Creates a windows notification box.

Syntax:
	(start code)
	new ION.Notify(target, options);
	(end)

Arguments:
	target - (string) ID of the window
	options

Options:
	className - (string) Optional box class name
	type - (string) Box type. Can be 'error', 'information', 'alert'

Returns:
	Notify box object.
	
Example :


*/
ION.Notify = new Class({

	Implements: [Events, Options],

	options:
	{
		type: 'info',			// 'info', 'success', 'alert', 'error'
		className: '',			// Additional CSS class
		autoHide: true			// Automatic close after few seconds
	},

	initialize: function(target, options)
	{
		this.windowEl = (typeOf(target) == 'string') ? $(target) : target;
		this.contentEl = this.windowEl.getElement('div.mochaContent');
		if ( ! this.contentEl) this.contentEl = this.windowEl;

		this.setOptions(options);

		var existing = this.exists();

		if ( ! existing)
		{
			var self = this;
			this.displayed = false;

			// Check options
			if (typeOf(this.options.type) == 'null') this.options.type = 'info';
			if (typeOf(this.options.className) == 'null') this.options.className = '';

			this.box = new Element('div');
			this.box.store('instance', this);

			this.boxContent = new Element('div').inject(this.box);
			this.setType(this.options.type);

			// Close button
			this.closeButton = new Element('div', {'class':'icon close white'})
				.setStyles({'position':'absolute', 'right':'7px', 'top':'7px'})
				.inject(this.box, 'top')
				.addEvent('click', function(e){
					e.stop();
					self.destroy();
				});

			if (this.contentEl)
			{
				(this.box).inject(this.contentEl, 'top');
				this.box.slide('hide');
				this.box.getParent('div').setStyle('margin', 0);
			}
			return this;
		}
		else
		{
			this.box = existing;
			return this.box.retrieve('instance');
		}
	},


	/**
	 * Sets the Notify type (style)
	 * @param type	'info', 'alert', 'success', 'error'
	 *
	 */
	setType: function(type)
	{
		this.options.type = type;
		this.boxContent.removeProperty('class');
		this.boxContent.addClass(this.options.className + ' contentNotify mochaContentNotify ' + this.options.type);
		this.boxContent.setProperty('data-type', type);
	},

	show: function(msg)
	{
		var self = this,
			type = typeOf(arguments[1]) != 'null' ? arguments[1] : null;

		this.setMessage(msg);

		// Change type ?
		if (type != null) this.setType(type);

		// Resize content
		if (this.displayed == false)
		{
			this.box.slide('in');

		//	this.adjustWindowHeight('plus');

			if (this.options.autoHide)
				self.hide.delay(3000, self);
		}
		else
		{
			this.resize();
		}

		this.displayed = true;
	},
	
	hide: function()
	{
		// Resize content
		if (this.displayed == true)
		{
			this.box.slide('out');

		//	this.adjustWindowHeight('minus');
		}
		this.displayed = false;
	},

	resize: function()
	{
		var cs = this.box.getSize();
		this.box.getParent('div').setStyle('height', cs.y + 'px');
		this.box.slide('in');

	},

	adjustWindowHeight: function(mode)
	{
		var cs = this.contentEl.getSize();
		var bs = this.contentEl.getElement('.mochaContentNotify').getSize();

		this.contentEl.getChildren('.validation-advice').each(function(item){
			bs.y += item.getSize().y;
		});

		// Resize the window... if window.
		if (this.windowEl.retrieve('instance'))
		{
			var newHeight = 	(mode == 'plus') ? (cs.y + bs.y + 10) : (cs.y - bs.y + 10);

			this.windowEl.retrieve('instance').resize(
			{
				height: newHeight,
				width: null,
				centered:false,
				top:null
			});
		}
	},

	setMessage: function(msg)
	{
		if (typeof(msg) == 'object')
		{
			this.boxContent.empty().adopt(msg);
		}
		else
		{
			if (Lang.get(msg) != null ) msg = Lang.get(msg);
			var div = new Element('div').set('html', msg);
			this.boxContent.empty().adopt(div);
		}
	},

	exists: function()
	{
		var selector = (typeOf(this.options.type) != 'null')
			? 'div.contentNotify[data-type=' + this.options.type + ']'
			: 'div.contentNotify';

		var boxes = $$(selector),
			box = null;

		if (Object.getLength(boxes) > 0)
			box = boxes[0];

		return (box != null)
			? box.getParent('div')
			: false;
	},

	destroy: function()
	{
		this.box.getParent('div').destroy();
	},

	removeAll:function()
	{
		var boxes = $$('div.contentNotify');

		boxes.each(function(box)
		{
			box.getParent('div').destroy();
		});
	}

});
