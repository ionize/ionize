/**
 *
 * @type {*}
 *
 */
ION.Authority = new Class({
})
// Static methods
.extend(
{
	Implements: [Events, Options],

	rules: null,

	has_all: false,

	is_initialized: false,

	onComplete: null,


	/**
	 * Must be called with options, once.
	 * Is called by init() with onComplete as option.
	 *
	 */
	initialize:function(options)
	{
		if (options)
		{
			if (ION.Authority.is_initialized == false)
			{
				ION.Authority.is_initialized = true;

				if (options && typeOf(options.onComplete) != 'null')
				{
					ION.Authority.onComplete = options.onComplete;
				}
				ION.Authority.load_rules();
			}
		}
	},


	load_rules:function()
	{
		new Request.JSON({
			url: admin_url + 'user/get_rules',
			method: 'post',
			loadMethod: 'xhr',
			onFailure: function(xhr){},
			onSuccess: function(responseJSON)
			{
				ION.Authority.rules = responseJSON.rules;
				ION.Authority.onSuccess();
			}
		}).send();
	},


	onSuccess: function()
	{
		Object.each(ION.Authority.rules, function(rule)
		{
			if (rule.action=='manage' && rule.resource=='all')
				ION.Authority.has_all = true;
		});

		if (ION.Authority.onComplete)
			ION.Authority.onComplete();
	},


	get_rules:function()
	{
		return ION.Authority.rules;
	},


	can:function(action, resource)
	{
		if (ION.Authority.has_all == true)
			return true;

		var rules = ION.Authority.get_rules();
		var can = false;

		Object.each(rules, function(rule)
		{
			if (action == rule.action && resource == rule.resource && rule.allowed == true)
				can = true;
		});

		return can;
	}
});
