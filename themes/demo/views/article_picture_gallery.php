
<!--
	Article used as a picture gallery
	The Mootools plugin "Milkbox" is used for this example (http://reghellin.com/milkbox)
	but you can use any other system, from any framework.
-->


	
	<h2><ion:title /></h2>

	<!-- The content as introduction text -->
	<ion:content />
	
	<!-- Loop in pictures linked to the article -->
	<ion:medias type="picture">
		
		<!-- Link to the full sized picture -->
		<a href="<ion:src />" data-milkbox="milkbox:g1" title="<ion:title />" class="imgborder gallery-thumb<?php if (<ion:index/>%5 == 0) :?> last<?php endif ;?>">

			<!-- 
				The displayed thumb comes from the folder 145
				Thumb Folder name in Ionize (Settings > Advanced Settings > Thumbails) : 145
				Physical thumb folder : /files/<picture_folder>/thumb_145
			-->
			<img src="<ion:src folder="150" />" alt="<ion:alt />" />
		</a>
				
	</ion:medias>



