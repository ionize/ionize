/**
 * Ionize User Object
 *
 * Current connected user
 *
 */

Ionize.User = (Ionize.User || {});

Ionize.User.append = function(hash){
	Object.append(Ionize.User, hash);
}.bind(Ionize.User);

Ionize.User.append(
{
	user: null,                     // User's object
	authorizations: new Array(),    // User's Authorizations

	initialize: function(options)
	{
		this.getUser();
		return this;
	},

	getLoggedUser: function()
	{
		var user = null;

		new Request.JSON(
		{
			url: admin_url + 'user/get_current_user',
			method: 'post',
			loadMethod: 'xhr',
			async: false,
			onFailure: function(xhr)
			{
				return null;
			},
			onSuccess: function(responseJSON)
			{
				user = responseJSON;
			}
		}).send();

		return user;
	},

	getUser: function()
	{
		if (typeOf(this.user) == 'null')
		{
			this.user = this.getLoggedUser();
		}
		return this.user;
	},

	getAuthorizations: function()
	{
		/*
		 * Needs to be written.
		 * Will be written with the Ionize's RBAC implementation
		 *
		 */
		/*
		if (typeOf(this.user) != 'null')
		{
			var self = this;
			new Request.JSON(
			{
				url: admin_url + 'user/get_user_authorizations',
				method: 'post',
				loadMethod: 'xhr',
				async: false,
				onFailure: function(xhr)
				{
					console.log('Ionize.User->getAuthorizations() : Authorizations not found OR not connected');
				},
				onSuccess: function(responseJSON)
				{
					self.authorizations = responseJSON;
					return self.user;
				}
			}).send();
		}
		*/
		return false;
	},

	/**
	 * Returns one field from the user object
	 * @param   field
	 * @return  {*}
	 *
	 */
	get: function(field)
	{
		if (typeOf(this.user) != 'null')
		{
			if (typeOf(this.user[field]) != 'null')
			{
				return this.user[field];
			}
		}
		console.log('Ionize.User->get(' + field + ') : ' + 'not found');
		return false;
	},

	getGroup: function()
	{
		if (typeOf(this.user.group) != 'null')
			return this.user.group;
	},

	getGroupField: function(field)
	{
		if (typeOf(this.user) != 'null')
		{
			if (typeOf(this.user.group[field]) != 'null')
				return this.user.group[field];
			return false;
		}
	},

	/**
	 * Return the current user's level
	 *
	 * @return {*}
	 *
	 */
	getGroupLevel:function()
	{
		return this.getGroupField('level');
	},

	getGroupCode:function()
	{
		return this.getGroupField('slug');
	},

	getGroupName:function()
	{
		return this.getGroupField('group_name');
	},

	getEmail: function()
	{
		return this.get('email');
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

