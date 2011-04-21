ION.ItemManager = new Class({

	Implements: [Events, Options],
	
	options: ION.options,
	
	initialize: function(options)
	{
		this.setOptions(options);
		
		this.container = $(this.options.container);

		this.baseUrl = this.options.baseUrl;
		
		this.adminUrl = this.options.adminUrl;

		this.element = this.options.element;

		// Set parent and id_parent (for ordering)
		if (options.parent_element && options.id_parent && options.parent_element !='')
		{
			this.parent_element = options.parent_element;
			this.id_parent = options.id_parent;
		}
		
		this.initDeleteEvent();
	},

	/**
	 * Adds the delete Event on each .delete anchor in the list
	 *
	 */
	initDeleteEvent: function()
	{
		var type = this.element;

		$$('#' + this.options.container + ' .delete').each(function(item)
		{
			ION.initItemDeleteEvent(item, type);
		});
	},

	
	/**
	 * Makes the containers elements sortable
	 * needs to be explicitely called after an itemManager init.
	 *
	 * handler element class : .drag
	 *
	 * Usage of this function needs that the CI controller has a "save_ordering" method
	 *
	 */
	makeSortable: function()
	{
		if (this.container)
		{
			var list = this.options.list;
			if (!list) list = this.options.container;
		
			// Init the sortable 
			this.sortables = new Sortables(list, {
				constrain: true,
				revert: true,
				handle: '.drag',
				referer: this,
				clone: true,
				opacity: 0.5,
				onComplete: function(item)
				{
					// Hides the current sorted element (correct a Mocha bug on hidding modal window)
					item.removeProperty('style');
					
					// Get the new order					
					var serialized = this.serialize(0, function (element, index) 
					{
						var rel = (element.getProperty('rel')).split(".");
						var id = rel[0];
						if (rel.length > 1) { id = rel[1]; }

						return id;
					});
					
					// Items sorting
					this.options.referer.sortItemList(serialized);
				}			
			});
		
			// Store the sortables in the container, for further access
			this.container.store('sortables', this.sortables);
		
			// Store the first ordering after picture list load
			this.container.store('sortableOrder', this.sortables.serialize(0,function (element, index) 
			{
				var rel = (element.getProperty('rel')).split(".");
				var id = rel[0];
				if (rel.length > 1) { id = rel[1]; }
				return id;
			}.bind(this)));
		}
	},

	
	/** 
	 * Items list ordering
	 * called on items sorting complete
	 * calls the XHR server ordering method
	 *
	 * @param	string	Media type. Can be 'picture', 'video', 'music', 'file'
	 * @param	string	new order as a string. coma separated
	 *
	 */
	sortItemList: function(serialized) 
	{
		var sortableOrder = this.container.retrieve('sortableOrder');

		// If current <> new ordering : Save it ! 
		if (sortableOrder.toString() != serialized.toString() ) 
		{
			// Store the new ordering
			this.container.store('sortableOrder', serialized);

			// Set the request URL
			var url = this.adminUrl + this.element + '/save_ordering';

			// If parent and parent ID are defined, send them to the controller through the URL
			if (this.parent_element && this.id_parent)
			{
				url += '/' + this.parent_element + '/' + this.id_parent
			}

			// Save the new ordering
			var myAjax = new Request.JSON(
			{
				url: url,
				method: 'post',
				data: 'order=' + serialized,
				onSuccess: function(responseJSON, responseText)
				{
					MUI.hideSpinner();

					// Get the update table and do the jobs
					if (responseJSON.update != null && responseJSON.update != '')
					{
						MUI.updateElements(responseJSON.update);
					}
					
					// Callbacks
					ION.execCallbacks(responseJSON.callback);

					// Success notification
					if (responseJSON && responseJSON.message_type)
					{
						ION.notification.delay(50, MUI, new Array(responseJSON.message_type, responseJSON.message));
					}
				},
				onFailure: this.failure.bind(this)
			}).post();
		}
	},


	/** 
	 * XHR failure
	 *
	 */
	failure: function(xhr)
	{
		ION.notification('error', xhr.responseText );

		// Hide the spinner
		MUI.hideSpinner();
	}
});


ION.ArticleManager = new Class({

	Extends: ION.ItemManager,

	initialize: function(options)
	{
		this.parent({
			'element': 'article',
			'container': options.container,
			'parent_element':'page',
			'id_parent': options.id_parent
		});
		
		
		// Init event on switch online / offline buttons
		this.initStatusEvents();
		
		// Init Article Unlink event
		this.initUnlinkEvents();
		
		// Makes items sortable
		this.makeSortable();
	},
	
	/**
	 * Init potential status buttons (switch online / offline)
	 *
	 */
	initStatusEvents: function()
	{
		$$('#' + this.options.container + ' .status').each(function(item,idx)
		{
			ION.initArticleStatusEvent(item);
		});
	},

	initUnlinkEvents: function()
	{
		$$('#' + this.options.container + ' .unlink').each(function(item,idx)
		{
			ION.initArticleUnlinkEvent(item);
		});
	}
});

ION.PageManager = new Class({

	Extends: ION.ItemManager,

	initialize: function(options)
	{
		this.parent({
			'element': 'page',
			'container': options.container
		});
		
		// Makes items sortable
		this.makeSortable();
	}
});

