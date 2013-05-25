/*
---
description: TextboxList

authors:
  - Guillermo Rauch

requires:
  core/1.2.1: '*'

provides:
  - textboxlist.autocomplete.binary
...
*/

(function(){

TextboxList.Autocomplete.Methods.binary = {

	filter: function(values, search, insensitive, max) {
		var method = insensitive ? 'toLowerCase' : 'toString', low = 0, high = values.length - 1, lastTry;
		search = search[method]();
		while (high >= low) {
			var mid = parseInt((low + high) / 2);
			var curr = values[mid][1].substr(0, search.length)[method]();
			var result = ((search == curr) ? 0 : ((search > curr) ? 1 : -1));
			if (result < 0) {
				high = mid - 1;
				continue;
			}
			if (result > 0) {
				low = mid + 1;
				continue;
			}
			if (result === 0) break;
		}	
		if (high < low) return [];
		var newvalues = [values[mid]], checkNext = true, checkPrev = true, v1, v2;
		for (var i = 1; i <= values.length - mid; i++) {
			if (newvalues.length === max) break;
			if (checkNext) {
				v1 = values[mid + i] ? values[mid + i][1].substr(0, search.length)[method]() : false;
			}
			if (checkPrev) {
				v2 = values[mid - i] ? values[mid - i][1].substr(0, search.length)[method]() : false;
			}
			checkNext = checkPrev = false;
			if (v1 === search) {
				newvalues.push(values[mid + i]);
				checkNext = true;
			}
			if (v2 === search) {
				newvalues.unshift(values[mid - i]);
				checkPrev = true;
			}
			if ( ! (checkNext || checkPrev)) break;
		}
		return newvalues;
	},

	highlight: function(element, search, insensitive, klass) {
		var regex = new RegExp('(<[^>]*>)|(\\b'+search.escapeRegExp()+')', insensitive ? 'ig' : 'g');
		return element.set('html', element.get('html').replace(regex, function(a, b, c, d) {
			return (a.charAt(0) == '<') ? a : '<strong class="'+klass+'">'+c+'</strong>'; 
		}));
	}

};

})();