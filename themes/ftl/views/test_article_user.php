<h1>&lt;ion:article:user /></h1>

<p>
	Displays information about the user who wrote the article in Ionize.
</p>

<pre>
&lt;ion:page id="3">
	&lt;ion:articles>
		&lt;h3>Article ID : &lt;ion:article:id/> - Title : &lt;ion:article:title/>&lt;/h3>
		&lt;p>Article user : &lt;/p>
		&lt;ion:article:user tag="ul">
			&lt;ion:name tag="li" />
			&lt;ion:email tag="li" />
			&lt;ion:join_date tag="li" />
		&lt;/ion:article:user>
	&lt;/ion:articles>
&lt;/ion:page>
</pre>

<h3>Result</h3>

<ion:page id="3">
	<ion:articles>
		<h3>Article ID : <ion:article:id/> - Title : <ion:article:title/></h3>
		<p>Article user : </p>
		<ion:article:user tag="ul">
			<ion:name tag="li" />
			<ion:email tag="li" />
			<ion:join_date tag="li" />
		</ion:article:user>
	</ion:articles>
</ion:page>
