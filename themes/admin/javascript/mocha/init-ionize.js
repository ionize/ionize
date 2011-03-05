/** 
 * Ionize UI Columns Init 
 *
 */
var initializeColumns = function() {

	/* 
	 * Main UI options	
	 *
	 */
	
	// Windows Corner radius
	windowOptions = MUI.Windows.windowOptions;
	windowOptions.cornerRadius = 0;
	
	// Windows Shadows
	// windowOptions.shadowBlur = 5;	
	MUI.Window.implement({ options: windowOptions });
	
	/*
	 * Create Columns
	 *	 
	 */	 
	new MUI.Column({
		id: 'sideColumn',
		placement: 'left',
		sortable: false,
		width: 280,
		resizeLimit: [222, 600]
	});

	new MUI.Column({
		id: 'mainColumn',
		placement: 'main',	
		sortable: false,
		resizeLimit: [100, 500],
		evalScripts: true
	});

	// Add Site structure panel to side column
	new MUI.Panel({
		id: 'structurePanel',
//		title: Lang.get('ionize_title_structure'),
		title: '',
		loadMethod: 'xhr',
		contentURL: admin_url + 'core/get_structure',
		column: 'sideColumn',
		panelBackground:'#f2f2f2',
		padding: { top: 15, right: 0, bottom: 8, left: 15 },
		headerToolbox: true,
		headerToolboxURL: admin_url + 'core/get/toolboxes/structure_toolbox',
		headerToolboxOnload: function(){

			// ToggleHeader Button
			$('toggleHeaderButton').addEvent('click', function(e)
			{
				e.stop();
				var cn = 'desktopHeader';
				var el = $(cn);
				var opened = 'true';
				
				if (Cookie.read(cn))
				{
					opened = (Cookie.read(cn));
				}
				if (opened == 'false')
				{
					Cookie.write(cn, 'true');
					el.show();
				}
				else
				{
					Cookie.write(cn, 'false');
					el.hide();
				}

//				$('desktopHeader').toggle();
				window.fireEvent('resize');
			});
			
			// Init desktopHeader status from cookie
			var dh = $('desktopHeader');
			var opened = (Cookie.read('desktopHeader'));
			if (opened == 'false') {dh.hide();}
			else {dh.show();} 
			window.fireEvent('resize');
		}
	});

	// Add Info panel to side column
/*
	new MUI.Panel({
		id: 'infoPanel',
		title: 'Debug',
		loadMethod: 'xhr',
		contentURL: base_url + 'admin/core/get_info',
		column: 'sideColumn',
		panelBackground: '#fff',
			padding: { top: 15, right: 15, bottom: 8, left: 15 },
		onContentLoaded: function(c) 
		{
//			log = new Log('debug');
		}		
		
	});
*/

	// Add panels to main column	
	new MUI.Panel({
		id: 'mainPanel',
		title: Lang.get('ionize_title_welcome'),
		loadMethod: 'xhr',
		contentURL: admin_url + 'dashboard',
		padding: { top: 15, right: 15, bottom: 8, left: 15 },
		addClass: 'pad-maincolumn',
		column: 'mainColumn',
		collapsible: false,
		panelBackground: '#fff',
		headerToolbox: true,
		headerToolboxURL: admin_url + 'core/get/toolboxes/empty_toolbox'
	});

	MUI.myChain.callChain();
}


/** 
 * Ionize UI Windows Init 
 *
 */
