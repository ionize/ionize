![Screenshot](https://raw.github.com/plasm/the-wall/master/logotipo.png)

The Wall - A Javascript plugin for Mootools
===========================================

The Wall is a plugin for Mootools javascript framework designed to create walls of infinite dimensions. Its flexibility allows different applications, from infinite wall mode to Coda slider mode. The Wall creates compatible interfaces with the newer browsers and iPhone and iPad mobile devices.

Urls The Wall
=============
The trailer: [Trailer](http://www.vimeo.com/plasm/the-wall "The Wall trailer")

Project site: [wall.plasm.it](http://wall.plasm.it "The Wall")

My portfolio: [www.plasm.it](http://www.plasm.it "Plasm")


How to use
----------

Snippet code Javascript:

	#JS
	
    // Define The Wall
    var maxLength = 100; // Max Number images or array length
    var counter   = 1;
    var wall = new Wall("wall", {
                    "width":150,
                    "height":150,
                    "rangex":[-100,100],
                    "rangey":[-100,100],
                    callOnUpdate: function(items){
                        items.each(function(e, i){
                            <!-- This is example code -->
                            var a = new Element("img[src=/your/folder/images/"+counter+".jpg]");
                                a.inject(e.node).fade("hide").fade("in");
                            counterFluid++;
                            // Reset counter
                            if( counter > maxLength ) counter = 1;
                        })
                    }
                });
    // Init Wall
    wall.initWall();

Snippet code HTML:

	#HTML
	
	<!-- Viewport and Wall -->
    <div id="viewport">
        <div id="wall"></div>
    </div>
    <!-- END Viewport and Wall -->

Snippet code CSS:

	#CSS

    /* Minimal Css Required */
    #viewport{
        width:900px;
        height:450px;
        position:relative;
        overflow:hidden;
        margin:0 auto;
        background:#111111  ;
    }

    #wall{
        z-index:1;
    }