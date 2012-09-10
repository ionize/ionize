<h1>Pagination</h1>

<ion:tree_navigation />

<hr/>

<pre>
&lt;ion:page id="3">
	&lt;ion:articles pagination="2">
		&lt;h3>&lt;ion:article:title/>&lt;/h3>
		&lt;ion:article:content paragraph="1" />
	&lt;/ion:articles>
&lt;/ion:page>

&lt;ion:page id="3">
	&lt;ion:articles:pagination pagination="2" loop="false" />
&lt;/ion:page>
</pre>

<h3>Result</h3>

<ion:page id="3">
	<ion:articles pagination="2">
		<h3><ion:article:title/></h3>
		<ion:article:content paragraph="1" />
	</ion:articles>
</ion:page>

<ion:page id="3">
	<ion:articles:pagination pagination="2" loop="false" />
</ion:page>


