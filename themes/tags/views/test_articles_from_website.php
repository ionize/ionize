<h1>Articles from website</h1>
<ion:tree_navigation />
<hr/>
<p>Get articles from the whole website.</p>
<p>
	In this example, we add one SQL filter on the menu name and article's "indexed" fields.<br/>
	We also limit the result to 3 articles
</p>
<hr/>

<pre>
&lt;ion:articles filter="menu.name='main' and indexed=1" order-by="date DESC" limit="3">
    &lt;ion:article>
        &lt;ion:title tag="h3" />
        &lt;ion:content paragraph="1" />
    &lt;/ion:article>
&lt;/ion:articles>
</pre>

<h3>Result</h3>
<ion:articles filter="menu.name='main' and indexed=1" order_by="date DESC" limit="3">
	<ion:article>
		<ion:title tag="h3" />
		<ion:content paragraph="1" />
	</ion:article>
</ion:articles>
