/*
---

name: Request.Blob

description: Uploading binary files via request. Mashup of Request.sendBlob by Juan Lago and Request.Blob by Arian Stolwijk

license: MIT-style license.

authors: 
- Arian Stolwijk
- Djamil Legato
- Juan Lago
- Mateusz Cyrankiewicz

requires: [Request]
provides: Request.Blob
credits: https://gist.github.com/a77b537e729aff97429c

...
*/

(function(){

var progressSupport = ('onprogress' in new Browser.Request);

Request.Blob = new Class({

	Extends: Request,

	options: {
		urlEncoded: false,
		noCache: true,
		emulation: false,
		trackProgress: false // used only for non-blob uploads (in Safari)
	},

	send: function (blob, options) {
	
		if (!this.check(options)) return this;
		
		this.options.isSuccess = this.options.isSuccess || this.isSuccess;
		this.running = true;

		var url = String(this.options.url),
			method = this.options.method.toLowerCase();

		if (!url) url = document.location.pathname;

		var trimPosition = url.lastIndexOf('/');
		if (trimPosition > -1 && (trimPosition = url.indexOf('#')) > -1) url = url.substr(0, trimPosition);

		// No cache is already done by Mootools Request.
		// Really need that ?
		// if (this.options.noCache) url += (url.contains('?') ? '&' : '?') + String.uniqueID();

		var xhr = this.xhr;
		
		if (progressSupport) {
			xhr.onloadstart = this.loadstart.bind(this);
			xhr.onprogress = this.progress.bind(this);
			xhr.upload.onprogress = this.progress.bind(this);
		}
		
		xhr.open(method.toUpperCase(), url, this.options.async, this.options.user, this.options.password);
		if (this.options.user && 'withCredentials' in xhr) xhr.withCredentials = true;

		xhr.onreadystatechange = this.onStateChange.bind(this);

		// Adds also vars to post data
		Object.each(this.headers, function (value, key) {
			try {
				xhr.setRequestHeader(key, value);
			} catch (e) {
				this.fireEvent('exception', [e, key, value]);
			}
		}, this);

		this.fireEvent('request');

		xhr.send(blob);

		if (!this.options.async) this.onStateChange();
		if (this.options.timeout) this.timer = this.timeout.delay(this.options.timeout, this);
		
		return this;
		
	}

});

})();

