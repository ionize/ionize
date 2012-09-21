<h1>Archives & Pagination</h1>
<ion:tree_navigation />
<hr/>

<h2>Archives menu</h2>
<p>Displayed on pages which haves articles.</p>

<pre>
&lt;ion:archives month="true" tag="ul">
	&lt;li>
		&lt;a class="&lt;ion:archive:active_class />" href="&lt;ion:archive:url />">
			&lt;ion:archive:period /> - (&lt;ion:archive:count /> articles)
		&lt;/a>
	&lt;/li>
&lt;/ion:archives>
</pre>
<ion:archives month="true" tag="ul">
	<li>
		<a class="<ion:archive:active_class />" href="<ion:archive:url />">
			<ion:archive:period /> - (<ion:archive:count /> articles)
		</a>
	</li>
</ion:archives>

<h2>Archives articles list</h2>
<pre>
&lt;!-- Articles list -->
&lt;ion:page:articles pagination="2" >
    &lt;ion:article:title tag="h3" />
    &lt;ion:article:content paragraph="1" />
&lt;/ion:page:articles>

&lt;!-- Pagination menu -->
&lt;ion:page:articles:pagination pagination="2" />
</pre>

id page : <ion:page:id />

<!-- Articles list -->
<ion:page:articles pagination="2" >
	<ion:article:title tag="h3" />
	<ion:article:content paragraph="1" />
</ion:page:articles>

<!-- Pagination menu -->
<ion:page:articles:pagination pagination="2" />
