/**
 * Ionize Tracker Object
 *
 * Sends / get infos to the backend about the current user activity
 * Used to know who's connected and on what he / her is working.
 *
 * Instanced once
 *
 */

Ionize.Tracker = (Ionize.Tracker || {});

Ionize.Tracker.append = function(hash){
	Object.append(Ionize.Tracker, hash);
}.bind(Ionize.Tracker);

Ionize.Tracker.append(
{
	user: null,                     // User's object
	updateDelay: 2000,
	updateInt:null,
	domDashboardUserList:'trackerCurrentConnectedUsers',

	initialize: function(options)
	{
		this.options = options;
		this.parent = $(options.parent);

		if (this.options.updateDelay)
			this.updateDelay = this.options.updateDelay;

		return this;
	},

	startTracking: function()
	{
		this.user = ION.User.getUser();
		clearInterval(this.updateInt);
		this.updateTrackingData.delay(1000, this);
		this.updateInt = this.updateTrackingData.periodical(this.updateDelay, this);
	},

	updateTrackingData: function()
	{
		var self = this;

		var data = {
			'user': ION.User.getUser(),
			'elements':this.getDomEditedElements()
		};

		new Request.JSON({
			url: admin_url + 'tracker',
			method: 'post',
			loadMethod: 'xhr',
			data: data,
			onFailure: function(xhr){},
			onSuccess: function(responseJSON, responseText)
			{
				// JS Callback
				if (responseJSON && responseJSON.callback)
				{
					ION.execCallbacks(responseJSON.callback);
				}

				if (typeOf(responseJSON.users) != null)
				{
					// Update dashboard
					self.updateDashboardUserList(responseJSON.users);

					// Udpate tracker flags
					self.updateElementTrackers(responseJSON.users);
				}
			}
		}).send();
	},

	getDomEditedElements: function()
	{
		var domElements = new Array();
		var elements = $$('.data-tracker');

		elements.each(function(el)
		{
			domElements.push({
				'element': el.getProperty('data-element'),
				'id': el.getProperty('data-id'),
				'title': el.getProperty('data-title'),
				'url': el.getProperty('data-url')
			});
		});
		return domElements;
	},

	updateDashboardUserList:function(users)
	{
		var container = $(this.domDashboardUserList);

		if (typeOf(container) != 'null')
		{
			container.empty();
			users.each(function(user)
			{
				var elements = user.elements;

				var div = new Element('div', {'class': 'desktopUserIcon'});

				new Element('div', {'class': 'avatar'}).inject(div);
				var name = new Element('span',{'class': 'name'}).set('text', user.screen_name);
				name.inject(div);

				if ( ! elements)
				{
					var action = new Element('span',{'class': 'action inactive'}).set('text', Lang.get('ionize_label_tracker_status_sleeping'));
					action.inject(div);
				}

				if (elements)
				{
					div.addClass('editing');

					elements.each(function(element)
					{
						// var a = new Element('a', {'href':element.url}).set('text', element.element + ' : ' + element.title).inject(div);
						var a = new Element('a').set('text', element.element + ' : ' + element.title).inject(div);
					});
				}
				div.inject(container);
			});
		}
	},

	updateElementTrackers:function(users)
	{
		var cUser = ION.User.getUser();
		$$('.tracker-flag').removeClass('tracker-flag');
		users.each(function(user)
		{
			if (user.id_user != cUser.id_user)
			{
				var elements = user.elements;

				if (elements)
				{
					elements.each(function(element)
					{
						var domTracker = $(element.element + '-tracker-' + element.id);

						if (typeOf(domTracker) != 'null')
						{
							domTracker.addClass('tracker-flag');
							domTracker.set('text', Lang.get('ionize_label_tracker_edited_by') + ' : ' + user.screen_name);
						}
					});
				}
			}
		});
	}
});
