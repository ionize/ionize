/**
 *  Ionize Tree class
 *  Build each website structure tree
 * 
 *  @author	Partikule Studio
 *  @since	0.9.5
 * 
 */
ION.TreeXhr = new Class({

	Implements: [Events, Options],
	
	/**
	 * @constructor
	 *
	 * @param	id_container    string		HTML DOM Element ID which will contains the tree
	 * @param	id_menu         string		Menu ID
	 * @param	options         object		Options
	 *
	 */
	initialize: function(id_container, id_menu, options)
	{
		this.click_timer = null;
		this.id_container = id_container;
		this.container = $(id_container);
		this.container.store('tree', this);
		
		this.id_menu = id_menu;
		
		this.mainpanel = ION.mainpanel;
		
		// Array of itemManagers
		this.itemManagers = {'page': new Array(), 'article': new Array()};
		
		this.elementIcon_Model = new Element('div', {'class': 'tree-img drag'});
		this.plusMinus_Model = new Element('div', {'class': 'tree-img plus'});
		this.treeLine_Model = new Element('div', {'class': 'tree-img'});
		
		this.action_Model = new Element('span', {'class': 'action', 'styles': { 'display':'none' }});
		this.span_Model = new Element('span');
		this.title_Model = new Element('a', {'class': 'title'});
		
		this.opened = new Array();
		if (Cookie.read(this.id_container)) this.opened = (Cookie.read(this.id_container)).split(',');
		
		this.get(0);
	},

	/**
	 * Gets one tree
	 *
	 */
	get: function(id_parent)
	{
		var self = this;
		
		// Get childs pages and articles
		new Request.JSON({
			url: admin_url + 'tree/get', 
			method: 'post',
			loadMethod: 'xhr',
			data: {
				'id_menu': this.id_menu,
				'id_parent': id_parent
			},
			onSuccess: function(responseJSON, responseText)
			{
				var pages = responseJSON.pages;
				var articles = responseJSON.articles;

				var pageContainer = self.injectContainer('page', id_parent, true);
				var articleContainer = self.injectContainer('article', id_parent, true);

				// Build tree
				pages.each(function(page) {
					self.insertElement(page, 'page');
				});
				
				articles.each(function(article)
				{
					self.insertElement(article, 'article');
				});
				
				var parentContainer = self.getParentContainer(id_parent);
				
				// Set the item managers (see ION.ItemManager)
				var pid = pageContainer.retrieve('id');
				var aid = articleContainer.retrieve('id');

				self.itemManagers['page'][pid] = new ION.ItemManager({'container': pageContainer, 'element':'page', 'sortable':true });
				self.itemManagers['article'][aid] = new ION.ArticleManager({ 'container': articleContainer, 'id_parent': id_parent});

				// Stores that the content is loaded
				parentContainer.store('loaded', true);
		
				// Opens the folder
				if (id_parent != 0)
					self.updateOpenClose(parentContainer);
				
				// Fires the event
				self.fireEvent('get', self);
			}
		}).send();
	},
	
	/**
	 * Inject or return an existing container for elements (page or articles)
	 *
	 */
	injectContainer: function(type, id_parent, erase)
	{
		var parentContainer = this.getParentContainer(id_parent);
		var container = parentContainer.getFirst('ul.' + type + 'Container');

		// Force new container
		if (erase == true)
		{
			if (typeOf(container) != 'null')
			{
				var cid = container.retrieve('id');
				container.dispose();
				container = null;
				if (typeOf(this.itemManagers[type][cid]) != 'null')
					delete this.itemManagers[type][cid];
			}
		}

		if (typeOf(container) == 'null')
		{
			container = new Element('ul', {'rel': id_parent});				
			
			// Root class
			if (id_parent == '0')
			{
				container.addClass('tree');
				container.store('id', type + 'ContainerTree' + this.id_menu);
			}
			else
			{
				container.store('id', type + 'Container' + id_parent);
			}

			container.addClass(type + 'Container');

			// Try to inject page container before the article container, else at the bottom
			var injected = false;
/*
			if (type == 'page')
			{

				if (typeOf($('articleContainer' + id_parent)) != 'null')
				{
console.log('inject page before articles');
					container.inject($('articleContainer' + id_parent), 'before');
					injected = true;
				}
			}
			
			if (injected == false)
			{
				container.inject(parentContainer, 'bottom');
			}
*/
			container.inject(parentContainer, 'bottom');

			// Hide the parentContainer if it should be, but not for the root.
			if (id_parent != 0)
				if ( ! (parentContainer.hasClass('f-open'))) { container.setStyle('display', 'none');}
		}

		return container;
	},
	
	/**
	 * Inserts one element (page / article) in a container (UL)
	 *
	 */
	insertElement: function(element, type)
	{
		// Inject or get the container
		var self = this;
		var id = (type == 'page') ? element.id_page : element.id_article;
		var id_parent = (type == 'page') ? element.id_parent : element.id_page;
		
		var flat_id = (type == 'page') ? element.id_page : element.id_page + 'x' + element.id_article;
		var rel = (type == 'page') ? element.id_page : element.id_page + '.' + element.id_article;
		var online = (element.online == '1') ? 'online' : 'offline'; 
		
		var title = (typeOf(element.nav_title) != 'null' && element.nav_title != '') ? element.nav_title : element.title;
		var type_description = (typeOf(element.type_description) != 'null' && element.type_description != '') ? ' : ' + element.type_description : '';

		if (title == '') title = element.name;
		
		var container = this.injectContainer(type, id_parent);

		// Element
		var li = new Element('li').setProperty('id', this.id_container + '_' + type + '_' + flat_id).addClass(online).addClass(type + flat_id).setProperty('rel', rel);
		li.store('loaded', false);
		li.store('id_' + type, id);
		
		// Action element
		var action = this.action_Model.clone();
        var iconOnline = new Element('a').addClass('icon').addClass('status').addClass(online).addClass(type + flat_id).setProperty('rel', rel);
        action.adopt(iconOnline);
		
		// Title element 
		var link = this.span_Model.clone().addClass('title');
		var a = this.title_Model.clone()
					.addClass(online).addClass(type + flat_id).addClass('title')
					.setProperty('rel', rel).setProperty('title', title + type_description).setProperty('data-type', type).setProperty('data-id', id)
					.set('text', String.htmlspecialchars_decode(title));
		link.adopt(a);
		li.adopt(action, link);

		this.addTitleClickEvent(li, type);
		
		// Icon
		var icon = this.elementIcon_Model.clone();
		icon.inject(li, 'top');
		
		// Page
		if (type == 'page')
		{
			li.addClass('folder').addClass('page');
			
			// Icons : Add Article, Add page
			action.adopt(new Element('a').addClass('addArticle').addClass('icon').addClass('article').addClass('add').setProperty('rel', rel));
			action.adopt(new Element('a').addClass('addPage').addClass('icon').addClass('page').addClass('add').setProperty('data-page', id).setProperty('data-menu', element.id_menu));

            // Actions
			this.addPageActionLinks(action);

			// Folder icon
			icon.addClass('folder');
			if (element.appears == '0') icon.addClass('hidden');

			// if home page, remove home from the old home page
			if (element.home == '1')
			{
				this.container.getElements('.folder.home').removeClass('home');
				icon.addClass('home');
			}
			
			// plus / minus icon
			var pm = this.plusMinus_Model.clone().addEvent('click', this.openclose.bind(this)).inject(li, 'top');

			// Make the title draggable : Move page
			ION.addDragDrop(a, '.dropPageAsLink,.dropPageInArticle', 'ION.dropPageAsLink,ION.dropPageInArticle');

			li.inject(container, 'bottom');
		}
		// Article
		else
		{
			li.addClass('file').addClass(type + id);
			
			// Icon : unlink
			if (Ionize.User.getGroupLevel() > 5000)
			{
                var iconUnlink = new Element('a', {'class': 'icon unlink', 'rel': rel});
                action.adopt(iconUnlink);
			}

			// File icon
			if (element.indexed == '0') icon.addClass('sticky');
			else icon.addClass('file');
			
			// Link icon
			if (element.link != '') icon.addClass('link');
			
			// Flag span : User's flag first, then Type flag
			var flag = (element.flag == '0' && element.type_flag != '') ? element.type_flag : element.flag;
			var span = new Element('span', {'class':'flag flag' + flag}).inject(a, 'top');
			if ((flag != '' || flag!='0') && Browser.ie7) a.setStyle('padding-left','6px');
			
			// Item node line
			this.treeLine_Model.clone().inject(li, 'top').addClass('line').addClass('node');

			// Make the article name draggable
			ION.addDragDrop(a, '.folder,.dropArticleAsLink,.dropArticleInPage', 'ION.dropArticleInPage,ION.dropArticleAsLink,ION.dropArticleInPage');

			// Inject LI at the correct position
			var lis = container.getChildren('li');
			if (element.ordering == '1')
			{
				li.inject(container, 'top');
			}
			else
			{
				if (typeOf(lis[element.ordering -2]) != 'null')
					li.inject(lis[element.ordering -2], 'after');
				else
					li.inject(container, 'bottom');
			}
		}

		// Get the parent : Build tree lines (nodes)
		li.getParents('li').each(function(parent){
			self.treeLine_Model.clone().inject(li, 'top');
		});

		// Makes the folder sortable (on folder icon)
		if (typeOf(container.retrieve('sortables')) != 'null')
		{
			(container.retrieve('sortables')).addItems(li);
		}

		// The element was dynamically inserted through XHR
		if (typeOf(element.inserted) != 'null')
		{
			var cid = container.retrieve('id');
			if (typeOf(this.itemManagers[type][cid]) != 'null')
				(this.itemManagers[type][cid]).init();
		}
		
		// Mouse over effect : Show / Hide actions
		this.addMouseOver(li);
		
		// Open the folder if cookie says...
		if (type == 'page' && this.opened.contains(id))
		{
			this.get(id);
		}
	},

	/**
	 * Updates one element in the Tree
	 * @param element
	 * @param type
	 */
	updateElement:function(element, type)
	{
		var id = (type == 'page') ? element.id_page : element.id_article;
		var selector = (type == 'page') ? '.folder.' + type + id : '.file.' + type + id;
		var status = (element.online == '1') ? 'online' : 'offline';
		var title = (typeOf(element.nav_title) != 'null' && element.nav_title != '') ? element.nav_title : element.title;
		if (title == '') title = element.name;

		// Items to update
		var items = this.container.getElements(selector);

		// Page
		if (type == 'page')
		{
			this.container.getElements('.folder').removeClass('home');
		}


		// Common updates
		items.each(function(item)
		{
			// Title
			item.getElement('a.title').set('text', title);

			// Status
			item.removeClass('offline').removeClass('online').addClass(status);

			// Page
			if (type == 'page')
			{
				// Home page icon
				var home_page = (element.home && element.home == '1') ? true : false;
				if (home_page)
				{
					item.getFirst('.folder').addClass('home');
				}

				// Displayed in navigation ?
				item.getFirst('.folder').removeClass('hidden');
				if (element.appears == '0')
				{
					item.getFirst('.folder').addClass('hidden');
				}
			}
		});

	},

	/**
	 * Plus / Minus folder icon click event	
	 *
	 */
	openclose:function(e)
	{
		if (typeOf(e.stop) == 'function') e.stop();
		el = e.target;
		
		// Folder (LI)
		var folder = el.getParent('li');

		// Update content : XHR
		if (folder.retrieve('loaded') == false)
			this.get(folder.retrieve('id_page'));
		else
			this.updateOpenClose(folder);
	},
	
	/**
	 * Displays / Hides content, updates Plus / Minus icons
	 *
	 *
	 */
	updateOpenClose: function(folder)
	{
		// All childrens UL
		var folderContents = folder.getChildren('ul');
		var folderIcon = folder.getChildren('div.folder');
		
		// Is the folder Open ? Yes ? Close it (Hide the content)
		if (folder.hasClass('f-open'))
		{
			var pmIcon = folder.getFirst('div.tree-img.minus');
			pmIcon.addClass('plus').removeClass('minus');
			
			folderIcon.removeClass('open');
			folderContents.each(function(ul){ ul.setStyle('display', 'none');});
			folder.removeClass('f-open');
			
			ION.listDelFromCookie(this.id_container, folder.retrieve('id_page'));
		}
		else
		{
			var pmIcon = folder.getFirst('div.tree-img.plus');
			pmIcon.addClass('minus').removeClass('plus');
		
			folderIcon.addClass('open');
			folderContents.each(function(ul){ ul.setStyle('display', 'block'); });
			folder.addClass('f-open');
			
			ION.listAddToCookie(this.id_container, folder.retrieve('id_page'));

			$('btnStructureExpand').store('status', 'expand');
			$('btnStructureExpand').value = Lang.get('ionize_label_collapse_all');
		}
	},

	/**
	 * Adds a link to a tree LI DOM element
	 *
	 * @param	DOMElement		tree LI
	 * @param	String			logical tree element type. "page" or "article"
	 *
	 */
	addTitleClickEvent: function(el, type)
	{
		var a = el.getElement('a.title');
		var self = this;
		
		a.addEvents(
		{
			'click': function(e)
			{
				clearTimeout(self.click_timer);
				self.click_timer = self.relaySingleOrDoubleClick.delay(700, self, [e, self, a, type, 1]);		
			},
			'dblclick': function(e)
			{
				clearTimeout(self.click_timer);
				self.click_timer = self.relaySingleOrDoubleClick.delay(0, self, [e, self, a, type, 2]);		
			}
		});		
	},
	
	relaySingleOrDoubleClick: function(e, self, el, type, clicks)
	{
		// IE7 / IE8 event problem
		if( ! Browser.ie)
			if (e) e.stop();
		
		// Open page
		if (clicks === 2 && type == 'page')
		{
			self.openclose(e);
		}
		// Edit Element
		else
		{
			ION.splitPanel({
				'urlMain': admin_url + type + '/edit/' + el.getProperty('rel'),
				'urlOptions': admin_url + type + '/get_options/' + el.getProperty('rel'),
				'title': Lang.get('ionize_title_edit_' + type) + ' : ' + el.get('text')
			});
		}
	},
	
	getParentContainer: function(id_parent)
	{
		// Parent DOM Element (usually a folder LI)
		var parentEl = this.id_container + '_page_' + id_parent;

		if (typeOf($(parentEl)) == 'null')
			parentEl = this.container;
		else
			parentEl = $(parentEl);

		return parentEl;
	},

	/**
	 * Adds Actions Buttons Events on one page Element
	 *
	 * @param	DOMElement		tree LI
	 *
	 */
	addPageActionLinks: function(el)
	{
		// Add Article
		var a = el.getElement('a.addArticle');
		var id = a.rel;
		var p = $(this.mainpanel);
		
		// Add "Create Article" icon
		a.addEvent('click', function(e)
		{
			e.stop();
			MUI.Content.update({
				'element': p,
				'url': admin_url + 'article/create/' + id,
				'title': Lang.get('ionize_title_create_article')
			});
		});

		// Add Page
		a = el.getElement('a.add.page');
		a.addEvent('click', function(e)
		{
			e.stop();
			MUI.Content.update({
				'element': p,
				'url': admin_url + 'page/create/' + a.getProperty('data-menu') + '/' + a.getProperty('data-page'),
				'title': Lang.get('ionize_title_create_page')
			});
		});
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
	}
});





