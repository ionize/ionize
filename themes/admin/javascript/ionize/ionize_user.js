
ION.UserClass = new Class({

	Implements: [Options, Class.Singleton, Events, Class.Binds],

	user: null,

	/**
	 * Singleton
	 *
	 * @returns {*}
	 */
	initialize: function()
	{
		return this.check() || this.init();
	},

	init:function()
	{
		this.user = null;
		this.getLoggedUser();
		return this;
	},

	getLoggedUser: function()
	{
		var self = this;
		new Request.JSON(
		{
			url: ION.adminUrl + 'user/get_current_user',
			method: 'post',
			loadMethod: 'xhr',
			async: false,
			onFailure: function()
			{
				return null;
			},
			onSuccess: function(json)
			{
				self.user = json;
				self.fireEvent('onUserLoaded');
			}
		}).send();
	},

	// @todo : write
	isLoggedIn: function()
	{

	},

	getUser: function()
	{
		return this.user;
	},

	getRole: function()
	{
		if (typeOf(this.user.role) != 'null')
			return this.user.role;
		return null;
	},


	is: function(role_codes)
	{
		if (typeOf(role_codes) == 'string')
			role_codes = [role_codes];

		var role = this.getRole();

		if (role != null)
		{
			if (role_codes.contains(role.role_code))
				return true;
		}

		return false;
	},

	is_not: function(role_codes)
	{
		if (typeOf(role_codes) == 'string')
			role_codes = [role_codes];

		var role = this.getRole();

		if (role != null)
		{
			if (role_codes.contains(role.role_code))
				return false;
		}

		return true;
	},

	getGroupField: function(field)
	{
		if (typeOf(this.user) != 'null')
		{
			if (typeOf(this.user.group[field]) != 'null')
				return this.user.group[field];
		}

		return null;
	},

	get: function(key)
	{
		return (typeOf(this.user[key]) != 'null') ? this.user[key] : null;
	},

	getGroupLevel:function()
	{
		return this.getGroupField('level');
	},

	getName: function()
	{
		return this.get('screen_name');
	},

	getCode: function()
	{
		return this.get('username');
	}
});







