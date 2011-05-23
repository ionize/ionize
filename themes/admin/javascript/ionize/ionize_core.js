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
	
	mainpanel: 'mainPanel',
	
	/**
	 * Reloads Ionize's interface
	 *
	 */
	reload: function(args)
	{
		window.top.location = this.baseUrl + args.url;
	},
	
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

String.extend({
	
	htmlspecialchars_decode:function(text)
	{
		var tmp = new Element('span',{ 'html':text });
		var ret_val = tmp.get('text');
		delete tmp;
		return ret_val;
	}
});
