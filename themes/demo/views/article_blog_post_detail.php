
<!-- View used for articles in blog's article detail -->

<div class="post">
	
	<ion:article:title tag="h2" />

	<ion:article:date format="complete" />
	
	<ion:medias type="picture">
	
		<img src="<ion:media:src folder="540" />" />
	
	</ion:medias>
	
	<!-- This article categories -->
	<p class="categories">
		<ion:translation term="categories" /> : <ion:article:categories separator=", " />
	</p>
	

	<ion:article:content />
	
</div>


