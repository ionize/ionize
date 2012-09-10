<ion:partial view="header" xmlns:ion="http://www.w3.org/1999/html"/>

<div class="span-22 prepend-1 append-1">
	<div id="home-slider" >
	
		<div id="wall" ></div>
		
	</div>
</div>

<script type="text/javascript">

	/**
	 * The Wall is a Mootools class created by Plasm : http://wall.plasm.it/
	 * This plugin is used as example and the purpose of the Demo theme isn't to document it.
	 *
	 * This script is Mootools based, but you can use any client side JS framework with Ionize.
	 *
	 */
	var images = new Array();
	var descriptions = new Array();
	var titles = new Array();
	var range_x = 0;
	
	/**
	 * Loop into Pictures linked to the page
	 * Purpose : Feed the javascript plugin images array.
	 *
	 * We use the "940" pixels width thumb declaration
	 * Physically, the thumb folder is named : "thumb_940"
	 * Logically, its name is "940"
	 *
	 * See the produced source in your browser
	 *
	 */
    <ion:page:medias type="picture">

        <ion:media>

            images.push('<ion:media:src size="940,614" adaptive="adaptive_resize" unsharp="true" />');

            /**
             * Title and description of each picture is processed by the PHP function "addslashes"
             * to prevent hangs if the string contains quotes.
             *
             */
            titles.push('<ion:media:title function="addslashes" />');
            descriptions.push('<ion:media:description function="addslashes" />');

            /**
             * range_x stores the total number of pictures linked to the page
             *
             */
            range_x = <ion:media:count />;

        </ion:media>

    </ion:page:medias>
	
   	/**
   	 * The Wall init
   	 */
   	var wall = new Wall("wall", {
		"slideshow":true,
		"showDuration":3000,
		"speed":1000,
		"preload":true,
		"autoposition":true,
		"inertia":true,
		"transition":Fx.Transitions.Expo.easeInOut,
		"width":870,
		"height":400,
		"rangex":[0,range_x],
		"rangey":[0,1]
    });

     callBack = function(items)
     {
		items.each(function(e, i)
		{
			e.node.setStyle("background", "url("+images[e.y]+") no-repeat center center");
			
			if (titles[e.y] != '')
			{
				var layer = new Element('div', {'class':'layer'});
				var h2 = new Element('h2').set('text', titles[e.y]);
				
				layer.set('html', descriptions[e.y]);
				/*.setStyles({
					'bottom': ((Math.random()*200) + 10) + 'px',
					'left': ((Math.random()*200) + 10) + 'px',
				});
				*/
				
				h2.inject(layer, 'top');
				e.node.adopt(layer);
			}
		});
	}
 
     // Define CallBack
     wall.setCallOnUpdate(callBack)	
	 wall.initWall();

</script>


<!-- Home Content -->
<div class="span-22 prepend-1 append-1">

	<!-- Page's subtitle -->
	<h2 class="title"><ion:page:subtitle /></h2>
	
	<!-- Home page articles
		 Loop into articles without "type"
	-->
    <ion:page:articles limit="3" type="">

        <ion:article>

            <div class="span-33 home <ion:if key="index" expression="index==3"> last</ion:if>">

                <!-- We display only one picture of each article
                     So, even the editor makes a mistake by linking 2 pictures, the website design will stay correct
                -->
                <ion:article:medias type="picture" limit="1">
                    <ion:media>
                        <div class="imgborder" >
                            <div class="img" style="background:url(<ion:media:src size="280" master="width" unsharp="true" />);height:130px;"></div>
                        </div>
                    </ion:media>
                </ion:article:medias>

                <!-- Article's title
                     Could also be written <ion:title tag="h2" />
                     if we don't want to display an empty tag if the article has no title
                -->
                <h2><ion:article:title /></h2>

                <!-- Article's content -->
                <ion:article:content />

            </div>

        </ion:article>

    </ion:page:articles>
	
	
	<hr />
		
    <div class="span-6 colborder">
    
    	<!-- Article in the one-fourth column of the home page.
    		 We limit the displayed article to this type
    	-->
        <ion:page:articles type="one-fourth">

                <ion:article>

                    <!-- Article's title
                         In this case, the H2 tag is only displayed if one title is set for the article
                    -->
                    <ion:article:title tag="h2" />

                    <ion:article:content />

                </ion:article>

        </ion:page:articles>
    
    </div>
    
    
    <div class="span-15 last">
    	

    	<!-- Static translated title
    		 This title is static in this view : It has nothing to do with articles.
    		 We call a translated "static term".
    		 Calling a term through a tag makes this term available in the "Static translations" panel of Ionize
    	-->
 		<h2 class="title"><ion:translation term="home_last_post" /></h2>
   		
   		
    	<!-- Articles from another page : Blog
    		 We display only 2 articles : limit attrbiute of the articles tag
    		 For each article, we only display the first paragrph of the content.
    		 We also limit the picture display to the first one.
    	-->
        <ion:page id="6">

            <ion:articles limit="2">

                <ion:article>

                    <!-- In this pecial case, we don't care about article's title -->
                    <div class="home-last-post">

                        <ion:medias type="picture" limit="1">
                            <ion:media>
                                <img src="<ion:media:src size="150" master="width" unsharp="true" />" alt="<ion:media:alt />" class="left imgborder"/>
                            </ion:media>
                        </ion:medias>

                        <!-- The title is a link to the post -->
                        <h3><a href="<ion:url />"><ion:article:title /></a></h3>

                        <!-- Content limited to the first paragraph -->
                        <ion:article:content paragraph="1" />

                    </div>

                </ion:article>

            </ion:articles>

        </ion:page>


    </div>

</div>



<!-- Partial : Footer -->
<ion:partial view="footer" />