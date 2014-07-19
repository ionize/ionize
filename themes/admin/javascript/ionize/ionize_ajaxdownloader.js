/**
 * Ionize Ajax Downloader
 *
 *
 *
 */

ION.AjaxDownloader = new Class({

	Implements: [Events, Options],

	options: {
		action:'',
		post: {}
	},

	initialize: function(options)
	{
		this.setOptions(options);

		this.container = (typeOf(options.container) == 'string') ? $(options.container) : options.container;

		var iframe = new IFrame({
			name: 'iFrame_' + ION.generateHash(8),
			styles: {
				display: 'none'
			}
		}).inject(this.container);

		// Form
		this.dataForm = new Element('form',
		{
			enctype: 'multipart/form-data',
			encoding: 'multipart/form-data',
			method: 'post',
			action: options.action,
			target: iframe.get('name')
		}).inject(this.container);
	},

	submit: function()
	{
		var self = this;

		this.dataForm.empty();

		// Post data
		Object.each(this.options.post, function(val, key)
		{
			new Element('input', {
				'name': key,
				'type': 'hidden',
				'value': val
			}).inject(self.dataForm);
		});

		this.dataForm.submit();
	},

	setPost: function(key, value)
	{
		this.options.post[key] = value;

		return this;
	}
});