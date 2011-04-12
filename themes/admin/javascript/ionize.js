/**
 * Ionize Core Object
 *
 *
 */

var ION = new Hash({
	
	baseUrl: base_url,
	
	adminUrl: admin_url,
	
	trees:  new Hash(),		// Should contain the structure trees instances. See how to call them when saving an article for example....
	
	hbg: '#df8',
	

	// ------------------------------------------------------------------------
	// Rewritten functions
	// ------------------------------------------------------------------------

	tinyMceSettings: function(id, mode, options)
	{
		var options = ($type(options) != false) ? options : {};

		var width = ($type(options.width) != false) ? options.width : '100%';
		var height = ($type(options.height) != false) ? options.height : 180;
	
		switch (mode)
		{
			case 'small':

				var settings =  
				{
					mode : 'exact',
					elements : id,
					theme : 'advanced',
					skin: 'ionizeMce',
					language : Lang.get('current'),
					entity_encoding : 'raw',
					height: height,
					width: width,
					dialog_type : 'modal',
					inlinepopups_skin: 'ionizeMce',
					extended_valid_elements  : "ion:*, a[href</<ion:*]",
					verify_html : false,
					relative_urls : false,
					auto_cleanup_word : false,
					theme_advanced_toolbar_location : 'top',
					theme_advanced_toolbar_align : 'left',
					theme_advanced_buttons1 : 'bold,italic,|,bullist,numlist',
					theme_advanced_buttons2 : '',
					theme_advanced_buttons3 : ''
				};
				return settings;
				break;
			
			default:
				// Removed plugin preelementfix
				var settings = {
					mode : 'exact',
					elements : id,
					theme : 'advanced',
					skin: 'ionizeMce',
					language : Lang.get('current'),
					entity_encoding : 'raw',
					height:'450',
					width:'100%',
					dialog_type : 'modal',
					inlinepopups_skin: 'ionizeMce',
					extended_valid_elements  : "ion:*, a[href</<ion:*]",
					verify_html : false,
					relative_urls : false,
					auto_cleanup_word : false,
					plugins : 'pdw, inlinepopups,codemirror,safari,nonbreaking,media,preview,directionality,paste,fullscreen,template,table,advimage,advlink,spellchecker',
					flash_menu : 'false',
					theme_advanced_toolbar_location : 'top',
					theme_advanced_toolbar_align : 'left',
					theme_advanced_resizing : true,
					theme_advanced_resizing_use_cookie : false,
					theme_advanced_path_location : 'bottom',
					theme_advanced_blockformats : 'p,h2,h3,h4,h5,pre,div',
					theme_advanced_disable : 'help',
					theme_advanced_buttons1 : tinyButtons1,
					theme_advanced_buttons2 : tinyButtons2,
					theme_advanced_buttons3 : tinyButtons3,
					content_css : tinyCSS,
		            // PDW Toggle Toolbars settings
		            pdw_toggle_on : 1,
		            pdw_toggle_toolbars : '2,3',
					file_browser_callback: 'openFilemanager'
				};
				return settings;
				break;
		}

	},


	/**
	 * GLOBAL REQUEST FUNCTIONS
	 * Not to be used directly. Use initRequestEvent() instead to add one event to an element.
	 *
	 */
	JSON: function(url, data)
	{
		new Request.JSON(MUI.getJSONRequestOptions(url, data)).send();
	},

	HTML: function(url, data, options)
	{
		new Request.HTML(MUI.getHTMLRequestOptions(url, data, options)).send();
	},


	/**
	 * Create one Request event
	 *
	 */
	initRequestEvent: function(item, url, data, message)
	{
		var rel = item.getProperty('rel') || '';

		// Some safety before adding the event.
		item.removeEvents('click');

		item.addEvent('click', function(e)
		{
			e.stop();
			
			// Confirmation screen
			if (options.message)
			{
				var message = (Lang.get(options.message)) ? Lang.get(options.message) : Lang.get('app_message_confirm'); 
				
				// Callback request
				var callback = ION.JSON.pass([url,data]);

				MUI.confirmation('requestConfirm' + rel, callback, message);
			}
			else
			{
				ION.JSON(url, data);
			}
		});
		
	},
	
	
	/**
	 * Adds drag'n'drop functionnality to one element
	 *
	 * @param	HTML Dom Element	Element to add the drag on.
	 * @param	String				Class, Array of classes which can drop the element.
	 * @param	String				Callback function object.
	 *
	 * @usage
	 * 			ION. (item, '.dropcreators', 
	 *			{
	 *				fn:'ION.JSON',
	 *				args: ['perfume/link_creator', {'creator_id' : rel, 'perfume_id': $('perfume_id').value} ]
	 *			});
	 *
	 */
	addDragDrop: function(el, droppables, onDrop)
	{
		el.makeCloneDraggable(
		{
			droppables: droppables,
			snap: 10,
			opacity: 0.5,
			revert: true,
			classe: 'ondrag',
			
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
					/*
					 * TODO : Allow multiple callbacks on drop
					 *
					 *
					onDrops = new Array();
					callbacks =  new Array();
					
					// Be sure onDrops will be an array
					if ($type(onDrop) == 'array') {
						onDrops = onDrop;
					}
					else {
						onDrops.push(onDrop)	
					}
					
					onDrops.each(function(item, idx)
					{
						callbacks.push({'fn':item, 'args':[element, droppable, event] });
					}
					*/
					
					// One callback : works great.
					// ION.execCallbacks({'fn':onDrop, 'args':[element, droppable, event] });
					
					// If onDrop is a string, it can only be a func name : execute it and sent him the standard args
					
					/* For each droppable class, a function need to be executed
					 * ION.addDragDrop(
					 *		el, 
					 *		'.drop-class-1, .drop-class-2',		// this.options.droppables, array
					 *		'drop-func-1, drop-func-2'			// onDrop, string of functions names
					 * );
					 *
					 */
					if (onDrop.contains(",") && this.options.droppables.length > 1)
					{
						var onDrops = (onDrop).replace(' ','').split(",");
						var index = false;
						
						$each(this.options.droppables, function(cl, idx)
						{
							cl = cl.replace('.', '');
							if (droppable.hasClass(cl)) { index = idx;}
						});
						
						if ($type(onDrops[index]) != false)
						{
							ION.execCallbacks({'fn':onDrops[index], 'args':[element, droppable, event] });
						}
					}
					else if ($type(onDrop) == 'string')
					{
						ION.execCallbacks({'fn':onDrop, 'args':[element, droppable, event] });
					}
					else
					{
						ION.execCallbacks(onDrop);
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

	
	deleteDomElements: function(selector)
	{
		$$(selector).each(function(item, idx) { item.dispose(); });
	},

	setHTML: function(selector, html)
	{
		$$(selector).each(function(item, idx) { item.set('html', html); });
	},

	initHelp: function(selector, table, title)
	{
		$$(selector).each(function(item, idx)
		{
//			item.removeEvents();
			item.addEvent('click', function(e)
			{
				e.stop();
				MUI.dataWindow(table + 'Help', 'Help', 'desktop/help/' + table + '/' + title, {resize:true});
				return false;
			});
		});
	},
	
	/**
	 * Init TinyMCE on tabbed selectors
	 * @param	string	CSS tabs selectors
	 *					Each tab much have the "rel" attribute set.
	 *					Example : rel="fr"
	 * @param	string	textareas selector. Must have the same "rel" attr. as the atb
	 *					Example : #categoryTabContent .tinyCategory
	 * @param	string	Editor mode. Can be 'small' or empty (normal editor)
	 *
	 *
	 */
	initTinyEditors: function(tab_selector, container_selector, mode, options)
	{
		var textareas = $$(container_selector);
		var mode = mode;
		
		$$(tab_selector).each(function(tab)
		{
			// Current tab language or identifier
			var tab_rel = tab.getProperty('rel');
		
			textareas.each(function(target)
			{
				// Current area language or identifier (related to tab)
				var target_rel = target.getProperty('rel');
				
				if (tab_rel == target_rel)
				{
					// Tab click : Init TinyMCE
					tab.addEvent('click', function(e)
					{
						var ed = tinyMCE.editors[target.id];
		
						if ($type(ed) != 'object')
						{
							tinyMCE.init(ION.tinyMceSettings(target.id, mode, options));
						}
					});
					// Remove tiny editor of object with this ID.
					(tinyMCE.editors).each(function(tiny)
					{
						if ($type(tiny) != false)
						{
							if (tiny.id == target.id)
							{
								tinyMCE.remove(tiny);
							}
						}
					});
					
					// Init Tiny on the visible tab
					if (tab.hasClass('selected'))
					{
						var ed = tinyMCE.editors[target.id];

						if ($type(ed) == false)
						{
							setTimeout(function() {
								tinyMCE.init(ION.tinyMceSettings(target.id, mode, options));
							}, 50);
						}
					}
				}
			});


// Debug : List of active tiny object in memory
/*
(tinyMCE.editors).each(function(tiny)
{
	if ($type(tiny) == 'object')
	{
		console.log(tiny.id + ' in memory.');
	}
});
*/
		});
	},
	
	
	/*
	 * Not working yet
	 */
	addClearField:function(input)
	{
		if ($type($(input)) != false)
		{
			var clear = new Element('a', {'class':'icon clearfield left'}).addEvent('click', function(e){ION.clearField(input);}).inject($(input), 'after');
			$(input).addClass('left');
		}
	},
	


	/*
	 * Splitted Article panel init
	 *
	 */
	editArticle: function(id, title) 
	{
		if ($('mainPanel')) {			
			
			MUI.updateContent({
				'element': $('mainPanel'),
				'url': admin_url + 'desktop/get/empty',
				loadMethod: 'xhr',
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
				
				$each(responseJSON, function(item, idx)
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
	

	updateTabNumber: function(id_tab, id_container)
	{
	// Add the media number to the tab
		var tab = $(id_tab);
		
		if ($type(tab) != false)
		{
			var nb = 0;
			
			if (tab.getElement('span')) tab.getElement('span').dispose();
			
			var ul = $(id_container).getElement('ul');
			
			if ($type(ul) != false)
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
				
				console.log(idx);
				console.log(section.getChildren());
				
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
		
			// Title
			var dt = new Element('dt', {'class': 'small'});
			var label = new Element('label').set('text', Lang.get('ionize_label_linkto')); 
			dt.adopt(label);
			dt.inject(dl, 'top');
	
			// Icon & link
			var dd = new Element('dd').inject(dl, 'bottom');
			var span = new Element('span', {'class': 'link-img ' + type}).inject(label, 'bottom');
		
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
		
					MUI.updateContent({
						'element': $('mainPanel'),
						'loadMethod': 'xhr',
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
			var title = (art.title != '') ? art.title : art.url;
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
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_menu'),
				url : admin_url + 'menu'		
			});
		});
		
		// Add page button
		add_page.addEvent('click', function(e)
		{
			e.stop();
			
			MUI.updateContent(
			{
				element: $('mainPanel'),
				title: Lang.get('ionize_title_new_page'),
				loadMethod: 'xhr',
				url: admin_url + 'page/create/' + id_menu
			});
		});
	},
	
	
	/**
	 * Updates all the tree articles
	 * Used after saving an article, in case of main data update (title or url, sticky, etc.)
	 *
	updateTreeArticles: function(args)
	{
		var title = (args.title != '') ? args.title : args.url;
		var id = args.id_article;
		
		// file or sticky
		var icon = (args.indexed && args.indexed == '1') ? 'file' : 'sticky';
		var old_icon = (args.indexed && args.indexed == '1') ? 'sticky' : 'file';
		
		// Update the title
		$$('.tree .article' + id + '.title').each(function(el)
		{
			var rel = (el.getProperty('rel')).split('.');
			var id_page = rel[0];
			
			// Flag span : If the article is flagged, display the article flag, else display the article's type flag.
			var flag = args.flag;
//console.log(id_page + ' :: '+ flag + ' :: ' + args.type_flag);
			if (id_page == args.id_page && flag == '0' && args.type_flag != '') {
				flag = args.type_flag;
			}
			
			el.empty();
			el.set('html', args.title).setProperty('title', title);
			new Element('span', {'class':'flag flag' + flag}).inject(el, 'top');
		});
		
		// Update the article icon (file or sticky)
		$$('.tree .article' + id + ' ' + '.' + old_icon).removeClass(old_icon).addClass(icon);
		
		// Update the main Panel title
		$('mainPanel_title').set('text', Lang.get('ionize_title_edit_article') + ' : ' + title);
	},
	*/
	
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
			var el = articleContainer.getElement('#article_' + args.id_page + 'x' + order[i]);
			el.inject(articleContainer, 'top');
			
			if (articleList)
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
		var title = (args.title !='') ? args.title : args.url;
		var id = args.id_page;
		var id_parent = args.id_parent;
		var status = (args.online == '1') ? 'online' : 'offline';
		var home_page = (args.home && args.home == '1') ? true : false;
		var element = $('page_' + id);
		
		// Parent ID from the page in the tree, before update
		var id_tree_parent = element.getParent('ul').getProperty('rel');
		
		var id_tree = args.menu.name + 'Tree';
		var parent = (id_parent != '0') ? $('page_' + id_parent) : $(id_tree);
		var id_container = (id_parent != '0') ? 'pageContainer' + id_parent : id_tree ;
		
		// link Title in tree (A tag)
		var el_link = '.title.page' + id;

		// Update the link text
		$$(el_link).set('text', title);
		
		// Update  Online/Offline class
		element.removeClass('offline').removeClass('online').addClass(status);

		// if the container doesn't exists, create it
		if ( ! (container = $(id_container)))
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
		if ( id_tree_parent != id_parent )
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
		if ($type($('id_page')) != false && $('id_page').value == id_page)
		{
			ION.HTML(admin_url + 'article/get_list', {'id_page':id_page}, {'update': 'articleListContainer'});
		}
	},
	
	updateSimpleItem: function(args)
	{
		// Items list exists ?
		if ($(args.type + 'List'))
		{
			// Item already exists ?
			var item = $(args.type + 'List').getFirst('[rel='+ args.rel +']');
			
			// Update
			if (item)
			{
				// Update the name
				item.getFirst('.title').set('text', args.name);
			}
			// Create
			else
			{
				ION.addSimpleItemToList(args);
			}
		}
	},
	
	
	/**
	 * Adds a simple LI item to a container (ION.ItemManager)
	 *
	 * @param	JSON object		Item to add
	 *
	 */
	addSimpleItemToList: function(args)
	{
		if ($(args.type + 'List'))
		{
			var title = args.name;
	
			var li = new Element('li', {'rel':args.rel, 'class':'sortme ' + args.type + args.rel });
	
			// Delete button
			var aDelete = new Element('a', {'class':'icon delete right', 'rel':args.rel});
			ION.initItemDeleteEvent(aDelete, args.type);
			li.adopt(aDelete);

			// Item's drag icon
			li.adopt(new Element('a', {'class':'icon left drag pr5'}));
			
			// Item's title
			var a = new Element('a', {'class':'title left pl5 ' + args.type + args.rel, 'rel':args.rel}).set('text', title);
			
			// Global edit link
			// One controller : admin/<args.type>/edit/ must exists
			// ... and one function : admin/<args.type>/edit/ must also exists
			a.addEvent('click', function()
			{
				MUI.formWindow(args.type + args.rel, args.type + 'Form' + args.rel, Lang.get('ionize_title_' + args.type + '_edit'), args.type + '/edit/' + this.getProperty('rel'), {});
			});
			li.adopt(a);


			// Add item to list DOM object
			$(args.type + 'List').adopt(li);
	
			// Add element to sortables
			var sortables = $(args.type + 'List').retrieve('sortables');
	
			sortables.addItems(li);
		}	
	},
	
	
	/**
	 * Add events (edit context, unlink) on given parent page element
	 *
	 */
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
			MUI.updateContent({
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
			MUI.confirmation('confDelete' + id_page + id_article, unlink_url, Lang.get('ionize_confirm_article_page_unlink'));
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
				
				tinyMCE.triggerSave();
				
				elements.each(function(item, idx)
				{
					langs.each(function(l)
					{
						if (l != lang)
						{
							var tiny = tinymce.EditorManager.get(item + '_' + l);
							if (tiny)
							{
								tiny.setContent($(item + '_' + lang).value);
							}
							else
							{
								$(item + '_' + l).value = $(item + '_' + lang).value;
							}
						}
					});
				});
				
				MUI.notification('success', Lang.get('ionize_message_article_lang_copied'));
				
				item.getParent().highlight();
				
			});
		})
	},
	
	/**
	 * Adds unlink event to one icon
	 * Used by ION.ArticleManager:initUnlinkEvents()
	 *
	 * The item must have the REL property set to "id_page.id_article".. ex : ... rel="1.5"
	 *
	 *
	 */
	initArticleUnlinkEvent: function(item)
	{
		var rel = (item.getProperty('rel')).split(".");
		var id_page = rel[0];
		var id_article = rel[1];
		var flat_rel = id_page + 'x' + id_article;
		
		var url = admin_url + 'article/unlink/' + id_page + '/' + id_article;
		
		// Some safety before adding the event.
		item.removeEvents('click');

		item.addEvent('click', function(e)
		{
			e.stop();
			MUI.confirmation('confDelete' + flat_rel, url, Lang.get('ionize_confirm_article_page_unlink'));
		});
	},
	
	
	initArticleStatusEvent: function(item)
	{
		var rel = (item.getProperty('rel')).split(".");
		var id_page = rel[0];
		var id_article = rel[1];
		
		var url = admin_url + 'article/switch_online/' + id_page + '/' + id_article;

		// Some safety before adding the event.
		item.removeEvents('click');

		item.addEvent('click', function(e)
		{
			e.stop();
			ION.switchArticleStatus(id_page, id_article);
		});
	},
	
	
	initArticleViewEvent: function(item)
	{
		var rel = item.getAttribute('rel').split(".");
		
		if (item.value != '0' && item.value != '') { item.addClass('a'); }

		// Some safe before adding the event.
		item.removeEvents('change');

		item.addEvents({
		
			'change': function(e)
			{
				e.stop();
			 	
				var url = admin_url + 'article/save_context';
				
				this.removeClass('a');
				
				if (this.value != '0' && this.value != '') { this.addClass('a'); }
				
				var data = {
					'id_page': rel[0],
					'id_article': rel[1],
					'view' : this.value
				};

 				MUI.sendData(url, data);
			}
		});
	},
	
	
	initArticleTypeEvent: function(item)
	{
		var rel = item.getAttribute('rel').split(".");

		if (item.value != '0' && item.value != '') { item.addClass('a'); }

		// Some safety before adding the event.
		item.removeEvents('change');

		item.addEvents({
		
			'change': function(e)
			{
				e.stop();
			 	
				var url = admin_url + 'article/save_context';
				
				this.removeClass('a');
				
				if (this.value != '0' && this.value != '') { this.addClass('a'); }
				
				var data = {
					'id_page': rel[0],
					'id_article': rel[1],
					'id_type': this.value
				};

 				MUI.sendData(url, data);
				
//				if (this.value != 'edit')
//				{
 //					MUI.sendData(url, data);
//				}
				
			}
		});
	},
	
	
	initPageStatusEvent: function(item)
	{
		var rel = (item.getProperty('rel')).split(".");
		var id_page = rel[0];
		
		var url = admin_url + 'page/switch_online/' + id_page;

		// Some safety before adding the event.
		item.removeEvents('click');

		item.addEvent('click', function(e)
		{
			e.stop();
			ION.switchPageStatus(id_page);
		});
	},
	
	
	initItemDeleteEvent: function(item, type)
	{
		var id = item.getProperty('rel');
		
		if (id)
		{
			// Callback definition
			var callback = ION.itemDeleteConfirm.pass([type, id]);
			
			item.removeEvents();
			
			// Confirmation modal window
			item.addEvent('click', function(e)
			{
				e.stop();
				MUI.confirmation('del' + type + id, callback, Lang.get('ionize_confirm_element_delete'));
			});
		}
	},

	
	/**
	 * Effective item delete
	 * callback function
	 *
	 * @param	String		Type. Can be 'page' or 'article' or anything else.
	 * @param	int			item ID
	 *
	 */
	itemDeleteConfirm: function(type, id, parent, id_parent)
	{
		// Shows the spinner
		MUI.showSpinner();

		// Delete URL
		var url = admin_url + type + '/delete/' + id;
		
		// If parent, include it to URL
		if (parent && id_parent)
		{
			url += '/' + parent + '/' + id_parent
		}
		
		// JSON Request
		var xhr = new Request.JSON(
		{
			url: url,
			method: 'post',
			onSuccess: function(responseJSON, responseText)
			{
				if (responseJSON.id)
				{
					// Remove all HTML elements with the CSS class corresponding to the element type and ID
					$$('.' + type + responseJSON.id).each(function(item, idx) { item.dispose(); });
					
					// Get the update array and do the jobs
					if (responseJSON.update != null && responseJSON.update != '') {	MUI.updateElements(responseJSON.update); }
					
					// As we delete the current edited item, let's return to the home page
					if($('id_' + type) && $('id_' + type).value == id)
					{
						MUI.updateContent({
							'element': $('mainPanel'),
							'loadMethod': 'xhr',
							'url': admin_url + 'dashboard',
							'title': Lang.get('ionize_title_welcome')
						});
						MUI.initToolbox();
					}
				}
				
				// JS Callback
				if (responseJSON && responseJSON.callback)
				{
					MUI.execCallbacks(responseJSON.callback);
				}

				
				// If Error, display a modal instead a notification
				if(responseJSON.message_type == 'error')
				{
					MUI.error(responseJSON.message);
				}
				else
				{
					// User message
					MUI.notification(responseJSON.message_type, responseJSON.message);		
				}
				
				
				// Hides the spinner
				MUI.hideSpinner();
			}
		}).send();
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
		var rel = (element.getProperty('rel')).split(".");
		var id_page_origin = rel[0];
		var id_article = rel[1];
		var id_page = droppable.getProperty('rel');

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

	
	addExternalLink: function(receiver_type, url, textarea)
	{
		new Request.JSON({
			url: admin_url + receiver_type + '/add_link',
			method: 'post',
			loadMethod: 'xhr',
			data: {
				'receiver_rel': $('rel').value,
				'link_type': 'external',
				'url': url
			},
			onSuccess: function(responseJSON, responseText)
			{
				// Set the link title to the textarea
				$(textarea).set('html', url);

				MUI.notification('success', Lang.get('ionize_message_operation_ok'));

				// JS Callback
				if (responseJSON && responseJSON.callback)
				{
					MUI.execCallbacks(responseJSON.callback);
				}
			}
		}).send();
	},

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

				MUI.notification('success', Lang.get('ionize_message_operation_ok'));

				// JS Callback
				if (responseJSON && responseJSON.callback)
				{
					MUI.execCallbacks(responseJSON.callback);
				}
			}
		}).send();
	},


	dropElementAsLink: function(link_type, element, droppable)
	{
		// Target link rel
		var link_rel = element.getProperty('rel');
		
		// Receiver's element type
		var receiver_type = $('element').value;

		// No circular link !
		if (receiver_type == link_type && $('rel').value == link_rel )
		{
			MUI.notification('error', Lang.get('ionize_message_no_circular_link'));
		}
		else
		{
			new Request.JSON({
				url: admin_url + receiver_type + '/add_link',
				method: 'post',
				loadMethod: 'xhr',
				data: {
					'link_rel': link_rel,
					'receiver_rel': $('rel').value,
					'link_type': link_type
				},
				onSuccess: function(responseJSON, responseText)
				{
					// Set the link title to the textarea
					droppable.getElement('textarea').set('html', element.getProperty('title'));

					MUI.notification('success', Lang.get('ionize_message_operation_ok'));

					// JS Callback
					if (responseJSON && responseJSON.callback)
					{
						MUI.execCallbacks(responseJSON.callback);
					}
				}
			}).send();
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

	
	switchArticleStatus: function(id_page, id_article)
	{
		// Show the spinner
		MUI.showSpinner();
		
		var xhr = new Request.JSON(
		{
			url: this.adminUrl + 'article/switch_online/' + id_page + '/' + id_article,
			method: 'post',
			onSuccess: function(responseJSON, responseText)
			{
				// Change the item status icon
				if ( responseJSON.message_type == 'success' )
				{
					// Set online / offline class to all elements with this selector
					var args = {
						'id_page': id_page,
						'id_article': id_article,
						'status': responseJSON.status
					}
					
					ION.updateArticleStatus(args);
					
					// Get the update table and do the jobs
					if (responseJSON.update != null && responseJSON.update != '')
					{
						MUI.updateElements(responseJSON.update);
					}
					
				}
				// User message
				MUI.notification.delay(50, this, new Array(responseJSON.message_type, responseJSON.message));
				
				// Hides the spinner
				MUI.hideSpinner();
				
			}.bind(this)

		}).send();
	},
	
	
	switchPageStatus: function(id_page)
	{
		// Show the spinner
		MUI.showSpinner();
		
		var xhr = new Request.JSON(
		{
			url: this.adminUrl + 'page/switch_online/' + id_page,
			method: 'post',
			onSuccess: function(responseJSON, responseText)
			{
				// Change the item status icon
				if ( responseJSON.message_type == 'success' )
				{
					// Set online / offline class to all elements with this selector
					var args = {
						'id_page': id_page,
						'status': responseJSON.status
					}
					
					ION.updatePageStatus(args);
					
				}
				// User message
				MUI.notification.delay(50, this, new Array(responseJSON.message_type, responseJSON.message));
				
				// Hides the spinner
				MUI.hideSpinner();
				
			}.bind(this)

		}).send();
	},
	
	
	/**
	 * Updates all the screen visible articles regarding the passed args
	 * Called after context data saving : See Article->save_context()
	 *
	 * @param	Object	Status arguments
	 *					args = {id_page:<int>,id_article:<int>,status:<0.1>}
	 *
	 */
	updateArticleStatus: function(args)
	{
		// Set online / offline class to all elements with this selector
		var elements = $$('.article' + args.id_page + 'x' + args.id_article);
		
		if (args.status == 1) {
			elements.removeClass('offline').addClass('online');
		}
		else
		{
			elements.removeClass('online').addClass('offline');
		}
	},
	
	
	updatePageStatus: function(args)
	{
		// Set online / offline class to all elements with this selector
		var elements = $$('.page' + args.id_page);
		var inputs = $$('.online' + args.id_page);
		
		inputs.each(function(item, idx)
		{
			item.setProperty('value', args.status);
		});


		if (args.status == 1) {
			elements.removeClass('offline').addClass('online');
		}
		else
		{
			elements.removeClass('online').addClass('offline');
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
	 * Init the droppables input and textareas
	 *
	 */
	initDroppable: function()
	{
		$$('.droppable').each(function(item, idx)
		{
			new ION.Droppable(item);
		});
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
	 * Init one list toggler
	 * Used by translation view
	 *
	 */	
	initListToggler: function(toggler, child)
	{
		if ($type(child) != false)
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
				this.getParent('li').toggleClass('highlight');
	//			this.getParent('ul').toggleClass('highlight');
			});
		}
	},
	
	insertDomElement: function(container, where, html)
	{
		var div = new Element('div');
		div.set('html', html);

		var el = div.getFirst();
		
		el.inject(container, where);
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
	},


	/**
	 * Cleans all the inputs (input + textarea) from a givve form
	 *
	 */
	clearFormInput: function(args)
	{
		// Inputs and textareas : .inputtext
		$$('#' + args.form + ' .inputtext').each(function(item, idx)
		{
			item.setProperty('value', '');
			item.set('text', '');
		});
		
		// Checkboxes : .inputcheckbox
		$$('#' + args.form + ' .inputcheckbox').each(function(item, idx)
		{
			item.removeProperty('checked');
		});
	},
	
	
	execCallbacks: function(callback)
	{
		// JS Callback
		if (callback)
		{
			callbacks = new Array();
			
			// More than one callback
			if ($type(callback) == 'array') {
				callbacks = callback;
			}
			else {
				callbacks.push(callback)	
			}
		
			callbacks.each(function(item, idx)
			{
				var cb = (item.fn).split(".");
				var func = null;
				var obj = null;
				
				if (cb.length > 1) {
					obj = window[cb[0]];
					func = obj[cb[1]];
				}
				else {
					func = window[cb];
				}
				func.delay(100, obj, item.args);
			});
		}
	}
});

/**
 * Main options
 *
 */
ION.options = {
	mainpanel: 		'mainPanel',
	baseUrl:		base_url,
	adminUrl:		admin_url
};


/**
 *  Ionize Tree class
 *  Build each website structure tree
 * 
 *  @author	Partikule Studio
 *  @since	0.9.5
 * 
 */
ION.Tree = new Class({

	Implements: [Events, Options],
	
	options: ION.options,
	
	initialize: function(element, options)
	{
		this.setOptions(options);
		this.element = element;
		
		var options = this.options;
		
		this.mainpanel = $(options.mainPanel);
		
		// Array of itemManagers
		this.pageItemManagers = new Array();
		
		var opened = new Array();
		if (Cookie.read('tree')) opened = (Cookie.read('tree')).split(',');
		
		var folders = $$('#' + element + ' li.folder');
		
		// Pages
		folders.each(function(folder, idx)
		{
			var folderContents = folder.getChildren('ul');
			
			var homeClass = (folder.hasClass('home')) ? ' home' : '' ;
			
			var folderImage = new Element('div', {'class': 'tree-img drag folder' + homeClass}).inject(folder, 'top');
	
			// Define which open and close graphic ( + - ) each folder gets
			var image = new Element('div', {'class': 'tree-img plus'});
			
			image.addEvent('click', this.openclose).inject(folder, 'top');

			if (opened.contains(folder.id))
			{
				folder.addClass('f-open');
				image.removeClass('plus').addClass('minus');
			}
			else
			{
				folderContents.each(function(el){ el.setStyle('display', 'none'); });
			}

			// Add connecting branches to each file node
			folderContents.each(function(element){
				var docs = element.getChildren('li.doc').extend(element.getChildren('li.sticky'));
				docs.each(function(el)
				{
					new Element('div', {'class': 'tree-img line node'}).inject(el.getElement('span'), 'before');
				});
			});
			
			this.addEditLink(folder, 'page');
			this.addPageActionLinks(folder);
			
			// Make the folder name draggable
			ION.addDragDrop(folder.getElement('a.title'), '.dropPageAsLink,.dropPageInArticle', 'ION.dropPageAsLink,ION.dropPageInArticle');
			

		}.bind(this));

		// All nodes (Page & Articles)
		$$('#'+element+' li').each(function(node, idx)
		{
			// Add connecting branches to each node
			node.getParents('li').each(function(parent){
				new Element('div', {'class': 'tree-img line'}).inject(node, 'top');
			});
			
			var typeClass = (node.hasClass('doc')) ? 'file' : 'sticky' ;
			
			// Articles
			if (node.hasClass('file'))
			{
				var title = node.getElement('a.title');
				
				var link = node.getElement('span');
				new Element('div', {'class': 'tree-img drag ' + typeClass}).inject(link, 'before');
				
				// Edit Link
				this.addEditLink(node, 'article');
				
				// Actions
				// this.addArticleActionLinks(node);
				
				// Make the article name draggable
				ION.addDragDrop(title, '.dropArticleInPage,.dropArticleAsLink,.folder', 'ION.dropArticleInPage,ION.dropArticleAsLink,ION.dropArticleInPage');
			}
			
			// Mouse over effect
			this.addMouseOver(node);
			
		}.bind(this));
	
		$$('#' + element + ' li span.action').setStyle('display','none');
		
		// Root Page Item Manager
		this.pageItemManagers[element] = new ION.PageManager({ container: element });

		// Create PageManagers
		$$('#' + element + ' .pageContainer').each(function(item, idx) { 
			this.pageItemManagers[item.id] = new ION.PageManager({ container: item.id });
		}.bind(this));
		
		// Article's containers
		$$('#' + element + ' .articleContainer').each(function(item, idx) { 
			item.store('articleManager', new ION.ArticleManager({ container: item.id , id_parent: item.getProperty('rel')}));
		}.bind(this));
	},


	openclose:function(evt)
	{
		evt.stop();
		el = evt.target;
		var folder = el.getParent();
		var folderContents = folder.getChildren('ul');
		var folderIcon = el.getNext('.folder');

		if (folder.hasClass('f-open')) {
			el.addClass('plus').removeClass('minus');
			folderIcon.removeClass('open');
			folderContents.each(function(ul){ ul.setStyle('display', 'none');});
			folder.removeClass('f-open');
			ION.treeDelFromCookie(folder.getProperty('id'));
		}
		else {
			el.addClass('minus').removeClass('plus');
			folderIcon.addClass('open');
			folderContents.each(function(ul){ ul.setStyle('display', 'block'); });
			folder.addClass('f-open');
			ION.treeAddToCookie(folder.getProperty('id'));
		}
	},


	/**
	 * Adds a link to a tree LI DOM element
	 *
	 * @param	DOMElement		tree LI
	 * @param	String			logical tree element type. "page" or "article"
	 *
	 */
	addEditLink: function(el, type)
	{
//		var a = el.getLast('span').getElement('a');
		var a = el.getElement('a.title');
		var rel = (a.getProperty('rel')).split(".");
		var id = rel[0];
		
		// id is the last rel
		if (rel.length > 1) { id = rel[1]; }

		var p = $(this.options.mainpanel);

		a.addEvent('click', function(e)
		{
			e.stop();
			
//			ION.editArticle(this.getProperty('rel'), Lang.get('ionize_title_edit_' + type));
			
			MUI.updateContent({
				'element': p,
				'loadMethod': 'xhr',
				'url': admin_url + type + '/edit/' + a.getProperty('rel'),
			//	'title': Lang.get('ionize_title_edit_' + type) + ' : ' + this.getProperty('title')	
				'title': Lang.get('ionize_title_edit_' + type)	
			});
		});
	},

/**
 * 
 *
 * HERE : Replace switchOnline() and deleteItem() by the ION one
 *
 *
 *
 *
 */	
	addPageActionLinks: function(el)
	{
		var a = el.getElement('a.addArticle');
		var id = a.rel;
		var p = $(this.options.mainpanel);
		
		// Add article icon
		a.addEvent('click', function(e)
		{
			e.stop();
			MUI.updateContent({
				'element': p,
				'loadMethod': 'xhr',
				'url': admin_url + 'article/create/' + id,
				'title': Lang.get('ionize_title_create_article')
			});
		});
		
		// Online / Offline icon
		a = el.getElement('a.status');
		ION.initPageStatusEvent(el.getElement('a.status'));

		// Page delete event
//		ION.initItemDeleteEvent(el.getElement('a.delete'), 'page');
	},

	
	addArticleActionLinks: function(el)
	{
		// Add the Status Event
		ION.initArticleStatusEvent(el.getElement('a.status'));

		// Add the delete event
//		ION.initItemDeleteEvent(el.getElement('a.delete'), 'article');

		// Unlink Event
		ION.initArticleUnlinkEvent(el.getElement('a.unlink'));
		
	},

	
	addMouseOver: function(node)
	{
		node.addEvent('mouseover', function(ev){
			ev.stopPropagation();
			ev.stop();
			this.addClass('highlight');
			this.getParent().getParent().getChildren('.action').setStyle('display', 'none');
			this.getChildren('.action').setStyle('display', 'block');
		});
		node.addEvent('mouseout', function(ev){
			this.removeClass('highlight');
		});
		node.addEvent('mouseleave', function(e)
		{
			this.getChildren('.action').setStyle('display', 'none');
		});
	},

	
	/**
	 * Insert One article in the tree
	 * @param	JSON object		The complete article object
	 *
	 */
	insertTreeArticle: function(options)
	{
		var title = (options.title !='') ? options.title : options.url;

		var page = $('page_' + options.id_page);

		var id = options.id_article;
		var id_page = options.id_page;
		var rel = id_page + '.' + id;			// rel : REL of article
		var flat_rel = id_page + 'x' + id;		// flat rel : Used to build unique class selectors
		
		var status = (options.online == '1') ? ' online ' : ' offline '; 
		
		// Main elements
		var li = 		new Element('li', {'id': 'article_' + flat_rel, 'class': 'file doc' + status + ' article' + id + ' article' + flat_rel, 'rel': rel });
		var action = 	new Element('span', {'class': 'action', 'styles': { 'display':'none' }});
		var icon = 		new Element('span', {'class': 'icon' });
		var link = 		new Element('span');

		// Title
		var a =			new Element('a', {'id':'al' + id, 'class': 'title ' + status + ' article' + id + ' article' + flat_rel, 'rel': rel, title: title }).set('text', title);
		var treeline = 	new Element('div', {'class': 'tree-img'});
		
		// Action elements
		var iconOnline = icon.clone().adopt(new Element('a', {'class': 'status ' + status + ' article' + flat_rel, 'rel': rel}));
		var iconUnlink = icon.clone().adopt(new Element('a', {'class': 'unlink', 'rel': rel}));
		action.adopt(iconOnline, iconUnlink);
		
		// Status & Delete icons
		this.addArticleActionLinks(action);
		
		// Add drag to article link
		ION.addDragDrop(a, '.dropArticleInPage,.dropArticleAsLink,.folder', 'ION.dropArticleInPage,ION.dropArticleAsLink,ION.dropArticleInPage');
		
		// Flag span
		var span = new Element('span', {'class':'flag flag' + options.flag});
		span.inject(a, 'top');
		link.adopt(a);
		li.adopt(action, link);
		
		this.addEditLink(li, 'article');
		
		var icon = treeline.clone().addClass('file drag');
		icon.inject(li, 'top');
		
		// Get the parent and the tree lines (nodes)
		var parent = $('page_' + id_page);
		var treeLines = $$('#page_' + id_page + ' > .tree-img');
		
		// item tree line
		var nodeLine = treeline.clone();
		nodeLine.addClass('line').addClass('node');
		
		// Try to get the articles UL container
		if ( container = $('articleContainer' + id_page))
		{
			var lis = container.getChildren('li');
			
			// Node lines
			nodeLine.inject(li, 'top');
			for (var i=0; i < treeLines.length -1; i++)	{ 
				treeline.clone().inject(li, 'top'); 
			}

			// Inject LI at the correct pos
			if (options.ordering == '1') li.inject(container, 'top');
			else li.inject(lis[options.ordering -2], 'after');
		}
		// if no article container, we will create one.
		else
		{
			// Node lines
			nodeLine.inject(li, 'top');
			for (var i=0; i < treeLines.length -1; i++)	{ treeline.clone().inject(li, 'top'); }

			container = new Element('ul', {'id':'articleContainer' + id_page});				
			container.adopt(li);
			container.inject(page, 'bottom');
			
			// Article Manager
			container.store('articleManager', new ION.ArticleManager({ container: 'articleContainer' + id_page , id_parent: id_page}));
			
			if ( ! (parent.hasClass('f-open'))) { container.setStyle('display', 'none');	}
		}

		// Add the article to the articleContainer
		var sortables = container.retrieve('sortables');
		sortables.addItems(li);

		// Add Mouse over effects
		this.addMouseOver(li);
	},


	/**
	 * Insert One page in the tree
	 *
	 */
	insertTreePage: function(options)
	{
		var title = (options.title !='') ? options.title : options.url;
		var menu = $(options.menu.name + 'Tree');
		var id = options.id_page;
		var id_parent = options.id_parent;
		var status = (options.online == '1') ? ' online ' : ' offline '; 
		var home_page = (options.home && options.home == '1') ? true : false;
		var containerName = (id_parent != '0') ? 'pageContainer' + id_parent : options.menu.name + 'Tree';

		/* Main elements */
		var li = 		new Element('li', {'id': 'page_' + id, 'class': 'folder page' + status + ' page' + id, 'rel':id});
		var action = 	new Element('span', {'class': 'action', 'styles': { 'display':'none' }});
		var icon = 		new Element('span', {'class': 'icon'	});
		var link = 		new Element('span');
		var a =			new Element('a', {'id':'pl' + id, 'class':'title' + status + ' page' + id, 'rel': id, title:title }).set('text', title);
		var treeline = 	new Element('div', {'class': 'tree-img'});
		

		/* Action element */
		var iconOnline = icon.clone().adopt(new Element('a', {'class': 'status ' + status + ' page' + id, 'rel': id}));
//		var iconDelete = icon.clone().adopt(new Element('a', {'class': 'delete', 'rel': id}));
		var iconArticle = icon.clone().adopt(new Element('a', {'class': 'addArticle article', 'rel': id}));
		action.adopt(iconOnline, iconArticle);
		this.addPageActionLinks(action);

		/* Link element */
		link.adopt(a);
		li.adopt(action, link);
		this.addEditLink(li, 'page');
		
		// drag folder icon
		var icon = treeline.clone().addClass('folder').addClass('drag');
		
		// if home page, remove hom from the old home page
		if (home_page == true)
		{
			$$('.folder.home').removeClass('home');
			icon.addClass('home');
		}
		icon.inject(li, 'top');

		// plus / minus icon
		var pm = treeline.clone().addClass('plus').addEvent('click', this.openclose.bind(this)).inject(li, 'top');
		
		/* Get the parent and the tree lines (nodes) */
		var parent = $('page_' + id_parent);
		var treeLines = $$('#page_' + id_parent + ' > .tree-img');
		
		/* Make the li draggable */
		ION.addDragDrop(a, '.dropPageAsLink,.dropPageInArticle', 'ION.dropPageAsLink,ION.dropPageInArticle');

	
		// Try to get the parent UL container
		if ( container = $(containerName))
		{
			var lis = container.getChildren('li');
			
			// Node lines
			for (var i=0; i < treeLines.length -1; i++)	{ treeline.clone().inject(li, 'top'); }

			// Inject LI at the correct pos
			li.inject(container, 'bottom');
			
			// Correct the upper article nodeline
			if (nb = li.getPrevious() && ! container.getElement('articleContainer' + id_parent))
			{
				// MUI.notification('', containerName);
				nb = li.getPrevious()
				nodeTree = nb.getChildren('.tree-img');
				nodeTree[nodeTree.length-2].removeClass('last').addClass('node');
			}			
		}
		// if no parent container, we will create one.
		else
		{
			// Node lines
			for (var i=0; i < treeLines.length -1; i++)	{ treeline.clone().inject(li, 'top'); }
			
			// container
			container = new Element('ul', {'id':containerName, 'class':'pageContainer', 'rel':id_parent});				
			container.adopt(li);
			container.inject(parent.getLast('span'), 'after');

			if ( ! (parent.hasClass('f-open'))) { container.setStyle('display', 'none');	}
			
			// Add one pageItemManager
			this.pageItemManagers[containerName] = new ION.PageManager({ container: containerName });
		}
		
		// Add the page to the pageContainer
		var sortables = container.retrieve('sortables');
		sortables.addItems(li);

		// Add Mouse over effects
		this.addMouseOver(li);
	}
});


/**
 * Gives ability to an input to have the "droppable" class and to chnage class on focus / blur
 *
 */
ION.Droppable = new Class({

	Implements: [Events, Options],
	
	options: ION.options,

	initialize: function(element, options)
	{
		this.setOptions(options);
		
		var options = this.options;
		
		/* Add focus in/out and blur events only if input has not the ".nofocus" class
		 *
		 */
		if (element.hasClass('nofocus') == false)
		{
			element.addEvents(
			{
				'change': function(e)
				{
					var alt = this.getProperty('alt');
					var value = this.getProperty('value');
					var text = this.get('text');
					
					if (value == '')
					{
						this.addClass('empty').set('text', alt).setProperty('value', alt);
					}
					else
					{
						this.removeClass('empty');
					}
				},
				
				'click': function(e)
				{
					var alt = this.getProperty('alt');
					var value = this.getProperty('value');
					
					if (value == alt)
					{
						this.removeClass('empty').set('text', '').setProperty('value', '');
					}
				},
			
				'blur': function(e)
				{
					this.fireEvent('change');
				}
			});

			element.fireEvent('change');	
		}
		else
		{
			if (element.hasClass('empty') == true)
			{
				var alt = element.getProperty('alt');
				element.set('text', alt).setProperty('value', alt);
			}

			element.addEvents(
			{
				'focus': function(e)
				{
					this.blur();
				}
			});
			
		}
	}	
	
});


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
						MUI.notification.delay(50, MUI, new Array(responseJSON.message_type, responseJSON.message));
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
		MUI.notification('error', xhr.responseText );

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


/**
 
Class: Notify
	Creates a windows notification box.

Syntax:
	(start code)
	new ION.Notify(target, options);
	(end)

Arguments:
	target - (string) ID of the window
	options

Options:
	className - (string) Optional box class name
	type - (string) Box type. Can be 'error', 'information', 'alert'

Returns:
	Notify box object.
	
Example :


*/
ION.Notify = new Class({

	Implements: [Events, Options],

	initialize: function(target, options)
	{
		this.setOptions(options);
		
		this.displayed = false;
		
		// new Element('p').set('text', options.message)
		this.box = new Element('div', {'class':options.className + ' ' + options.type});
		
		this.box.set('slide', 
		{
			duration: 'short',
			transition: 'sine:out',
			onComplete: function()
			{
//				MUI.windowResize(target);
			}
		});

		
		// All Application windows are prefixed with "w".
		if ($('w' + target + '_content'))
		{
			this.target = target;
			
			this.windowEl = $('w' + target);
			this.contentEl = $('w' + target + '_content');
			
			(this.box).inject(this.contentEl, 'top');

			this.box.slide('hide');
		}
	},
	
	show: function(msg)
	{
		this.setMessage(msg);
	
		this.box.slide('in');
		
		if ($(this.options.hide))
		{
			$(this.options.hide).fade('out');
		}
		
		// Resize content
		if (this.displayed == false)
		{
			this.windowEl.retrieve('instance').resize({height: (this.contentEl.getSize()).y + (this.box.getSize()).y + 10});
		}

		this.displayed = true;
	},
	
	hide: function()
	{
		this.box.slide('out');

		if ($(this.options.hide))
		{
			$(this.options.hide).fade('in');
		}
		
		// Resize content
		if (this.displayed == true)
		{
			this.windowEl.retrieve('instance').resize({height: (this.contentEl.getSize()).y - (this.box.getSize()).y + 10});
		}
		this.displayed = false;
	},
	
	setMessage: function(msg)
	{
		this.box.empty();
		
		if (typeof(msg) == 'object')
		{
			this.box.adopt(msg);
		}
		else
		{
			if (Lang.get(msg) != '' ) msg = Lang.get(msg);
			
			this.box.set('html', msg);
		}
	}
});




/** MediaManager
 *	Opens the choosen media / file manager and get the transmitted file name
 *
 *	Options :
 *
 *		baseUrl:			URL to the website
 *		parent:				type of the parent. 'article', 'page', etc. Used to update the database table.                 
 *		idParent:			ID of the parent element      
 *		pictureContainer:	The picture container DOM element 
 *		musicContainer:		The MP3 list container DOM element
 *		videoContainer:		The video list container DOM element
 *		button:				DOM opener button name
 *		mode:				'tinyBrowser' : Using the tinyBrowser plugin to browse files
 *							'mcFileManager' : Using MoxeCode MceImageManager / MceFileManager to browse files
 */

var IonizeMediaManager = new Class(
{
	Implements: Options,

    options: {
		parent:			false,
		idParent:		false,
		mode:			'',								// 'mcFileManager', 'tinyBrower', 'ezfilemanager', 'kcfinder',
		musicArray:		Array('mp3'),					// Array of authorized files extensions
		pictureArray:	Array('jpg', 'gif', 'png', 'jpeg'),
		videoArray:		Array('flv', 'fv4'),
		fileArray:		Array(),
		thumbSize:		120
    },

	initialize: function(options)
	{
		this.setOptions(options);
		
		this.baseUrl =		this.options.baseUrl;

		this.adminUrl =		this.options.adminUrl;
		
		this.themeUrl =		theme_url;

		this.idParent =		options.idParent;
		this.parent =		options.parent;

		// Containers storing
		this.containers = 	new Hash({
							 'picture' : options.pictureContainer,
							 'music' : options.musicContainer,
							 'video': options.videoContainer,
							 'file': options.fileContainer
							});
		// Filemanager mode
		this.mode =			options.mode;

		// Filemanager opening buttons
		var self = this;
		$$(options.fileButton).each(function(item)
		{
			item.addEvent('click', function(e)
			{
				var e = new Event(e).stop();
				self.toggleFileManager();
			});
		});
		
		// Check if a fileManager is already open. If yes, change the callback ref.
		// Needed in case of page / article change with the filemanager open
		if ($('filemanagerWindow'))
		{
			var self = this;
			
			filemanager.removeEvents('complete');
			
			filemanager.setOptions(
			{
				'onComplete': self.addMedia.bind(self)
			});
		}
	},
	
	initParent: function(parent, id_parent)
	{
		this.parent = parent;
		this.idParent = id_parent;
	},
	
	/**
	 * Adds one medium to the current parent
	 * Called by callback by the file / image manager
	 * 
	 * @param	string	Complete URL to the media. Slashes ('/') were replaced by ~ to permit CI management
	 *
	 */
	addMedia:function(url) 
	{
		// File extension
		var extension = (url.substr(url.lastIndexOf('.') + 1 )).toLowerCase();

		// Check media type regarding the extension
		var type = false;
		if (this.options.pictureArray.contains(extension)) { type='picture';}
		if (this.options.musicArray.contains(extension)) { type='music';}
		if (this.options.videoArray.contains(extension)) { type='video';}
		if (this.options.fileArray.contains(extension)) { type='file';}

		// Media type not authorized : error message
		if (type == false)
		{
			MUI.notification('error', Lang.get('ionize_message_media_not_authorized'));
		}
		else
		{
			// Complete relative path to the media
			var path =	url.replace(/\//g, "~");

			// Send the media to link
			var xhr = new Request.JSON(
			{
				'url': this.adminUrl + 'media/add_media/' + type + '/' + this.parent + '/' + this.idParent, 
				'method': 'post',
				'data': 'path=' + path,
				'onSuccess': this.successAddMedia.bind(this), 
				'onFailure': this.failure.bind(this)
			}).send();
		}
	},


	/**
	 * called after 'addMedia()' success
	 * calls 'loadMediaList' with the correct media type returned by the XHR call
	 *
	 */
	successAddMedia: function(responseJSON, responseText)
	{
		MUI.notification(responseJSON.message_type, responseJSON.message);

		// Media list reload
		if (responseJSON.type)
		{
			this.loadMediaList(responseJSON.type);
		}
	},


	/**
	 * Loads a media list through XHR regarding its type
	 * called after a medi list loading through 'loadMediaList'
	 *
	 * @param	string	Media type. Can be 'picture', 'music', 'video', 'file'
	 *
	 */
	loadMediaList: function(type)
	{
		// Only loaded if a parent exists
		if (this.idParent)
		{
			var myAjax = new Request.JSON(
			{
				'url' : this.adminUrl + 'media/get_media_list/' + type + '/' + this.parent + '/' + this.idParent,
				'method': 'get',
				'onFailure': this.failure.bind(this),
				'onComplete': this.completeLoadMediaList.bind(this)
			}).send();
		}
	},

	
	/**
	 * Initiliazes the media list regarding to its type
	 * called after a media list loading through 'loadMediaList'
	 *
	 * @param object	JSON response object
	 * 					responseJSON.type : media type. Can be 'picture', 'video', 'music', 'file'
	 * 					responseJSON.content : 
	 *
	 */
	completeLoadMediaList: function(responseJSON, responseText)
	{
		// Hides the spinner
		MUI.hideSpinner();

		// Receiver container
		var container = $(this.containers.get(responseJSON.type));
	

		if (responseJSON && responseJSON.content)
		{
			// Feed the container with responseJSON content		
			container.set('html', responseJSON.content);

			// Init the sortable 
			sortableMedia = new Sortables(container, {
				revert: true,
				handle: '.drag',
				referer: this,
				clone: true,
				opacity: 0.5,
				onComplete: function()
				{
					var serialized = this.serialize(0, function(element, index) 
					{
						// Get the ID list by replacing 'type_' by '' for each item
						// Example : Each picture item is named 'picture_ID' where 'ID' is the media ID
						return element.getProperty('id').replace(responseJSON.type + '_','');
					});
					
					// Items sorting
					this.options.referer.sortItemList(responseJSON.type, serialized);
				}		
			});
		
			// Store the first ordering after picture list load
			container.store('sortableOrder', sortableMedia.serialize(0, function (element, index) 
			{
				return element.getProperty('id').replace(responseJSON.type + '_','');
			}));
			
			// Set tips
			new Tips('#' + this.containers.get(responseJSON.type) + ' .help', {'className' : 'tooltip'});
		}
		// If no media, feed the content HMTLDomElement with transmitted message
		else
		{
			container.set('html', responseJSON.message);
		}

		// Add the media number to the tab
		ION.updateTabNumber(responseJSON.type + 'Tab', container.getProperty('id'));
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
	sortItemList: function(type, serialized) 
	{
		var container = $(this.containers.get(type))
		var sortableOrder = container.retrieve('sortableOrder');

		// If current <> new ordering : Save it ! 
		if (sortableOrder.toString() != serialized.toString() ) 
		{
			// Store the new ordering
			container.store('sortableOrder', serialized);

			// Save the new ordering
			var myAjax = new Request.JSON(
			{
				url: this.adminUrl + 'media/save_ordering/' + this.parent + '/' + this.idParent,
				method: 'post',
				data: 'order=' + serialized,
				onSuccess: function(responseJSON, responseText)
				{
					MUI.hideSpinner();
					
					MUI.notification(responseJSON.message_type, responseJSON.message);
				}
			}).post();
		}
	},



	/** 
	 * Called when one request fails
	 */
	failure: function(xhr)
	{
		MUI.notification('error', xhr.responseText );

		// Hide the spinner
		MUI.hideSpinner();
	},


	/**
	 * Unlink one media from his parent
	 *
	 * @param	string	Media type
	 * @param	string	Media ID
	 *
	 */
	detachMedia: function(type, id) 
	{
		// Show the spinner
		MUI.showSpinner();
		
		var xhr = new Request.JSON(
		{
			url: this.adminUrl + 'media/detach_media/' + type + '/' + this.parent + '/' + this.idParent + '/' + id,
			method: 'post',
//			onSuccess: this.disposeMedia.bind(this),
			onSuccess: function()
			{
				this.loadMediaList(type);
			}.bind(this),
			onFailure: this.failure.bind(this)
		}).send();
	},


	/**
	 * Unlink all media from a parent depending on the type
	 *
	 * @param	string	Media type. Can be 'picture', 'music', 'video', 'file'
	 *
	 */	
	detachMediaByType: function(type)
	{
		// Show the spinner
		MUI.showSpinner();
		
		var xhr = new Request.JSON(
		{
			url: this.adminUrl + 'media/detach_media_by_type/' + this.parent + '/' + this.idParent + '/' + type,
			method: 'post',
			onSuccess: function(responseJSON, responseText)
			{
				$(this.containers.get(type)).empty();
				
				// Message
				MUI.notification(responseJSON.message_type, responseJSON.message);
				
				// Hides the spinner
				MUI.hideSpinner();
				
			}.bind(this),
			onFailure: this.failure.bind(this)
		}).send();
	},

	
	/**
	 * Dispose one HTMLDomElement
	 *
	 * @param	object	JSON XHR request answer
	 * @param	object	Text XHR request answer
	 *
	 */
	disposeMedia: function(responseJSON, responseText)
	{
		// HTMLDomElement to dispose
		var el = responseJSON.type + '_' + responseJSON.id;
		
		if ( responseJSON.id && $(el))
		{
			$(el).dispose();
			MUI.notification('success', responseJSON.message);		
		}
		else
		{
			MUI.notification('error', responseJSON.message);
		}
		
		MUI.hideSpinner();
	},


	/** 
	 * Init thumbnails for one picture
	 * to be called on pictures list
	 * @param	string	picture ID
	 *
	 */
	initThumbs:function(id_picture) 
	{
		// Show the spinner
		MUI.showSpinner();

		var myAjax = new Request.JSON(
		{
			url: this.adminUrl + 'media/init_thumbs/' + id_picture,
			method: 'post',
			onSuccess: function(responseJSON, responseText)
			{
				MUI.notification(responseJSON.message_type, responseJSON.message );
				
				if (responseJSON.message_type == 'success')
				{
					this.loadMediaList('picture');
				}
			}.bind(this)
		}).send();
	},


	/** 
	 * Init all thumbs for one parent
	 *
	 */
	initThumbsForParent: function()
	{
		// Show the spinner
		MUI.showSpinner();
		
		var myAjax = new Request.JSON(
		{
			url: this.adminUrl + 'media/init_thumbs_for_parent/' + this.parent + '/' + this.idParent,
			method: 'get',
			onSuccess: function(responseJSON, responseText)
			{
				MUI.notification(responseJSON.message_type, responseJSON.message );
				
				if (responseJSON.message_type == 'success')
				{
					this.loadMediaList('picture');
				}
			}.bind(this)	
		}).send();
	},
	
	
	/** 
	 * Opens fileManager
	 *
	 */
	toggleFileManager:function() 
	{
		// If no parent exists : don't show the filemanager but an error message	
		if ( ! this.idParent || this.idParent == '')
		{
			MUI.notification('error', Lang.get('ionize_message_please_save_first'));
		}
		else
		{
			switch (this.mode)
			{
				case 'filemanager': 
					mcImageManager.init({
						remove_script_host : false,
						iframe : false
					});
					mcImageManager.open('fileManagerForm','hiddenFile', false, this.addMedia.bind(this));
					break;
				
				case 'ezfilemanager':
					
					var url = this.themeUrl + 'javascript/tinymce/jscripts/tiny_mce/plugins/ezfilemanager/ezfilemanager.php?type=file&sa=1';
					var xPos = (window.screen.availWidth/2) - (w/2);
					var yPos = 60;
					var config = 'width=750, height=450, left='+xPos+', top='+yPos+', toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, directories=no, status=no';
					var w = window.open(url, 'filemanager', config);
					w.focus();
					break;

                case 'kcfinder':

                    var url = this.themeUrl + 'javascript/kcfinder/browse.php?type=pictures&lng='+Lang['current'];+'&noselect=1&opener=custom';
                    var xPos = (window.screen.availWidth/2) - (750/2);
                    var yPos = window.screen.availHeight/4;
                    var config = 'width=750, height=450, left='+xPos+', top='+yPos+', toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, directories=no, status=no';
                    window.KCFinder = {};
                    window.KCFinder.media = this;
                    window.KCFinder.callBack = function(url) {
                        window.KCFinder.media.addMedia(url);
                        window.KCFinder = null;
                    };
                    var w = window.open(url, 'kcfinder', config);
                    w.focus();
                    break;

				case 'mootools-filemanager':
					
					// Init of the FileManager is done in the desktop.php view.

					// Exit if another fileManager is already running
					if ($('filemanagerWindow'))
					{
						var inst = $('filemanagerWindow').retrieve('instance');
						
						// Re-open window if minimized or shake if triing to open another FM
						if (inst.isMinimized) 
						{
							inst.restore();
						}
						else
						{
							$('filemanagerWindow').shake(); 
						}

						return;
					}

					// Referer to ionizeMediaManager
					var self = this;
					var baseUrl = this.baseUrl;
					var adminUrl = this.adminUrl;
					
					var themeUrl = this.themeUrl;
					
					
					// First try to get a tokken : The tokken is only returned if the user is connected.
					var xhr = new Request.JSON(
					{
						url: this.adminUrl + 'media/get_tokken',
						method: 'post',
						onSuccess: function(responseJSON, responseText)
						{
							// Open the filemanager if the tokken isn't empty
							if (responseJSON && responseJSON.tokken != '')
							{
								filemanager = new FileManager({
									baseURL: baseUrl,
									url: adminUrl + 'media/filemanager',
									assetBasePath: themeUrl + 'javascript/mootools-filemanager/Assets',
									language: Lang.get('current'),
									selectable: true,
									hideOnClick: true,
									thumbSize: self.options.thumbSize,
									'onComplete': self.addMedia.bind(self),
									'uploadAuthData': responseJSON.tokken
								});
							
								// Display the filemanager
								filemanager.show();
							}
							else
							{
								MUI.notification('error', Lang.get('ionize_session_expired'));
							}
						}
					}, self).send();

					break;
				
				default : 
					MUI.notification('error', 'No mode set for mediaManager');
			}
		}
	}
	
});






/**
 * ION object extensions
 */
ION.extend({

	/**
	 * Reloads Ionize's interface
	 *
	 */
	reload: function(args)
	{
		window.top.location = this.baseUrl + args.url;
	},

	
	openFilemanager: function(callback)
	{
		var self = this;

		var xhr = new Request.JSON(
		{
			url: this.adminUrl + 'media/get_tokken',
			method: 'post',
			onSuccess: function(responseJSON, responseText)
			{
				// Open the filemanager if the tokken isn't empty
				if (responseJSON && responseJSON.tokken != '')
				{
				
					filemanager = new FileManager({
						baseURL: baseUrl,
						url: adminUrl + 'media/filemanager',
						assetBasePath: baseUrl + 'themes/admin/javascript/mootools-filemanager/Assets',
						language: Lang.get('current'),
						selectable: true,
						hideOnClick: true,
						onComplete: complete,
						'uploadAuthData': responseJSON.tokken
					});
				
					// Display the filemanager
					filemanager.show();
				}
				else
				{
					MUI.notification('error', Lang.get('ionize_session_expired'));
				}
			}
		}, self).send();
	},


	/**
	 * Add tree element to cookie
	 *
	 */
	treeAddToCookie: function(value)
	{
		var opened = Array();
		if (Cookie.read('tree'))
			opened = (Cookie.read('tree')).split(',');
		if (!opened.contains(value))
		{
			opened.push(value);
			Cookie.write('tree', opened.join(','));
		}
	},

	
	/**
	 * Remove tree elements from cookie
	 *
	 */
	treeDelFromCookie: function(value)
	{
		var opened = Array();
		if (Cookie.read('tree'))
			opened = (Cookie.read('tree')).split(',');
		if (opened.contains(value))
		{
			opened.erase(value);
			Cookie.write('tree', opened.join(','));
		}
	},


	/**
	 * Updates the given HTML DOM object ID with the source text on source value change
	 * 
	 * @param	String		HTML DOM object ID
	 * @param	String		HTML DOM object ID
	 * @param	Boolean		true if the lang code must be added HTML DOM object ID
	 
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
			if ($type(src) == 'string') {src = $(src)};
			if ($type(dest) == 'string') {dest = $(dest)};
			
			dest.set('html', src.value);
		}
	},
	
	
	/**
	 * Save one view
	 *
	editAreaSave: function(id, content)
	{
		MUI.showSpinner();
		
		var id = id.replace('edit_','');
		
		var data = 'view=' + $('view_' + id).value + '&path=' + $('path_' + id).value + '&content=' + content;
		
		new Request.JSON(
		{
			url: admin_url + 'setting/save_view',
			data: data,
			onSuccess: function(responseJSON, responseText)
			{
				MUI.hideSpinner();
	
				// Notification
				MUI.notification(responseJSON.message_type, responseJSON.message);
			},
			onFailure: function(xhr)
			{
				MUI.hideSpinner();
	
				// Error notification
				MUI.notification('error', xhr.responseJSON);
			
			}
		}).send();
	},
	 */
	
	
	/**
	 * Generates a random key
	 * @param	int		Size of the returned key
	 * @return	String	A random key
	 */
	generateKey: function(size)
	{
		var vowels = 'aeiouyAEIOUY';
		var consonants = 'bcdfghjklmnpqrstvwxzBCDFGHJKLMNPQRSTVWXZ1234567890@#$!()';
	 
		var key = '';

		var alt = Date.time() % 2;
		for (var i = 0; i < size; i++) {
			if (alt == 1) {
				key += consonants[(Number.rand() % (consonants.length))];
				alt = 0;
			} else {
				key += vowels[(Number.rand() % (vowels.length))];
				alt = 1;
			}
		}
		return key;
	},
	
	
	/** 
	 * Clears one form field
	 *
	 */
	clearField: function(field) 
	{
		if ($type($(field)) != false )
		{
			$(field).value = '';
			$(field).focus();
		}
	},

	checkUrl: function(url)
	{
		var RegexUrl = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
		return RegexUrl.test(url);
	}

});


Number.extend({

	/**
	 * Returns a random number 
	 * version: 1008.1718
	 * discuss at: http://phpjs.org/functions/rand    // +   original by: Leslie Hoare
	 *
	 */
	rand: function(min, max) {
		var argc = arguments.length;
		if (argc === 0) {
			min = 0;
			max = 2147483647;    } else if (argc === 1) {
			throw new Error('Warning: rand() expects exactly 2 parameters, 1 given');
	    }
		return Math.floor(Math.random() * (max - min + 1)) + min;
	}

});


Date.extend({
	
	/**
	 * Return current UNIX timestamp
	 * version: 1008.1718
	 * discuss at: http://phpjs.org/functions/time    // +   original by: GeekFG (http://geekfg.blogspot.com)
	 *
	 */
	time:function()
	{
		return Math.floor(new Date().getTime()/1000);
	}

});


