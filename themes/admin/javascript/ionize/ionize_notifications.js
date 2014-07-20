ION.Notifications = new Class({

	Implements: [Events, Options],

	initialize: function(options)
	{
		this.setOptions(options);

		this.container = (typeOf(options.container) == 'string') ? $(options.container) : options.container;
		this.notCont = null;
	},

	get: function()
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'notification/get_ionize_notifications',
			{},
			{
				onSuccess: function(json)
				{
					if (Object.getLength(json) > 0)
						self.display(json);
					else
						new Element('p', {'class':'lite', html:Lang.get('ionize_message_no_network')}).inject(self.container);
				}
			}
		);
	},

	display: function(json)
	{
		// Compare versions
		var yours = Settings.get('ionize_version'),
			upToDate = this.versionCompare(yours, json.version),
			dl = new Element('dl', {'class':'m0'}),
			dt = new Element('dt', {'class':'lite'}).inject(dl),
			dd = new Element('dd').inject(dl),
			dl2 = new Element('dl', {'class':'m0'}),
			dt2 = new Element('dt', {'class':'lite'}).inject(dl2),
			dd2 = new Element('dd').inject(dl2)
		;

		new Element('span', {text:'Your version'}).inject(dt);
		var yourVersion = new Element('strong', {text:yours}).inject(dd);
		new Element('span', {text:'Current version'}).inject(dt2);
		new Element('strong', {text:json.version}).inject(dd2);
		dl.inject(this.container);
		dl2.inject(this.container);

		if (upToDate > -1) yourVersion.addClass('green');
		else yourVersion.addClass('red');

		this.displayNotifications(json);
	},

	displayNotifications: function(json)
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'notification/get_local_notifications',
			{
				category: 'Message'
			},
			{
				onSuccess: function(json)
				{
					if (self.notCont) self.notCont.destroy();

					if (typeOf(json) == 'array' && Object.getLength(json) > 0)
					{
						self.notCont = new Element('div', {'class':'clearfix mt20'}).inject(self.container);

						new ION.TableList({
							items: json,
							container: self.notCont,
							headerClass: 'pr10',
							columns:[
								{key:'date_creation',label:'Date',elementClass:'lite','class':'pr10'},
								{key:'category',label:Lang.get('ionize_label_category'),'class':'pr10'},
								{key:'content',label:Lang.get('ionize_label_content')},
								{
									key:'id_notification','class':'w20',type:'icon',element:'a',elementClass:'icon remove',
									title:Lang.get('ionize_label_set_as_read'),
									onClick: function(item){self.setAsRead(item)}
								}
							]
						});
					}
				}
			}
		);
	},

	setAsRead: function(item)
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'notification/set_ionize_notification_as_read',
			item,
			{onSuccess: function(){self.displayNotifications({});}}
		);
	},

	/**
	 * Compares two software version numbers (e.g. "1.7.1" or "1.2b").
	 *
	 * This function was born in http://stackoverflow.com/a/6832721.
	 *
	 * @param {string} v1 The first version to be compared.
	 * @param {string} v2 The second version to be compared.
	 * @param {object} [options] Optional flags that affect comparison behavior:
	 * 
	 * lexicographical: true compares each part of the version strings lexicographically instead of
	 * naturally; this allows suffixes such as "b" or "dev" but will cause "1.10" to be considered smaller than
	 * "1.2".
	 * 
	 * zeroExtend: true changes the result if one version string has less parts than the other. In
	 * this case the shorter string will be padded with "zero" parts instead of being considered smaller.
	 * 
	 * @returns {number|NaN}
	 * 
	 * 0 if the versions are equal
	 * a negative integer iff v1 < v2
	 * a positive integer iff v1 > v2
	 * NaN if either version string is in the wrong format
	 *
	 * @copyright by Jon Papaioannou (["john", "papaioannou"].join(".") + "@gmail.com")
	 * @license This function is in the public domain. Do what you want with it, no strings attached.
	 *
	 */
	versionCompare: function(v1, v2, options) {
		var lexicographical = options && options.lexicographical,
			zeroExtend = options && options.zeroExtend,
			v1parts = v1.split('.'),
			v2parts = v2.split('.');

		function isValidPart(x) {
			return (lexicographical ? /^\d+[A-Za-z]*$/ : /^\d+$/).test(x);
		}

		if (!v1parts.every(isValidPart) || !v2parts.every(isValidPart)) {
			return NaN;
		}

		if (zeroExtend) {
			while (v1parts.length < v2parts.length) v1parts.push("0");
			while (v2parts.length < v1parts.length) v2parts.push("0");
		}

		if (!lexicographical) {
			v1parts = v1parts.map(Number);
			v2parts = v2parts.map(Number);
		}

		for (var i = 0; i < v1parts.length; ++i) {
			if (v2parts.length == i) {
				return 1;
			}

			if (v1parts[i] == v2parts[i]) {
				continue;
			}
			else if (v1parts[i] > v2parts[i]) {
				return 1;
			}
			else {
				return -1;
			}
		}

		if (v1parts.length != v2parts.length) {
			return -1;
		}

		return 0;
	}

});