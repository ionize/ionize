<h1>Medias from current page articles</h1>
<ion:tree_navigation />
<hr/>

<h3>Test</h3>

<pre>
	&lt;ion:page>
		&lt;ion:articles>
			&lt;ion:article:title tag="h3"/>
			&lt;ul class="boxes">
				&lt;ion:article:medias>
					&lt;ion:media size='200' square='true' unsharp='true'>
						&lt;li>
							&lt;img src="&lt;ion:src />" />
							&lt;p>
								Media ID &lt;b>&lt;ion:id />&lt;/b>&lt;br/>
								Title : &lt;b>&lt;ion:title />&lt;/b>&lt;br/>
							&lt;/p>
						&lt;/li>
					&lt;/ion:media>
				&lt;/ion:article:medias>
			&lt;/ul>
		&lt;/ion:articles>
	&lt;/ion:page>
</pre>

<h3>Result</h3>

<ion:page>
	<ion:articles>
		<ion:article:title tag="h3"/>
		<ul class="boxes">
			<ion:article:medias>
				<ion:media size='200' square='true' unsharp='true'>
					<li>
						<img src="<ion:src />" />
						<p>
							Media ID <b><ion:id /></b><br/>
							Title : <b><ion:title /></b><br/>
						</p>
					</li>
				</ion:media>
			</ion:article:medias>
		</ul>
	</ion:articles>
</ion:page>
