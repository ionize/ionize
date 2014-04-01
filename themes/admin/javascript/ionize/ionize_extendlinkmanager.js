/**
 * Extend Manager
 *
 */
ION.ExtendLinkManager = new Class({

	Implements: [Events, Options],

	/**
	 *
	 * @param options
	 */
	initialize: function(options)
	{
		this.setOptions(options);

		if (typeOf(options.container) == 'null')
			return;

		this.container = (typeOf(options.container) == 'string') ? $(options.container) : options.container;

		this.parent = this.options.parent;
		this.id_parent = this.options.id_parent;
		this.id_extend = this.options.id_extend;
		this.lang = this.options.lang;

		this.buildContainer();

		return this;
	},


	buildContainer: function()
	{
		var self = this;

		this.ul = new Element('ul', {
			'class':'m0 droppable dropArticle dropPage mh30 w95p'
		}).inject(this.container);

		this.ul.onDrop = function(element)
		{
			self.addLink(element.getProperty('data-type') + ':' + element.getProperty('data-id'));
		};
	},


	/**
	 *
	 * @param link      Ex : article:6.10
	 *                       page:6
	 */
	addLink:function(link)
	{
		var data = {
			value: link,
			parent: this.parent,
			id_parent: this.id_parent,
			id_extend: this.id_extend,
			lang: this.lang
		};

		// Extend Field
		new Request.JSON(
		{
			url: ION.adminUrl + 'extend_field/add_value_to_extend_field',
			data: data,
			onSuccess: this.successAddLink.bind(this)
		}).send();
	},


	successAddLink: function()
	{
		this.loadList();
	},


	loadList: function()
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'extend_field/get_extend_link_list/json',
			{
				id_extend: this.id_extend,
				parent: this.parent,
				id_parent: this.id_parent,
				lang: this.lang
			},
			{
				onSuccess: function(json)
				{
					self.completeLoadList(json);
				}
			}
		);
	},


	completeLoadList: function(json)
	{
		var self = this;

		this.ul.empty();

		new ION.List(
			{
				container: this.ul,
				buildUl: false,
				items: json,
				sort: {
					handler: '.drag',
					id_key: 'extend_value',
					url: ION.adminUrl + 'extend_field/save_extend_ordering'
				},
				post:{
					parent: this.parent,
					id_parent: this.id_parent,
					id_extend: this.id_extend,
					lang: this.lang
				},
				elements:
				[
					// Sort
					{
						element: 'span', 'class': 'icon drag left'
					},
					// Type Icon
					{
						element: function(item)
						{
							return new Element('a', {'class':'icon left link-img mr5 ' + item.type});
						}
					},
					// Unlink
					{
						element: 'a',
						'class': 'icon unlink right',
						onClick: function(item)
						{
							ION.JSON(
								ION.adminUrl + 'extend_field/remove_value_from_extend_field',
								{
									value: item.extend_value,
									parent: self.parent,
									id_parent: self.id_parent,
									id_extend: self.id_extend,
									lang: self.lang

								},
								{
									onSuccess: function()
									{
										self.loadList()
									}
								}
							);
						}
					},
					// Title
					{
						element: 'a',
						'class': 'title',
						text: 'title'
					}
				]
			}
		);

		// Style on container
		if (Object.getLength(json) > 0) this.ul.addClass('filled');
		else this.ul.removeClass('filled');

	},


	getContainer: function()
	{
		var selector = '.' + this.container + '[data-id-extend=' + this.id_extend + ']';

		if(this.lang != null)
			selector = selector + '[data-lang=' + this.lang + ']';

		var container = $$(selector);

		if (container.length > 0)
			return container[0];

		return null;
	}
});