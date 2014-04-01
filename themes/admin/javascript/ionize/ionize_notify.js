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

	initialize: function(target, options)
	{
		this.windowEl = (typeOf(target) == 'string') ? $(target) : target;
		this.contentEl = target.getElement('div.mochaContent');

		if ( ! this.contentEl)
			this.contentEl = target;

		this.setOptions(options);

		var existing = this.exists();

		if ( ! existing)
		{
			var self = this;
			this.displayed = false;

			// Check options
			if (typeOf(this.options.type) == 'null') this.options.type = 'info';
			if (typeOf(this.options.className) == 'null') this.options.className = '';

			this.box = new Element('div', {
				'class': this.options.className + ' contentNotify mochaContentNotify ' + this.options.type,
				'data-type': this.options.type
			});

			this.box.store('instance', this);

			// Close button
			this.closeButton = new Element('div', {
				'class':'icon close white'}
			).setStyles({'position':'absolute', 'right':'7px', 'top':'7px'})
			.inject(this.box, 'top')
			.addEvent('click', function(e){
				e.stop();
				self.destroy();
			});


			if (this.contentEl)
			{
				(this.box).inject(this.contentEl, 'top');
				this.box.slide('hide');
			}
			return this;
		}
		else
		{
			this.box = existing;
			return this.box.retrieve('instance');
		}

	},
	
	show: function(msg)
	{
		this.setMessage(msg);
	
		// Resize content
		if (this.displayed == false)
		{
			this.box.slide('in');

			this.adjustWindowHeight('plus');
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

			this.adjustWindowHeight('minus');
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
			if (mode == 'plus')
				var newHeight = 	cs.y + bs.y + 10;
			else
				var newHeight = 	cs.y - bs.y + 10;

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
			this.box.adopt(msg);
		}
		else
		{
			if (Lang.get(msg) != null ) msg = Lang.get(msg);
			var div = new Element('div').set('html', msg);
			this.box.adopt(div);
		}
	},

	exists: function()
	{
		if (typeOf(this.options.type) != 'null')
			var selector = 'div.contentNotify[data-type=' + this.options.type + ']';
		else
			var selector = 'div.contentNotify';

		var boxes = $$(selector);

		var box = boxes.pick();

		if (typeOf(box) != 'null')
			return box;

		return false;
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
