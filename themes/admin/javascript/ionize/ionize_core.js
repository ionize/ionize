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
	instances: 		new Hash(),
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

	register: function(key, instance){
		this.instances.set(key, instance);
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
				sources.push(source)
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
			todo = sources.length;

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
					Asset.javascript(source, {
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
					Asset.css(source);

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








/**
 * JS and mootools enhancements
 *
 */

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
	},

	formatFromMySql: function(d, format)
	{
		var f = '';

		if (typeOf(d) != 'null')
		{
			// Split timestamp into [ Y, M, D, h, m, s ]
			var t = d.split(/[- :]/);

			// Apply each element to the Date function
			var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);

			var h = d.getHours();
			var m = d.getMonth();

			for (var i = 0; i < format.length; i++) {
				switch(format.charAt(i)) {
					case '\\': i++; f+= format.charAt(i); break;
					case 'd': f += Date.leadZero(d.getDate()); break;
					case 'm': f += Date.leadZero(m + 1); break;
					case 'Y': f += d.getFullYear(); break;
					case 'y': f += (100 + t.getYear() + '').substring(1); break
					case 'H': f += Date.leadZero(h); break;
					case 'h': f += Date.leadZero(h % 12 ? h % 12 : 12); break;
					case 'i': f += Date.leadZero(d.getMinutes()); break;
					case 's': f += Date.leadZero(d.getSeconds()); break;
					case 'a': f += (h > 11 ? 'pm' : 'am'); break;
					case 'A': f += (h > 11 ? 'PM' : 'AM'); break;
					case '%': break;
					default:  f += format.charAt(i);
				}
			}
		}
		return f;
	},

	leadZero: function(v) {
		return v < 10 ? '0'+v : v;
	},


	format: function(t, format) {
		var f = '';
		var h = t.getHours();
		var m = t.getMonth();

		for (var i = 0; i < format.length; i++) {
			switch(format.charAt(i)) {
				case '\\': i++; f+= format.charAt(i); break;
				case 'y': f += (100 + t.getYear() + '').substring(1); break
				case 'Y': f += t.getFullYear(); break;
				case 'm': f += this.leadZero(m + 1); break;
				case 'n': f += (m + 1); break;
				case 'M': f += this.options.months[m].substring(0,this.options.monthShort); break;
				case 'F': f += this.options.months[m]; break;
				case 'd': f += this.leadZero(t.getDate()); break;
				case 'j': f += t.getDate(); break;
				case 'D': f += this.options.days[t.getDay()].substring(0,this.options.dayShort); break;
				case 'l': f += this.options.days[t.getDay()]; break;
				case 'G': f += h; break;
				case 'H': f += this.leadZero(h); break;
				case 'g': f += (h % 12 ? h % 12 : 12); break;
				case 'h': f += this.leadZero(h % 12 ? h % 12 : 12); break;
				case 'a': f += (h > 11 ? 'pm' : 'am'); break;
				case 'A': f += (h > 11 ? 'PM' : 'AM'); break;
				case 'i': f += this.leadZero(t.getMinutes()); break;
				case 's': f += this.leadZero(t.getSeconds()); break;
				case 'U': f += Math.floor(t.valueOf() / 1000); break;
				default:  f += format.charAt(i);
			}
		}
		return f;
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

/**
 * Transform one form into JSON object
 */
Element.implement(
{
	toJSON: function()
	{
		var json = {};

		this.getElements('input, select, textarea', true).each(function(el)
		{
			if (!el.name || el.disabled || el.type == 'submit' || el.type == 'reset' || el.type == 'file') return;
			var value = (el.tagName.toLowerCase() == 'select') ? Element.getSelected(el).map(function(opt){
				return opt.value;
			}) : ((el.type == 'radio' || el.type == 'checkbox') && !el.checked) ? null : el.value;

			if (typeOf(value) == 'array')
			{
				Array.each(value, function(val){
					json[el.name] = val;
				});
			}
			else
				json[el.name] = value;
		});
		return json;
	}
});



Object.append(Element.NativeEvents, {
	dragenter: 2, dragleave: 2, dragover: 2, dragend: 2, drop: 2
});

(function(window) {
	var re = {
		not_string: /[^s]/,
		number: /[def]/,
		text: /^[^\x25]+/,
		modulo: /^\x25{2}/,
		placeholder: /^\x25(?:([1-9]\d*)\$|\(([^\)]+)\))?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-fosuxX])/,
		key: /^([a-z_][a-z_\d]*)/i,
		key_access: /^\.([a-z_][a-z_\d]*)/i,
		index_access: /^\[(\d+)\]/,
		sign: /^[\+\-]/
	}

	function sprintf() {
		var key = arguments[0], cache = sprintf.cache
		if (!(cache[key] && cache.hasOwnProperty(key))) {
			cache[key] = sprintf.parse(key)
		}
		return sprintf.format.call(null, cache[key], arguments)
	}

	sprintf.format = function(parse_tree, argv) {
		var cursor = 1, tree_length = parse_tree.length, node_type = "", arg, output = [], i, k, match, pad, pad_character, pad_length, is_positive = true, sign = ""
		for (i = 0; i < tree_length; i++) {
			node_type = get_type(parse_tree[i])
			if (node_type === "string") {
				output[output.length] = parse_tree[i]
			}
			else if (node_type === "array") {
				match = parse_tree[i] // convenience purposes only
				if (match[2]) { // keyword argument
					arg = argv[cursor]
					for (k = 0; k < match[2].length; k++) {
						if (!arg.hasOwnProperty(match[2][k])) {
							throw new Error(sprintf("[sprintf] property '%s' does not exist", match[2][k]))
						}
						arg = arg[match[2][k]]
					}
				}
				else if (match[1]) { // positional argument (explicit)
					arg = argv[match[1]]
				}
				else { // positional argument (implicit)
					arg = argv[cursor++]
				}

				if (get_type(arg) == "function") {
					arg = arg()
				}

				if (re.not_string.test(match[8]) && (get_type(arg) != "number" && isNaN(arg))) {
					throw new TypeError(sprintf("[sprintf] expecting number but found %s", get_type(arg)))
				}

				if (re.number.test(match[8])) {
					is_positive = arg >= 0
				}

				switch (match[8]) {
					case "b":
						arg = arg.toString(2)
						break
					case "c":
						arg = String.fromCharCode(arg)
						break
					case "d":
						arg = parseInt(arg, 10)
						break
					case "e":
						arg = match[7] ? arg.toExponential(match[7]) : arg.toExponential()
						break
					case "f":
						arg = match[7] ? parseFloat(arg).toFixed(match[7]) : parseFloat(arg)
						break
					case "o":
						arg = arg.toString(8)
						break
					case "s":
						arg = ((arg = String(arg)) && match[7] ? arg.substring(0, match[7]) : arg)
						break
					case "u":
						arg = arg >>> 0
						break
					case "x":
						arg = arg.toString(16)
						break
					case "X":
						arg = arg.toString(16).toUpperCase()
						break
				}
				if (!is_positive || (re.number.test(match[8]) && match[3])) {
					sign = is_positive ? "+" : "-"
					arg = arg.toString().replace(re.sign, "")
				}
				pad_character = match[4] ? match[4] == "0" ? "0" : match[4].charAt(1) : " "
				pad_length = match[6] - (sign + arg).length
				pad = match[6] ? str_repeat(pad_character, pad_length) : ""
				output[output.length] = match[5] ? sign + arg + pad : (pad_character == 0 ? sign + pad + arg : pad + sign + arg)
			}
		}
		return output.join("")
	}

	sprintf.cache = {}

	sprintf.parse = function(fmt) {
		var _fmt = fmt, match = [], parse_tree = [], arg_names = 0
		while (_fmt) {
			if ((match = re.text.exec(_fmt)) !== null) {
				parse_tree[parse_tree.length] = match[0]
			}
			else if ((match = re.modulo.exec(_fmt)) !== null) {
				parse_tree[parse_tree.length] = "%"
			}
			else if ((match = re.placeholder.exec(_fmt)) !== null) {
				if (match[2]) {
					arg_names |= 1
					var field_list = [], replacement_field = match[2], field_match = []
					if ((field_match = re.key.exec(replacement_field)) !== null) {
						field_list[field_list.length] = field_match[1]
						while ((replacement_field = replacement_field.substring(field_match[0].length)) !== "") {
							if ((field_match = re.key_access.exec(replacement_field)) !== null) {
								field_list[field_list.length] = field_match[1]
							}
							else if ((field_match = re.index_access.exec(replacement_field)) !== null) {
								field_list[field_list.length] = field_match[1]
							}
							else {
								throw new SyntaxError("[sprintf] failed to parse named argument key")
							}
						}
					}
					else {
						throw new SyntaxError("[sprintf] failed to parse named argument key")
					}
					match[2] = field_list
				}
				else {
					arg_names |= 2
				}
				if (arg_names === 3) {
					throw new Error("[sprintf] mixing positional and named placeholders is not (yet) supported")
				}
				parse_tree[parse_tree.length] = match
			}
			else {
				throw new SyntaxError("[sprintf] unexpected placeholder")
			}
			_fmt = _fmt.substring(match[0].length)
		}
		return parse_tree
	}

	var vsprintf = function(fmt, argv, _argv) {
		_argv = (argv || []).slice(0)
		_argv.splice(0, 0, fmt)
		return sprintf.apply(null, _argv)
	}

	/**
	 * helpers
	 */
	function get_type(variable) {
		return Object.prototype.toString.call(variable).slice(8, -1).toLowerCase()
	}

	function str_repeat(input, multiplier) {
		return Array(multiplier + 1).join(input)
	}

	/**
	 * export to either browser or node.js
	 */
	if (typeof exports !== "undefined") {
		exports.sprintf = sprintf
		exports.vsprintf = vsprintf
	}
	else {
		window.sprintf = sprintf
		window.vsprintf = vsprintf

		if (typeof define === "function" && define.amd) {
			define(function() {
				return {
					sprintf: sprintf,
					vsprintf: vsprintf
				}
			})
		}
	}
})(typeof window === "undefined" ? this : window)


