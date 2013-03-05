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

		this.displayed = false;
		
		// new Element('p').set('text', options.message)
		if ( this.box = this.contentEl.getElement('.mochaContentNotify'))
			this.box.getParent('div').destroy();

		this.box = new Element('div', {'class':options.className + ' mochaContentNotify ' + options.type});
/*
		this.box.set('slide',
		{
			duration: 'short',
			transition: 'sine:out'
		});
*/
		if (this.contentEl)
		{
			(this.box).inject(this.contentEl, 'top');
			this.box.slide('hide');
		}
		return this;
	},
	
	show: function(msg)
	{
		this.setMessage(msg);
	
		this.box.slide('in');

		// Resize content
		if (this.displayed == false)
		{
			var cs = this.contentEl.getSize();
			var bs = this.contentEl.getElement('.mochaContentNotify').getSize();

			this.contentEl.getChildren('.validation-advice').each(function(item){
				console.log(item);
				bs.y += item.getSize().y;
			});

			// Resize the window... if window.
			if (this.windowEl.retrieve('instance'))
			{
				this.windowEl.retrieve('instance').resize(
				{
					height: cs.y + bs.y + 10,
					width: null,
					centered:false,
					top:null
				});
			}
		}

		this.displayed = true;
	},
	
	hide: function()
	{
		this.box.slide('out');

		// Resize content
		if (this.displayed == true)
		{
			var cs = this.contentEl.getSize();
			var bs = this.contentEl.getElement('.mochaContentNotify').getSize();

			if (this.windowEl.retrieve('instance'))
			{
				this.windowEl.retrieve('instance').resize(
				{
					height: cs.y - bs.y + 10,
					width: null,
					centered:false,
					top:null
				});
			}

		}
		this.displayed = false;
	},
	
	setMessage: function(msg)
	{
		this.box.empty();
		
		if (typeof(msg) == 'object')
		{
			this.box.adopt(msg);
		}
		else
		{
			if (Lang.get(msg) != null ) msg = Lang.get(msg);
			this.box.set('html', msg);
		}
	}
});
