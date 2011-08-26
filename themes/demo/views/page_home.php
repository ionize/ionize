<ion:partial view="header" />

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
	<ion:medias type="picture">
	
		images.push('<ion:src folder="940" />');
		
		/**
		 * Title and description of each picture is processed by the PHP function "addslashes"
		 * to prevent hangs if the string contains quotes.
		 *
		 */
		titles.push('<ion:title function="addslashes" />');
		descriptions.push('<ion:description function="addslashes" />');
		
		/**
		 * range_x stores the total number of pictures linked to the page
		 *
		 */
		range_x = <ion:count />;
	
	</ion:medias>
	
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
	<h2 class="title"><ion:subtitle /></h2>
	
	<!-- Home page articles
		 Loop into articles without linked view : Article's fields are directly displayed
	-->
	<ion:articles limit="3" type="">

		<div class="span-33 home <?php if('<ion:index />' == 3) :?> last<?php endif;?>">
	
		
			<!-- We display only one picture of each article
				 So, even the editor makes a mistake by linking 2 pictures, the website design will stay correct
			-->
			<ion:medias type="picture" limit="1">
				<div class="imgborder" >
					<div class="img" style="background:url(<ion:src folder="280" />);height:130px;"></div>
				</div>
			</ion:medias>

			<!-- Article's title 
				 Could also be written <ion:title tag="h2" /> 
				 if we don't want to display an empty tag if the article has no title
			-->
			<h2><ion:title /></h2>
			
			<!-- Article's content -->
			<ion:content />
			
		</div>
		
	</ion:articles>
	
	
	<hr />
		
    <div class="span-6 colborder">
    
    	<!-- Article in the one-fourth column of the home page.
    		 We limit the displayed article to this type
    	-->
    	<ion:articles type="one-fourth">
    	
    		<!-- Article's title
    			 In this case, the H2 tag is only displayed if one title is set for the article
    		-->
    		<ion:title tag="h2" />
    		
    		<ion:content />
    	
    	</ion:articles>
    
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
    	<ion:articles from="blog" limit="2">
    	
			<!-- In this pecial case, we don't care about article's title -->
			<div class="home-last-post">
				
				<ion:medias type="picture" limit="1">
					<img src="<ion:src folder="150" />" alt="" class="left imgborder"/>				
				</ion:medias>
				
				<!-- The title is a link to the post -->
				<h3><a href="<ion:url />"><ion:title /></a></h3>
				
				<!-- Content limited to the first paragraph -->
				<ion:content paragraph="1" />

			</div>

    	</ion:articles>

    </div>

</div>



<!-- Partial : Footer -->
<ion:partial view="footer" />