String.prototype.trimLeft = function(charlist) {
	if (charlist === undefined)
		charlist = "\s";

	return this.replace(new RegExp("^[" + charlist + "]+"), "");
};
String.prototype.trimRight = function(charlist) {
	if (charlist === undefined)
		charlist = "\s";

	return this.replace(new RegExp("[" + charlist + "]+$"), "");
};
String.prototype.charTrim = function(charlist) {
	return this.trimLeft(charlist).trimRight(charlist);
};

/*
 ---

 script: Array.sortOn.js

 description: Adds Array.sortOn function and related constants that works like in ActionScript for sorting arrays of objects (applying all same strict rules)

 license: MIT-style license.

 authors:
 - gonchuki

 docs: http://www.adobe.com/livedocs/flash/9.0/ActionScriptLangRefV3/Array.html#sortOn()

 requires:
 - core/1.2.4: [Array]

 provides:
 - [Array.sortOn, Array.CASEINSENSITIVE, Array.DESCENDING, Array.UNIQUESORT, Array.RETURNINDEXEDARRAY, Array.NUMERIC]

 ...
 */

Array.CASEINSENSITIVE = 1;
Array.DESCENDING = 2;
Array.UNIQUESORT = 4;
Array.RETURNINDEXEDARRAY = 8;
Array.NUMERIC = 16;