/**
 *  Ionize Browser Tree class
 *  Build the website structure tree for one tree browser usage
 *
 *  @author	Partikule Studio
 *  @since	0.9.9
 *
 */
ION.BrowserTreeXhr = new Class({

	Extends: ION.TreeXhr,

	/**
	 * Dedicated method for Browser tree : Different actions, no edit on title
	 *
	 * @param element
	 * @param type
	 */
	insertElement: function(element, type)
	{
		// Inject or get the container
		var self = this;
		var id = (type == 'page') ? element.id_page : element.id_article;
		var id_parent = (type == 'page') ? element.id_parent : element.id_page;

		var flat_id = (type == 'page') ? element.id_page : element.id_page + 'x' + element.id_article;
		var rel = (type == 'page') ? element.id_page : element.id_page + '.' + element.id_article;
		var online = (element.online == '1') ? 'online' : 'offline';

		var title = (typeOf(element.nav_title) != 'null' && element.nav_title != '') ? element.nav_title : element.title;
		var type_description = (typeOf(element.type_description) != 'null' && element.type_description != '') ? ' : ' + element.type_description : '';

		if (title == '') title = element.name;

		var container = this.injectContainer(type, id_parent);

		// Element
		var li = new Element('li').setProperty('id', this.id_container + '_' + type + '_' + flat_id).addClass(online).addClass(type + flat_id).setProperty('rel', rel);
		li.store('loaded', false);
		li.store('id_' + type, id);

		// Action element
		var action = this.action_Model.clone();

		// Link Icon
		if (Ionize.User.getGroupLevel() > 5000)
		{
			var iconLink = new Element('a', {'class': 'icon link', 'rel': rel});

			iconLink.addEvent('click', function(e)
			{
				e.stop();
				clearTimeout(self.click_timer);
				self.click_timer = self.relaySingleOrDoubleClick.delay(700, self, [e, self, iconLink, type, 1]);
			});
			action.adopt(iconLink);
		}

		// Title element
		var link = this.span_Model.clone().addClass('title');
		var a = this.title_Model.clone()
			.addClass(online).addClass(type + flat_id).addClass('title')
			.setProperty('rel', rel).setProperty('title', title + type_description).setProperty('data-type', type).setProperty('data-id', id)
			.set('text', String.htmlspecialchars_decode(title));
		link.adopt(a);
		li.adopt(action, link);

		this.addTitleClickEvent(li, type);

		// Icon
		var icon = this.elementIcon_Model.clone();
		icon.inject(li, 'top');

		// Page
		if (type == 'page')
		{
			li.addClass('folder').addClass('page');

			// Folder icon
			icon.addClass('folder');
			if (element.appears == '0') icon.addClass('hidden');

			// if home page, remove home from the old home page
			if (element.home == '1')
			{

				$$('.folder.home').removeClass('home');
				icon.addClass('home');
			}

			// plus / minus icon
			var pm = this.plusMinus_Model.clone().addEvent('click', this.openclose.bind(this)).inject(li, 'top');

			li.inject(container, 'bottom');
		}
		// Article
		else
		{
			li.addClass('file').addClass(type + id);


			// File icon
			if (element.indexed == '0') icon.addClass('sticky');
			else icon.addClass('file');

			// Link icon
			if (element.link != '') icon.addClass('link');

			// Flag span : User's flag first, then Type flag
			var flag = (element.flag == '0' && element.type_flag != '') ? element.type_flag : element.flag;
			var span = new Element('span', {'class':'flag flag' + flag}).inject(a, 'top');
			if ((flag != '' || flag!='0') && Browser.ie7) a.setStyle('padding-left','6px');

			// Item node line
			this.treeLine_Model.clone().inject(li, 'top').addClass('line').addClass('node');

			// Inject LI at the correct position
			var lis = container.getChildren('li');
			if (element.ordering == '1')
			{
				li.inject(container, 'top');
			}
			else
			{
				if (typeOf(lis[element.ordering -2]) != 'null')
					li.inject(lis[element.ordering -2], 'after');
				else
					li.inject(container, 'bottom');
			}
		}

		// Get the parent : Build tree lines (nodes)
		li.getParents('li').each(function(parent){
			self.treeLine_Model.clone().inject(li, 'top');
		});

		// Mouse over effect : Show / Hide actions
		this.addMouseOver(li);

		// Open the folder if cookie says...
		if (type == 'page' && this.opened.contains(id))
		{
			this.get(id);
		}
	},


	relaySingleOrDoubleClick: function(e, self, el, type, clicks)
	{
		// IE7 / IE8 event problem
		if( ! Browser.ie)
			if (e) e.stop();

		// Open page
		if (clicks === 2 && type == 'page')
		{
			self.openclose(e);
		}
		// Edit Element
		else
		{
			var rel = el.getProperty('rel');

			// Get the window container
			var windowContainer = this.container.getParent('.mocha');

			// If one container, fire the select Event
			if (typeOf(windowContainer) != 'null')
			{
				// Get the MUI window instance
				var win = windowContainer.retrieve('instance');
				win.fireEvent('select', [rel]);
			}
			else
			{
				// Fire self.select Event
				self.fireEvent('select', [rel]);
			}
		}
	}
});