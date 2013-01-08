<h1>Articles : "tag" attrbute test</h1>
<ion:tree_navigation />
<hr/>

<ul>
	<li>Get the articles from the current page</li>
	<li>For each article, use the "article" tag and call &lt;ion:id/> to display its ID</li>
</ul>
<hr/>


<ion:page>
	<ion:articles>
		<ion:article>
			<ion:title tag="h3" />
		</ion:article>
		<ion:article:content paragraph="1" />
	</ion:articles>
</ion:page>
