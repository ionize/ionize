/*
Script: PlaceholderInput.js
	Sets a default message for input type="text" and textareas

	License:
		MIT-style license.

	Authors:
		Guillermo Rauch
*/

var PlaceholderInput = new Class({
  
  initialize: function(element, klass){
    this.klass = klass || 'input_placeholder';
    this.element = $(element).store('placeholder', this);    
    this.placeholder = this.element.get('placeholder');
    this.element.addEvents({
      'focus': this.focus.bind(this),
      'blur': this.blur.bind(this)
    }).removeClass(this.klass).set('autocomplete', 'off');
    this.reset();
  },
  
  focus: function(){
    if(this.placeholder && (this.element.get('value', true) == this.placeholder) && this.active) {
      this.element.set('value', '', true).removeClass(this.klass);
			this.active = false;
    }
  },
  
  blur: function(){
    if(this.placeholder && this.element.get('value', true) == '') {
      this.element.addClass(this.klass).set('value', this.placeholder, true);
      this.active = true;
    }
  },
  
  reset: function(){
    this.focus();
    this.blur();
    return this;
  },
  
  setText: function(v){
    this.placeholder = v;
    this.element.set('placeholder', v).set('value', '');
  }
  
});

Element.Properties.value = {

  get: function(real){
    var value = this.value, place = this.retrieve('placeholder');
    if(real || ! place) return value;
    if(place && place.active) return '';
    return value;
  },

  set: function(value, real){
    var place = this.retrieve('placeholder');
    this.value = value;
    if(place && ! real) place.reset();
  }
  
};