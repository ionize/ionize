/*
---
description: The Wall is a plugin for Mootools javascript framework designed to create walls of infinite dimensions. Its flexibility allows different applications, from infinite wall mode to Coda slider mode. The Wall creates compatible interfaces with the newer browsers and iPhone and iPad mobile devices.

license: MIT-style

authors:
- Marco Dell'Anna

requires:
- core/1.3: '*'

provides: [MooStarRating]

...
*/

/*
 * Mootools The Wall
 * Version 1.0
 * Copyright (c) 2011 Marco Dell'Anna - http://www.plasm.it
 *
 * Inspiration:
 * - Class implementation inspired by [Infinite Drag] (http://ianli.com/infinitedrag/) by Ian Li, Licensed under the MIT (http://www.opensource.org/licenses/mit-license.php) license.
 *
 * Requires:
 * MooTools http://mootools.net
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 *
 * Log:
 * 1.0 - Inizio implementazione release stabile
 */
 
 
 
var Wall = new Class({
    __target: undefined,
    init : false,
    Implements : Options,
    id   : 0, // ID Elemento Attivo
    coordinates :[],
    wall : undefined,
    viewport : undefined,
    grid : [],
    minx : 0,
    maxx : 0,
    wallFX : undefined,
    slideshowInterval:undefined,
    options : {
        printCoordinates : false,             // Inserisce le coordinate nel tile
        speed            : 1000,              // Velocità spostamento
        transition       : Fx.Transitions.Quad.easeOut,
        autoposition     : false,             // Autoposizionamento wall
        draggable        : true,              // Abilita drag
        inertia          : false,             // Abilita inertia
        invert           : false,             // Inverte direzione drag
        width            : 0,                 // W tile
        height           : 0,                 // H tile
        startx           : 0,                 // Tile iniziale
        starty           : 0,                 // Tile iniziale
        rangex           : [-500, 500],       // Definisce il numero di colonne (non pixel)
        rangey           : [-500, 500],       // Definisce il numero di righe (non pixel)
        handle           : undefined,         // Definisce un differente handle
        slideshow        : false,             // Abilita Slideshow Wall
        showDuration     : 3000,              // Durata visualizzazione Slideshow
        preload          : false,             // Precarica contenuto
        callOnUpdate     : Function,          // Azione on drag/complete
        callOnChange     : Function           // Azione scatenata quando viene impostato id elemento attivo
    },

    initialize : function(id, options) {
        // Set opzioni
        this.setOptions(options);
        this.__target   = id;
        // Imposta wall e Viewport
        this.wall       = $(this.__target);
        this.viewport   = $(this.__target).getParent();
    },
    
    /**
     * Initialize The Wall
     */
    initWall : function() {
        // Calcola tutte le coordinate
        this.coordinates = this.calculateCoordinates();
        // Prepopolate
        if( this.options.preload == true ) this.preloadContent();
        // Calcola Spostamento Min e Max per Assi X,Y
        var bb = this.setBoundingBox();
        // Imposta Coordiname BB
        this.maxx = bb.maxx;
        this.maxy = bb.maxy;
        this.minx = bb.minx;
        this.miny = bb.miny;
        
        // Verifica Init Class
        if( this.init == false ){
            // Definisce Effetto di spostamento
            this.wallFX = new Fx.Morph(this.wall, {
                duration: this.options.speed,
                transition: this.options.transition,
                onStart: function(){
                  /*periodicalID = (function(){ 
                      this.options.callOnUpdate(this.updateWall());
                  }).periodical(Math.floor(this.options.speed/4), this);*/
                }.bind( this ),
                onComplete: function(){
                    this.options.callOnUpdate(this.updateWall());
                   // clearTimeout(periodicalID);
                }.bind( this )
            });
            // Inizializza Resize Windows
            // window.addEvent('resize', function(){ this.initialize() }.bind( this ));
            // Inizializza Class
            this.init = true;
        }else{
            // Sgancia elemento solo se draggabile
            if( this.options.draggable == true ) this.wallDrag.detach();
        }

        // Definisce Handler
        var handler = this.options.handle != undefined ? $(this.options.handle) : $(this.__target);
        // Click sul Wall
        $(this.__target).addEvent("click", function(e){
            e.stopPropagation();
            // Reset Movement
            this.moved = 0;
        }.bind( this ))
        
        // Definisce oggetto draggabile
        if( this.options.draggable == true ){
            this.wallDrag = $(this.__target).makeDraggable({
                handle:handler,
                limit: {
                            x: [this.minx, this.maxx],
                            y: [this.miny, this.maxy]
                        },
                invert:this.options.invert,
                onStart: function(el, e){
                    clearTimeout(this.periodicalID);
                    // Reset Movement
                    this.moved = 0;
                    // Posizione Inizio Drag
                    this.xPos = e.page.x;
                    this.yPos = e.page.y;
                }.bind( this ),
                onDrag: function(el, e){
                    this.xspeed = e.page.x - this.xPos; // x mouse speed
                    this.yspeed = e.page.y - this.yPos; // y mouse speed
                    this.xPos   = e.page.x;
                    this.yPos   = e.page.y;
                    //
                    e.stopPropagation();
                    // Interrompe Slideshow
                    this.clearSlideShow();
                    // Tronca transizione se riparte il drag
                    if( this.wallFX ) this.wallFX.cancel();
                    this.options.callOnUpdate(this.updateWall());
                    // Considera movimento
                    this.moved++;
                }.bind( this ),
                onComplete: function(el, e){
                    e.preventDefault();
                    // Verifica inertia
                    if( this.options.inertia == true ){
                        // START Inertia
                        this.periodicalID = (function(){ 
                            if( this.options.invert == true ){
                                var finX = this.wall.getStyle("left").toInt() - this.xspeed;
                                var finY = this.wall.getStyle("top").toInt()  - this.yspeed;
                            }else{
                                var finX = this.wall.getStyle("left").toInt() + this.xspeed;
                                var finY = this.wall.getStyle("top").toInt()  + this.yspeed;
                            }
                            if( finX < 0) this.wall.setStyle("left", Math.max(this.minx, finX));
                            if( finY < 0) this.wall.setStyle("top",  Math.max(this.miny, finY));
                            if( finX > 0) this.wall.setStyle("left", Math.min(this.maxx, finX));
                            if( finY > 0) this.wall.setStyle("top",  Math.min(this.maxy, finY));
                            
                            // Decrementa velocità di spostamento
                            this.xspeed *= 0.9;
                            this.yspeed *= 0.9;
                            // Aggiorna Wall
                            this.options.callOnUpdate(this.updateWall());
                            // Interrompe spostamento se prossimo a 0.6
                            if (Math.abs(this.xspeed) < 2 && Math.abs(this.yspeed) < 2) {
                                // Attiva elemento del coda, se presente
                                var p = this.calculateProximity();
                                // Calcola l'id in base alle coordinate
                                this.id = this.getIdFromCoordinates(p.c,p.r);
                                // Attiva elemento del coda
                                this.codaActiveItem(this.id);
                                this.options.callOnUpdate(this.updateWall());
                                // Ricalcola posizione
                                if( this.options.autoposition == true ) this.normalizePosition();
                                // Clear Periodical
                                clearTimeout(this.periodicalID);
                            }
                        }).periodical(20, this);
                        // END Inertia
                    }
                    // Riposizionamento automatico
                    if( this.options.autoposition == true && this.options.inertia == false){
                        // Riposiziona, se richiesto e se lo slideshow è terminato
                        if( this.slideshowInterval == undefined || this.options.slideshow == false ) this.normalizePosition();
                    }else{
                        // Attiva elemento del coda, se presente
                        var p = this.calculateProximity();
                        // Calcola l'id in base alle coordinate
                        this.id = this.getIdFromCoordinates(p.c,p.r);
                        // Attiva elemento del coda
                        this.codaActiveItem(this.id);
                    }
                    // Callback wall    
                    this.options.callOnUpdate(this.updateWall());
                }.bind( this )
            });
            // Imposta Cursore
            this.wall.setStyle("cursor", "move");
            // Scarica Prediodical
            this.wallDrag.addEvent("mousedown", function(e){
                e.stop();
                clearTimeout(this.periodicalID);
                e.stopPropagation();
            }.bind(this));
        }else{
            // Imposta Cursore default
            this.wall.setStyles({
                                    "cursor":"default",
                                    "position":"absolute"
                                });            
        }

        // Imposta posizione iniziale
        this.wall.setStyles({
            "left": this.options.startx*this.options.width,
            "top": this.options.starty*this.options.height
        });
        
        // Aggiorna Wall ed esegue CallBack di creazione
       this.options.callOnUpdate(this.updateWall());

        // Inizializza Slideshow
        if( this.options.slideshow == true ) this.initSlideshow();
       
        // Inizializza Device Mobile
        if( this.detectMobile() ) this.initMobile();

        //
        return this;
    },
    
    /**
     * Verifica se il Wall si è spostato
     * @return boolean
     */
    getMovement: function(){
        var m = this.moved > 0 ? true : false;
        // Resetta variabile movimento
        this.moved = 0;
        return m;
    },
    
    /**
     * @PRIVATE
     * Calcola lo spazio di contenimento del wall e il relativo spostamento
     * @return oggetto {minx, miny, maxx, maxy}
     */
    setBoundingBox: function(){
        // Estrae Coordinate Viewport
        var vp_coordinate = this.viewport.getCoordinates();
        // Tile Size
        var tile_w = this.options.width;
        var tile_h = this.options.height;
        // Viewport Size
        var vp_w = vp_coordinate.width;
        var vp_h = vp_coordinate.height;
        var vp_cols   = Math.ceil(vp_w / tile_w);
        var vp_rows   = Math.ceil(vp_h / tile_h);
        // Calcola X min e X max
        var maxx = Math.abs(this.options.rangex[0]) * tile_w;
        var maxy = Math.abs(this.options.rangey[0]) * tile_h;
        var minx = -( (Math.abs(this.options.rangex[1])) * tile_w ) + vp_w;
        var miny = -( (Math.abs(this.options.rangey[1])) * tile_h ) + vp_h;
        return {"minx":minx,"miny":miny,"maxx":maxx,"maxy":maxy}
    },

    /**
     * @PRIVATE
     * Calcola tutte le coordinate possibili del Wall
     * @return array di oggetti {colonna, riga}
     */
    calculateCoordinates: function(){
        var indice      = 0;
        var coordinates = [];
        for(var r=this.options.rangey[0]; r<this.options.rangey[1]; r++){
            for(var c=this.options.rangex[0]; c<this.options.rangex[1]; c++){
                coordinates[indice] = {"c":c, "r":r};
                if(c==0&&r==0){ this.id = indice; }
                indice++;
            }
        }
        return coordinates;
    },
    
    /**
     * Estrae id da Coordinate spaziali
     * @return numeric id
     */
    getIdFromCoordinates: function(gc,gr){
        var indice = 0;
        for(var r=this.options.rangey[0]; r<this.options.rangey[1]; r++){
            for(var c=this.options.rangex[0]; c<this.options.rangex[1]; c++){
                if(c==gc&&r==gr){ return indice; }
                indice++;
            }
        }
        return indice;
    },
    
    /**
     * Restituisce le coordinate del tassello richiesto
     * @return object o.c, o.r
     */
    getCoordinatesFromId: function(id){
      return this.coordinates[id];
    },
    
    /**
     * Restituisce id elemento attivo
     * @return numeric
     */
    getActiveItem: function(){
        return this.id;
    },
    
    /**
     * @PRIVATE
     * Calcola la posizione più prossima al punto raggiunto
     * @return Object - Coordinate del punto
     */
    calculateProximity: function(){
        var wallx = this.wall.getStyle("left").toInt()*-1;
        var wally = this.wall.getStyle("top").toInt()*-1;
        var w     = this.options.width;
        var h     = this.options.height;
        // Calcola posizione
        var npx = Math.round(wallx/w);
        var npy = Math.round(wally/h);
        return {"c":npx, "r":npy};
    },

    /**
     * @PRIVATE
     * Normalizza la posizione del Wall se è impostato il settaggio "autoposition"
     * @return
     */
    normalizePosition: function(){
        var p = this.calculateProximity();
        // Sposta al punto
        this.moveTo(p.c, p.r);
        return;
    },
    
    /**
     * @PRIVATE
     * Aggiorna gli elementi del wall. Calcola gli elementi visibili non ancora generati
     * @return array new nodes
     */
    updateWall: function(){
        // Array Nodes
        var newItems = [];
        // Estrae Coordinate Wall e Viewport
        var vp_coordinate   = this.viewport.getCoordinates();
        var wall_coordinate = this.wall.getCoordinates();

        // Tile Size
        var tile_w = this.options.width;
        var tile_h = this.options.height;
        
        // Viewport Size
        var vp_w = vp_coordinate.width;
        var vp_h = vp_coordinate.height;
        var vp_cols   = Math.ceil(vp_w / tile_w);
        var vp_rows   = Math.ceil(vp_h / tile_h);

        // Posizioni
        var pos = {
            left: wall_coordinate.left - vp_coordinate.left,
            top:  wall_coordinate.top  - vp_coordinate.top
        }
        
        // Calcola visibilità elemento
        var visible_left_col = Math.ceil(-pos.left / tile_w)  - 1;
        var visible_top_row  = Math.ceil(-pos.top /  tile_h)  - 1;

        for (var i = visible_left_col; i <= visible_left_col + vp_cols; i++) {
            for (var j = visible_top_row; j <= visible_top_row + vp_rows; j++) {
                if (this.grid[i] === undefined) {
                    this.grid[i] = {};
                }
                if (this.grid[i][j] === undefined) {
                    var item = this.appendTile(i, j);
                    if( item.node !== undefined )  newItems.push(item);
                }
            }
        }
        
        // Update viewport info.
        wall_width  = wall_coordinate.width;
        wall_height = wall_coordinate.height;
        wall_cols = Math.ceil(wall_width  / tile_w);
        wall_rows = Math.ceil(wall_height / tile_h);
        
        return newItems;
    },
    
    /**
     * @PRIVATE
     * Aggiunge un elemento al Wall
     * @return object {nodo_Dom, x, y}
     */
    appendTile: function(i,j){
        this.grid[i][j] = true;
        
        // Tile Size
        var tile_w = this.options.width;
        var tile_h = this.options.height;
        // Valori Min/Max
        var range_col = this.options.rangex;
        var range_row = this.options.rangey;
        if (i < range_col[0] || (range_col[1]) < i) return {};
        if (j < range_row[0] || (range_row[1]) < j) return {};
        
        var x    = i * tile_w;
        var y    = j * tile_h;
        var e    = new Element("div").inject(this.wall);
            e.setProperties({
                "class": "tile",
                "col": i,
                "row": j,
                "rel": i+"x"+j
            }).setStyles({
                "position": "absolute",
                "left": x,
                "top": y,
                "width": tile_w,
                "height": tile_h
            })
            if( this.options.printCoordinates ) e.set("text", i+"x"+j);
            return {"node":e, "x":j, "y":i};
    },
    
    /**
     * Esegue operazione di alimentazione massificata eseguendo la generazione di tutti i tasselli
     * Azione applicabile al coda, sconsigliato su wall di grandi dimensioni
     */
    preloadContent: function(){
        // Array Nodes
        var newItems = [];
        Object.each(this.coordinates, function(e){
            if (this.grid[e.c] === undefined) this.grid[e.c] = {};
                var item = this.appendTile(e.c, e.r);
                    newItems.push(item);
        }.bind(this))
        // Popola tutto il wall
        this.options.callOnUpdate(newItems);
        return newItems;
    },
    
    /**
     * Imposta CallBack di di inizializzazione tile del Wall
     */
    setCallOnUpdate: function(f){
        this.options.callOnUpdate = f;
        return f;
    },
    
    /**
     * Imposta CallBack di aggiornamento focus elemento
     */
    setCallOnChange: function(f){
        this.options.callOnChange = f;
        return f;
    },

    /**
     * @PRIVATE
     * Inizializza Slideshow
     * Lo slideshow viene interrotto al Drag o Touch
     */
    initSlideshow: function(){
        // Controllo Speed
        if( this.options.showDuration < this.options.speed ) this.options.showDuration = this.options.speed;
        this.slideshowInterval = this.getAutomaticNext.periodical(this.options.showDuration, this );
    },
    
    /**
     * @PRIVATE
     * Richiede elemento successivo nel coda Slideshow
     * return
     */
    getAutomaticNext: function(){
        this.clearSlideShow();
        if( this.options.slideshow == true ){
            this.slideshowInterval = this.getAutomaticNext.periodical(this.options.showDuration, this );
        }
        // Verifica elemento
        1+this.id > this.coordinates.length-1 ? this.id = 0 : this.id++;
        this.moveTo(this.coordinates[this.id].c, this.coordinates[this.id].r); // Richiede prossima slide
    },

    /**
     * @PRIVATE
     * Interrompe Slideshow
     * return
     */
    clearSlideShow: function(){
        clearTimeout(this.slideshowInterval);
        this.slideshowInterval = undefined;
    },
    
    /**
     * Esegue spostamento del Wall alle coordinate indicate
     * return false || nodo Dom attivo
     */
    moveTo: function(c,r){

        // Verifica validità valori possibile e valore indicato
        if( c < 0 ) c = Math.max(c, this.options.rangex[0]);
        if( c > 0 ) c = Math.min(c, this.options.rangex[1]);
        if( r < 0 ) r = Math.max(r, this.options.rangey[0]);
        if( r > 0 ) r = Math.min(r, this.options.rangey[1]);

        // Esegue Morph
        this.wallFX.cancel().start({
            'left': Math.max(-(c*this.options.width), this.minx),
            'top':  Math.max(-(r*this.options.height), this.miny)
        });
        
        // Calcola l'id in base alle coordinate
        this.id = this.getIdFromCoordinates(c,r);

        // Attiva elemento del coda
        this.codaActiveItem(this.id);
        //
        var name = this.coordinates[this.id].c+"x"+this.coordinates[this.id].r;
        var item = $$("#"+this.__target+" div[rel="+name+"]");
        if( item.length > 0) return $$("#"+this.__target+" div[rel="+name+"]")[0];
        return false;
    },
    
    /**
     * Posiziona il Wall su elemento attivo
     * return Object node Dom elemento con focus di posizionamento
     */
    moveToActive: function(){
        // Muove il Wall alle coordinate del tile con id attivo
        return this.moveTo(this.coordinates[this.id].c, this.coordinates[this.id].r)
    },
    
    /**
     * Posiziona il Wall su elemento successivo
     * return Object node Dom elemento con focus di posizionamento
     */
    moveToNext: function(){
        this.clearSlideShow();
        if( 1+this.id < this.coordinates.length ){ this.id++; }
        return this.moveTo(this.coordinates[this.id].c, this.coordinates[this.id].r)
    },

    /**
     * Posiziona il Wall su elemento precedente
     * return Object node Dom elemento con focus di posizionamento
     */
    moveToPrev: function(){
        this.clearSlideShow();
        if( (this.id-1) >= 0 ){ this.id--; }
        return this.moveTo(this.coordinates[this.id].c, this.coordinates[this.id].r)
    },
    
    /**
     * Richiede la lista dei punti sotto forma di Link
     * @target: ID DOM element dove inserire i links
     * @return array list element
     */
    getListLinksPoints: function( id_target ){
        var items = [];
        // Crea Hyperlink per ogni elemento del Wall
        $each(this.coordinates, function(e,i){
            var a = new Element("a.wall-item-coda[html="+(1+i)+"][href=#"+(1+i)+"]");
                a.addEvent("click", function(evt){
                    // Disabilita slideshow
                    this.clearSlideShow();
                    this.id = i;
                    this.codaActiveItem(i);
                    evt.stop();
                    this.moveTo(e.c, e.r);
                }.bind( this ))
                // Inserisce nel target
                a.inject($(id_target));
                // Aggiunge ad array elementi
                items.push(a);
        }.bind( this ))
        // Imposta id coda target
        this.coda_target = id_target;
        // Imposta lista elementi del coda
        this.coda_items  = items;
        // Imposta attivo il primo elemento del coda
        this.codaActiveItem(0);
        return items;
    },

    /**
     * @PRIVATE
     * Attiva Elemento del Coda console
     * @i indice dell'elemento cliccato 1,2,3,4,5
     * @return node Dom element
     */
    codaActiveItem: function(i){
        // Esegue CallBack
        this.options.callOnChange(i);
        // Attivazione
        if( this.coda_target ){
            // Rimuove link attivi
            $each(this.coda_items, function(e,i){ e.removeClass("wall-item-current"); })
            // Attiva corrente
            this.coda_items[i].addClass("wall-item-current");
            return this.coda_items[i];
        }
    },
    
    /**
     * @PRIVATE
     * Esegue Detect di device iPad, iPod, iPhone
     * @return boolean
     */
    detectMobile: function(){
        var ua = navigator.userAgent;
        var isiPad = /iPad/i.test(ua) || /iPhone OS 3_1_2/i.test(ua) || /iPhone OS 3_2_2/i.test(ua) || /iPhone/i.test(ua) || /iPod/i.test(ua)
        return isiPad;
    },
    
    /**
     * @PRIVATE
     * Inizializza comportamenti per il magico ditino
     */
    initMobile: function(){
        // Touch Start Slider
        this.wall.__root = this
        this.wall.addEvent('touchstart',function(e) {
            if( e ) e.stop();
            
            // Interrompe Slideshow
            this.__root.clearSlideShow();
            
            // Data Start
            this._startXMouse = e.page.x;
            this._startYMouse = e.page.y;
            this._startLeft   = this.getStyle("left").toInt();
            this._startTop    = this.getStyle("top").toInt();
            this._width       = this.getStyle("width").toInt();
            this._height      = this.getStyle("height").toInt();
        });

        // Touch Move Slider
        this.wall.addEvent('touchmove',function(e) {
            if( e ) e.stop();
            // Horizontal
            var _deltax = this._startXMouse - e.page.x;
            var _x      = this.getStyle("left").toInt();

            if( _x  > Math.min(this.__root.minx, 0) ){
                endx = Math.min(this._startLeft - _deltax, this.__root.maxx)
            }else{
                endx = Math.max( this.__root.minx, this._startLeft - _deltax)
            }
            // Imposta posizione X
            if( endx <= this.__root.maxx) this.setStyle("left",  endx );
            
            // Vertical
            var _deltay = this._startYMouse - e.page.y;
            var _y  = this.getStyle("top").toInt();

            if( _y  > Math.min(this.__root.miny, 0) ){
                endy = Math.min(this._startTop - _deltay, this.__root.maxy)
            }else{
                endy = Math.max( this.__root.miny, this._startTop - _deltay)
            }
            // Imposta posizione Y
            if( endy <= this.__root.maxy) this.setStyle("top",  endy );
            
            // Aggiorna Wall ed esegue CallBack di creazione
            this.__root.options.callOnUpdate(this.__root.updateWall());
        });

        // Touch Move End
        this.wall.addEvent('touchend',function(e) {
            if( this.options.autoposition == true){
                // Riposiziona, se richiesto e se lo slideshow è terminato
                if( this.slideshowInterval == undefined || this.options.slideshow == false ) this.normalizePosition();
            }else{
                // Attiva elemento del coda, se presente
                var p = this.calculateProximity();
                // Calcola l'id in base alle coordinate
                this.id = this.getIdFromCoordinates(p.c,p.r);
                // Attiva elemento del coda
                this.codaActiveItem(this.id);
            }
            // Aggiorna Wall ed esegue CallBack di creazione
            this.options.callOnUpdate(this.updateWall());
        }.bind(this));
    }
});