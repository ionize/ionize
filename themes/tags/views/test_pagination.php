<h1>Pagination</h1>
<ion:tree_navigation />

<hr/>

<h2>Pagination with attribute value</h2>
<pre>
&lt;!-- Articles list -->
&lt;ion:page:articles pagination="2">
    &lt;h3>&lt;ion:article:title/>&lt;/h3>
    &lt;ion:article:content paragraph="1" />
&lt;/ion:page:articles>

&lt;!-- Pagination -->
&lt;ion:page:articles:pagination pagination="2" />
</pre>

<!-- Articles list -->
<ion:page:articles pagination="3">
	<h3><ion:article:title/></h3>
	<ion:article:content paragraph="1" />
</ion:page:articles>

<!-- Pagination -->
<ion:page:articles:pagination pagination="3" />


<h2>Pagination set by Ionize</h2>
<ion:page:articles>
    <h3><ion:article:title/></h3>
    <ion:article:content paragraph="1" />
</ion:page:articles>

<!-- Pagination -->
<ion:page:articles:pagination />
