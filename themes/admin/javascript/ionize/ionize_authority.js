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
	rules: null,

	/**
	 * Must be called before
	 */
	initialize:function()
	{
		ION.Authority.get_rules();
	},


	get_rules:function()
	{
		if (typeOf(ION.Authority.rules) == 'null')
		{
			new Request.JSON({
				url: admin_url + 'user/get_rules',
				method: 'post',
				loadMethod: 'xhr',
				onFailure: function(xhr){},
				onSuccess: function(responseJSON)
				{
					ION.Authority.rules = responseJSON.rules;
					return ION.Authority.rules
				}
			}).send();
		}
		else
		{
			return ION.Authority.rules;
		}
	},


	can:function(action, resource)
	{
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
