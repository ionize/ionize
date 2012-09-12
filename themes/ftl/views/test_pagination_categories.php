<h1>Categories & Pagination</h1>
<ion:tree_navigation />
<hr/>

<h2>Categories used by articles linked to the current page</h2>
<pre>
&lt;ion:page>
	&lt;ion:categories tag="ul">
		&lt;ion:category>
			&lt;li>
				&lt;a href="&lt;ion:url />">&lt;ion:title /> - (&lt;ion:nb_articles /> articles)&lt;/a>
			&lt;/li>
		&lt;/ion:category>
	&lt;/ion:categories>
&lt;/ion:page>
</pre>

<ion:page>
	<ion:categories tag="ul">
		<ion:category>
			<li>
				<a href="<ion:url />"><ion:title /> - (<ion:nb_articles /> articles)</a>
			</li>
		</ion:category>
	</ion:categories>
</ion:page>

<hr/>


<h2>Current category articles list</h2>

<p>
	Filter on Category : <b class="red"><ion:category:current:title /></b>
</p>
<ion:articles pagination="2">
	<ion:article:title tag="h3" />
	<ion:article:categories link="true" separator=" &bull; " tag="p" />
	<ion:article:content paragraph="1" />
</ion:articles>

<!-- Pagination menu -->
<ion:articles:pagination pagination="2" loop="false" />
