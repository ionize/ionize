<h1>Article's writer</h1>
<ion:tree_navigation />
<hr/>

<p>
	Displays information about the user who wrote the article in Ionize.
</p>
<hr/>

<pre>
&lt;ion:page>
	&lt;ion:articles>
		&lt;h3>Article ID : &lt;ion:article:id/> - Title : &lt;ion:article:title/>&lt;/h3>
		&lt;p>Article writer : &lt;/p>
		&lt;ion:article:writer tag="ul">
			&lt;ion:name tag="li" />
			&lt;ion:email tag="li" />
			&lt;ion:join_date tag="li" />
		&lt;/ion:article:writer>
	&lt;/ion:articles>
&lt;/ion:page>
</pre>

<h3>Result</h3>

<ion:page>
	<ion:articles>
		<h3>Article ID : <ion:article:id/> - Title : <ion:article:title/></h3>
		<p>Article writer : </p>
		<ion:article:writer tag="ul">
			<ion:name tag="li" prefix="Name : " />
			<ion:email tag="li" prefix="Email : "  />
			<ion:join_date tag="li" prefix="Join date : "  />
			<ion:last_visit tag="li" prefix="Last visit : "  />
			<ion:birth_date tag="li" prefix="Birth date : "  />
		</ion:article:writer>
	</ion:articles>
</ion:page>
