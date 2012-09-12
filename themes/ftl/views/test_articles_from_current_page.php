<h1>Articles from current page</h1>
<ion:tree_navigation />
<hr/>


<ul>
	<li>Get the articles from the current page</li>
	<li>For each article, use the "article" tag and call &lt;ion:id/> to display its ID</li>
</ul>
<hr/>

<pre>
&lt;ion:page>
	&lt;ion:articles>
		&lt;h3>Article ID : &lt;ion:article:id/>&lt;/h3>
		&lt;ion:article>
			&lt;h4>&lt;ion:id /> : &lt;ion:title/>&lt;/h4>
			&lt;ion:content/>
		&lt;/ion:article>
	&lt;/ion:articles>
&lt;/ion:page>
</pre>

<h3>Result</h3>
<ion:page>
	<ion:articles>
		<h3>Article ID : <ion:article:id/></h3>
		<ion:article>
			<h4><ion:id /> : <ion:title/></h4>
			<ion:content paragraph="1" />
		</ion:article>
	</ion:articles>
</ion:page>
