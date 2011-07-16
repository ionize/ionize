<ion:partial view="header" />

<!-- Home Picture Slider
<div class="span-22 prepend-1 append-1">
	<div id="home-slider" >
		<div id="slideshow">
			<div id="s3slider">
				<ul id="s3sliderContent">
				
					<ion:medias type="picture">
						<li class="s3sliderImage">
							<img src="<ion:src folder="940" />" alt="" />
							<div>
								<h1><ion:title /></h1>
								<p><ion:description /></p>
								
								<!-- The copyright will be displayed inside a "p" tag with class"copyright"
									 Nothing will be displayed if no copyright is filled on the picture infos
								<ion:copyright tag="p" class="copyright" prefix="© " /><ion:date format="Y" tag="p" class="date" />
							</div>
						</li>
					</ion:medias>
	
					<li class="clear s3sliderImage"></li>
				</ul>
			</div>
		</div>
	</div>
</div>
-->

<div class="span-22 prepend-1 append-1">
	<div id="home-slider" >
	
		<div id="wall" ></div>
		
	</div>
</div>

<script type="text/javascript">

	var images = new Array();
	var descriptions = new Array();
	var titles = new Array();
	
	<ion:medias type="picture">
		images.push('<ion:src folder="940" />');
		titles.push('<ion:title />');
		descriptions.push('<ion:description />');
	</ion:medias>
	
   	
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
		"rangex":[0,4],
		"rangey":[0,1]
    });

     callBack = function(items)
     {
		items.each(function(e, i){
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
   	
    	<!-- Article of type "three-fourth" -->
    	<ion:articles type="three-fourth">
    	
			<!-- In this pecial case, we don't care about article's title -->
			<div>
				<ion:medias type="picture" limit="1">
					<img src="<ion:src folder="145" />" alt="" class="alignleft imgborder"/>				
				</ion:medias>
				
				<ion:title tag="h5" />
				
				<ion:content />

			</div>

    	</ion:articles>

    </div>

</div>



<!-- Partial : Footer -->
<ion:partial view="footer" />