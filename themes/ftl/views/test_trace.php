<h1>Debug : trace</h1>

<p>
	Returns the common parent tag content, as got from the model.<br/>
	Only works on expanded tags.
</p>

<ion:tree_navigation />
<hr/>

<pre>
&lt;ion:articles>
	&lt;ion:article:title tag="h3" />
	&lt;ion:article:trace />
&lt;/ion:articles>
</pre>	
<ion:articles>
	<ion:article:title tag="h3" />
	<ion:article:trace />
</ion:articles>

