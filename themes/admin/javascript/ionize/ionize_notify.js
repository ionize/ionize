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
		this.setOptions(options);
		
		this.displayed = false;
		
		// new Element('p').set('text', options.message)
		this.box = new Element('div', {'class':options.className + ' ' + options.type});
		
		this.box.set('slide', 
		{
			duration: 'short',
			transition: 'sine:out'
		});

		// All Application windows are prefixed with "w".
		if ($('w' + target + '_content'))
		{
			this.target = target;
			
			this.windowEl = $('w' + target);
			this.contentEl = $('w' + target + '_content');
			
			(this.box).inject(this.contentEl, 'top');

			this.box.slide('hide');
		}
	},
	
	show: function(msg)
	{
		this.setMessage(msg);
	
		this.box.slide('in');
		
		if ($(this.options.hide))
		{
			$(this.options.hide).fade('out');
		}
		
		// Resize content
		if (this.displayed == false)
		{
			this.windowEl.retrieve('instance').resize({height: (this.contentEl.getSize()).y + (this.box.getSize()).y + 10});
		}

		this.displayed = true;
	},
	
	hide: function()
	{
		this.box.slide('out');

		if ($(this.options.hide))
		{
			$(this.options.hide).fade('in');
		}
		
		// Resize content
		if (this.displayed == true)
		{
			this.windowEl.retrieve('instance').resize({height: (this.contentEl.getSize()).y - (this.box.getSize()).y + 10});
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
			if (Lang.get(msg) != '' ) msg = Lang.get(msg);
			
			this.box.set('html', msg);
		}
	}
});