(function() {
	var dup_fn = function(field, field_options) {
		var filtered = (field_options & Array.NUMERIC)
			? this.map(function(item) {return item[field].toFloat(); })
			: (field_options & Array.CASEINSENSITIVE)
			? this.map(function(item) {return item[field].toLowerCase(); })
			: this.map(function(item) {return item[field]; });
		return filtered.length !== [].combine(filtered).length;
	};

	var sort_fn = function(item_a, item_b, fields, options) {
		return (function sort_by(fields, options) {
			var ret, a, b,
				opts = options[0],
				sub_fields = fields[0].match(/[^.]+/g);

			(function get_values(s_fields, s_a, s_b) {
				var field = s_fields[0];
				if (s_fields.length > 1) {
					get_values(s_fields.slice(1), s_a[field], s_b[field]);
				} else {
					a = s_a[field].toString();
					b = s_b[field].toString();
				}
			})(sub_fields, item_a, item_b);

			if (opts & Array.NUMERIC) {
				ret = (a.toFloat() - b.toFloat());
			} else {
				if (opts & Array.CASEINSENSITIVE) { a = a.toLowerCase(); b = b.toLowerCase(); }

				ret = (a > b) ? 1 : (a < b) ? -1 : 0;
			}

			if ((ret === 0) && (fields.length > 1)) {
				ret = sort_by(fields.slice(1), options.slice(1));
			} else if (opts & Array.DESCENDING) {
				ret *= -1;
			}

			return ret;
		})(fields, options);
	};

	Array.implement({
		sortOn: function(fields, options) {
			fields = Array.from(fields);
			options = Array.from(options);

			if (options.length !== fields.length) options = [];

			if ((options[0] & Array.UNIQUESORT) && (fields.some(function(field, i){return dup_fn(field, options[i]);}))) return 0;

			var curry_sort = function(item_a, item_b) {
				return sort_fn(item_a, item_b, fields, options);
			};

			if (options[0] & Array.RETURNINDEXEDARRAY)
				return this.slice().sort(curry_sort);
			else
				this.sort(curry_sort);
		}
	});

})();


/**
 * Simple Pub/Sub pattern
 * Implementation of http://davidwalsh.name/pubsub-javascript
 *
 */
Events.topics = {};
Events.hOP = Events.topics.hasOwnProperty;

Events.subscribe= function(topic, listener)
{
	// Create the topic's object if not yet created
	if( ! Events.hOP.call(Events.topics, topic)) Events.topics[topic] = [];

	// Add listener to queue
	var index = Events.topics[topic].push(listener) -1;

	// Provide handle back for removal of a listener for a topic
	return {
		remove: function() {
			delete Events.topics[topic][index];
		}
	};
};

Events.publish= function(topic, info)
{
	// If the topic doesn't exist, or there's no listeners in queue, just leave
	if( ! Events.hOP.call(Events.topics, topic)) return;

	// Cycle through topics queue, fire!
	Events.topics[topic].forEach(function(item) {
		item(info||{});
	});
};