var initializeWindows = function(){


	MUI.hideSpinner = function()
	{
		if ($('spinner')) $('spinner').hide();
	}

	MUI.showSpinner = function()
	{
		if ($('spinner')) $('spinner').show();
	}

	/**
	 * Ionize notification window
	 * Launch a notification window creation
	 *
	 * @param	string 	type of notification. Can be : error, notice, success
	 * @param	string	Notification message
	 */
	MUI.notification = function(type, message)
	{
		new MUI.Window({
			loadMethod: 'html',
			closeAfter: 2500,
			type: 'notification',
			addClass: 'notification ',
			content: '<div class="'+ type +'">' + message + '</div>',
			width: 350,
			height: 50,
			y: 1,
			padding:  { top: 15, right: 12, bottom: 10, left: 12 },
			shadowBlur: 5,
			bodyBgColor: [250, 250, 250],
			contentBgColor: '#e5e5e5'
		});
	}
	
	/**
	 * Ionize Add Confirmation modal window
	 *
	 * @param	string		Window ID
	 * @param	string		Button or any element ID on wich add the link
	 * @param	string		URL or JS function called in case of user confirmation
	 * @param	string		Element name to update after the request success
	 *
	 */
	MUI.addConfirmation = function(id, button, callback, msg, options)
	{
		$(button).addEvent('click', function(e)
		{
			var e = new Event(e).stop();
			MUI.confirmation(id, callback, msg, options);
		});	
	}

	/**
	 * Ionize Confirmation modal window
	 * Opens a windows with yes / no buttons
	 *
	 * @param	string		Window ID
	 * @param	string		URL or Callback JS function to call if yes answer
	 * @param	string		Message
	 * @param	object		Window extended options
	 *
	 */
	MUI.confirmation = function(id, callback, msg, wOptions)
	{
		// Get the buttons container
		wButtons = MUI._getConfirmationButtons(id, callback);

		// Window question message
		var wMsg = (Lang.get(msg)) ? Lang.get(msg) : msg ;

		// Message HTML Element & window content container		
		var wMessage = new Element('div', {'class':'message'}).set('text', wMsg);		// Message
		var wContent = new Element('div').adopt(wMessage, wButtons);					// Windows content final container

		// Window options
		var options = {
			id: 'w' + id,
			content: wContent,
			title: Lang.get('ionize_modal_confirmation_title'),
			addClass:'confirmation',
			draggable: true,
			y: 150,
			padding: { top: 15, right: 15, bottom: 8, left: 15 }			
		}

		// Extends the window options
		if (wOptions) {$extend(options, wOptions);}
		
		// Open the confirmation modal window
		new MUI.Modal(options);
	}

	/**
	 * Modal windows
	 *
	 */
	MUI.error = function(msg, wOptions)
	{
		var options = MUI._getModalOptions('error', msg);
		new MUI.Modal(options);		
	}

	MUI.alert = function(msg, wOptions)
	{
		var options = MUI._getModalOptions('alert', msg);
		new MUI.Modal(options);		
	}

	MUI.information = function(msg, wOptions)
	{
		var options = MUI._getModalOptions('information', msg);
		new MUI.Modal(options);		
	}

	
	/**
	 * Ionize generic form window
	 * Use to load a window which contains a form 
	 *
	 * @param	string		Window ID
	 * @param	string		Window Form ID
	 * @param	string		Lang translation key or string as title of the window
	 * @param	string		URL called in case of form validation
	 * @param	object		Window extended options
	 *
	 */
	MUI.formWindow = function(id, form, title, wUrl, wOptions)
	{
		// Cleans URLs
		wUrl = MUI.cleanUrl(wUrl);

		var options  = 
		{
			id: 'w' + id,
			title: (typeof(Lang.get(title)) == 'undefined') ? title : Lang.get(title),
			loadMethod: 'xhr',
			contentURL: admin_url + wUrl,
			onContentLoaded: function(c)
			{
				// Get the form action URL and adds 'true' so the transport is set to XHR
				var formUrl = $(form).getProperty('action') + '/true';

				// Set the form submit button action and send the DOMElement to update with the according URL
				MUI.setFormSubmit(form, ('bSave' + id), formUrl);

				// Add the cancel event if cancel button exists
				// All buttons name starts with 'b'
				if (bCancel = $('bCancel' + id))
				{
					bCancel.addEvent('click', function(e)
					{
						var e = new Event(e).stop();
						MUI.closeWindow($('w' + id));
					});
				}
				
				// Event on save button
				if (bSave = $('bSave' + id))
				{
					bSave.addEvent('click', function(e)
					{
						var e = new Event(e).stop();
						MUI.closeWindow($('w' + id));
					});
				}
				
				// Window resize
				if (wOptions.resize == true)
				{
					// windows content size
					var cs = $('w' + id + '_content').getSize();
					
					// main panel content size
					var mps = $('mainPanel').getSize();
					
					if ((cs.y + 80) < mps.y)
					{
						$('w' + id).retrieve('instance').resize({height: cs.y + 10, width: cs.x, centered:true, top:70 });
					}
					else
					{
						$('w' + id).retrieve('instance').resize({height: mps.y - 30, width: cs.x, centered:true, top:70 });						
					}
				}
			},
			y: 70,
			padding: { top: 12, right: 12, bottom: 10, left: 12 },
			maximizable: false,
			contentBgColor: '#fff'			
		};
		
		// Extends the window options
		if (wOptions) {$extend(options, wOptions);}
		
		// Window creation
		new MUI.Window(options);
	}

	/**
	 * Opens a data window, without buttons
	 * Usefull for editing a list
	 *
	 */
	MUI.dataWindow = function(id, title, wUrl, wOptions)
	{
		// Cleans URLs
		wUrl = MUI.cleanUrl(wUrl);

		var options  = 
		{
			id: 'w' + id,
			title: (typeof(Lang.get(title)) == 'undefined') ? title : Lang.get(title),
			loadMethod: 'xhr',
			contentURL: admin_url + wUrl,
			evalResponse: true,
			width: 310,
			height: 130,
			y: 80,
			padding: { top: 12, right: 12, bottom: 10, left: 12 },
			maximizable: false,
			contentBgColor: '#fff'			
		};
		
		// Extends the window options
		if (wOptions) {$extend(options, wOptions);}
		
		// Window creation
		return new MUI.Window(options);
	}
	

	MUI._getModalOptions = function(type, msg)
	{
		// Window message
		var wMsg = (Lang.get(msg)) ? Lang.get(msg) : msg ;
	
		var btnOk = new Element('button', {'class':'button yes right mr35'}).set('text', Lang.get('ionize_button_ok'));

		var button = new Element('div', {'class':'buttons'}).adopt(btnOk);

		// Message HTML Element & window content container		
		var wMessage = new Element('div', {'class':'message'}).set('text', wMsg);
		var wContent = new Element('div').adopt(wMessage, button);

		// Window options
		var id = new Date().getTime();
		var options = {
			id: 'w' + id,
			content: wContent,
			title: Lang.get('ionize_modal_' + type + '_title'),
			addClass: type,
			draggable: true,
			y: 150,
			padding: { top: 15, right: 15, bottom: 8, left: 15 }			
		}

		// Event on btn No : Simply close the window
		btnOk.addEvent('click', function() 
		{
			MUI.closeWindow($('w' + id));
		}.bind(this));
		
		return options;
	}
	
	/**
	 * Returns the buttons yes / no HTMLDOMElement
	 *
	 * @param	string		Window ID (to link with the close button)
	 * @param	string		URL or Callback JS function to call if yes answer
	 * @param	string		Element to update after url completion
	 * @param	string		URL of the update element
	 *
	 */
	MUI._getConfirmationButtons = function(id, callback)
	{
		// Btn Yes / No creation
		var btnYes = new Element('button', {'class':'button yes right mr35'}).set('text', Lang.get('ionize_button_confirm'));
		var btnNo = new Element('button', {'class':'button no '}).set('text', Lang.get('ionize_button_cancel'));
	
		// Event on btn No : Simply close the window
		btnNo.addEvent('click', function() 
		{
			MUI.closeWindow($('w' + id));
		}.bind(this));

		// Event on btn Yes
		btnYes.addEvent('click', function()
		{
			/*
			 * Check if callback is an  URL or a JS callback function
			 * No RegExp check on URL because some URL can be passed without "http://"
			 * if fact you wish to use a regexp : var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
			 * An URL is supposing containing "/"
			 * Case URL : 		Form sending
			 * Case Callback : 	Execution of callback function
			 *
			 */
			
			// URL case
			if ( (callback + '').indexOf('/') > -1 )
			{
				// Send the standard form object
				MUI.sendForm(callback);
			}
			// Callback case
			else
			{
				callback();
			}

			// Close the modal window
			MUI.closeWindow($('w' + id));
			
			
		}.bind(this));
	
		// Buttons container
		return new Element('div', {'class':'buttons'}).adopt(btnYes, btnNo)
	}
	
	MUI.myChain.callChain();
}


