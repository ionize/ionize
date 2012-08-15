<ion:partial view="header" />


<div class="row">
	<div class="eight columns">
	
		<?php if ('<ion:category field="title" />' != '') :?>

			<p id="category_highlight"><em>//</em> <ion:translation term="you_are_browsing_category" /> : <span><ion:category field="title" /></span></p>	
	
		<?php endif; ?>


			<!--
				In the "Blog" page edition panel of Ionize, we set the views of articles for this page :
				
				List view : 	"Blog Post List"
								This view will be used for the post list or if just aticle is posted
								
				Article View : 	"Blog Post"
								This view will be used for one post single view
				
			-->
			<ion:article />
		
	</div>

	<div class="four columns">

		<div class="side-block">
		
			<h3><ion:translation term="title_categories" /></h3>
			
			<ul class="links">
				<ion:categories>
				
					<li><a class="<ion:active_class />" href="<ion:url />"><ion:title /></a></li>
					
				</ion:categories>
			</ul>
		
		</div>
		
		<div class="side-block">
			
			<h3><ion:translation term="title_archives" /></h3>
			
			<ul class="links">

				<ion:archives with_month="true">
			
					<li><a class="<ion:active_class />" href="<ion:url />"><ion:period /></a></li>
			
				</ion:archives>
			</ul>
			
		</div>

	</div>

</div>





<!-- Partial : Footer -->
<ion:partial view="footer" />