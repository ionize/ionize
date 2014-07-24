ION.append({

	grayPanels:Array('dashboard'),


	contentUpdate:function(options)
	{
		// @todo :
		// Replace by (ION.User.isLogged()
		//
		//
		var user = ION.User.getUser();

		if (typeOf(user) != 'null')
		{
			$('mainPanel').removeClass('bg-gray');
			if (ION.grayPanels.contains(options.url) == true)
				$('mainPanel').addClass('bg-gray');

			$('mainPanel').getElements('iframe').each(function(el){el.destroy()});

			options.method = 'post';
			options.url = admin_url + ION.cleanUrl(options.url);
			MUI.Content.update(options);
		}
		else
		{
			ION.reload();
		}
	},


	/**
	 * Updates the mainPanel toolbox
	 *
	 * @param toolbox_url       Name of the toolbox view to load.
	 *                          Must be located in the themes/admin/views/toolboxes folder
	 * @param onContentLoaded   Function to execute when the toolbox is loaded.
	 * @param data              Additional data
	 *
	 */
	initToolbox: function(toolbox_url, onContentLoaded, data)
	{
		// Creates the header toolbox if it doesn't exists
		if ( ! $('mainPanel_headerToolbox')) {
			this.panelHeaderToolboxEl = new Element('div', {
				'id': 'mainPanel_headerToolbox',
				'class': 'buttonbar'
			}).inject($('mainPanel_header'));
		}
	
		if (typeOf(toolbox_url) != 'null')
		{
			cb = '';
			if (onContentLoaded)
				cb = onContentLoaded;
		
			MUI.Content.update({
				element: 'mainPanel_headerToolbox',
				url: admin_url + 'desktop/get/toolboxes/' + toolbox_url,
				method: 'post',
				data: data,
				onLoaded: cb
			});
		}
		else
		{
			$('mainPanel_headerToolbox').empty();

			if (typeOf(onContentLoaded) == 'function')
				onContentLoaded($('mainPanel_headerToolbox'));
		}
	},


	getToolbox: function(toolbox_url, onContentLoaded, data)
	{
		// Creates the header toolbox if it doesn't exists
		if ( ! $('mainPanel_headerToolbox')) {
			this.panelHeaderToolboxEl = new Element('div', {
				'id': 'mainPanel_headerToolbox',
				'class': 'buttonbar'
			}).inject($('mainPanel_header'));
		}

		if (toolbox_url)
		{
			cb = '';
			if (onContentLoaded)
				cb = onContentLoaded;

			MUI.Content.update({
				element: 'mainPanel_headerToolbox',
				url: ION.adminUrl + 'desktop/get/' + toolbox_url,
				method: 'post',
				data: data,
				onLoaded: cb
			});
		}
		else
		{
			$('mainPanel_headerToolbox').empty();
		}
	},


	/**
	 * Init a module toolbox
	 *
	 * @param module        module name
	 * @param toolbox_url   toolbox_url for this module
	 */
	initModuleToolbox: function(module, toolbox_url)
	{

		// Creates the header toolbox if it doesn't exists
		if ( ! $('mainPanel_headerToolbox')) {
			this.panelHeaderToolboxEl = new Element('div', {
				'id': 'mainPanel_headerToolbox',
				'class': 'panel-header-toolbox'
			}).inject($('mainPanel_header'));
		}
	
		if (toolbox_url)
		{
			MUI.Content.update({
				'element': $('mainPanel_headerToolbox'),
				'url': admin_url + 'module/' + module + '/' +  module + '/get/admin/toolboxes/' + toolbox_url
			});
		}
		else
		{
			$('mainPanel_headerToolbox').empty();
		}
	
	},


	/**
	 * Initialize the Edit mode
	 * Hides the selectors if the cookie is set to true
	 *
	 * @param button_id       HTML DOM ID of the switcher button
	 * @param element       'article', 'page', 'media', etc.
	 * @param selectors     HTML Dom Elements selectors to show / hide
	 *
	 */
	initEditMode: function(button_id, element, selectors)
	{
		var button = $(button_id);
		var cookieName = 'editmode-' + element;

		if (button)
		{
			button.store('selectors', selectors);

			button.addEvents({
				'click' : function(e)
				{
					e.stop();
					if (Cookie.read(cookieName))
						activated = Cookie.read(cookieName);

					if (activated == 'false')
						this.fireEvent('activate');
					else
						this.fireEvent('deactivate');
				},
				'activate': function(e)
				{
					$$(selectors).hide();
					$('toggleHeaderButton').fireEvent('hide');
					$('sideColumnSwitcher').fireEvent('hide');
					Cookie.write(cookieName, 'true');
					ION.setButtonLabel(button, Lang.get('ionize_button_full_mode'), 'icon-edit_article');
				},
				'deactivate': function(e)
				{
					$$(selectors).show();
					$('toggleHeaderButton').fireEvent('show');
					Cookie.write(cookieName, 'false');
					ION.setButtonLabel(button, Lang.get('ionize_button_edit_mode'), 'icon-edit_article');
				}
			});

			// Init
			var activated = Cookie.read(cookieName);

			if (typeOf(activated) != 'null' && activated == 'true')
				button.fireEvent('activate');
			else
				button.fireEvent('deactivate');
		}
	},


	/**
	 * Creates Accordion
	 * @param togglers
	 * @param elements
	 * @param openAtStart
	 * @param cookieName
	 * @return {Fx.Accordion}
	 *
	 */
	initAccordion: function(togglers, elements, openAtStart, cookieName) 
	{
		// Hack IE 7 : No Accordion
		if (Browser.name=='ie' == true && Browser.version < 8)
		{
			return false;
		}
		var cookieDays = 999;
		var disp = (typeOf(openAtStart) != 'null') ? 0 : -1;

		if (cookieName)
			disp = [Cookie.read(cookieName), disp].pick();
			
		var acc = new Fx.Accordion(togglers, elements, {
			display: disp,
			opacity: false,
			alwaysHide: true,
			initialDisplayFx: false,
			onActive: function(toggler, element){
				toggler.addClass('expand');
				Cookie.write(cookieName, this.togglers.indexOf(toggler), {duration:this.options.cookieDays});
			},
			onBackground: function(toggler, element){
				if (typeOf(toggler) != 'null')
					toggler.removeClass('expand');
			},
			duration:'short'
		});
		
		return acc;
	},

	
	/**
	 * SideColumn open / close
	 *
	 * @param button_id       HTML DOM ID of the switcher button
	 *
	 */
	initSideColumn: function(button_id)
	{
		var button = $(button_id);
		var cookieName = 'sidecolumn';

		if (typeOf(button) != 'null')
		{
			var column = MUI.get('splitPanel_sideColumn');

			if (column)
			{
				button.store('column', column);

				button.addEvents({
					'click' : function(e)
					{
						e.stop();
						if (Cookie.read(cookieName))
							opened = (Cookie.read(cookieName));

						if (opened == 'false')
							this.fireEvent('show');
						else
							this.fireEvent('hide');
					},
					'show': function(e)
					{
						ION.setButtonLabel(button, Lang.get('ionize_label_hide_options'), 'icon-options');
						var column = this.retrieve('column');
						if (column.isCollapsed == true)
						{
							column.expand();
							Cookie.write(cookieName, 'true');
						}
					},
					'hide': function(e)
					{
						ION.setButtonLabel(button, Lang.get('ionize_label_show_options'), 'icon-options');
						var column = this.retrieve('column');
						if (column.isCollapsed == false)
						{
							column.collapse();
							Cookie.write(cookieName, 'false');
						}
					}
				});

				// Init
				var opened = Cookie.read(cookieName);

				if (typeOf(opened) != 'null' && opened == 'true')
					button.fireEvent('show');
				else
					button.fireEvent('hide');
			}
		}
		else
		{
			console.log('initSideColumn ERROR : #sideColumnSwitcher button not found in toolbox');
		}
	},

	initFormAutoGrow: function(parent)
	{
		var elements = null;

		if (typeOf(parent) == 'string')
			elements = $$('#' + parent + ' .autogrow');
		else if (typeOf(parent) == 'object')
			elements = parent.getElements('.autogrow');
		else
			elements = $$('.autogrow');

		if (elements != null)
		{
			Array.each(elements, function(item){
			new Form.AutoGrow(item, {
				minHeightFactor: 1
			});
		});
		}
	},


	setButtonLabel: function(button, label, icon)
	{
		button.set('html', '<i class="' + icon + '"></i>' + label);
	},

	/**
	 * Updates multiple elements
	 *
	 * @param	array	Array of elements to update. Array('element_id' => 'url_to_call')
	 *
	 */
	updateElements: function (elements)
	{
		elements.each(function(options, key)
		{
			ION.updateElement(options);
		});
	},

	emptyElement: function (element)
	{
		var tag = $(element).get('tag');

		if (tag == 'textarea' || tag == 'input')
			$(element).set('value', null);
		else
			$(element).empty();
	},

	/**
	 * Updates one / several DOM elements
	 * based on one HTML Request result
	 *
	 * @param	Object	Options
	 *					'url' : URL of the controller to call
	 *					'element' : ID, selectors of the DOM element(s) to update
	 *
	 */
	updateElement: function (options)
	{
		// Cleans URLs
		options.url = admin_url + ION.cleanUrl(options.url);
			
		// If the panel doesn't exists, try to update directly one DomHTMLElement
		if ( ! MUI.get(options.element) )
		{
			var elements = (options.element).split(',');
			elements.each(function(item, idx){
				item = item.trim();
				var firstChar = item.substring(0,1);
				if (firstChar != '.' && firstChar != '#') item = '#' + item;
				elements[idx] = item;
			});
			elements = elements.join(',');
			elements = $$(elements);

			if (options.element)
				options.update = options.element;

			ION.HTML(options.url, {}, options);

			/*
			new Request.HTML({
				'url': options.url,
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
				{
					elements.each(function(element){
						$(element).set('html', responseHTML);
						// Browser.exec(responseJavaScript);
					});
				}
			}).send()
			*/
		}
		else
		{
			// Update options.element to be the DOM object
			options.element = $(options.element);
			
			// Update the Mocha UI panel with Core.updateContent() method
			MUI.Content.update(options);
		}
	},

	
	/**
	 * Init one list toggler
	 * Used by translation view
	 *
	 */	
	initListToggler: function(toggler, child)
	{
		if (typeOf(child) == 'element')
		{
			toggler.fx = new Fx.Slide(child, {
			    mode: 'vertical',
			    duration: 200
			});
			toggler.fx.hide();
	
			toggler.addEvent('click', function()
			{
				this.fx.toggle();
				this.toggleClass('expand');
			});
		}
	},
	
	/**
	 * Inits the datepickers
	 * Originally using the Monkey Physics Datepicker.
	 * Currently using this one : abidibo / mootools-datepicker
	 *
	 * @param	String		PHP Date format
	 *
	 */
	initDatepicker: function(dateFormat, options)
	{
		if (ION.datePicker)
		{
			ION.datePicker.close();
		}
		else
		{
		if (typeOf(dateFormat) == 'null') dateFormat = '%d.%m.%Y';
			var date_format = (dateFormat).replace(/%/g, '');

			if (typeOf(options) == 'null') options = {};

			var oTimePicker = (options.timePicker) ? options.timePicker : false;
			var oInputFormat = (oTimePicker == true) ? date_format + ' H:i:s' : date_format;
			var oOutputFormat = (oTimePicker == true) ? 'Y-m-d H:i:s' : 'Y-m-d';

			ION.datePicker = new DatePicker('input.date', {
				pickerClass: 'datepicker_dashboard', 
				timePicker: oTimePicker,
				format: oInputFormat,
				inputOutputFormat: oOutputFormat,
				allowEmpty:true, 
				useFadeInOut:false
			//	positionOffset: {x:-60,y:0},
				/*
				onSelect: function(d, input)
				{
					console.log(input.getProperty('data-item'));
				}
				*/
			});
		}

		ION.datePicker.attach($$('.date'));
	},
	
	
	/**
	 * Displays one CSS "help link" on each label which have a title
	 * For other elements than labels, adding the .help class and one title will be enough to display the tip
	 *
	 */
	initLabelHelpLinks: function(element)
	{
		if (show_help_tips == '1')
		{
			$$(element + '.help, ' + element + ' label').each(function(el, id)
			{
				if (el.getProperty('title'))
				{
					el.addClass('help');
				}
			});
			
			new Tips(element + ' .help', {'className' : 'tooltip', 'text': 'rel', 'title' : 'title'});
		}
	},
	
	initSelectableText: function()
	{
		$$('.selectable').each(function(item)
		{
			item.addEvent('click', function(e)
			{
				if (document.selection)
				{
					var div = document.body.createTextRange();
					div.moveToElementText(item);
					div.select();
				}
				else
				{
					var div = document.createRange();
					div.setStartBefore(item);
					div.setEndAfter(item) ;
					window.getSelection().addRange(div);
				}
			});
		});
	},
	
	
	emptyDomElement: function(element)
	{
		if (typeOf(element) == 'string')
			element = $(element);
			
		element.empty();
	},
	
	appendDomElement: function(container, html)
	{
		var div = new Element('div');
		div.set('html', html);

		if (typeOf(container) == 'string')
			container = $(container);

		if (typeOf(html) == 'element')
			container.adopt(html);
		else
			container.set('html', container.get('html') + html);
	},


	insertDomElement: function(container, where, html)
	{
		var div = new Element('div');
		div.set('html', html);

		var el = div.getFirst();
		
		el.inject(container, where);
	},

	deleteDomElements: function(selector)
	{
		$$(selector).each(function(item, idx)
		{
			item.dispose();
		});
	},

	setHTML: function(selector, html)
	{
		$$(selector).each(function(item, idx) { item.set('html', html); });
	},
	
	addClass: function(selector, className)
	{
		$$(selector).addClass(className);
	},
	
	
	/**
	 * Init the dynamic title update
	 * @param	string	Input source ID
	 * @param	string	HTML Dom Element destination ID
	 * @param	string	Lang code
	 *
	 */
	initTitleUpdate: function(source, dest, lang)
	{
		if (lang == true)
		{
			// Add event to sources elements
			(Lang.get('languages')).each(function(l, idx)
			{
				if ($(source + l))
				{
					$(source + l).addEvent('keyup', function(e)
					{
						ION.updateTitle(this, dest);
					});
				}
			});
			source = source + Lang.get('default');
		}
		else
		{
			$(source).addEvent('keyup', function(e)
			{
				ION.updateTitle(this, dest);
			});
		}

		// Init the title
		ION.updateTitle(source, dest);
	},
	
	
	updateTitle: function(src, dest)
	{
		if (src && dest)
		{
			if (typeOf(src) == 'string') {src = $(src)};
			if (typeOf(dest) == 'string') {dest = $(dest)};
			
			dest.set('html', src.value);
		}
	},


	/**
	 * Todo : Write this method.
	 * Attach function on droppable
	 *
	 * Todo 2 : Rewrite addDragnDrop()
	 * in tree structure first check if droppable has this function end execute it
	 * if droppable doesn't have function then execute draggable function
	 * if droppable have function than don't execute draggable functions
	 *
	 */
	linkDropMethod: function(droppable, functions)
	{
	
	},
	
	/**
	 * Adds drag'n'drop functionnality to one element
	 *
	 * @param	HTML Dom Element	Element to add the drag on.
	 * @param	String				CSS classes which can drop the element, comma separated names.
	 * @param	String				Callback function(s), comma separated names.
	 *
	 * @usage
	 * 			ION.addDragDrop (item, '.dropcreators', 
	 *			{
	 *				fn:'ION.JSON',
	 *				args: ['perfume/link_creator', {'creator_id' : rel, 'perfume_id': $('perfume_id').value} ]
	 *			});
	 *
	 */
	addDragDrop: function(el, droppables, dropCallbacks)
	{
		el.makeCloneDraggable(
		{
			droppables: droppables,
			snap: 10,
			opacity: 0.5,
			revert: true,
			classe: 'ondrag',
			container: $('desktop'),
			dropCallbacks: dropCallbacks,
			
			onSnap: function(el, event)
			{
				el.addClass('move');
				if (typeOf(MUI.Windows.highestZindex) != 'null')
					el.setStyle('z-index', (MUI.Windows.highestZindex).toInt() + 1);

				// ION.register('dragElement', el);
				el.setProperty('dropClass', droppables.replace('.', ''));

				// Register the dragged Element : No other way to get the dragged element
				// when pseudo collision between a DOM element and a SVG parent node
				// ION.register('dragElement', el);

				/*
				console.log(event.event);

				el.addEventListener('dragstart', function (event)
				{
					console.log('drag start');

					// Makes HTML draggable active on Firefox
					event.dataTransfer.setData('draggable', true);

				});
				*/
			},
			
			onDrag: function(element, event)
			{
//				var evt = new CustomEvent('dragstart', event.event);
//				element.dispatchEvent(evt);

				//element.fireEvent('dragstart');

				// element.fireEvent('drag');
				// var event = new CustomEvent('drag', event.event);
				// element.dispatchEvent(evt);
				// element.fireEvent('drag', event.event);

				if (event.shift) { element.addClass('plus'); }
				else { element.removeClass('plus'); }
			},
			
			onDrop: function(element, droppable, event)
			{
				if (droppable)
				{
					if (typeOf(droppable.onDrop) == 'function')
					{
						droppable.onDrop(element, droppable, event);
					}
					else
					{
						/* For each droppable class, a function need to be executed
						 * ION.addDragDrop(
						 *		el,
						 *		'.drop-class-1, .drop-class-2',		// this.options.droppables, string of comma separated classes names
						 *		'drop-func-1, drop-func-2'			// this.options.dropCallbacks, string of functions names
						 * );
						 *
						 */
						var dropCB = this.options.dropCallbacks;

						if (typeOf(dropCB) == 'array')
						{
							var obj = dropCB[1];
							var func = obj[dropCB[0]];
							(func).delay(100, obj, [element, droppable, event]);
						}
						else if (typeOf(dropCB) == 'function')
						{
							dropCB.delay(100, null, [element, droppable, event]);
						}
						else
						{
							// New method : attach the callback to the element
							var callbacks = element.retrieve('dropCallbacks');

							if (typeOf(callbacks) != 'null')
							{
								var funcNames = Object.keys(callbacks);
								Array.each(funcNames, function(funcName)
								{
									if (droppable.hasClass(funcName))
									{
										dropCB = callbacks[funcName];
										dropCB.delay(100, null, [element, droppable, event]);
								//		ION.execCallbacks({'fn':dropCB, 'args':[element, droppable, event] });
									}
								});
							}
							else if(dropCB)
							{
								if (dropCB.contains(',') && this.dropClasses.length > 1)
								{
									var onDrops = (dropCB).replace(' ','').split(',');
									var index = false;

									// Search the method to execute.
									(this.dropClasses).each(function(cl, idx)
									{
										cl = cl.replace('.', '');
										if (droppable.hasClass(cl)) { index = idx;}
									});
									if (typeOf(onDrops[index]) != 'null')
									{
										ION.execCallbacks({'fn':onDrops[index], 'args':[element, droppable, event] });
									}
								}
								else if (typeOf(dropCB) == 'string')
								{
									ION.execCallbacks({'fn':dropCB, 'args':[element, droppable, event] });
								}
								else
								{
									ION.execCallbacks(dropCB);
								}
							}
						}
					}

					droppable.fireEvent('onDrop', [element, droppable, event]);
					
					droppable.removeClass('onenter');
				}
			},
			onEnter: function(el, droppable)
			{
				el.addClass('enter');
				droppable.addClass('onenter');
			},
			onLeave: function(el, droppable) 
			{
				el.removeClass('enter');
				droppable.removeClass('onenter');
			}
		});
	},

	
	initSortable: function(lists, onDrop)
	{
		var mySortables = new Sortables(lists, {
		    clone: true,
		    revert: true,
		    snap:10,
		    opacity:0.8,
		    preventDefault:true
		});
	},

	highlight: function(element, droppable, event)
	{
		droppable.highlight(ION.hbg);
	},

	

	initHelp: function(selector, table, title)
	{
		$$(selector).each(function(item, idx)
		{
			item.addEvent('click', function(e)
			{
				e.stop();
				ION.dataWindow(table + 'Help', 'Help', 'desktop/help', {resize:true}, {title:title, table:table});
				return false;
			});
		});
	},
	
	initClearField: function(selector)
	{
		$$(selector + ' .clearfield').each(function(item, idx)
		{
			if(item.hasClass('date'))
			{
				var dataInput = $(item.getAttribute('data-id'));
				var visibleInput = dataInput.getPrevious('input');

				item.addEvent('click', function(e) {
					e.stop();
					if (typeOf(visibleInput) != 'null') visibleInput.value = '';
					dataInput.value = '';
				});
			}
			else
			{
				item.addEvent('click', function(e) {
					e.stop();
					ION.clearField(item.getAttribute('data-id'));
				});
			}
		});
	},
	

	initAutocompleter: function(input, options)
	{
		var searchUrl = ION.cleanUrl(options.searchUrl),
			detailUrl = typeOf(options.detailUrl) != 'null' ? ION.cleanUrl(options.detailUrl) : null,
			item_id = options.item_id,
			update = options.update,
			zIndex = (typeOf(options.zIndex != 'null')) ? options.zIndex : 100,
			listKeys = typeOf(options.listKeys) == 'array' ? options.listKeys : null,
			inputKeys = typeOf(options.inputKeys) == 'array' ? options.inputKeys : null
		;

		new Autocompleter.Request.HTML($(input), admin_url + searchUrl,
		{
			'postVar': 'search',
			'indicatorClass': 'autocompleter-loading',
			minLength: 2,
		    maxChoices: 20,
		    zIndex: zIndex,
		    relative: true,
			selectMode : options.selectMode ? options.selectMode : false,
			width: typeOf(options.width) != 'null' ? options.width : 'inherit',
		    'injectChoice': function(choice)
		    {
				// choice is one <li> element
				var text = choice.getFirst();
				
				// the first element in this <li> is the <span> with the text
				var value = text.innerHTML;
				
				// inputValue saves value of the element for later selection
				choice.inputValue = choice.getProperty('data-display');
				
				// overrides the html with the marked query value (wrapped in a <span>)
				text.set('html', this.markQueryValue(value));
				
				// add the mouse events to the <li> element
				this.addChoiceEvents(choice);
			},
			onSelection: function(selection, item, value, input)
			{
				var id = item.getProperty('data-id');

				if (options.onSelection)
				{
					options.onSelection(selection, item, value, input);
				}
				else
				{
					if ($(update))
					{
						ION.HTML(admin_url + detailUrl, {item_id:id}, {'update':$(update)});
					}
				}
			}
		});

	},
	
	/**
	 * Insert Content Elements Definition tabs
	 *
	 */
	getContentElements: function(parent, id_parent)
	{
		// tabSwapper elements
		var tabSwapper = $('desktop').retrieve('tabSwapper');
		var tabs = 		tabSwapper.tabs;
		
		// DOM elements
		var tabsContainer = $(tabSwapper.options.tabsContainer);
		var sectionsContainer = $(tabSwapper.options.sectionsContainer);
		var ul = tabsContainer.getElement('ul');

		var r = new Request.JSON(
		{
			url: admin_url + 'element_definition/get_definitions_from_parent', 
			method: 'post',
			loadMethod: 'xhr',
			data:
			{
				'parent': parent,
				'id_parent': id_parent
			},
			onSuccess: function(responseJSON)
			{
				var index = 0;

				responseJSON.each(function(item, idx)
				{
					var id = item.id_element_definition;
					var found = false;
					index = tabs.length;
					
					tabs.each(function(tab) { if (tab.hasClass('tab' + id)) { found = tab; } });

					// Not found ? Build it !
					if (found == false)
					{
						// Tab
						var title = (item.title != '' && typeOf(item.title) != 'null') ? item.title : item.name;
						var a = new Element('a').set('html', title);
						var li = new Element('li', {'id':'tab' + id, 'class': 'tab' + id}).adopt(a);
						li.inject(ul, 'bottom');
						
						// Section
						var div = new Element('div', {'id':'tabcontent' + id, 'class': 'tabcontent tabcontent' + id}).inject(sectionsContainer, 'bottom');
						tabSwapper.addTab(li, div, a, index);
						
						// Get the content
						ION.HTML('element/get_elements_from_definition', {'parent':parent, 'id_parent':id_parent, 'id_element_definition': id}, {'update': div, 'onSuccess': function(){ION.updateTabNumber('tab' + id, 'tabcontent' +id )} });
					}
					// Found : Update
					else
					{
						//	tabSwapper.addTab(found, found.retrieve('section'), found.retrieve('clicker'), idx);
						ION.HTML('element/get_elements_from_definition', {'parent':parent, 'id_parent':id_parent, 'id_element_definition': id}, {'update': 'tabcontent' + id, 'onSuccess': function(){ION.updateTabNumber('tab' + id, 'tabcontent' + id)}});
					}
				});
				
				if (index > 0)
				{
	//				tabSwapper.show(0);
				}
			}
		}).send();
	},
	
	
	/**
	 * Adds the number of medias to the corresponding tab
	 *
	 *
	 */
	updateTabNumber: function(id_tab, id_container)
	{
		var tab = $(id_tab);
		
		if (typeOf(tab) != 'null')
		{
			var nb = 0;
			
			if (tab.getElement('span')) tab.getElement('span').dispose();
			
			var ul = $(id_container).getElement('ul');
			
			if (typeOf(ul) != 'null')
			{
				nb = (ul.getChildren('li')).length;		
			}
			else
			{
				nb = ($(id_container).getChildren()).length;
			}
			
			if (nb > 0)
			{
				tab.adopt(new Element('span', {'class':'tab-detail'}).set('html',nb));
			}
		}
	},

	
	/**
	 * Deletes one tab and its section based on its ID.
	 * Called after a delete by the controller if the element definition
	 * has no more element for this parent.
	 *
	 */
	deleteTab: function(id)
	{
		var tabSwapper = $('desktop').retrieve('tabSwapper');
		var tabs = tabSwapper.tabs;

		tabs.each(function(tab, idx)
		{
			if (tab.hasClass('tab' + id))
			{
				//	tabSwapper.removeTab(idx);
				tab.retrieve('section').destroy();
				tab.destroy();
				//	arrayName.splice(i,1);

				tabSwapper.tabs.splice(idx, 1);
				if (tab.hasClass(tabSwapper.options.selectedClass) && tabs.length > 1)	{ tabSwapper.show(0); }

				//	$('memory').store('tabSwapper', tabSwapper);
				//	return;
			}
		});
	},
	
	
	updateTabs: function()
	{
		var tabSwapper = $('desktop').retrieve('tabSwapper');
			
		if (tabSwapper)
		{
			var tabs = tabSwapper.tabs;
			
			tabs.each(function(tab, idx)
			{
				var section = tab.retrieve('section');
//				console.log(idx);
//				console.log(section.getChildren());
			});
		}
	},
	
	
	/**
	 * Updates all the content elements from a given parent
	 *
	 *
	 */
	updateContentTabs: function(parent, id_parent)
	{
		// Retrieve the tabSwapper
		var tabSwapper = $('desktop').retrieve('tabSwapper');

		if(tabSwapper)
		{
			ION.getContentElements(parent, id_parent, tabSwapper);
		}
	},
	
	
	
	
	/**
	 * Updates the info about the link
	 *
	 */
	updateLinkInfo: function(options)
	{
		var type = options.type;
		var id = options.id;
		var text = options.text;
		
		// Empty the link_info DL
		var dl = $('link_info');
		if (dl) {dl.empty();}
		
		// Link build
		if (type != '')
		{
			var url = admin_url + type + '/edit/' + id;
			
			var a = new Element('a').set('html', text);
		
			// Title (DT)
			var dt = new Element('dt');
			var label = new Element('label').set('text', Lang.get('ionize_label_linkto')); 
			dt.adopt(label);
			dt.inject(dl, 'top');
	
			// Icon & link
			var dd = new Element('dd').inject(dl, 'bottom');
			var span = new Element('span', {'class': 'link-img left ' + type}).inject(a, 'top');
		
			if (type == 'external')
			{
				a.setProperty('href', text);
				a.setProperty('target', '_blank');
			}
			else
			{
				a.removeEvent('click').addEvent('click', function(e)
				{
					e.stop();
		
					MUI.Content.update({
						'element': $('mainPanel'),
						'url': url,
						'title': Lang.get('ionize_title_edit_' + type)
					});
				});
			}
		
			a.inject(dd, 'bottom');
		}
	},
	
	
	/**
	 * Removes link and link info in the page / article panel
	 * Used when removing one link
	 *
	 */
	removeLink: function()
	{
		// remove form data
		$('link').set('text', '').setProperty('value','').fireEvent('change');
		$('link_type').value='';
		$('link_id').value='';
		
		// Empty the link_info DL
		$('link_info').empty();
	},
	
	
	/**
	 * Update the context of each article passed as args.
	 * Updates the tree names, flags, etc.
	 *
	 * Replaces ION.updateTreeArticles()
	 *
	 */
	updateArticleContext: function(articles)
	{
		articles.each(function(art)
		{
			var title = (art.title != '') ? art.title : art.name;
			var id = art.id_article;
			
			var rel = art.id_page + '.' + art.id_article;
			
			// Update the title
			$$('.file .title[data-id=' + rel + ']').each(function(el)
			{
				el.empty();
				el.set('html', title).setProperty('title', rel + ' : ' + title);
				
				var flag = (art.type_flag) ? art.type_flag : '0';
			
				new Element('span', {'class':'flag flag' + flag}).inject(el, 'top');
			});
			
			// Article icons
			$$('li.file[data-id=' + rel + '] div.tree-img.drag').each(function(el)
			{
				// Indexed icon
				if (art.indexed == '1')
					el.removeClass('sticky').addClass('file');
				else
					el.removeClass('file').addClass('sticky');
				
				// Link icon
				if (art.link !='')
					el.addClass('link');
				else
					el.removeClass('link');
				
			});
		});
	},
	
	
	/**
	 * Inits one tree menu title
	 *
	 */
	initTreeTitle: function(el)
	{
		var edit = el.getElement('.edit');
		var add_page = el.getElement('.add_page');
		var id_menu = el.getProperty('data-id');

		// Edit button
		if (edit)
		{
			edit.addEvent('click', function(e)
			{
				e.stop();

				ION.contentUpdate({
					element: $('mainPanel'),
					title: Lang.get('ionize_title_menu'),
					url : admin_url + 'menu'
				});
			});
		}
		
		// Add page button
		if (add_page)
		{
			add_page.addEvent('click', function(e)
			{
				e.stop();

				ION.contentUpdate({
					'element': $('mainPanel'),
					'url': admin_url + 'page/create/' + id_menu,
					title: Lang.get('ionize_title_new_page')
				});



/*
				MUI.Content.update(
				{
					element: $('mainPanel'),
					title: Lang.get('ionize_title_new_page'),
					loadMethod: 'xhr',
					url: admin_url + 'page/create/' + id_menu
				});
*/
			});
		}
	},


	updateArticleOrder: function(args)
	{
		var articleContainer = $$('.articleContainer[data-id=' + args.id_page + ']');
		articleContainer = (articleContainer.length > 0) ? articleContainer[0] : false;

			var articleList = $('articleList' + args.id_page);
		var order = (args.order).split(',');
		order = order.reverse();
		
		for (var i=0; i< order.length; i++)
		{
			if (articleContainer)
			{
				var el = articleContainer.getElement('li.article' + args.id_page + 'x' + order[i]);
				el.inject(articleContainer, 'top');
			}
			
			if (typeOf(articleList) != 'null')
			{
				var el = articleList.getElement('li.article' + args.id_page + 'x' + order[i]);
				el.inject(articleList, 'top');
			}
		}
	},
	
	
	/**
	 * Links one article to a page
	 * @param	int		ID of the article
	 * @param	int		ID of the page to add as parent
	 * @param	int		ID of the original page. 0 if no one.
	 * @param	Event	Event object.
	 *
	 */
	linkArticleToPage: function(id_article, id_page, id_page_origin, event)
	{
		var data = {
			'id_article': id_article,
			'id_page': id_page,
			'id_page_origin': id_page_origin
		};
		
		// Copy if SHIFT
		if (event.shift)
		{
			data['copy'] = true;
		}
		
		ION.JSON('article/link_to_page', data);
	},
	
	
	/**
	 * Removes the article form the orphan article list (Dashboard)
	 *
	 */
	removeArticleFromOrphan: function(args)
	{
		if ($('orphanArticlesList'))
		{
			$$('#orphanArticlesList .0x' + args.id_article).dispose();
		}
	},
	
	/**
	 * Adds the DOM page element to the page parents list
	 * called by ION.linkArticleToPage()
	 *
	 * <li data-id="id_page.id_article" class="parent_page"><span class="link-img page"></span><a class="icon right unlink"></a><a class="page">Page Title in Default Language</a></li>
	 *
	 */
	addPageToArticleParentListDOM: function(args)
	{
		if ($('parent_list'))
		{
			var li = new Element('li', {'data-id':args.id_page + '.' + args.id_article, 'class':'parent_page'});
			
			li.adopt(new Element('a', {'class':'icon right unlink'}));
			
			var title = (args.title !='' ) ? args.title : args.name;
			var aPage = new Element('a', {'class': 'page'}).set('text', title).inject(li, 'bottom');
			var span = new Element('span', {'class':'link-img page left mr5'}).inject(aPage, 'top');
			
			ION.addParentPageEvents(li);
			$('parent_list').adopt(li);
		}
	},
	
	
	/**
	 * Reloads the page's articles list
	 * called by ION.linkArticleToPage()
	 *
	 */
	reloadPageArticleList: function(id_page)
	{
		if (typeOf($('id_page')) != 'null' && $('id_page').value == id_page)
		{
			ION.HTML(admin_url + 'article/get_list', {'id_page':id_page}, {'update': 'articleListContainer'});
		}
	},

	
	/**
	 * Add events (edit context, unlink) on given parent page element
	 *
	 */
	addParentPageEvents: function(item)
	{
		var rel = (item.getProperty('data-id')).split(".");
		var id_page = rel[0];
		var id_article = rel[1];

		var unlink_url = admin_url + 'article/unlink/' + id_page + '/' + id_article;

		// Event on page name anchor
		var a = item.getElement('a.page');
		a.addEvent('click', function(e) {
			e.stop();
			ION.splitPanel({
				'urlMain': admin_url + 'page/edit/' + id_page,
				'urlOptions' : admin_url + 'page/get_options/' + id_page,
				'title': Lang.get('ionize_title_edit_page')
			});
		});
		
		// Event on unlink icon
		var del = item.getElement('a.unlink');
		del.addEvent('click', function(e) {
			e.stop();
			ION.confirmation('confDelete' + id_page + id_article, unlink_url, Lang.get('ionize_confirm_article_page_unlink'));
		});
	},
	
	initCopyLang: function(selector, elements)
	{
		$$(selector).each(function(item, idx)
		{
			item.addEvent('click', function()
			{
				var lang = item.getProperty('rel');
				var langs = Lang.languages;

				if (typeOf(tinyMCE) != 'null')
					tinyMCE.triggerSave();

				elements.each(function(item, idx)
				{
					langs.each(function(l)
					{
						if (l != lang)
						{
							if (typeOf($(item + '_' + l)) != 'null')
							{
								if (
									($(item + '_' + l).hasClass('tinyTextarea') ||  $(item + '_' + l).hasClass('smallTinyTextarea'))
									&& (typeOf(tinyMCE) != 'null')
								)
								{
									var tiny = tinyMCE.EditorManager.get(item + '_' + l);
									if (tiny)
										tiny.setContent($(item + '_' + lang).value);
								}
								else
								{
									$(item + '_' + l).value = $(item + '_' + lang).value;
								}
							}
						}
					});
				});
				
				ION.notification('success', Lang.get('ionize_message_article_lang_copied'));
				// item.getParent().highlight();
			});
		})
	},
	

	/**
	 * Link an Element to a parent
	 * Called by ION.dropContentElementInPage(), ION.dropContentElementInArticle()
	 *
	 * @param	String			Parent type. Can be 'article', 'page', etc.
	 * @param	DOM Element		The copied / moved element
	 * @param	DOM Element		DOM target
	 * @param	Event			Event
	 *
	 */
	dropContentElement: function(parent, element, droppable, event)
	{
		// Element
		var rel = (element.getProperty('data-id')).split(".");
		var id_element = (rel.length > 1) ? rel[1] : rel[0];
		
		// Target
		rel = (droppable.getProperty('data-id')).split(".");
		var id_parent = (rel.length > 1) ? rel[1] : rel[0];
		
		// Old Parent
		rel = ($('rel').value).split(".");
		var old_id_parent = (rel.length > 1) ? rel[1] : rel[0];
		
		var data = {
			'id_element': id_element,
			'parent': parent,
			'id_parent': id_parent,
			'old_parent': $('element').value,
			'old_id_parent': old_id_parent
		};

		// Copy if SHIFT
		if (event.shift)
		{
			data['copy'] = true;
		}

		ION.JSON('element/link_element', data);
	},
	
	/**
	 * Drop one Content Element in a page (in the tree)
	 * Copy / Move depending on the SHIFT key
	 *
	 */
	dropContentElementInPage: function(element, droppable, event)
	{
		event.stop();
		ION.dropContentElement('page', element, droppable, event);
	},
	
	/**
	 * Drop one Content Element in an article (in the tree)
	 * Copy / Move depending on the SHIFT key
	 *
	 */
	dropContentElementInArticle: function(element, droppable, event)
	{
		event.stop();
		ION.dropContentElement('article', element, droppable, event);
	},
	
	
	/**
	 * Drops an article into a page (from tree to page)
	 *
	 */
	dropArticleInPage: function(element, droppable, event)
	{
		var rel = (element.getProperty('data-id')).split('.');
		var id_page_origin = rel[0];
		var id_article = rel[1];
		var id_page = droppable.getProperty('data-id');
		
		if (id_page_origin != id_page)
			ION.linkArticleToPage(id_article, id_page, id_page_origin, event);
	},

	/**
	 * Drops a page into an article (from tree to article's parents)
	 *
	 */
	dropPageInArticle: function(element, droppable, event)
	{
		var id_article = droppable.getProperty('data-id');
		var id_page = element.getProperty('data-id');
		ION.linkArticleToPage(id_article, id_page, '0', event);
	},


	dropElementAsLink: function(link_type, element, droppable)
	{
		// Target link rel
		var link_rel = element.getProperty('data-id');
		
		// Receiver's element type
		var receiver_type = $('element').value;

		// No circular link !
		if (receiver_type == link_type && $('rel').value == link_rel )
		{
			ION.notification('error', Lang.get('ionize_message_no_circular_link'));
		}
		else
		{
			ION.JSON(
				admin_url + receiver_type + '/add_link',
				{
					'link_rel': link_rel,
					'receiver_rel': $('rel').value,
					'link_type': link_type
				}
			);
		}
	},


	/**
	 * Drops one article as link for another article / page
	 *
	 *
	 */
	dropArticleAsLink: function(element, droppable, event)
	{
		ION.dropElementAsLink('article', element, droppable);
	},


	/**
	 * Drops one page as link for another article / page
	 *
	 *
	 */
	dropPageAsLink: function(element, droppable, event)
	{
		ION.dropElementAsLink('page', element, droppable);
	},

	
	switchOnlineStatus: function(args)
	{
		if (args.status == 1) {
			$$(args.selector).removeClass('offline').addClass('online');
		}
		else
		{
			$$(args.selector).removeClass('online').addClass('offline');
		}
	},

	switchCss: function(args)
	{
		if (args.add && args.add == 1) {
			$$(args.selector).addClass(args['class']);
		}
		else
		{
			$$(args.selector).removeClass(args['class']);
		}
	},

	/**
	 * Removes all DOM elements which display the link between a page and an article
	 * Based on the used of <li rel="id_page.id_article" />
	 *
	 */
	unlinkArticleFromPageDOM: function(args)
	{
		$$('li[data-id=' + args.id_page + '.' + args.id_article + ']').each(function(item, idx) { item.dispose(); });
	},
	
	
	/**
	 * Adds one translation term to the translation list
	 *
	 */
	addTranslationTerm: function(parent)
	{
		parent = $(parent);
		
		var childs = parent.getChildren('ul');
		var nb = childs.length + 1;

		var clone = $('termModel').clone();
		var toggler = clone.getElement('.toggler');
		toggler.setProperty('rel', nb);
		
		var input = clone.getElement('input');
		input.setProperty('name', 'key_' + nb);
		
		var translation = clone.getElement('.translation');
		translation.setProperty('id', 'el_' + nb);
		
		var labels = clone.getElements('label');
		labels.each(function(label, idx)
		{
			label.setProperty('for', label.getProperty('for') + nb);
		});
		
		var textareas = clone.getElements('textarea');
		textareas.each(function(textarea, idx)
		{
			textarea.setProperty('name', textarea.getProperty('name') + nb);
		});
		
		clone.inject($('block'), 'top').setStyle('display', 'block');
		input.focus();
		
		ION.initListToggler(toggler, translation);
	},

	
	
	/**
	 * Cleans the base URL
	 *
	 * @return string	URL without the first part
	 *
	 */
	cleanUrl: function(url)
	{
		// Cleans URLs
		url = url.replace(admin_url, '');

		// Be sure the admin url without lang is also removed
		var admin_uri = Settings.get('admin_url');
		if (admin_uri)
			url = url.replace(base_url + admin_uri + '/', '');

		// Base URL contains the lang code. Try to clean without the lang code
		url = url.replace(admin_url.replace(Lang.current + '/', ''), '');

		url = url.replace(base_url + Lang.current + '/', '');
		url = url.replace(base_url, '');

		return url;
	},


	/**
	 * URL correction init
	 *
	 */
	initCorrectUrl: function(src, target)
	{
		var sep = arguments[2];
		if ( ! sep) sep = '-';

		var src = $(src);
		var target = $(target);
		
		if (src && target)
		{
			src.addEvent('keyup', function(e)
			{
				var text = ION.correctUrl(this.value, sep);
				target.setProperty('value', text);
			});
		}
	},
	
	
	/**
	 * URL correct one String 
	 *
	 */
	correctUrl: function(text)
	{
		var sep = arguments[1];
		if ( ! sep) sep = '-';

		var text = text.toLowerCase();

		/*
		text = text.replace(/ /g, '-');
		text = text.replace(/&/g, '-');
		text = text.replace(/:/g, '-');
		text = text.replace(/à/g, 'a');
		text = text.replace(/ä/g, 'a');
		text = text.replace(/â/g, 'a');
		text = text.replace(/ā/g, 'a');
		text = text.replace(/č/g, 'c');
		text = text.replace(/é/g, 'e');
		text = text.replace(/è/g, 'e');
		text = text.replace(/ë/g, 'e');
		text = text.replace(/ê/g, 'e');
		text = text.replace(/ï/g, 'i');
		text = text.replace(/î/g, 'i');
		text = text.replace(/ì/g, 'i');
		text = text.replace(/ô/g, 'o');
		text = text.replace(/ö/g, 'o');
		text = text.replace(/ò/g, 'o');
		text = text.replace(/ü/g, 'u');
		text = text.replace(/û/g, 'u');
		text = text.replace(/ù/g, 'u');
		text = text.replace(/µ/g, 'u');
		text = text.replace(/ç/g, 'c');
		text = text.replace(/ş/g, 's');
		text = text.replace(/š/g, 's');
		text = text.replace(/ı/g, 'i');
		text = text.replace(/ğ/g, 'g');
		*/
		var str = '';
		
		/*
		 * Permitted ASCII code for URLs : 
		 *
		 * 045 : - (minus or dash)
		 * 095 : _ (underscore)
		 * 048 : 0
         * 049 : 1
         * 050 : 2
         * 051 : 3
         * 052 : 4
         * 053 : 5
         * 054 : 6
         * 055 : 7
         * 056 : 8
         * 057 : 9
         * 097 : a
         * 098 : b
         * 099 : c
         * 100 : d
         * 101 : e
         * 102 : f
         * 103 : g
         * 104 : h
         * 105 : i
         * 106 : j
         * 107 : k
         * 108 : l
         * 109 : m
         * 110 : n
         * 111 : o
         * 112 : p
         * 113 : q
         * 114 : r
         * 115 : s
         * 116 : t
         * 117 : u
         * 118 : v
         * 119 : w
         * 120 : x
         * 121 : y
         * 122 : z
         *
		for (i=0;i<text.length;i++)
		{
			var c = text.charCodeAt(i);
			if (c==45 || c==95 || (c>47 && c<58) || (c>96 && c<123) )
			{
				str = str + text.charAt(i);
			}
		}
		 */

		var a = {
			// Non char
			" ":sep,"&":sep,":":sep,

			// Latin
			"à":"a","ä":"a","â":"a","ā":"a","č":"c","é":"e","è":"e","ë":"e","ê":"e","ï":"i","î":"i","ì":"i","ô":"o","ö":"o","ò":"o","ü":"u","û":"u","ù":"u","µ":"u","ç":"c","ş":"s","š":"s","ı":"i","ğ":"g",

            // Croatian letters
            "š":"s","đ":"d","č":"c","ć":"c","ž":"z",
            
			// Cyrillic
			"Ё":"YO","Й":"I","Ц":"TS","У":"U","К":"K","Е":"E","Н":"N","Г":"G","Ш":"SH","Щ":"SCH","З":"Z","Х":"H","Ъ":"'","ё":"yo","й":"i","ц":"ts","у":"u","к":"k","е":"e","н":"n","г":"g","ш":"sh","щ":"sch","з":"z","х":"h","ъ":"'","Ф":"F","Ы":"I","В":"V","А":"a","П":"P","Р":"R","О":"O","Л":"L","Д":"D","Ж":"ZH","Э":"E","ф":"f","ы":"i","в":"v","а":"a","п":"p","р":"r","о":"o","л":"l","д":"d","ж":"zh","э":"e","Я":"Ya","Ч":"CH","С":"S","М":"M","И":"I","Т":"T","Ь":"'","Б":"B","Ю":"YU","я":"ya","ч":"ch","с":"s","м":"m","и":"i","т":"t","ь":"'","б":"b","ю":"yu",

			// Hungarian
			"á":"a","í":"i","ó":"o","ő":"o","ú":"u","ű":"u",

			// Polish letters (in Latin Extended)
			"ą":"a","ć":"c","ę":"e","ł":"l","ń":"n","ó":"o","ś":"s","ż":"z","ź":"z","Ą":"A","Ć":"C","Ę":"E","Ł":"L","Ń":"N","Ó":"O","Ś":"S","Ż":"Z","Ź":"Z"
		};

			text = text.split('').map(function (char) {
				return a[char] || char;
			}).join("");

		for (var i=0; i<text.length; i++)
		{
			var c = text.charCodeAt(i);
			if (c==45 || c==95 || (c>44 && c<58) || (c>96 && c<123) )
			{
				str = str + text.charAt(i);
			}
		}

		return str;
	}

});