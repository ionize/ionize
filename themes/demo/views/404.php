<!-- 
	404 page view.
	This view is linked to the 404 page in Ionize.
	It uses the pictures linked to the page.
-->

<ion:partial view="header" />


<div  class="span-22 prepend-1">

	<!-- The Wall script placeholder -->
	<div id="viewport404">
		<div id="overlay404"></div>
		<div id="wall"></div>
	</div>
	
	<!-- Display a map of the website, so the user can jump to another place in the website -->
	
	
	
</div>


<script type="text/javascript">
	
	var images = new Array();
	var nb_medias = 0;
	
	/**
	 * Pictures linked to the page are used to feed a javascript array
	 * See the produced source in your browser
	 *
	 */
	<ion:medias type="picture">
		images.push('<ion:src folder="150" />');
		nb_medias = <ion:count />;
	</ion:medias>

	/**
	 * Wall is a Mootools class created by Plasm : http://wall.plasm.it/
	 *
	 */
	var wall = new Wall("wall", {
		"draggable":true,
		"preload":true,
		"autoposition":true,
		"handle":"overlay404",
		"inertia":true,
        "startx":0,
        "starty":0,
		"width":150,
		"height":150,
		"rangex":[-nb_medias*2,nb_medias*2],
		"rangey":[-nb_medias*2,nb_medias*2],
		callOnUpdate: function(items){
			items.each(function(e, i){
				var counter = Math.ceil(Math.random()*(nb_medias))
				var img = new Element("img[src="+images[counter-1]+"]");
				img.inject(e.node);
			})
		}
	});
	wall.initWall();

</script>

<!-- Partial : Footer -->
<ion:partial view="footer" />