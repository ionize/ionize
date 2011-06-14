/**
	Ionize Item Manager
	
	Manage a list of elements : 
	- Make them sortable
	- Init the delete icon event
	- Init the status (online / offline) icon event

	@example
 	var myItemsManager = new ION.ItemManager(
	{
		container: 'myUL',
		'controller':'module/my_module/controller_name',
		'sortable': true,
		'confirmDeleteMessage': Lang.get('my_module_confirm_element_delete')
	});

 
 */
ION.ItemManager = new Class({

	Implements: [Events, Options],
	
	options: 
	{
		'controller': false,
		'confirmDelete': true,
		'confirmDeleteMessage': Lang.get('ionize_confirm_element_delete'),
		'sortable': false
	},
	
	/**
	 * @constructor
	 * @param	Object	Options object
	 *
	 */
	initialize: function(options)
	{
		this.adminUrl = ION.adminUrl;

		// Options
		this.setOptions(options);
		this.container = $(this.options.container);
		if (this.options.controller == false) this.options.controller = this.options.element;
		
		// Set parent and id_parent (for ordering)
		if (options.parent_element && options.id_parent && options.parent_element !='')
		{
			this.parent_element = options.parent_element;
			this.id_parent = options.id_parent;
		}

		this.init();
	},

	init: function()
	{
		this.initDeleteEvent();
		this.initStatusEvents();

		if (this.options.sortable == true)
		{
			this.makeSortable();
		}
	},

	/**
	 * Adds the delete Event on each .delete anchor in the list
	 *
	 */
	initDeleteEvent: function()
	{
		var self = this;
 		var url = this.adminUrl + this.options.controller + '/delete/';
		
		$$('#' + this.options.container + ' .delete').each(function(item)
		{
			ION.initRequestEvent(item, url + item.getProperty('rel'), {}, {'confirm': self.options.confirmDelete, 'message': self.options.confirmDeleteMessage})
		});
	},

	/**
	 * Adds the status Event on each .status anchor in the list
	 *
	 */
	initStatusEvents: function()
	{

		var url = this.adminUrl + this.options.controller;
		
		$$('#' + this.options.container + ' .status').each(function(item, idx)
		{
			ION.initRequestEvent(item, url + '/switch_online/' + item.getProperty('rel'));
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
			
			var self = this;

			// Init the sortable 
			this.sortables = new Sortables(list, {
				revert: true,
				handle: '.drag',
				clone: true,
				constrain: true,
				opacity: 0.5,
				onComplete: function(item, clone)
				{
					// Hides the current sorted element (correct a Mocha bug on hidding modal window)
					item.removeProperty('style');

					// Get the new order					
					var serialized = this.serialize(0, function (element, index) 
					{
						// Check for the not removed clone
						if (element.id != '')
						{
							var rel = (element.getProperty('rel')).split(".");
							var id = rel[0];
							if (rel.length > 1) { id = rel[1]; }
							return id;
						}
						return;
					});

					// Items sorting
					self.sortItemList(serialized);
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

		// Remove "undefined" from serialized. Undefined comes from the clone, which isn't removed before serialize.
		var serie = new Array();
		serialized.each(function(item)
		{
			if (typeOf(item) != 'null')
				serie.push(item);
		});

		// If current <> new ordering : Save it ! 
		if (sortableOrder.toString() != serie.toString() ) 
		{
			// Store the new ordering
			this.container.store('sortableOrder', serie);

			// Set the request URL
			var url = this.adminUrl + this.options.controller + '/save_ordering';

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
				data: 'order=' + serie,
				onSuccess: function(responseJSON, responseText)
				{
					MUI.hideSpinner();

					// Get the update table and do the jobs
					if (responseJSON.update != null && responseJSON.update != '')
					{
						ION.updateElements(responseJSON.update);
					}
					
					// Callbacks
					if (typeOf(responseJSON.callback) != 'null')
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
			'id_parent': options.id_parent,
			'sortable': true
		});
	},

	init: function()
	{
		this.initDeleteEvent();
		this.initStatusEvents();

		if (this.options.sortable == true)
		{
			this.makeSortable();
		}
		
		this.initUnlinkEvents();
	},

	
	/**
	 * Init potential status buttons (switch online / offline)
	 *
	 */
	initStatusEvents: function()
	{
		var url = this.adminUrl + 'article/switch_online/';
		
		$$('#' + this.options.container + ' .status').each(function(item, idx)
		{
			var rel = (item.getProperty('rel')).split(".");
			ION.initRequestEvent(item, url + rel[0] + '/' + rel[1]);
		});
	},

	initUnlinkEvents: function()
	{
		var url = this.adminUrl + 'article/unlink/';

		$$('#' + this.options.container + ' .unlink').each(function(item,idx)
		{
			var rel = (item.getProperty('rel')).split(".");
			ION.initRequestEvent(item, url + rel[0] + '/' + rel[1], {}, {message: Lang.get('ionize_confirm_article_page_unlink')});
		});
	}
});


