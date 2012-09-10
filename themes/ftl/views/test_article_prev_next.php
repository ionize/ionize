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



<h2>Next / Previous Article</h2>

<pre>
&lt;h3>Current Article&lt;/h3>
&lt;h4>&lt;ion:article:id /> : &lt;ion:article:title />&lt;/h4>

&lt;h3>Next and previuous&lt;/h3>
&lt;ion:article:prev loop="false" href="true" tag="p" prefix="lang('previous_article')" />
&lt;ion:article:next loop="false" href="true" tag="p" prefix="lang('next_article')" />
</pre>
	
<h3>Current Article</h3>
<h4><ion:article:id /> : <ion:article:title /></h4>

<h3>Next and previuous</h3>
<ion:article:prev loop="false" href="true" tag="p" prefix="lang('previous_article')" />
<ion:article:next loop="false" href="true" tag="p" prefix="lang('next_article')" />



