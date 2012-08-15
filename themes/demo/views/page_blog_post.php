<ion:partial view="header" />


<div class="span-24">

	<div class="span-14 prepend-1 colborder blog">
	

		<?php if ('<ion:category field="title" />' != '') :?>

			<p id="category_highlight"><em>//</em> <ion:translation term="you_are_browsing_category" /> : <span><ion:category field="title" /></span></p>	
	
		<?php endif; ?>


		<!--
			We explicitely get the articles which don't have any type set.
		-->
		<ion:article>

			<div class="post">

				<h2><ion:title /></h2>
				<ion:date format="complete" />

				<ion:medias type="picture">

					<img src="<ion:src folder="540" />" />

				</ion:medias>

				<!-- Categories -->
				<p class="categories">
					<ion:translation term="categories" /> : <ion:categories separator=", " />
				</p>


				<ion:content />

			</div>
		</ion:article>

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


	</div>

</div>


<!-- Partial : Footer -->
<ion:partial view="footer" />