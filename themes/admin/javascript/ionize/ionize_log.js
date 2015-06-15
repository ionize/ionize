var LogWindowManager = new Class({

	Implements: [Events, Options],

	options: {
		container: 'body'
	},

	active: true,

	reloadTimer: 0,

	initialize: function()
	{
		this.container = window.document.getElement(this.options.container);

		var self = this,
			menu = new Element('div', {class:'header'}).inject(this.container);

		this.title = new Element('div', {class:'title'}).inject(menu);
		var buttons = new Element('div', {class:'buttons'}).inject(menu);
		this.btnStop = new Element('button', {class:'button left red', text:'Pause Log'}).inject(buttons);
		this.btnStart = new Element('button', {class:'button left green', text:'Start Log'}).inject(buttons);

		this.btnStart.hide();

		this.btnStop.addEvent('click', function(){
			if (self.active == true)
				self.stopRetrieve();
		});

		this.btnStart.addEvent('click', function(){
			if (self.active == false)
				self.startRetrieve();
		});

		this.logContainer = new Element('div', {class:'terminal'}).inject(this.container);

		this.startRetrieve();
	},


	getLogs: function()
	{
		var self = this;

		clearTimeout(this.reloadTimer);

		if (this.container)
		{
			new Request.JSON({
				url: window.location.href + '/get_logs',
				method: 'post',
				loadMethod: 'xhr',
				onSuccess: function (json)
				{
					self.title.set('html', json.file_name);

					self.logContainer.removeClass('error');

					if (json.error == 1)
					{
						self.logContainer.addClass('error');
						self.logContainer.set('html', json.message);
					}
					else
					{
						self.logContainer.set('html', json.lines);
						window.scrollTo(0,document.body.scrollHeight);
					}

					self.reloadTimer = setTimeout(function(){self.getLogs()}, 2000);
				}
			}).send();
		}
	},

	stopRetrieve: function()
	{
		this.active = false;
		this.btnStop.hide();
		this.btnStart.show();
		clearTimeout(this.reloadTimer);
	},

	startRetrieve: function()
	{
		this.active = true;
		this.btnStart.hide();
		this.btnStop.show();
		this.getLogs();
	}
});

// Laod the Log Manager
window.addEvent('load', function()
{
	new LogWindowManager();
});
