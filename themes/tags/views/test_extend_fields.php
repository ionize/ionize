<h1>Extend fields</h1>

<p>
To use this view, you need :
</p>
<ul>
	<li>One Extend field, not translated, with the name "extend1"</li>
	<li>One Extend field, translated, with the name "extend2"</li>
</ul>

<hr/>

<ion:tree_navigation />

<hr/>

<pre>
</pre>

<ion:page>
	<ion:articles>
		<ion:article>
			<ion:title tag="h3" />
			<p>Extend 1 : <b><ion:get key="extend1"/></b></p>
			<p>Extend 2 : <b><ion:get key="extend2"/></b></p>
		</ion:article>
	</ion:articles>
</ion:page>
