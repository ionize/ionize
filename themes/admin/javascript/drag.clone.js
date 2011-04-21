/**
 * Drag.Clone
 * Mootools 1.2.4 Addon
 * Creates a clone from one element and make it draggable
 *
 * Author : Partikule Studio
 * Inspiration : by MonkeyPhysics.com
 * Since:	Ionize 0.9.5
 *
 * Needs : 	
 *  Drag
 *	Drag.Move
 *
 * Arguments:
 *  1. el - (element) The Element to apply the drag to.
 *  2. options - (object, optional) The options object. See below.
 *
 * Options:
 *  All the base Drag.Move options, plus:
 *  - handle : (string: defaults to false) A selector to select an element inside the element to be used as the handle for dragging that element.If no match is found, the element is used as its own handle.
 *
 * Usage : 
 *
 *		var myDrag = new Drag.Clone('draggable', {
 *			
 *			droppables: '.droppable',
 *			
 *			handle: '.drag',
 *			
 *			onDrop: function(element, droppable, event)
 *			{
 *				if (!droppable) console.log(element, ' dropped on nothing');
 *				else console.log(element, 'dropped on', droppable, 'event', event);
 *			},
 *			
 *			onEnter: function(el, droppable)
 *			{
 *				droppable.tween('background-color','#98B5C1');
 *				console.log('enter droppable');
 *			},
 *			
 *			onLeave: function(el, droppable)
 *			{
 *				droppable.tween('background-color','#fff');
 *				console.log('leave droppable');
 *			}
 *		});
 *
 *
 */

Drag.Clone = new Class({
	
	Implements: [Events, Options],
	
	options: {
		droppables: [],
		snap: 4,
		opacity: 1,
		revert: false,
		handle: false,
		precalculate: true,
		style: false,
		classe: false,			// 'class' hangs on Webkit
		'width': false,			//
		'height': false		//

	},
	
	initialize: function(element, options)
	{
		this.setOptions(options);
		this.idle = true;
		
		if (this.options.revert) this.effect = new Fx.Morph(null, Object.merge({},{duration: 250, link: 'cancel'}, this.options.revert));
		
		(this.options.handle ? element.getElement(this.options.handle) || element : element).addEvent('mousedown', function(event) {
			this.start(event, element);
		}.bind(this));
		
		this.dropClasses = this.options.droppables;
		this.droppables = $$(this.options.droppables);
		
		if (typeOf(this.dropClasses) == 'string')
		{
			this.dropClasses = (this.dropClasses).replace(' ', '').split(",");
/*
			droppables.each(function(d)
			{
			console.log($$(d));
				this.options.droppables = (this.options.droppables).append($$(d));
			}.bind(this));
*/
		}
	},

	start: function(event, element)
	{
		if (!this.idle) return;
		this.idle = false;
		this.element = element;
		this.opacity = element.getStyle('opacity');
		this.clone = this.getClone(event, element);
		
		this.drag = new Drag.Move(this.clone, {
			snap: this.options.snap,
			droppables: this.droppables,
			onSnap: function(){
				event.stop();
				this.element.setStyle('opacity', this.options.opacity || 0);
				this.clone.setStyle('visibility', 'visible');
				this.snapped(this.clone);
			}.bind(this),
			onDrag: this.dragged.bind(this),
			onDrop: this.dropped.bind(this),
			onEnter: this.entered.bind(this),
			onLeave: this.leaved.bind(this),
			onCancel: this.reset.bind(this),
			onComplete: this.end.bind(this)
		});

		this.drag.start(event);
	},

	end: function()
	{
		this.drag.detach();
		this.element.setStyle('opacity', this.opacity);
		this.reset();
	},

	reset: function()
	{
		this.idle = true;
		this.clone.destroy();
		this.fireEvent('complete', this.element);
	},
	
	dragged: function(element, event) { this.fireEvent('drag', [element, event]); },
	dropped: function(element, droppable, event) { this.fireEvent('drop', [element, droppable, event]); },
	snapped: function(element) {this.fireEvent('snap', element); },
	entered: function(element, droppable) { this.fireEvent('enter', [element, droppable]); },
	leaved: function(element, droppable) { this.fireEvent('leave', [element, droppable]); },

	getClone: function(event, element)
	{
		if (typeOf(this.options.clone) == 'function') return this.options.clone.call(this, event, element);

		var dim = element.getDimensions();
		var dim = element.getComputedSize();

		var clone = element.clone().setStyles({
			'position': 'absolute',
			'top': element.getCoordinates()['top'],
			'left': element.getCoordinates()['left'],
			'visibility': 'hidden',
			'width': dim.totalWidth + 10 + 'px',
			'display': 'block',
			'z-index': 10000
		});
		
		if (this.options.style) { clone.setStyles(this.options.style);}
		
		if (this.options.classe){ clone.addClass(this.options.classe);}
		
		//prevent the duplicated radio inputs from unchecking the real one
		if (clone.get('html').test('radio')) {
			clone.getElements('input[type=radio]').each(function(input, i) {
				input.set('name', 'clone_' + i);
			});
		}
		return clone.inject(document.body);
	}

});

Element.implement({
	makeCloneDraggable: function(options) {
		return new Drag.Clone(this, options);
	}
});