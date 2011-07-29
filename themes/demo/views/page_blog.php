<ion:partial view="header" />



<div class="span-24">

	<div class="span-14 prepend-1 colborder">
	

		<?php if ('<ion:category field="title" />' != '') :?>

			<p id="category_highlight"><em>//</em> <ion:translation term="you_are_browsing_category" /> : <span><ion:category field="title" /></span></p>	
	
		<?php endif; ?>



		<ion:articles type="">

			<ion:medias type="picture">
			
				<img src="<ion:src folder="430" />" />
			
			</ion:medias>
	
			<ion:title tag="h2" class="pagetitle" />

			<ion:categories separator=", " />

			
			<ion:content />

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