/** 
 * Ionize UI Menu Init 
 *
 */
var initializeMenu = function(){

	// Default padding
	var default_padding= { top: 12, right: 15, bottom: 8, left: 15 };


	// Dashboard link...
	if ($('dashboardLink')){ 
		$('dashboardLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_welcome'),
				url : admin_url + 'dashboard'		
			});
		});
	}

	$('logoAnchor').addEvent('click', function(e){
		$('dashboardLink').fireEvent('click',e);
	});

	// Content : Manage Menu ...
	if ($('menuLink')){ 
		$('menuLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_menu'),
				url : admin_url + 'menu'		
			});
		});
	}

	// Content : New Page...
	if ($('newPageLink')){ 
		$('newPageLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_new_page'),
				url : admin_url + 'page/create/0'		
			});
		});
	}

	// Content : Articles
	if ($('articlesLink')){ 
		$('articlesLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_articles'),
				url : admin_url + 'article/list_articles'		
			});
		});
	}

	// Translations
	if ($('translationLink')){ 
		$('translationLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_translation'),
				url : admin_url + 'translation/'
			});
		});
	}

	// Content : Media manager
	if ($('mediaManagerLink')){ 
		$('mediaManagerLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_menu_media_manager'),
				url : admin_url + 'media/get_media_manager',
				padding: {top:0, left:0, right:0}
			});
		});
	}

	// Content : Extended fields
	if ($('extendfieldsLink')){ 
		$('extendfieldsLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_menu_extend_fields'),
				url : admin_url + 'extend_field/index'
			});
		});
	}


	// Modules : List
	if ($('modulesLink')){ 
		$('modulesLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_modules'),
				url : admin_url + 'modules/'
			});
		});
	}
	
	// Themes
	if ($('themesLink')){ 
		$('themesLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_theme'),
				url : admin_url + 'setting/themes/'
			});
		});
	}
	
	// Settings : Ionize
	if ($('ionizeSettingLink')){ 
		$('ionizeSettingLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_menu_ionize_settings'),
				url : admin_url + 'setting/ionize'
			});
		});
	}

	// Settings : Global Website settings
	if ($('settingLink')){ 
		$('settingLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_menu_site_settings_global'),
				url : admin_url + 'setting'
			});
		});
	}

	// Settings : Technical settings
	if ($('technicalSettingLink')){ 
		$('technicalSettingLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_menu_site_settings_technical'),
				url : admin_url + 'setting/technical'
			});
		});
	}

	// Settings : Languages...
	if ($('languagesLink')){ 
		$('languagesLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_menu_languages'),
				url : admin_url + 'lang'
			});
		});
	}

	// Settings : Users...
	if ($('usersLink')){ 
		$('usersLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_menu_users'),
				url : admin_url + 'users'
			});
		});
	}

	// Documentation link
	/*
	if ($('docLink')){ 
		$('docLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				loadMethod: 'iframe',
				title: Lang.get('ionize_title_documentation'),
				url : base_url + '../user_guide/index.html',
				padding: {top:0, left:0, right:0}
			});
		});
	}
	*/


	// About
	MUI.aboutWindow = function() {
		new MUI.Modal({
			id: 'about',
			title: 'MUI',			
			contentURL: admin_url + 'desktop/get/about',
			type: 'modal2',
			width: 360,
			height: 210,
			y:200,
			padding: { top: 70, right: 12, bottom: 10, left: 22 },
			scrollbars: false
		});
	}
	if ($('aboutLink')) {
		$('aboutLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.aboutWindow();
		});
	}


	// Deactivate menu header links
	$$('a.returnFalse').each(function(el){
		el.addEvent('click', function(e){
			new Event(e).stop();
		});
	});

	MUI.myChain.callChain();

}


