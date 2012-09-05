
<h1>&lt;ion:get /></h1>

<h2>Example 1 : Get internal ID</h2>
<pre>
&lt;ion:page id="3">
	&lt;ion:articles>
		&lt;ul>
			&lt;li>&lt;ion:article:get key="id_article" />&lt;/li>
		&lt;/ul>
	&lt;/ion:articles>
&lt;/ion:page>
</pre>

<h3>Result</h3>

<ion:page id="3">
	<ion:articles>
		<ul>
			<li><ion:article:get key="id_article" /></li>
		</ul>
	</ion:articles>
</ion:page>

<hr/>

<h2>Example 2 : Get extend field</h2>
<pre>
&lt;ion:page id="3">
	&lt;ion:articles>
		&lt;ul>
			&lt;li>&lt;ion:article:get key="extend1" />&lt;/li>
		&lt;/ul>
	&lt;/ion:articles>
&lt;/ion:page>
</pre>

<h3>Result</h3>

<ion:page id="3">
	<ion:articles>
		<ul>
			<li><ion:article:get key="extend1" /></li>
		</ul>
	</ion:articles>
</ion:page>

