/*
---
description: TextboxList

authors:
  - Guillermo Rauch

requires:
  core/1.3: '*'

provides:
  - textboxlist.autocomplete
...
*/

(function(){

TextboxList.Autocomplete = new Class({

	Implements: Options,

	options: {
		highlight: true,
		highlightSelector: null,
		insensitive: true,
		maxResults: 10,
		minLength: 1,
		method: 'standard',
		mouseInteraction: true,
		onlyFromValues: false,
		showAllValues: false,
		reAddValues: false,
		placeholder: 'Type to receive suggestions',
		queryRemote: false,
		remote: {
			emptyResultPlaceholder: 'No matches found',
			extraParams: {},
			loadPlaceholder: 'Please wait...',
			method: 'post',
			param: 'search',
			url: ''
		},
		resultsFilter: null
	},

	addCurrent: function() {
		var value = this.current.retrieve('textboxlist:auto:value');
		var box = this.textboxlist.create('box', value.slice(0, 3));
		if (box) {
			box.autoValue = value;
			if (this.index !== null) {
				this.index.push(value);
			}
			this.currentInput.setValue([null, '', null]);
			box.inject(document.id(this.currentInput), 'before');
		}
		this.blur();
		return this;
	},

	addResult: function(result, search) {
		var element = new Element('li.'+this.prefix+'-result[html="'+[result[3], result[1]].pick()+'"]').store('textboxlist:auto:value', result);
		this.list.adopt(element);
		if (this.options.highlight) {
			$$(this.options.highlightSelector ? element.getElements(this.options.highlightSelector) : element).each(function(el) {
				if (el.get('html')) {
					this.method.highlight(el, search, this.options.insensitive, this.prefix+'-highlight');
				}
			}, this);
		}
		if (this.options.mouseInteraction) {
			element.setStyle('cursor', 'pointer').addEvents({
				mouseenter: function() {
					this.focus(element);
				}.bind(this),
				mousedown: function(ev) {
					ev.stop();
					clearTimeout(this.hidetimer);
					this.doAdd = true;
				}.bind(this),
				mouseup: function() {
					if (this.doAdd) {
						this.addCurrent();
						this.currentInput.focus();
						this.search();
						this.doAdd = false;
					}
				}.bind(this)
			});
			if ( ! this.options.onlyFromValues) {
				element.addEvent('mouseleave', function() {
					if (this.current == element) {
						this.blur();
					}
				}.bind(this));
			}
		}
	},

	blur: function() {
		if (this.current) {
			this.current.removeClass(this.prefix+'-result-focus');
			this.current = null;
		}
	},

	focus: function(element) {
		if (element) {
			this.blur();
			this.current = element.addClass(this.prefix+'-result-focus');
		}
		return this;
	},

	focusFirst: function() {
		return this.focus(this.list.getFirst());
	},

	focusRelative: function(dir) {
		if ( ! this.current) return this;
		return this.focus(this.current['get'+dir.capitalize()]());
	},

	hide: function(ev) {
		this.hidetimer = (function() {
			this.hidePlaceholder();
			this.list.setStyle('display', 'none');
			this.container.setStyle('display', 'none');
			this.currentSearch = null;
		}).delay(Browser.name=='ie' ? 150 : 0, this);
	},

	hidePlaceholder: function() {
		if (this.placeholder) {
			this.placeholder.setStyle('display', 'none');
			this.container.setStyle('display', 'none');
		}
	},

	reAddValue: function(bit) {
		// var
		var addValue = true;
		this.values.each(function(value){
			if(value[1] === bit.value[1])
				addValue = false;
		});
		if(addValue)
			this.values.push([bit.value[1],bit.value[1]]);
	},

	initialize: function(textboxlist, options) {
		this.setOptions(options);
		this.textboxlist = textboxlist;
		this.textboxlist.addEvent('bitEditableAdd', this.setupBit.bind(this), true)
			.addEvent('bitEditableFocus', this.search.bind(this), true)
			.addEvent('bitEditableBlur', this.hide.bind(this), true);
		if (Browser.name=='ie') {
			this.textboxlist.setOptions({bitsOptions: {editable: {addOnBlur: false}}});
		}
		if (this.textboxlist.options.unique || this.options.reAddValues) {
			this.index = [];
			this.textboxlist.addEvent('bitBoxRemove', function(bit) {
				if (this.textboxlist.options.unique && bit.autoValue) {
					this.index.erase(bit.autoValue);
				}
				if(this.options.reAddValues) {
					this.reAddValue(bit);
				}
			}.bind(this), true);
		}
		this.prefix = this.textboxlist.options.prefix+'-autocomplete';
		this.method = TextboxList.Autocomplete.Methods[this.options.method];
		this.container = new Element('div.'+this.prefix).inject(this.textboxlist.container);
		if ((width = this.textboxlist.container.getStyle('width').toInt()) > 0) {
			this.container.setStyle('width', width);
		}
		if ($chk(this.options.placeholder) || this.options.queryServer) {
			this.placeholder = new Element('div.'+this.prefix+'-placeholder').inject(this.container);
		}
		this.list = new Element('ul.'+this.prefix+'-results').inject(this.container);
		this.list.addEvent('click', function(ev) {
			ev.stop();
		});
		this.values = this.results = this.searchValues = [];
		this.navigate = this.navigate.bind(this);
	},

	navigate: function(ev) {
		switch (ev.key) {
			case 'up':
				ev.stop();
				if (!this.options.onlyFromValues && this.current && this.current == this.list.getFirst()) {
					this.blur();
				}
				else {
					this.focusRelative('previous');
				}
				break;
			case 'down':
				ev.stop();
				if (this.current) {
					this.focusRelative('next');
				}
				else {
					this.focusFirst();
				}
				break;
			case 'enter':
				ev.stop();
				if (this.current) {
					this.addCurrent();
				} else if (!this.options.onlyFromValues) {
					var value = this.currentInput.getValue();
					var box = this.textboxlist.create('box', value);
					if (box){
						box.inject(document.id(this.currentInput), 'before');
						this.currentInput.setValue([null, '', null]);
					}
				}
		}
	},

	search: function(bit) {
		if (bit) {
			this.currentInput = bit;
		}
		if ( ! this.options.queryRemote && ! this.values.length) return;
		var search = this.currentInput.getValue()[1];
		if (search.length < this.options.minLength) {
			this.showPlaceholder(this.options.placeholder);
		}
		if (search == this.currentSearch) return;
		this.currentSearch = search;
		this.list.setStyle('display', 'none');

		if(!this.placeholder)
			this.container.setStyle('display', 'none');

		if (search.length < this.options.minLength) return;
		if (this.options.queryRemote) {
			if (this.searchValues[search]) {
				this.values = this.searchValues[search];
			}
			else {
				var data = this.options.remote.extraParams, that = this;
				if (typeOf(data) == 'function') {
					data = data.run([], this);
				}
				data[this.options.remote.param] = search;
				if (this.currentRequest) {
					this.currentRequest.cancel();
				}
				this.currentRequest = new Request.JSON({
					data: data,
					method: that.options.remote.method,
					onRequest: function() {
						that.showPlaceholder(that.options.remote.loadPlaceholder);
						that.textboxlist.fireEvent('request');
					},
					onSuccess: function(data){
						that.searchValues[search] = data;
						that.values = data;
						that.textboxlist.fireEvent('response');
						that.showResults(search);
					},
					url: this.options.remote.url
				}).send();
			}
		}
		if (this.values.length) {
			this.showResults(search);
		}
	},

	setupBit: function(bit) {
		bit.element.addEvent('keydown', this.navigate, true).addEvent('keyup', function() {
			this.search();
		}.bind(this), true);
	},

	setValues: function(values) {
		this.values = values;
	},

	showPlaceholder: function(customHTML) {
		if (this.placeholder) {
			this.placeholder.setStyle('display', 'block');
			this.container.setStyle('display', 'block');
			if (customHTML) {
				this.placeholder.set('html', customHTML);
			}
		}
	},

	showResults: function(search) {
		var results = this.method.filter(this.values, search, this.options.insensitive, this.options.maxResults);
		if (this.index) {
			var ids = this.index.map(function(value) {
				return value[0];
			});
			results = results.filter(function(value) {
				return ! ids.contains(value[0]);
			}, this);
		}
		if (typeOf(this.options.resultsFilter) == 'function') {
			results = this.options.resultsFilter(results);
		}
		this.hidePlaceholder();
		if ( !this.options.showAllValues && ! results.length) {
			this.showPlaceholder(this.options.remote.emptyResultPlaceholder);
		} else
			this.container.setStyle('display', 'block');

		if ( !this.options.showAllValues && ! results.length) return;
		this.blur();
		this.list.empty().setStyle('display', 'block');

		if(this.options.showAllValues) {
			var values = [];
			this.values.each(function(value){
				if(!results.contains(value))
					values.push(value);
			});
			results = results.append(values);
		}

		results.each(function(result) {
			this.addResult(result, search);
		}, this);
		if (this.options.onlyFromValues) {
			this.focusFirst();
		}
		this.results = results;
	},

	setSelected: function(selected)
	{
		for (var i = 0; i < selected.length; i++)
		{
			var value = selected[i];
			var b = this.textboxlist.create('box', value.slice(0, 3));
			if (b){
				b.autoValue = value;
				if (this.index != null)
					this.index.push(value);
				var afterEl = this.textboxlist.list.getLast('.' + this.textboxlist.options.prefix + '-bit-box');
				b.inject(afterEl || this.textboxlist.list, afterEl ? 'after' : 'top');
			}
		}
	}
});

TextboxList.Autocomplete.Methods = {

	standard: {
		filter: function(values, search, insensitive, max) {
			var newvals = [], regexp = new RegExp('\\b'+search.escapeRegExp(), insensitive ? 'i' : '');
			for (var i = 0; i < values.length; i++) {
				if (newvals.length === max) break;
				if (values[i][1].test(regexp)) {
					newvals.push(values[i]);
				}
			}
			return newvals;
		},

		highlight: function(element, search, insensitive, klass) {
			var regex = new RegExp('(<[^>]*>)|(\\b'+search.escapeRegExp()+')', insensitive ? 'ig' : 'g');
			return element.set('html', element.get('html').replace(regex, function(a, b, c) {
				return (a.charAt(0) == '<') ? a : '<strong class="'+klass+'">'+c+'</strong>';
			}));
		}
	}

};

})();