/** 
 * Ionize UI Forms Init 
 *
 */
var initializeForms = function() 
{

	/**
	 * Returns the Ionize form object
	 *
	 * @param	string		URL to send the form data. With or without the base URL prefix. Will be cleaned.
	 * @param	mixed		Form data
	 */
	MUI.getFormObject = function(url, data)
	{
		if (!data) {
			data = '';
		}

		// Cleans URLs
		url = MUI.cleanUrl(url);

		return {
			url: admin_url + url, 
			method: 'post',
			loadMethod: 'xhr',
			data: data,
			onRequest: function()
			{
				MUI.showSpinner();
			},
			onFailure: function(xhr) 
			{
				MUI.hideSpinner();

				// Error notification
				MUI.notification('error', xhr.responseJSON);
			},
			onSuccess: function(responseJSON, responseText)
			{
				MUI.hideSpinner();
				
				// Update the elements transmitted through JSON
				if (responseJSON && responseJSON.update)
				{
					// Updates all the elements in the update array
					// look at init-content.js for more details
					MUI.updateElements(responseJSON.update);
				}

				// JS Callback
				if (responseJSON && responseJSON.callback)
				{
					callbacks = new Array();
					
					// More than one callback
					if ($type(responseJSON.callback) == 'array') {
						callbacks = responseJSON.callback;
					}
					else {
						callbacks.push(responseJSON.callback)	
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
	
				// User notification
				if (responseJSON && responseJSON.message_type)
				{
					if (responseJSON.message_type == 'error')
					{
						MUI.error(responseJSON.message);
					}
					else
					{
						MUI.notification.delay(50, MUI, new Array(responseJSON.message_type, responseJSON.message));
					}
				}
			}
		};
	}

	/**
	 * Get the associated form object and send it directly
	 *
	 * @param	string		URL to send the form data
	 * @param	string		Element to update
	 * @param	string		Element update URL
	 */
	MUI.sendForm = function(url)
	{
		new Request.JSON(MUI.getFormObject(url)).send();
	}

	/**
	 * Get the associated form object and send attached data directly
	 *
	 * @param	string		URL to send the form data
	 * @param	string		Element to update
	 * @param	string		Element update URL
	 */
	MUI.sendData = function(url, data)
	{
		new Request.JSON(MUI.getFormObject(url, data)).send();
	}


	/**
	 * Set an XHR action to a form and add click event to the attached element
	 *
	 * @param	string	form ID
	 * @param	string	element on wich attach the action (ID)
	 * @param	string	action URL (with or without the base URL prefix)
	 * @param	object	Confirmation object	{message: 'The confirmation question'}
	 *
	 */
	MUI.setFormSubmit = function(form, button, url, confirm)
	{
		// Add the form submit event with a confirmation window
		if ($(button) && ($type(confirm) == 'object'))
		{
			var func = function()
			{
				MUI.showSpinner();

				var options = MUI.getFormObject(url, $(form));
				
				var r = new Request.JSON(options);
				
				r.send();
			};
		
			// Form submit or button event
			$(button).addEvent('click', function(e)
			{
				new Event(e).stop();
				
				MUI.confirmation('conf' + button.id, func, confirm.message);
			});
		}
		// Add the form submit button event without confirmation
		else if ($(button))
		{
			// Form submit or button event
			$(button).addEvent('click', function(e)
			{
				new Event(e).stop();
				
				// Show spinner
				MUI.showSpinner();
				
				// tinyMCE and CKEditor trigerSave
				// mandatory for text save. See how to externalize without make it too complex.
				if (typeof tinyMCE != "undefined")
					tinyMCE.triggerSave();
				if (typeof CKEDITOR != "undefined")
				{
					for (instance in CKEDITOR.instances)
						CKEDITOR.instances[instance].updateElement();
				}
				
				// Get the form
				var options = MUI.getFormObject(url, $(form));
				
				var r = new Request.JSON(options);
				
				r.send();
			});
		}
	}


	/**
	 * CTRL+s or Meta+s save event
	 *
	 */
	MUI.addFormSaveEvent = function(button)
	{
		if ($(button))
		{
			// Remove all existing Ctrl+S Save Event
			$(document).removeEvents('keydown');
			
			// Add new keydown 
			$(document).addEvent('keydown', function(event)
			{
				if((event.control || event.meta) && event.key == 's')
				{
					event.stop();
					if ($(button))
					{
						$(button).fireEvent('click', event);
					}
				}
			});
		}
	}

	
	MUI.myChain.callChain();

}


/** 
 * Ionize UI Content Init 
 *
 */
var initializeContent = function()
{

	/** 
	 * Updates the mainPanel toolbox
	 *
	 * @param	string		Name of the toolbox view to load.
	 *						Must be located in the themes/admin/views folder
	 * @param	function	Function to execute when the toolbox is loaded.
	 *	
	 */
	MUI.initToolbox = function(toolbox_url, onContentLoaded)
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
			cb = '';
			if (onContentLoaded)
			{
				cb = onContentLoaded;
			}
		
			MUI.updateContent({
				'element': $('mainPanel'),
				'childElement': $('mainPanel_headerToolbox'),
				'loadMethod': 'xhr',
				'url': admin_url + 'core/get/toolboxes/' + toolbox_url
				// 'onContentLoaded': onContentLoaded
			});
		}
		else
		{
			$('mainPanel_headerToolbox').empty();
		}
	
	};
	
	
	/** 
	 * Init a module toolbox
	 * @param	string 	module name
	 * @param	toolbox_url for this module
	 *	
	 */
	MUI.initModuleToolbox = function(module, toolbox_url)
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
			MUI.updateContent({
				'element': $('mainPanel'),
				'childElement': $('mainPanel_headerToolbox'),
				'loadMethod': 'xhr',
				'url': admin_url + 'module/' + module + '/' +  module + '/get/admin/toolboxes/' + toolbox_url
			});
		}
		else
		{
			$('mainPanel_headerToolbox').empty();
		}
	
	};	



	/** 
	 * Creates Accordion
	 * @param	string 	HTMLElement ID
	 *	
	 */
	MUI.initAccordion = function(togglers, elements) 
	{	
		var acc = new Fx.Accordion(togglers, elements, {
			display: 0,
			opacity: false,
			alwaysHide: true,
			initialDisplayFx: false,
			onActive: function(toggler, element){
				toggler.addClass('expand');
			},
			onBackground: function(toggler, element){
				toggler.removeClass('expand');
			}
		});
	};

	
	/**
	 * Adds effect to sideColumn
	 *
	 */
	MUI.initSideColumn = function()
	{
		// element to slide & linked button
		var maincolumn = $('maincolumn');
		var element = $('sidecolumn');		
		var button = $('sidecolumnSwitcher');
		
		if (button)
		{
			// button event
			button.addEvent('click', function(e)
			{
				var e = new Event(e).stop();
				
				if (this.retrieve('status') == 'close')
				{
					element.removeClass('close');
					maincolumn.addClass('with-side');
	
					this.set('value', Lang.get('ionize_label_hide_options'));
					this.store('status', 'open');
	
					Cookie.write('sidecolumn', 'open');
					
				}
				else
				{
					element.addClass('close');
					maincolumn.removeClass('with-side');
					
					this.set('value', Lang.get('ionize_label_show_options'));
					this.store('status', 'close');
					Cookie.write('sidecolumn', 'close');
				}
				
			});
			
			/*
			 * Get the cookie stored option state and apply
			 */
			var pos = Cookie.read('sidecolumn');
	
			if (typeof(pos) != 'undefined' && pos == 'close')
			{
				// element.hide();
				element.addClass('close');
				maincolumn.removeClass('with-side');
				
				button.set('value', Lang.get('ionize_label_show_options'));
				button.store('status', 'close');
			}
			else
			{
				element.removeClass('close');
				maincolumn.addClass('with-side');
	
				button.store('status', 'open');
				button.set('value', Lang.get('ionize_label_hide_options'));
			}
		}
	};

	
	/**
	 * Updates multiple elements
	 *
	 * @param	array	Array of elements to update. Array('element_id' => 'url_to_call')
	 *
	 */
	MUI.updateElements = function (elements)
	{
		$each(elements, function(options, key)
		{
			MUI.updateElement(options);
		});
	};


	/**
	 * Updates one element
	 *
	 * @param	string Element ID
	 * @param	Object Core.updateContent options object
	 *
	 */
	MUI.updateElement = function (options)
	{
		// Cleans URLs
		options.url = admin_url + MUI.cleanUrl(options.url);
			
		// If the panel doesn't exists, try to update directly one DomHTMLElement
		if ( ! MUI.Windows.instances.get(options.element) && ! MUI.Panels.instances.get(options.element))
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
			MUI.updateContent(options);
		}
	};

	
	/**
	 * Cleans the base URL
	 *
	 * @return string	URL without the first part
	 *
	 */
	MUI.cleanUrl = function(url)
	{
		// Cleans URLs
		url = url.replace(admin_url, '');
		
		// Base URL contains the lang code. Try to clean without the lang code
		url = url.replace(admin_url.replace(Lang.get('current') + '/', ''), '');
		
		return url;
	};

	
	/**
	 * Displays one CSS "help link" on each label which have a title
	 * For other elements than labels, adding the .help class and one title will be enough to display the tip
	 *
	 */
	MUI.initLabelHelpLinks = function(element)
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
	};
	
	
	MUI.onContentLoaded = function()
	{
		// alert('loaded');
	};


	MUI.myChain.callChain();
}
