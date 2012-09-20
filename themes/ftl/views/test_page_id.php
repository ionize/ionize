<h1>&lt;ion:page />, &lt;ion:id /></h1>
<ion:tree_navigation />
<hr/>

<p>From the current page, get articles</p>
<p>Foreach article : </p>
<ul>
	<li>display the ID and the title</li>
	<li>Get the Page 3 articles and display each article ID</li>
</ul>

<h3>Result</h3>

<!-- Current page -->
<ion:page>
	<h4>Page ID : <ion:id /></h4>
	<ion:articles>
		<h5>Article ID : <ion:article:id /></h5>
		<h5>Article title : <ion:article:title/></h5>

		<!-- Page 3 -->
		<ul>
		<ion:page id="3">
			<li>
				Page ID : <ion:id /><br/>
				Articles :
				<ul>
					<ion:articles>
						<li>
							Article ID : <ion:article:id /><br/>
							Article title : <ion:article:title />
						</li>
					</ion:articles>
				</ul>
			</li>
		</ion:page>
		</ul>
	</ion:articles>
</ion:page>

