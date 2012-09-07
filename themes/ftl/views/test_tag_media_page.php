
<h1>Pages : &lt;ion:medias/>, &lt;ion:media /></h1>

<pre>
&lt;h4>Media from page ID : &lt;ion:page:id />&lt;/h4>
&lt;ul class="boxes">
	&lt;ion:medias>
		&lt;ion:media size='400' square='true' unsharp='true'>
			&lt;li>
				&lt;img src="&lt;ion:src size='200' />" />
				&lt;p>
					Media ID &lt;b>&lt;ion:id />&lt;/b>&lt;br/>
					Title : &lt;b>&lt;ion:title />&lt;/b>&lt;br/>
				&lt;/p>
			&lt;/li>
		&lt;/ion:media>
	&lt;/ion:medias>
&lt;/ul>
</pre>

<h3>Result</h3>

<ion:page id="3">
	<h4>Media from page ID : <ion:page:id /></h4>
	<ul class="boxes">
		<ion:medias>
			<ion:media size='400' square='true' unsharp='true'>
				<li>
					<img src="<ion:src size='200' />" />
					<p>
						Media ID <b><ion:id /></b><br/>
						Title : <b><ion:title /></b><br/>
					</p>
				</li>
			</ion:media>
		</ion:medias>
	</ul>
</ion:page>

