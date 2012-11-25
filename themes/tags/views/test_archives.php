<h1>&lt;ion:archives /></h1>

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
			<ion:archive:period /> - (<ion:archive:nb_articles /> articles)
		</a>
	</li>
</ion:archives>

<h2>Archives articles list</h2>

<ion:page:articles>
	<ion:article:title tag="h3" />
	<ion:article:content paragraph="1" />
</ion:page:articles>