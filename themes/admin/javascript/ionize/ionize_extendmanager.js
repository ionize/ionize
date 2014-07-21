/**
 * Extend Manager
 *
 */
ION.ExtendManager = new Class({

	Implements: [Events, Options],

	options: {
		wId:   'wExtend'          // Window ID
	},

	/**
	 *
	 * @param options
	 */
	initialize: function()
	{
		var self = this;
		var options = arguments[0];

		this.setOptions(options);

		this.baseUrl =		ION.baseUrl;
		this.adminUrl =		ION.adminUrl;
		this.themeUrl =		ION.themeUrl;

		// Extend Types
		this.extendTypes =  null;

		// Extends Instances parent
		this.parent =		null;
		this.id_parent =	null;

		// Extend definition parent
		this.id_field_parent = 0;

		// Extend context of usage
		this.context =		null;
		this.id_context =	null;

		this.w =            null;       // window
		this.wContainer =   null;       // Items container (in window)
		this.destination =  null;       // Destination container (ID) : TabSwapper only for the moment
		this.destinationTitle = null    // Destination container title : Tab title only for the moment

		// Data posted by getWindowExtendListContent()
		this.post = null;

		this.click_timer = null;

		ION.JSON(
			ION.adminUrl + 'extend_field/get_extend_types/json',
			{},
			{
				onSuccess: function(json)
				{
					self.extendTypes = json;
					if (options) self.init(options);
					self.fireEvent('onLoaded', self);
				}
			}
		);

		// Delete Event, fired by extend/field.php
		Events.subscribe('/extend/delete', self.onExtendDelete.bind(this));

		return this;
	},

	/**
	 * Initialize basis properties
	 *
	 * @param options
	 */
	init: function(opt)
	{
		var self = this,
			opt = typeOf(opt) != 'null' ? opt : {};

		this.post = null;
		if (opt.context) this.context = opt.context;
		if (opt.id_context) this.id_context = opt.id_context;
		if (opt.parent) this.parent = opt.parent;

		this.parent = typeOf(opt.parent) != 'null' ? opt.parent : null;
		this.id_parent = typeOf(opt.id_parent) != 'null' ? opt.id_parent : null;
		if (opt.id_field_parent) this.id_field_parent = (typeOf(opt.id_field_parent) != 'null')? opt.id_field_parent : 0;

		// Destination DOM HTML element (Extend contexts container)
		if (opt.destination) this.destination = (typeOf(opt.destination) != 'null')? opt.destination : null;
		if (opt.destinationTitle) this.destinationTitle = (typeOf(opt.destinationTitle) != 'null')? opt.destinationTitle : null;

		// Data
		self.post = typeOf(opt.conditions) != 'null' ? opt.conditions : null;

		if (opt.onLoad)
			opt.onLoad(this);

		this.setWindowInfo();
	},

	getExtendTypes: function()
	{
		return this.extendTypes;
	},

	/**
	 * Opens New Extend Window
	 *
	 * @param Object 	{
	 * 						parent: 'contact',
	 * 						id_parent: null,
	 * 						onSuccess: function(json, this){}
	 * 					}
	 *
	 */
	createExtend: function()
	{
		var self = this;

		// options
		var opt = typeOf(arguments[0]) != 'null' ? arguments[0] : {};

		// post data
		var data = {
			parent : (opt && opt.parent) ? opt.parent : this.parent,
			id_parent : (opt && opt.id_parent) ? opt.id_parent : this.id_parent
		};

		// Context
		var context = opt.context ? opt.context : this.context,
			id_context = opt.id_context ? opt.id_context : this.id_context;

		if (context != null && id_context != null)
		{
			Object.append(data, {
				'context':context,
				'id_context':id_context
			});
		}

		// On Success function
		var onSuccess = typeOf(opt.onSuccess) == 'function' ?
						opt.onSuccess :
						function(json)
						{
							if (json.message_type == 'success')
								self.getWindowExtendListContent();
						};

		var options = {
			width:500,
			height:400,
			onSuccess: onSuccess
		};

		Object.append(options, opt);

		ION.formWindow(
			'extendfield',
			'extendfieldForm',
			'ionize_title_extend_fields',
			ION.adminUrl + 'extend_field/create',
			options,
			data
		);
	},


	/**
	 * Extend Definition Edition
	 * Opens Extend field definition Editor window
	 *
	 * @param id
	 */
	editExtend: function(id)
	{
		var self = this;

		var data = {'id_extend_field': id};

		if (this.parent) data['parent'] = this.parent;

		// Add context if set
		if (this.context) data['context'] = this.context;
		if (this.id_context) data['id_context'] = this.id_context;

		// options
		var opt = arguments[1];

		var options = {
			width:500,
			height:400,
			// Alternative to use of Events.publish('/extend/edit/after')
			onSuccess: function(json)
			{
				if (json.message_type == 'success')
					self.getWindowExtendListContent();
			}
		};

		if (typeOf(opt) == 'object') Object.append(options, opt);

		ION.formWindow(
			'extendfield' + id,
			'extendfieldForm' + id,
			'ionize_title_extend_field',
			ION.adminUrl + 'extend_field/edit',
			options,
			data
		);
	},


	/**
	 * Refreshes the extends list after delete
	 *
	 * @param id_extend
	 */
	onExtendDelete: function(id_extend)
	{
		if (this.w != null)
		{
			this.getWindowExtendListContent();
		}
	},

	/**
	 * Adds info to the window
	 *
	 */
	setWindowInfo: function()
	{
		if (this.w != null)
		{
			this.w.setInfo(String.capitalize(this.context) + ' : ' + this.id_context);
		}
	},


	/**
	 * Opens window of all items,
	 * grouped by item definitions
	 *
	 * @param options
	 */
	openListWindow: function()
	{
		if (typeOf(arguments[0]) != 'null') this.init(arguments[0]);

		// Init Window container
		if (this.w == null)
		{
			this.initListWindow();
			// this.setWindowInfo();
		}

		this.getWindowExtendListContent();
	},


	/**
	 * Creates the Items List Window
	 * Main Window
	 *
	 */
	initListWindow: function()
	{
		var self = this;

		// Mocha Window
		this.w = new MUI.Window({
			id: this.options.wId,
			title: Lang.get('ionize_title_extend_fields'),
			container: document.body,
			cssClass: 'panelAlt',
			content:{},
			width: 310,
			height: 340,
			y: 40,
			padding: { top: 12, right: 12, bottom: 10, left: 12 },
			maximizable: false,
			contentBgColor: '#fff',
			onClose: function()
			{
				self.w = null;
			},
			onDrawEnd: function(w)
			{
				var newLeft = window.getSize().x - (w.el.windowEl.offsetWidth + 50);
				w.el.windowEl.setStyles({left:newLeft});
				w.el.contentWrapper.addClass('panelAlt');
			}
		});

		// Build the Header
		var container = $(this.options.wId + '_content');

		var wTitle = this.parent ? String.capitalize(this.parent) : Lang.get('ionize_label_extends');

		var h2 = new Element('h2', {
			'class': 'main extends',
			'text' : wTitle
		}).inject(container);

		var subtitle = new Element('div', {'class': 'main subtitle'}).inject(container, 'bottom');
		var p = new Element('p').inject(subtitle);

		var span = new Element('span', {'class': 'lite', 'text': Lang.get('module_crm_subtitle_extends')}).inject(p);

		// Buttons : Create
		var button = new Element('a', {'class':'button light ml-10', text:'Create New Extend'}).inject(subtitle);
		new Element('i', {'class':'icon-plus'}).inject(button);

		button.addEvent('click', function(){
			self.createExtend();
		});

		// Content container (list of elements)
		this.wContainer = new Element('div', {'id':'extends'}).inject(container, 'bottom');
	},


	/**
	 * Gets the Window Items List
	 * (Window of extends selection)
	 *
	 * By context and id_context if exists
	 *
	 */
	getWindowExtendListContent:function()
	{
		if (this.wContainer)
		{
			var self = this;
			var url = ION.adminUrl + 'extend_field/get_extend_fields';

			var data = {
				mode: 'json',
				order_by: 'label ASC'
			};

			// Filter on Parent, Context and Context ID
			if (this.parent) data['parent'] = this.parent;

			// Add context and id_context
			if (this.context && this.id_context)
			{
				url = ION.adminUrl + 'extend_field/get_context_list/json';
				data = Object.append(data, {
					context: this.context,
					id_context: this.id_context
				});
			}

			if (this.post != null)
				Object.append(data, this.post);

			// Get definitions, with items linked to them
			ION.JSON(
				url,
				data,
				{
					onSuccess: function(responseJSON)
					{
						self.buildWindowExtendList(responseJSON);
					}
				}
			);
		}
	},


	/**
	 * Build the Extend list HTML
	 * (Displayed in Extends select window)
	 *
	 * @param json
	 */
	buildWindowExtendList: function(json)
	{
		var self = this;

		if (this.wContainer)
		{
			this.wContainer.empty();

			if (json.length > 0)
			{
				// List of one defined parent
				if (this.parent)
				{
					var ul = self._getWindowExtendList(json);
					ul.inject(this.wContainer);
				}
				// Group extends by parent type
				else
				{
					var parents = this._groupExtendByParents(json);

					// Each Parent Type
					Object.each(parents, function(parent, name)
					{
						// Title : Toggler
						var h = new Element('h3', {
							'class': 'toggler toggler-extends',
							'text': String.capitalize(name)
						}).inject(self.wContainer);

						var tw = new Element('div', {'class':'element element-extends'}).inject(self.wContainer);

						var ul = self._getWindowExtendList(parent);
						ul.inject(tw);
					});

					ION.initAccordion(
						'.toggler.toggler-extends',
						'.element.element-extends',
						true,
						'wExtendsAccordion'
					);
				}
			}
		}
	},


	/**
	 * Build the Extend List
	 * Displayed in Extend Fields Window
	 *
	 * @param arr
	 * @returns {HTMLElement}
	 * @private
	 */
	_getWindowExtendList: function(arr)
	{
		var self = this;
		var ul = new Element('ul', {'class':'list pb15 pl15'});

		Object.each(arr, function(extend)
		{
			var li = new Element('li', {
				'class': 'list pointer',
				'data-id': extend.id_extend_field
			}).inject(ul);

			// Store the extend data
			li.store('data', extend);

			// Title
			var title = new Element('a', {
				'class': 'left title unselectable',
				'text': extend.label
			}).inject(li);

			// Edit icon
			var edit = new Element('a', {'class':'icon edit right'}).inject(li);
			edit.addEvent('click', function(){ self.editExtend(extend.id_extend_field); });

			// Type
			var type_name = new Element('span', {
				'class':'right lite',
				text:extend.type_name
			}).inject(li);

			// Double click on item : Link To Context
			/*
			li.addEvents({
				'click': function(e)
				{
					clearTimeout(self.click_timer);
					self.click_timer = self.relayItemListClick.delay(700, self, [e, this, 1]);
				},
				'dblclick': function(e)
				{
					clearTimeout(self.click_timer);
					self.click_timer = self.relayItemListClick.delay(0, self, [e, this, 2]);
				}
			});
			*/

			/*
			ION.addDragDrop(
				li,
				'#mainPanel_pad.pad', 	                // Droppables class
				function(element, droppable, event)
				{
					self.linkToContext(element.getProperty('data-id'));
				}
			);
			*/
			// Drag'n'drop on '.dropExtend' classes.
			// Drp method must be linked to the droppable area, which will know what to do.
			ION.addDragDrop(li,	'.dropExtend');
		});

		return ul;
	},


	linkToContext: function(id_extend_field)
	{
		var self = this;

		// Get definitions, with items linked to them
		ION.JSON(
			ION.adminUrl + 'extend_field/link_to_context',
			{
				id_extend_field: id_extend_field,
				context: this.context,
				id_context: this.id_context
			},
			{
				onSuccess: function()
				{
					self.getContextExtendList();
				}
			}
		);
	},


	unlinkFromContext: function(id_extend_field)
	{
		var self = this;

		// Get definitions, with items linked to them
		ION.JSON(
			ION.adminUrl + 'extend_field/unlink_from_context',
			{
				id_extend_field: id_extend_field,
				context: this.context,
				id_context: this.id_context
			},
			{
				onSuccess: function()
				{
					self.getContextExtendList();
				}
			}
		);
	},


	/**
	 * List of Extend Field definitions
	 * linked to one parent context
	 *
	 * @param options
	 */
	getContextExtendList: function()
	{
		var options = arguments[0];

		// Only do it if the context is set
		if (this.context)
		{
			var self = this;

			ION.JSON(
				ION.adminUrl + 'extend_field/get_context_list/json',
				{
					context: this.context,
					id_context: this.id_context,
					parent: this.parent
				},
				{
					onSuccess: function(json)
					{
						if (options && options.onSuccess)
						{
							options.onSuccess(json);
						}
						else
						{
							if(json.length > 0)
							{
								self.buildContextList(json);
							}
							else
							{
								self.cleanContextList();
							}
						}
					}
				}
			);
		}
	},


	/**
	 * Removes parent context's tabs in no extends are linked to the
	 * current edited parent
	 *
	 */
	cleanContextList: function()
	{
		var parents = arguments[0];
		if ( ! parents) parents = [];

		var container = $(this.destination);

		if (container.hasClass('mainTabs'))
		{
			// Get back the instance
			var tabSwapper = container.retrieve('tabSwapper');

			if (typeOf(tabSwapper) != 'null')
			{
				var tabs = tabSwapper.getTabs();

				tabs.each(function(tab)
				{
					if (tab.hasClass('extend'))
					{
						var found = false;
						var parent = tab.getProperty('data-parent');

						Object.each(parents, function(item, name)
						{
							if (name == parent)
								found = true;
						});
						if ( ! found)
							tabSwapper.removeTabById(parent);
					}
				});
			}
		}
	},


	/**
	 * Build the context's parent extend list
	 * in the parent edition panel
	 *
	 * @param json
	 */
	buildContextList: function(json)
	{
		var self = this;

		var parents = this._groupExtendByParents(json);

		this.cleanContextList(parents);

		// Each Parent type
		Object.each(parents, function(parent, name)
		{
			var container = self.getContextExtendContainer(name);

			if (container)
			{
				container.empty();
				var _nb_items = 0;

				new ION.List({
					container: container,
					items: parent,
					sortable: true,
					sort: {
						handler: '.drag',
						id_key: 'id_extend_field',
						url: ION.adminUrl + 'extend_field/save_ordering'
					},
					elements:[
						// Sort
						{
							element: 'span',
							'class': 'icon drag left'
						},
						// Title
						{
							element: 'span',
							'class': 'unselectable',
							text: 'label'
						},
						// Unlink
						 /*
						{
							element: 'a',
							'class': 'icon unlink right',
							onClick: function(item)
							{
								console.log(item);
							}
						},
						*/
						// Delete
						{
							element: 'a',
							'class': 'icon delete right',
							onClick: function(item)
							{
								console.log(item);
							}
						},
						// Type name
						{
							element: 'span',
							'class': 'right lite',
							text: 'type_name'
						}
					],
					onItemDraw: function()
					{
						_nb_items += 1;
					}
				});

				self.setContextNbItemsInfo(name, _nb_items);
			}
		});
	},


	_groupExtendByParents: function(json)
	{
		var r = [];

		Object.each(json, function(extend)
		{
			if ( ! extend.parent) extend.parent = 'global';

			if ( ! r[extend.parent]) r[extend.parent] = [];

			r[extend.parent].push(extend);
		});

		return r;
	},


	/**
	 * Get one context Extends Fields (instances)
	 * linked to one context
	 * and to one parent
	 *
	 * @param options	if 'onSucess' is set in options, this method will be called.
	 * 					{
	 * 						onSuccess: function(extends)
	 * 					}
	 */
	getContextInstances: function()
	{
		// Only do it if the context is set
		if (this.context)
		{
			var self = this,
				options = typeOf(arguments[0]) != 'null' ? arguments[0] : {};

			ION.JSON(
				ION.adminUrl + 'extend_field/get_context_instances_list/json',
				{
					context: this.context,
					id_context: this.id_context,
					parent: this.parent,
					id_parent: this.id_parent
				},
				{
					onSuccess: function(json)
					{
						if (options.onSuccess)
						{
							options.onSuccess(json);
						}
						else if(json.length > 0)
						{
							self.buildInstancesListContainer(json);
						}
					}
				}
			);
		}
	},


	/**
	 * Get Extend fileds definitions for one parent type.
	 *
	 * @param options	if 'onSucess' is set in options, this method will be called.
	 * 					{
	 * 						onSuccess: function(extends)
	 * 					}
	 */
	getParentDefinitions: function()
	{
		var options = typeOf(arguments[0]) != 'null' ? arguments[0] : {};

		if (this.parent)
		{
			ION.JSON(
				ION.adminUrl + 'extend_field/get_parent_list',
				{
					parent: this.parent
				},
				{
					onSuccess: function(json)
					{
						if(options.onSuccess)
							options.onSuccess(json);
					}
				}
			);
		}
	},

	/**
	 *
	 */
	getParentInstances: function()
	{
		if (this.parent)
		{
			var self = this,
				options = typeOf(arguments[0]) != 'null' ? arguments[0] : {};

			ION.JSON(
				ION.adminUrl + 'extend_field/get_instances_list/json',
				{
					parent: this.parent,
					id_parent: this.id_parent,
					id_field_parent: this.id_field_parent
				},
				{
					onSuccess: function(json)
					{
						if (options.onSuccess)
						{
							options.onSuccess(json);
						}
						else if(json.length > 0)
						{
							self.buildInstancesListContainer(json);
						}
					}
				}
			);
		}
	},


	/**
	 * Builds the list of Extend Fields Instances in the context
	 * of one parent
	 *
	 * @param json
	 *
	 */
	buildInstancesListContainer: function(json)
	{
		var self = this;

		// We're supposed to have one defined parent
		var container = this.getContextExtendContainer(this.parent);

		if (container)
			this.buildInstancesList(json, container);
	},


	buildInstancesList: function(json, container)
	{
		var self = this;

		var languages = Settings.get('languages');
		
		// 1. First pass : Non translated extends
		var fields = this._getInstances(json, 0);

		Array.each(fields, function(field)
		{
			// DOM Form field (Label + Field container)
			var formField = new ION.FormField({container: container, label: {text: field.label}}),
				ffc = formField.getContainer()
				;

			// Add Help : Description as Title
			if (field.description) formField.getLabel().set('title', field.description);

			// Get the Field (Only the field), and send it to the FormField container
			self.getExtendField(field, {container: ffc});
		});

		// 2. Second pass : Translated extends
		fields = this._getInstances(json, 1);
		if (fields.length > 0)
		{
			// Create Languages Tabs
			var tabId = this.buildInstancesLangTab(container);

			Array.each(fields, function(field)
			{
				Array.each(languages, function(lang)
				{
					var formField = new ION.FormField({	label: {text: field.label} }),
						ffc = formField.getContainer()
						;

					// Add Help : Description as Title
					if (field.description) formField.getLabel().set('title', field.description);

					var el = self.getExtendField(field, {container:ffc, lang:lang.lang});

					// Add the
					if (el != null)	self.addInstanceToLangTab(formField.getDomElement(), tabId, lang.lang);
				});
			});
		}

		// Init some magic : Datepickers, Editors, etc.
		this.initExtendFieldContainer(container);
	},


	/**
	 * Return Array of translated or not translated instances
	 *
	 * @param instances
	 * @param translated
	 * @returns {Array}
	 * @private
	 */
	_getInstances: function(instances, translated)
	{
		var result = [];

		Array.each(instances, function(extend)
		{
			// Only returns type which should be displayed
			if (extend.translated == translated && extend.display == '1')
				result.push(extend);
		});

		return result;
	},


	/**
	 * Builds one Extend Field field
	 * based on the extend
	 *
	 * 1 => Input
	 * 2 => Textarea
	 * 3 => Textarea + Editor,
	 * 4 => Checkbox,
	 * 5 => Radio,
	 * 6 => Select,
	 * 7 => Date & Time,
	 * 8 => Medias
	 * 100 => Numeric
	 * ...
	 *
	 * @param extend
	 * @param options	Object
	 * 					{
	 *						lang: '', 		// Lang code. Eg. 'fr'. If set, the extend is translated.
	 *						id: ''			// If set, will replace the auto-defined field ID
	 *						name: ''		// If set, will replace the auto-defined field name,
	 *						renderAs: ''	// If set, gives the ability to render one date as multiple
	 *					}
	 *
	 */
	getExtendField: function(extend, options)
	{
		var lang = 			options && options.lang ? options.lang : null,
			content =		options.lang ?
							extend['lang_data'][lang]['content'] :
							(extend.content == null ? extend.default_value : extend.content),
			input_name = 	options.name ?	options.name : 'cf_' + extend.id_extend_field,
			dom_type =		extend.html_element_type,
			dom_tag = 		extend.html_element,
			container =		options.container,
			cssClass =		typeOf(options['class']) != 'null' ? ' ' + options['class'] : '',
			renderAs = 		typeOf(options['renderAs']) != 'null' ? options['renderAs'] : null,
			validateClass = typeOf(options['validateClass']) != 'null' ? options['validateClass'] : null,
			// Produced field & label
			field = 		null
		;

		// Add the lang code to the extend name if needed
		if (options.lang)
			input_name += '_' + options.lang;

		//
		// Input, Textarea
		//
		if (['text','textarea','editor','email','number','tel'].contains(dom_type))
		{
			// Temporary disabling HTML5 type for number, tel, email
			// @todo: See how to make it compat. with Mootools validator
			var field_dom_type = dom_type;

			if (['email','number','tel'].contains(dom_type)) field_dom_type="text";

			field = new Element('div', {'class': 'relative'});

			var get_input_field = function(input_name, id, content)
			{
				var i = new Element(dom_tag, {
					type: field_dom_type,
					'class': extend.html_element_class + cssClass,
					name: input_name,
					id: input_name,
					value: content
				}).inject(field);

				// Validator
				if (validateClass != null) i.addClass(validateClass);
			};

			if ((dom_type == 'number') && (renderAs == 'multiple'))
			{
				var id = input_name;
				input_name += '[]';
				content = content.split(',');
				get_input_field(input_name, id+1, content[0]);
				get_input_field(input_name, id+2, content[1]);
			}
			else
				get_input_field(input_name, input_name, content);
		}

		//
		// Checkbox / Radio / Select
		//
		if (['checkbox','radio'].contains(dom_type))
		{
			field = new Element('div');

			content = content.split(',');

			var values = 		(extend.value).split('\n'),
				input_name = 	dom_type == 'checkbox' ? input_name +'[]' : input_name
			;

			Array.each(values, function(value, idx)
			{
				var val = value.split(':');

				if (typeOf(val[0]) != 'null' && typeOf(val[1]) != 'null')
				{
					var input = new Element(dom_tag, {
						type: dom_type,
						id: input_name + idx,
						name: input_name,
						value: val[0],
						'class': extend.html_element_class
					}).inject(field);

					if (content.contains(val[0])) input.setProperty('checked', 'checked');

					new Element('label', {
						'for': input_name + idx,
						text: val[1]
					}).inject(field);
				}
			});
		}

		//
		// Select
		//
		if (['select','select-multiple'].contains(dom_type))
		{
			if (typeOf(content) == 'null') content = '';
			
			content = content.split(',');

			var values = (extend.value).split('\n');

			field = new Element('select', {
				'class': extend.html_element_class + cssClass,
				id: input_name,
				name: input_name
			});

			if (dom_type == 'select-multiple') {
				field.setProperty('multiple', 'multiple');
				field.setProperty('name', input_name + '[]');
			}

			Array.each(values, function(value)
			{
				var val = value.split(':');

				if (typeOf(val[0]) != 'null' && typeOf(val[1]) != 'null')
				{
					var option = new Element('option', {
						value: val[0],
						text: val[1]
					}).inject(field);

					if (content.contains(val[0])) option.setProperty('selected', 'selected');
				}
			});
		}

		//
		// Date
		//
		if (['date','date-multiple'].contains(dom_type))
		{
			field = new Element('div', {'class': 'relative'});

			var get_date_field = function(input_name, id, content)
			{
				var date_container = new Element('div', {'class':'relative mr30 mb5'}).inject(field);

				new Element(dom_tag, {
					type: dom_type,
					'class': extend.html_element_class + cssClass,
					name: input_name,
					id: id,
					value: content
				}).inject(date_container);

				new Element('a', {'class': 'icon clearfield date', 'data-id': id}).inject(date_container);
			};

			// Multiple Dates
			if ((dom_type == 'date-multiple') || (renderAs == 'multiple'))
			{
				var id = input_name;
				input_name += '[]';
				content = content.split(',');
				get_date_field(input_name, id+1, content[0]);
				get_date_field(input_name, id+2, content[1]);
			}
			// Simple Date
			else
			{
				get_date_field(input_name, input_name, content);
			}
		}

		//
		// Medias
		//
		if (dom_type == 'media')
		{
			field = new Element('div', {'class':'clearfix'});

			// Can be linked : The parent exists
			if (this.id_parent)
			{
				// Extend Media Manager
				var emOptions = {
					container: 		field,
					parent: 		extend.parent,
					id_parent: 		this.id_parent,
					id_extend: 		extend.id_extend_field,
					extend_label: 	extend.label,
					lang: 			lang
				};

				// ExtendMediaManager
				var extendMediaManager = new ION.ExtendMediaManager(emOptions);

				// Load existing media list
				extendMediaManager.loadList();
			}
			else
			{
				new Element('i', {
					'class': 'lite',
					text: Lang.get('ionize_message_please_save_first')
				}).inject(field)
			}
		}

		//
		// Internal Link
		//
		if (dom_type == 'link')
		{
			field = new Element('div');

			if (this.id_parent)
			{
				// ExtendLinkManager
				var extendLinkManager = new ION.ExtendLinkManager({
					container: field,
					id_extend: extend.id_extend_field,
					parent: extend.parent,
					id_parent: this.id_parent,
					lang: lang
				});
				extendLinkManager.loadList();
			}
			else
			{
				new Element('i', {
					'class': 'lite',
					text: Lang.get('ionize_message_please_save_first')
				}).inject(field)
			}
		}


		//
		// Pattern
		// Not compatible with Mootools Validator.
		// @todo : Needs to be checked
		//
		// if (extend.html_element_pattern) field.setProperty('pattern', extend.html_element_pattern);

		// Inject
		if (container && field)
		{
			field.inject(container);

			//
			// Validation
			//
			if (extend.validate)
			{
				var f = field;
				if ( ! ['input','select','textarea'].contains(f.get('tag')))
					f = field.getElement(dom_tag);

				f.addClass('validate-' + extend.validate);

				// Add the validator but does not validate : The submit button has to do !
				var form = f.getParent('form');
				if (form && ! form.retrieve('validator'))
				{
					var validator = new Form.Validator.Inline(form, {
						errorPrefix: '',
						showError: function(element) {
							element.show();
						}
					});

					// Name of the validator : Standard ionize !
					form.store('validator', validator);
				}

				// label.setProperty('title', Locale.get('FormValidator.' + extend.validate));
			}
		}


		return field;
	},


	/**
	 * Inits external lib on Extend Field instances of the container :
	 * Datepickers, Editors, etc.
	 *
	 * @notice : The container must have one ID set.
	 *
	 * @param container
	 */
	initExtendFieldContainer: function(container)
	{
		// Autogrow for potential textareas
		ION.initFormAutoGrow(container);

		// TinyMCE
		var containerId = container.getProperty('id');
		ION.initTinyEditors(null, '#' + containerId + ' .smallTinyTextarea', 'small', {'height':80});

		// Date & Time
		ION.initDatepicker(Lang.get('dateformat_backend'));
		ION.initClearField('#' + containerId);
	},



	buildInstancesLangTab: function(container)
	{
		var idLangTab = 'langTab' + this.parent + this.id_parent;
		var idLangTabContent = idLangTab + 'Content';

		var languages = Settings.get('languages');

		var divTab = new Element('div', {id:idLangTab, 'class':'mainTabs clear mt20'}).inject(container);
		var divContent = new Element('div', {id:idLangTabContent}).inject(container);

		var ul = new Element('ul', {'class':' tab-menu'}).inject(divTab);

		Array.each(languages, function(lang)
		{
			// Tab
			var li = new Element('li').inject(ul);
			var a = new Element('a', {text:lang.name}).inject(li);

			// Content Div
			new Element('div', {'class':'tabcontent', 'data-lang':lang.lang}).inject(divContent);
		});

		// Tabs init
		new TabSwapper({
			tabsContainer: idLangTab,
			sectionsContainer: idLangTabContent,
			selectedClass: 'selected',
			deselectedClass: '',
			tabs: 'li',
			clickers: 'li a',
			sections: 'div.tabcontent'
		});


		return idLangTab;
	},

	addInstanceToLangTab: function(instance, idLangTab, lang_code)
	{
		var idLangTabContent = idLangTab + 'Content';

		var divs = $(idLangTabContent).getElements('.tabcontent');

		Array.each(divs, function(div)
		{
			if (div.getProperty('data-lang') == lang_code)
				instance.inject(div);
		})
	},


	/**
	 * Analyse the destination and try to find out
	 * one existing Extend List container
	 * Build it if mandatory
	 *
	 */
	getContextExtendContainer: function(parent)
	{
		var container = $(this.destination),
			section = null;

		// No container : Stop here
		if ( ! container) return null;

		// Case of tabs
		if (container.hasClass('mainTabs'))
		{
			// Get back the instance
			var tabSwapper = container.retrieve('tabSwapper');

			if (typeOf(tabSwapper) != 'null')
			{
				section = tabSwapper.getSection('.' + parent);

				// Build one tabswapper section
				if ( ! section)
				{
					var title = (this.destinationTitle != null) ? this.destinationTitle : Lang.get('ionize_label_extends') + ' ' + String.capitalize(parent);

					section = tabSwapper.addNewTab(
						title,
						parent,
						{
							'class': 'extend',       // be able to identify it as extend tab
							'data-parent': parent
						}
					);
					section.addClass('mt20');
					section.addClass('p10');

					// Could be an unique string
					section.setProperty('id', 'tabSwapper' + parent + this.id_parent);
				}
			}
		}
		else
		{
			section = container;
		}

		return section;
	},

	/**
	 * Returns type from type ID
	 *
	 * @param id_type
	 * @returns {*}
	 * @private
	 */
	_getItemTypeFromId: function(id_type)
	{
		var type = null;

		Array.each(this.extendTypes, function(item)
		{
			if (item.id_extend_field_type == id_type)
			{
				type = item;
			}
		});
		return type;
	},


	/**
	 * Set tab number of items
	 * @param name
	 * @param nb_items
	 */
	setContextNbItemsInfo: function(name, nb_items)
	{
		var container = $(this.destination);

		// Set number of extends to tab
		// Case of tabs
		if (container.hasClass('mainTabs'))
		{
			// Get back the instance
			var tabSwapper = container.retrieve('tabSwapper');

			tabSwapper.setTabInfo('#tab' + name, nb_items);
		}
	},

	/**
	 * Click on one item in the Extends List window
	 *
	 * @param e
	 * @param element
	 * @param clicks
	 */
	relayItemListClick: function(e, element, clicks)
	{
		// IE7 / IE8 event problem
		if( Browser.name!='ie') if (e) e.stop();

		if (clicks === 2)
		{
			var id = element.getProperty('data-id');
			this.linkToContext(id);
		}
		else{}
	}
});
