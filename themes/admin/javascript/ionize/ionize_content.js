ION.append({
	
	/** 
	 * Updates the mainPanel toolbox
	 *
	 * @param	string		Name of the toolbox view to load.
	 *						Must be located in the themes/admin/views folder
	 * @param	function	Function to execute when the toolbox is loaded.
	 *	
	 */
	initToolbox: function(toolbox_url, onContentLoaded)
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
				url: admin_url + 'desktop/get/toolboxes/' + toolbox_url
			});
		}
		else
		{
			$('mainPanel_headerToolbox').empty();
		}
	},
	
	
	/** 
	 * Init a module toolbox
	 * @param	string 	module name
	 * @param	toolbox_url for this module
	 *	
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
	 * Creates Accordion
	 * @param	string 	HTMLElement ID
	 *	
	 */
	initAccordion: function(togglers, elements, openAtStart, cookieName) 
	{
		// Hack IE 7 : No Accordion
		if (Browser.ie == true && Browser.version < 8)
		{
			return;
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
				toggler.removeClass('expand');
			},
			duration:'short'
		});
		
		return acc;
	},

	
	/**
	 * Adds effect to sideColumn
	 *
	 */
	initSideColumn: function()
	{
		// element to slide & linked button
		var maincolumn = $('maincolumn');
		var element = $('sidecolumn');		
		var button = $('sidecolumnSwitcher');
		
		if (button && element)
		{
			// button event
			button.addEvent('click', function(e)
			{
				var e = new Event(e).stop();
				
				if (this.retrieve('status') == 'close')
				{
					element.removeClass('close');
					maincolumn.addClass('sidecolumn');
	
					this.set('value', Lang.get('ionize_label_hide_options'));
					this.store('status', 'open');
	
					Cookie.write('sidecolumn', 'open');
					
				}
				else
				{
					element.addClass('close');
					maincolumn.removeClass('sidecolumn');
					
					this.set('value', Lang.get('ionize_label_show_options'));
					this.store('status', 'close');
					Cookie.write('sidecolumn', 'close');
				}
				
			});
			
			/*
			 * Get the cookie stored option state and apply
			 */
			var pos = Cookie.read('sidecolumn');
	
			if (typeOf(pos) != 'null' && pos == 'open')
			{
				element.removeClass('close');
				maincolumn.addClass('sidecolumn');
	
				button.store('status', 'open');
				button.set('value', Lang.get('ionize_label_hide_options'));

			}
			else
			{
				element.addClass('close');
				maincolumn.removeClass('sidecolumn');
				
				button.set('value', Lang.get('ionize_label_show_options'));
				button.store('status', 'close');
				
				/*
				*/
			}
		}
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


	/**
	 * Updates one element
	 *
	 * @param	Object	Options
	 *					'url' : URL of the controller to call
	 *					'element' : ID of the DOM element to update
	 *
	 */
	updateElement: function (options)
	{
		// Cleans URLs
		options.url = admin_url + ION.cleanUrl(options.url);
			
		// If the panel doesn't exists, try to update directly one DomHTMLElement
		if ( ! MUI.get(options.element) )
		{
			new Request.HTML({
				'url': options.url,
				'update': $(options.element)
			}).send()
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
				this.getParent('li').toggleClass('highlight');				// Not tip top
			});
		}
	},
	
	/**
	 * Inits the datepickers
	 * Originally using the Monkey Physics Datepicker.
	 * Currently using this one : abidibo / mootools-datepicker
	 *
	 */
	initDatepicker: function()
	{
		/**
		 * Implementation of https://github.com/arian/mootools-datepicker
		 * Not useful because no inputOutputFormat capabilities
		 *
		if (ION.datePicker)
		{
			ION.datePicker.close();
			ION.datePicker.detach($$('input.date'));
		}
		else
		{
			ION.datePicker = new Picker.Date($$('.date'), {
				timePicker: true,
				positionOffset: {x: -30, y: 0},
				pickerClass: 'datepicker_dashboard',
				format: '%d.%m.%Y %H:%M:%S',
				useFadeInOut: !Browser.ie,
				draggable: false,
				onSelect: function(date,all)
				{
					var inputs = ((this.input && !all) ? [this.input] : this.inputs);
					
					inputs.each(function(input){
						input.set('value', 'toto');
					}, this);

				}
			});
		}
		ION.datePicker.attach($$('input.date'));
		*/
		if (ION.datePicker)
		{
			ION.datePicker.close();
		}
		else
		{
			var display_format = (date_format).replace(/%/g, '');
			
			ION.datePicker = new DatePicker('.date', {
				pickerClass: 'datepicker_dashboard', 
				timePicker:true, 
				format: display_format + ' H:i:s', 
				inputOutputFormat:'d.m.Y H:i:s', 
				allowEmpty:true, 
				useFadeInOut:false, 
				positionOffset: {x:-30,y:0}
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
			$$(element + ' label').each(function(el, id)
			{
				if (el.getProperty('title'))
				{
					el.addClass('help');
				}
			});
			
			new Tips(element + ' .help', {'className' : 'tooltip'});
		}
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
			
			onSnap: function(el) { el.addClass('move'); },
			
			onDrag: function(element, event)
			{
				if (event.shift) { element.addClass('plus'); }
				else { element.removeClass('plus'); }
			},
			
			onDrop: function(element, droppable, event)
			{
				if (droppable)
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
				ION.dataWindow(table + 'Help', 'Help', 'desktop/help/' + table + '/' + title, {resize:true});
				return false;
			});
		});
	},
	
	
	
	/*
	 * Not working yet
	 */
	addClearField:function(input)
	{
		if (typeOf($(input)) != 'null')
		{
			var clear = new Element('a', {'class':'icon clearfield left'}).addEvent('click', function(e){ION.clearField(input);}).inject($(input), 'after');
			$(input).addClass('left');
		}
	},
	


	/*
	 * Splitted Article panel init
	 * Test
	 *
	 */
	editArticle: function(id, title) 
	{
		if ($('mainPanel')) {			
			
			MUI.Content.update({
				'element': $('mainPanel'),
				'url': admin_url + 'desktop/get/empty',
				'title': title,
				onContentLoaded: function(c)
				{
					new MUI.Column({
						container: 'mainPanel',
						id: 'mainColumn2',
						placement: 'main',
						width: null,
						resizeLimit: [100, 300]
					});
		
					new MUI.Column({
						container: 'mainPanel',
						id: 'sideColumn2',
						placement: 'right',
						width: 290,
						resizeLimit: [290, 400]
					});
					
					// mainPanel
					new MUI.Panel({
						id: 'splitPanel_mainPanel',
						title: title,
						loadMethod: 'xhr',
						contentURL: admin_url + 'article/edit/' + id,
						padding: { top: 15, right: 15, bottom: 8, left: 15 },
						addClass: 'maincolumn',
						column: 'mainColumn2',
						collapsible: false,
						header: false
					});
					
					new MUI.Panel({
						id: 'articlePanel',
						title: Lang.get('ionize_title_article_settings'),
						contentURL: admin_url + 'article/options/' + id,
						column: 'sideColumn2'
					//	addClass: 'maincolumn'
				//,tabsURL: admin_url + 'desktop/get/tabs/article_tabs'
					});
				}
			});
		}
	},

	
	initAutocompleter: function(input, options)
	{
		var searchUrl = ION.cleanUrl(options.searchUrl);
		var detailUrl = ION.cleanUrl(options.detailUrl);
		var item_id = options.item_id;
		
		var update = options.update;
		var zIndex = (typeOf(options.zIndex != 'null')) ? options.zIndex : 100;
		
		new Autocompleter.Request.HTML($(input), admin_url + searchUrl, 
		{
			'postVar': 'search',
			'indicatorClass': 'autocompleter-loading',
			minLength: 2,
		    maxChoices: 20,
		    zIndex: zIndex,
		    relative: true,
//			'selectMode': 'type-ahead',
		    'injectChoice': function(choice)
		    {
				// choice is one <li> element
				var text = choice.getFirst();
				
				// the first element in this <li> is the <span> with the text
				var value = text.innerHTML;
				
				// inputValue saves value of the element for later selection
				choice.inputValue = value;
				
				// overrides the html with the marked query value (wrapped in a <span>)
				text.set('html', this.markQueryValue(value));
				
				// add the mouse events to the <li> element
				this.addChoiceEvents(choice);
			},
			onSelection: function(selection, item, value, input)
			{
				var id = item.getProperty('rel');
				
				if ($(update))
				{
					ION.HTML(admin_url + detailUrl, {item_id:id}, {'update':$(update)});
				}
			}
		});
	},
	
	
	
	
	// ------------------------------------------------------------------------
	// / Rewritten functions
	// ------------------------------------------------------------------------

	/**
	 * Test function to get all the elements
	 *
	 
	getContentElements: function(parent, id_parent)
	{
		// ION.JSON(admin_url + 'element/get_elements', {'parent': parent, 'id_parent': id_parent});
		var r = new Request.JSON(
		{
			url: admin_url + 'element/get_elements', 
			method: 'post',
			loadMethod: 'xhr',
			data:
			{
				'parent': parent,
				'id_parent': id_parent
			},
			onSuccess: function(responseJSON, responseText)
			{
				$each(responseJSON, function(item, idx)
				{
					console.log(item.name);
					
					$each(item.elements, function(element, idx)
					{
						console.log('-' + element.id_element);
		
						$each(element.fields, function(field, idx)
						{
							console.log('--' + field.label);
						});
					});
				});
			}
		}).send();
	},	
	*/

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
			onSuccess: function(responseJSON, responseText)
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
						var title = (item.title != '') ? item.title : item.name;
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
	 * Called after a dleete by the controller if the element definition
	 * has no more element for this parent.
	 *
	 * TODO : Implement
	 *
	 */
	deleteTab: function(id)
	{
			var tabSwapper = $('desktop').retrieve('tabSwapper');
			var tabs = tabSwapper.tabs;
/*
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

		console.log(tabs.length);
*/
		
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
			$$('.file .title[rel=' + rel + ']').each(function(el)
			{
				el.empty();
				el.set('html', title).setProperty('title', title);
				
				var flag = art.flag;
				if (flag == '0' && art.type_flag != '') { flag = art.type_flag; }
			
				new Element('span', {'class':'flag flag' + flag}).inject(el, 'top');
			});
			
			// Update doc / sticky icon
			$$('li.file[rel=' + rel + '] div.tree-img.drag').each(function(el)
			{
				if (art.indexed == '1')
					el.removeClass('sticky').addClass('file');
				else
					el.removeClass('file').addClass('sticky');
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
		var id_menu = add_page.getProperty('rel');

		// Edit button
		edit.addEvent('click', function(e)
		{
			e.stop();
			MUI.Content.update({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_menu'),
				url : admin_url + 'menu'		
			});
		});
		
		// Add page button
		add_page.addEvent('click', function(e)
		{
			e.stop();
			
			MUI.Content.update(
			{
				element: $('mainPanel'),
				title: Lang.get('ionize_title_new_page'),
				loadMethod: 'xhr',
				url: admin_url + 'page/create/' + id_menu
			});
		});
	},
	
	
	/**
	 * Updates the folder or file icon when editing an article or a page
	 * If the edited article is a link to one page or article in the tree, 
	 * the icon of the "linked to" element will chnage, to show this link.
	 *
	 */
	updateTreeLinkIcon: function(args)
	{		
		// Remove link icon from all articles in trees
		$$('.tree .file').removeClass('filelink');
		$$('.tree .folder').removeClass('folderlink');
	},
	
	
	updateArticleOrder: function(args)
	{
		var articleContainer = $('articleContainer' + args.id_page);
		var articleList = $('articleList' + args.id_page);
		
		var order = (args.order).split(',');
		order = order.reverse();
		
		
		for (var i=0; i< order.length; i++)
		{
			if (typeOf(articleContainer) != 'null')
			{
				var el = articleContainer.getElement('#article_' + args.id_page + 'x' + order[i]);
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
	 * Updates one page in the tree
	 *
	 */	
	updateTreePage: function(args)
	{
		var title = (args.nav_title !='') ? args.nav_title : args.title;
		if (title == '') title = args.url;
		var id = args.id_page;
		var id_parent = args.id_parent;
		var status = (args.online == '1') ? 'online' : 'offline';
		var home_page = (args.home && args.home == '1') ? true : false;
		var element = $('page_' + id);
		
		// Parent ID from the page in the tree, before update
		var id_tree_parent = element.getParent('ul').id;
		
		var id_tree = args.menu.name + 'Tree';
		var parent = (id_parent != '0') ? $('page_' + id_parent) : $(id_tree);
		var id_container = (id_parent != '0') ? 'pageContainer' + id_parent : 'pageContainerTree' + args.menu.id_menu ;

		
		// link Title in tree (A tag)
		var el_link = '.title.page' + id;

		// Update the link text
		$$(el_link).set('text', title);
		
		// Update  Online/Offline class
		element.removeClass('offline').removeClass('online').addClass(status);

		// if the container doesn't exists, create it
		if ( ! (container = $(id_container)) && typeOf($(parent)) != 'null')
		{
			container = new Element('ul', {'id': 'pageContainer' + id_parent, 'class':'pageContainer', 'rel':id_parent });
			
			// If the parent already contains an article container, inject the page container before.
			if (articleContainer = $('articleContainer' + id_parent))
			{
				container.inject(articleContainer, 'before');
			}
			else
			{
				container.inject($('page_' + id_parent), 'bottom');
			}
		
			// Update visibility of container regarding the parent
			if ( ! (parent.hasClass('f-open'))) { container.setStyle('display', 'none'); }
		}
		
		// Moves the element in the tree
		if ( id_tree_parent != id_container )
		{
			if (typeOf(container) != 'null')
			{
				var childs = container.getChildren();
				
				// Put the page in the last position in the container
				container.adopt(element);
	
				// Update tree lines
				var pNbLines = parent.getChildren('.tree-img').length;
				var eNbLines = element.getChildren('.tree-img').length;
				
				var treeline = 	new Element('div', {'class': 'tree-img line'});
				var lis = element.getElements('li');
				lis.push(element);
				
				lis.each(function(li)
				{
					for (var i=0; i < eNbLines -2; i++) { (li.getFirst()).dispose();}
					for (var i=0; i < pNbLines -1; i++) { treeline.clone().inject(li, 'top'); }
				});
				
				// Update the relevant ID
				element.setProperty('rel', id);
			}
			else
			{
				element.dispose();
			}
		}
		
		// Update Home page icon, if mandatory
		if (home_page == true)
		{
			$$('.folder').removeClass('home');
			element.getFirst('.folder').addClass('home');
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
	 * <li rel="id_page.id_article" class="parent_page"><span class="link-img page"></span><a class="icon right unlink"></a><a class="page">Page Title in Default Language</a></li>
	 *
	 */
	addPageToArticleParentListDOM: function(args)
	{
		if ($('parent_list'))
		{
			var li = new Element('li', {'rel':args.id_page + '.' + args.id_article, 'class':'parent_page'});
			
			li.adopt(new Element('a', {'class':'icon right unlink'}));
			
			var title = (args.title !='' ) ? args.title : args.name;
			var aPage = new Element('a', {'class': 'page'}).set('text', title).inject(li, 'bottom');
			var span = new Element('span', {'class':'link-img page left'}).inject(aPage, 'top');
			
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

// TODO : Replace by ION.initRequestEvent...

	addParentPageEvents: function(item)
	{
		var rel = (item.getProperty('rel')).split(".");
		var id_page = rel[0];
		var id_article = rel[1];
		var flat_rel = id_page + 'x' + id_article;
				
		var edit_url = admin_url + 'article/edit_context/' + id_page + '/' + id_article;
		var unlink_url = admin_url + 'article/unlink/' + id_page + '/' + id_article;

		var titleInput = $('title_' + Lang.get('default')).value;
		var urlInput = $('url_' + Lang.get('default')).value;
		
		var articleTitle = (titleInput != '') ? titleInput : urlInput;

		// Event on page name anchor
		var a = item.getElement('a.page');
		a.addEvent('click', function(e) {
			e.stop();
//			MUI.formWindow('ArticleContext' + flat_rel, 'formArticleContext'+flat_rel, Lang.get('ionize_title_article_context'), edit_url, {width:500, height:350});
			MUI.Content.update({
				'element': $('mainPanel'),
				'loadMethod': 'xhr',
				'url': admin_url + 'page/edit/' + id_page,
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
				var langs = Lang.get('languages');
				
				if (tinyMCE != undefined)
					tinyMCE.triggerSave();
				
				elements.each(function(item, idx)
				{
					langs.each(function(l)
					{
						if (l != lang)
						{
							if (tinymce != undefined)
							{
								var tiny = tinymce.EditorManager.get(item + '_' + l);
								if (tiny)
								{
									tiny.setContent($(item + '_' + lang).value);
								}
							}
							else
							{
								$(item + '_' + l).value = $(item + '_' + lang).value;
							}
						}
					});
				});
				
				ION.notification('success', Lang.get('ionize_message_article_lang_copied'));
				
				item.getParent().highlight();
				
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
		var rel = (element.getProperty('rel')).split(".");
		var id_element = (rel.length > 1) ? rel[1] : rel[0];
		
		// Target
		rel = (droppable.getProperty('rel')).split(".");
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
		var rel = (element.rel).split('.');
		var id_page_origin = rel[0];
		var id_article = rel[1];
		var id_page = droppable.getProperty('rel');
		
		if (id_page_origin != id_page)
			ION.linkArticleToPage(id_article, id_page, id_page_origin, event);
	},

	/**
	 * Drops a page into an article (from tree to article's parents)
	 *
	 */
	dropPageInArticle: function(element, droppable, event)
	{
		var id_article = $('id_article').value;
		var id_page = droppable.getProperty('rel');

		ION.linkArticleToPage(id_article, id_page, '0', event);
	},

	
/*
	removeElementLink: function()
	{
		// Receiver's element type
		var receiver_type = $('element').value;
		var rel = $('rel').value;
		
		new Request.JSON({
			url: admin_url + receiver_type + '/remove_link',
			method: 'post',
			loadMethod: 'xhr',
			data: {
				'receiver_rel': rel
			},
			onSuccess: function(responseJSON, responseText)
			{
				// empty the textarea
				$('link').set('text', '').setProperty('value','').fireEvent('change');

				ION.notification('success', Lang.get('ionize_message_operation_ok'));

				// JS Callback
				if (responseJSON && responseJSON.callback)
				{
					ION.execCallbacks(responseJSON.callback);
				}
			}
		}).send();
	},
*/

	dropElementAsLink: function(link_type, element, droppable)
	{
		// Target link rel
		var link_rel = element.getProperty('rel');
		
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
	
	
	/**
	 * Removes all DOM elements which display the link between a page and an article
	 * Based on the used of <li rel="id_page.id_article" />
	 *
	 */
	unlinkArticleFromPageDOM: function(args)
	{
		$$('li[rel=' + args.id_page + '.' + args.id_article + ']').each(function(item, idx) { item.dispose(); });
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

		// Base URL contains the lang code. Try to clean without the lang code
		url = url.replace(admin_url.replace(Lang.get('current') + '/', ''), '');

		url = url.replace(base_url + Lang.get('current') + '/', '');
		url = url.replace(base_url, '');
		
		return url;
	},


	/**
	 * URL correction init
	 *
	 */
	initCorrectUrl: function(src, target)
	{
		var src = $(src);
		var target = $(target);
		
		if (src && target)
		{
			src.addEvent('keyup', function(e)
			{
				var text = ION.correctUrl(this.value);
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
		var text = text.toLowerCase();

		text = text.replace(/ /g, '-');
		text = text.replace(/&/g, '-');
		text = text.replace(/:/g, '-');
		text = text.replace(/à/g, 'a');
		text = text.replace(/ä/g, 'a');
		text = text.replace(/â/g, 'a');
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
		text = text.replace(/ı/g, 'i');
		text = text.replace(/ğ/g, 'g');

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
         */
		for (i=0;i<text.length;i++)
		{
			var c = text.charCodeAt(i);
			if (c==45 || c==95 || (c>47 && c<58) || (c>96 && c<123) )
			{
				str = str + text.charAt(i);
			}
		}

		return str;
	}

});