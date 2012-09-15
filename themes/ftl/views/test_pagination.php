<h1>Pagination</h1>
<ion:tree_navigation />

<hr/>

<pre>
&lt;!-- Articles list -->
&lt;ion:articles pagination="2">
	&lt;h3>&lt;ion:article:title/>&lt;/h3>
	&lt;ion:article:content paragraph="1" />
&lt;/ion:articles>
	
&lt;!-- Pagination -->
&lt;ion:articles:pagination pagination="2" />
</pre>

<h3>Result</h3>
<!-- Articles list -->
<ion:articles pagination="2">
	<h3><ion:article:title/></h3>
	<ion:article:content paragraph="1" />
</ion:articles>

<!-- Pagination -->
<ion:articles:pagination pagination="2" />


