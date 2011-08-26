<ion:partial view="header" />

<div class="span-24">

	<div class="span-14 prepend-1 colborder">

		<ion:articles type="">
	
			<div class="clear">

				<!-- 
					In this case, we let the article display itself through its defind view.
					To define an article view, go in the page edition panel.
					The view is page dependent.
					In other words, one article can have one view in one page and one another view in another page
					
					See page_home.php for a page display of the aticle content.
				-->
				<ion:article />
			
			</div>

		</ion:articles>

	</div>
	

	<div class="span-7">
	
	<!-- Check if the navigation tag returns something for the level 1 -->
	<?php if('<ion:navigation level="1" index="1" />' != '') :?>
	
		<!-- The sub navigation title -->
		<ion:sub_navigation_title tag="h2" />
		
		<!-- Sub navigation menu -->
		<ul class="links">
			<ion:navigation level="1" />
		</ul>
	
	<?php endif ;?>
		
	</div>
</div>


<!-- Partial : Footer -->
<ion:partial view="footer" />