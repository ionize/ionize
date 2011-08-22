<ion:partial view="header" />



<div class="span-24">

	<div class="span-14 prepend-1 colborder blog">
	

		<?php if ('<ion:category field="title" />' != '') :?>

			<p id="category_highlight"><em>//</em> <ion:translation term="you_are_browsing_category" /> : <span><ion:category field="title" /></span></p>	
	
		<?php endif; ?>


		<!-- 
			We explicitely get the articles which don't have any type set.
		-->
		<ion:articles type="">
			
			<!-- 
				In the "Blog" page edition panel of Ionize, we set the views of articles for this page :
				
				List view : 	"Blog Post List"
								This view will be used for the post list or if just aticle is posted
								
				Article View : 	"Blog Post"
								This view will be used for one post single view
				
			-->
			<ion:article />
		
		</ion:articles>
	
	</div>
	

	<div class="span-7">
		
		<div class="side-block">
		
			<h2><ion:translation term="title_categories" /></h2>
			
			<ul class="links">
				<ion:categories>
				
					<li><a class="<ion:active_class />" href="<ion:url />"><ion:title /></a></li>
					
				</ion:categories>
			</ul>
		
		</div>
		
		<div class="side-block">
			
			<h2><ion:translation term="title_archives" /></h2>
			
			<ul class="links">

				<ion:archives with_month="true">
			
					<li><a class="<ion:active_class />" href="<ion:url />"><ion:period /></a></li>
			
				</ion:archives>
			</ul>
			
		</div>


		<div class="side-block">
			
			<ion:widget name="weather" id="FRXX0076" unit="c" />

		</div>
		
		<div class="side-block">
			
			<ion:widget name="rss" url="http://www.ecrans.fr/spip.php?page=backend" nb="3" />

		</div>
		
	</div>

</div>


<!-- Partial : Footer -->
<ion:partial view="footer" />