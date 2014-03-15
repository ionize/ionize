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
	baseUrl: base_url,
	adminUrl: admin_url,
	modulesUrl: modules_url,
	themeUrl: theme_url,
	siteThemeUrl: site_theme_url,
	mainpanel: 'mainPanel',
	instances: new Hash(),

	registry: function(key){
		return this.instances[key];
	},

	register: function(key, instance){
		this.instances.set(key, instance);
		return instance;
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
	 */
	clearField: function(field) 
	{
		if (typeOf($(field)) != 'null' )
		{
			$(field).value = '';
			$(field).focus();
		}
	},

	checkUrl: function(url)
	{
		var RegexUrl = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
		return RegexUrl.test(url);
	},

	setSelected: function(selector, selectors, cl)
	{
		$$(selectors).each(function(el){
			el.removeClass(cl);
		});

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
	},

	/**
	 * Add one list of values to cookie
	 *
	 */
	listAddToCookie: function(name, value)
	{
		var list = Array();
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
	 */
	listDelFromCookie: function(name, value)
	{
		var list = Array();
		if (Cookie.read(name))
			list = (Cookie.read(name)).split(',');
		if (list.contains(value))
		{
			list.erase(value);
			Cookie.write(name, list.join(','));
		}
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

		var options  =
		{
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

		return options;
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

Number.extend({


	round: function(num, dec) {
		var result = Math.round( Math.round( num * Math.pow( 10, dec + 1 ) ) / Math.pow( 10, 1 ) ) / Math.pow(10,dec);
		return result;
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

String.extend({
	
	htmlspecialchars_decode: function(text)
	{
		var tmp = new Element('span',{ 'html':text });
		var ret_val = tmp.get('text');
		delete tmp;
		return ret_val;
	},

	strip_tags: function(html)
	{
		if(arguments.length < 3)
		{
			html = html.replace(/<\/?(?!\!)[^>]*>/gi, '');
		} 
		else
		{
			var allowed = arguments[1];
			var specified = eval("["+arguments[2]+"]");
			if(allowed)
			{
				var regex='</?(?!(' + specified.join('|') + '))\b[^>]*>';
				html = html.replace(new RegExp(regex, 'gi'), '');
			}
			else
			{
				var regex='</?(' + specified.join('|') + ')\b[^>]*>';
				html = html.replace(new RegExp(regex, 'gi'), '');
			}
		}

		var clean_string = html;
		return clean_string;
	},

	capitalize: function(text)
	{

		return text.charAt(0).toUpperCase() + text.slice(1);
	}

});

Array.prototype.insert = function(index) {
	this.splice.apply(this, [index, 0].concat(
		Array.prototype.slice.call(arguments, 1)));
	return this;
};




FocusTracker = {
    startFocusTracking: function() {
       this.store('hasFocus', false);
       this.addEvent('focus', function() { this.store('hasFocus', true); });
       this.addEvent('blur', function() { this.store('hasFocus', false); });
    },
	
    hasFocus: function() {
       return this.retrieve('hasFocus');
    }
};

Element.implement(FocusTracker);

