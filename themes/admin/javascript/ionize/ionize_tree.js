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
		
		this.mainpanel = ION.mainpanel;

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
			folderContents.each(function(element)
			{
				var docs = element.getChildren('li.doc').append(element.getChildren('li.sticky'));
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
				ION.addDragDrop(title, '.folder,.dropArticleAsLink,.dropArticleInPage', 'ION.dropArticleInPage,ION.dropArticleAsLink,ION.dropArticleInPage');
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
		var a = el.getElement('a.title');
		var rel = (a.getProperty('rel')).split(".");
		var id = rel[0];
		
		// id is the last rel
		if (rel.length > 1) { id = rel[1]; }

		var p = $(this.mainpanel);
//console.log(p);
		a.addEvent('click', function(e)
		{
			e.stop();
			
			MUI.Content.update({
				'element': p,
				'url': admin_url + type + '/edit/' + a.getProperty('rel'),
				'title': Lang.get('ionize_title_edit_' + type) + ' : ' + a.get('text')	
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
		var p = $(this.mainpanel);
		
		// Add article icon
		a.addEvent('click', function(e)
		{
			e.stop();
			MUI.Content.update({
				'element': p,
				'url': admin_url + 'article/create/' + id,
				'title': Lang.get('ionize_title_create_article')
			});
		});
		
		// Online / Offline icon
		a = el.getElement('a.status');
		ION.initPageStatusEvent(el.getElement('a.status'));
	},

	
	addArticleActionLinks: function(el)
	{
		// Add the Status Event
		ION.initArticleStatusEvent(el.getElement('a.status'));

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
			
			// Article Manager : Sortables
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
				// ION.notification('', containerName);
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


ION.append({


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
	}
	
});
