/**
 * Ionize Core Object
 *
 *
 */
var ION = (ION || {});

ION.append = function(hash){
	Object.append(ION, hash);
}.bind(ION);

ION.append({
	
	/**
	 * URLs used by depending classes
	 *
	 */
	baseUrl: 		base_url,
	adminUrl: 		admin_url,
	modulesUrl: 	modules_url,
	themeUrl: 		theme_url,
	siteThemeUrl: 	site_theme_url,
	mainpanel: 		'mainPanel',
	instances: 		{},
	assets:			new Hash(),
	loadedAssets:			{},		// Assets loaded
	plannedAssets:			{},		// Assets planned to be load.
	plannedAssetSeries:		{},		// Series of Assets planned to be load.

	// Uncomment if Mediator is used.
	// See : http://thejacklawson.com/Mediator.js/
	// Mediator: new Mediator(),

	registry: function(key)
	{
		if (typeOf(this.instances[key]) != 'null')
			return this.instances[key];

		return null;
	},

	register: function(key, instance)
    {
		this.instances[key] = instance;
		return instance;
	},

	getRegistry: function()
	{
		return this.instances;
	},

	/**
	 * Modules Registration
	 *
	 * /modules/XXXX/assets/javascript/admin.js is autoloaded
	 * This file can contains one ION.registerModule(Class) name.
	 * If it does, the module is registered and its init() function is called.
	 * This is useful to load some other JS from the /modules/XXXX/assets/javascript/ folder.
	 *
	 * Important :
	 * The JS main methods are not supposed to be directly in the admin.js file.
	 * This file is supposed to load other classes through its init() method
	 *
	 * @param k
	 *
	 */
	registerModule: function(k)
	{
		if (typeOf(k.init) == 'function')
			k.init();
	},

	isAssetPlanned: function(assetUrl)
	{
		return typeOf(this.plannedAssets[assetUrl]) != 'null';
	},

	isAssetLoaded: function(assetUrl)
	{
		return typeOf(this.loadedAssets[assetUrl]) != 'null';
	},

	setAssetLoaded: function(assetUrl)
	{
		this.loadedAssets[assetUrl] = true;
	},

	/**
	 * Adds one assets serie to the planned series
	 *
	 * @param assetUrl
	 * @param funcs
	 */
	planAssetSerie: function(assetUrl, funcs)
	{
		var self = this,
			sid = this.getHash(32),
			pFuncs = {};

		Object.each(funcs, function(func, name){
			if (name == 'onComplete')
				pFuncs[name] = func;
		});

		this.plannedAssetSeries[sid] = {sources:[], funcs:pFuncs};

		assetUrl.each(function(source){
			self.plannedAssetSeries[sid]['sources'].push(source);
		});
	},

	/**
	 * Tries to complete all assets series.
	 *
	 */
	completeAssetSeries: function()
	{
		var self = this;

		Object.each(this.plannedAssetSeries, function(serie, sid)
		{
			var exec = true;

			serie.sources.each(function(source)
			{
				if (exec) exec = self.isAssetLoaded(source);
			});

			// Execute the serie's functions
			if (exec)
			{
				// Remove this serie from planned
				delete(self.plannedAssetSeries[sid]);

				Object.each(serie.funcs, function(func){
					func.call();
				});

			}
		});
	},


	/**
	 * Load one or multiple assets.
	 *
	 * In single mode, if the asset is already loaded, calls the onLoad function.
	 *
	 * @param assetUrl
	 */
	loadAsset: function(assetUrl)
	{
		var self = this,
			funcs = Object.merge({
				onComplete: Function.from
			//	onLoad: Function.from
			}, arguments[1]);

		if (typeOf(assetUrl) == 'string')
			assetUrl = [assetUrl];

		var sources = [];

		this.planAssetSerie(assetUrl, funcs);

		assetUrl.each(function(source)
		{
			if ( ! self.isAssetPlanned(source))
			{
				sources.push(source);
			}
		});

		this.loadAssets(sources, funcs);
	},

	/**
	 * Loads multiple assets sequencially
	 *
	 * See:  http://fragged.org/lazyloading-multiple-sequential-javascript-dependencies-in-mootools_1389.html
	 *
	 * @param sources
	 * @param options
	 */
	loadAssets: function(sources, options)
	{
		// load an array of js dependencies and fire events as it walks through
		options = Object.merge({
			onComplete: Function.from,
			onProgress: Function.from
		}, options);

		var self = this,
			counter = 0,
			todo = sources.length,
			uniq = '?u=' + new Date().valueOf()
		;

		if (todo == 0)
			self.completeAssetSeries();
		// options.onComplete.call(this, counter);

		var loadNext = function()
		{
			if (sources[0])
			{
				var source = sources[0];

				var ext = source.split('.').pop();

				if(ext == 'js')
				{
					Asset.javascript(source + uniq, {
						onLoad: function()
						{
							counter++;

							self.setAssetLoaded(source);

							options.onProgress.call(this, counter, source);

							sources.erase(source);

							// Fired when all assets are loaded
							if (counter == todo)
								self.completeAssetSeries();
							else
								loadNext();
						}
					});
				}
				else if(ext == 'css')
				{
					Asset.css(source + uniq);

					counter++;

					self.setAssetLoaded(source);

					options.onProgress.call(this, counter, source);

					sources.erase(source);

					if (counter == todo)
						self.completeAssetSeries();
					else
						loadNext();
				}
			}
		};

		loadNext();
	},


	/**
	 * Reloads Ionize's interface
	 *
	 */
	reload: function(args)
	{
		var url = this.adminUrl;
		if (typeOf(args) != 'null')
			url = this.baseUrl + args.url;

		top.location.href = url;
	},

	getHash: function()
	{
		var size = arguments[0] || 16;
		return this.generateHash(size);
	},

	/**
	 * Generates a random hash
	 *
	 * @param	size		Size of the returned key or 16 by default
	 */
	generateHash: function()
	{
		var vowels = 'aeiouyAEIOUY',
			consonants = 'bcdfghjklmnpqrstvwxzBCDFGHJKLMNPQRSTVWXZ1234567890',
			size = arguments[0] || 16,
			key = ''
		;

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
	 * Generates a random key
	 *
	 * @param	size		Size of the returned key
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
	 * @param	{String}	field
	 */
	clearField: function(field) 
	{
		if (typeOf($(field)) != 'null' )
		{
			$(field).value = '';
			$(field).focus();
		}
	},

	/**
	 * @param	{String} url
	 * @returns {Boolean}
	 */
	checkUrl: function(url)
	{
		var RegexUrl = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
		return RegexUrl.test(url);
	},

	/**
	 * @param	{String} selector
	 * @param	{String} selectors
	 * @param	{String} cl
	 */
	setSelected: function(selector, selectors, cl)
	{
		$$(selectors).each(function(el){
			el.removeClass(cl);
		});

		if (selector != null)
		{
			if ($(selector))
			{
				$(selector).addClass(cl);
			}
			else
			{
				$$(selector).each(function(el){
					el.addClass(cl);
				});
			}
		}
	},

	/**
	 * Add one list of values to cookie
	 *
	 * @param {String} name
	 * @param {String|number} value
	 */
	listAddToCookie: function(name, value)
	{
		var list = [];
		if (Cookie.read(name))
			list = (Cookie.read(name)).split(',');
		if (!list.contains(value))
		{
			list.push(value);
			Cookie.write(name, list.join(','));
		}
	},

	
	/**
	 * Remove one list of values from cookie
	 *
	 * @param	{String}		name
	 * @param	{String|number} value
	 */
	listDelFromCookie: function(name, value)
	{
		var list = [];
		if (Cookie.read(name))
			list = (Cookie.read(name)).split(',');
		if (list.contains(value))
		{
			list.erase(value);
			Cookie.write(name, list.join(','));
		}
	},

	/**
	 * Returns default width for popup windows, calculated from window size bearing reasonable minimum/maximum scale
	 *
	 * @returns {number}
	 */
	getPopupDefaultWidth: function()
	{
		var width = window.innerWidth * 0.7;

		return width < 500 ? 500 : ( width > 1000 ? 1000 : width );
	},

	/**
	 * Returns default height for popup windows
	 *
	 * @returns {number}
	 */
	getPopupDefaultHeight: function()
	{
		var height = window.innerHeight * 0.7;

		return height < 350 ? 350 : ( height > 750 ? 750 : height );
	},
	
	/**
	 * Returns the FileManager window options
	 *
	 */
	getFilemanagerWindowOptions: function()
	{
		// Window size
		var wSize = {
			'width': 800,
			'height': 360,
			'x': 70,
			'y': null
		};

		// Get the size stored in the cookie, if any.
		if (Cookie.read('fm'))
		{
			var fm = new Hash.Cookie('fm', {duration: 365});

			wSize = {
				'width': fm.get('width'),
				'height': fm.get('height'),
				'y': fm.get('top'),
				'x': fm.get('left')
			}
		}

		return {
			id: 'filemanagerWindow',
			title: 'Media Manager',
			container: document.body,
			width: wSize.width,
			height: wSize.height,
			y: 35,
			padding: { top: 0, right: 0, bottom: 0, left: 0 },
			maximizable: false,
			contentBgColor: '#fff',
			onClose: function()
			{
				// Hides the filemanager
				this.filemanager.hide();
				this.filemanager = null;
			}
		};
	},

	getWeekDayName: function(date)
	{
		return (typeOf(date) == 'date')
			? Lang.get('day_' + date.getDay())
			: '';
	},

	getMonthName: function(date)
	{
		return (typeOf(date) == 'date')
			? Lang.get('month_' + (date.getMonth() + 1))
			: '';
	}
});
