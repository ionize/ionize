<h1>Categories & Pagination</h1>
<ion:tree_navigation />
<hr/>

<h2>Categories used by the articles of the current page</h2>
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
	<b class="red">How it works : </b>
</p>
<ol>
    <li>Click on one category above to activate the category filter</li>
    <li>Click on one pagination link</li>
</ol>

<p>
	Current Category Filter :
	<b class="red">
		<ion:category:current:title expression="!=''">
			<ion:category:current:title/>
		</ion:category:current:title>
		<ion:else>
			No category filter, please click first on one category above !
		</ion:else>
	</b>
</p>

<pre>
&lt;ion:page:articles pagination="2">
    &lt;ion:article:title tag="h4" />
    Categories of this article : &lt;ion:article:categories link="true" separator=" &bull; " />
&lt;/ion:page:articles>

&lt;!-- Pagination menu -->
&lt;ion:page:articles:pagination pagination="2" tag="p" />
</pre>

<h3>Result</h3>
<ion:page:articles pagination="2">
	<ion:article:title tag="h4" />
	Categories of this article : <ion:article:categories link="true" separator=" &bull; " />
</ion:page:articles>

<!-- Pagination menu -->
<ion:page:articles:pagination pagination="2" tag="p" />
