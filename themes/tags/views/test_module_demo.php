<h1>Module Demo</h1>

<p>
	This module manage and displays "authors" of articles.
</p>

<ion:tree_navigation />
<hr/>

<h2>Display all authors</h2>
<pre>

</pre>
<ion:demo:authors:author field="name" tag="p" />


<h2>Display article's authors</h2>
<pre>
</pre>
<ion:page:articles>

	<ion:article:title tag="h3" />

    <ion:article:authors:author field="name" tag="p" />

</ion:page:articles>

