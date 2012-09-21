<h1>Debug : trace</h1>

<p>
	Returns the common parent tag content, as got from the model.<br/>
	Only works on expanded tags.
</p>

<ion:tree_navigation />
<hr/>

<pre>
&lt;ion:page:articles>
    &lt;ion:article:title tag="h3" />
    &lt;ion:article:trace />
&lt;/ion:page:articles>
</pre>	
<ion:page:articles>
	<ion:article:title tag="h3" />
	<ion:article:trace />
</ion:page:articles>

