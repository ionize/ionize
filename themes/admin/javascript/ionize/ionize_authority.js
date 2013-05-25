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

	/**
	 * User's rules
	 */
	rules: null,

	/**
	 * All rules, by type
	 */
	all_rules: Array(),

	has_all: false,

	is_initialized: false,

	onComplete: null,


	/**
	 * Must be called with options, once.
	 * Is called by init() with onComplete as option.
	 *
	 */
	initialize: function(options)
	{
		if (options)
		{
			if (ION.Authority.is_initialized == false || options.refresh == true)
			{
				ION.Authority.is_initialized = true;

				if (typeOf(options.onComplete) != 'null')
				{
					ION.Authority.onComplete = options.onComplete;
				}
				ION.Authority.loadRules();
			}
		}
	},


	loadRules: function()
	{
		new Request.JSON({
			url: admin_url + 'user/get_rules',
			method: 'post',
			loadMethod: 'xhr',
			onFailure: function(xhr){},
			onSuccess: function(responseJSON)
			{
				ION.Authority.rules = responseJSON.rules;
				ION.Authority.loadAllRules();
			}
		}).send();
	},


	loadAllRules: function()
	{
		new Request.JSON({
			url: admin_url + 'rule/get_all',
			method: 'post',
			data: {
			//	'type':'backend'
			},
			loadMethod: 'xhr',
			onFailure: function(xhr){},
			onSuccess: function(responseJSON)
			{
				ION.Authority.all_rules = responseJSON.rules;
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


	/**
	 * Check if one action is allowed.
	 * If check_has_rule is set to true, if only make the check if one
	 * permission exists for the resource.
	 *
	 * @param action
	 * @param resource
	 * @param check_has_rule
	 * @returns {boolean}
	 *
	 */
	can:function(action, resource, check_has_rule)
	{
		var can = false;

		if (ION.Authority.has_all == true)
			return true;

		if (typeOf(check_has_rule) != 'null' && ! ION.Authority.resourceHasRule(resource))
			return true;

		Object.each(ION.Authority.rules, function(rule)
		{
			if (action == rule.action && resource == rule.resource && rule.allowed == true)
				can = true;
		});

		return can;
	},


	cannot:function(action, resource)
	{
		return ! (ION.Authority.can(action, resource));
	},


	resourceHasRule: function(resource)
	{
		var has = false;

		Object.each(ION.Authority.all_rules, function(rule)
		{
			if (resource == rule.resource)
			{
				has = true;
			}
		});

		return has;
	}
});
