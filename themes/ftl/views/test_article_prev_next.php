<h1>Previous / Next article</h1>

<h2>Navigation menus</h2>

<h3>Pages : </h3>
<ion:navigation helper="false" tag="ul">
	<li>
		<a <ion:href /> >
		<ion:id /> : <ion:url /><ion:is_active> <span class="red">(is active)</span></ion:is_active>
		</a>
	</li>
</ion:navigation>

<h3>Articles</h3>
<ion:articles tag="ul">
	<ion:article:url href="true" tag="li" display="title"/>
</ion:articles>




<h2></h2>

<h3>Result</h3>

<ion:articles>
	<h4><ion:article:id /> : <ion:article:title /></h4>
</ion:articles>

<ion:articles:article:prev loop="false" href="true" tag="p" prefix="lang('previous_article')" />
<ion:articles:article:next loop="false" href="true" tag="p" prefix="lang('next_article')" />



