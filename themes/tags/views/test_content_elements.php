<h1>Content elements</h1>

<p>
To use this view, you need one Content Element, with 2 fields :
</p>
<ul>
	<li>Create one Content Element, with the name "link", with 2 fields :
		<ol>
			<li>One with the name set to "url"</li>
			<li>One with the name set to "style"</li>
		</ol>
	<li>At least add one instance of this content element to one article</li>
</ul>

<hr/>

<ion:tree_navigation />

<hr/>

<pre>
</pre>

<p>In the following articles list, the content "link" is displayed only if one is added to one article</p>
<ion:page:articles:article>

			<ion:title tag="h3" />

			<ion:element key="link">

				<h4>Element Definition : <ion:title /></h4>
				<h4>Items :</h4>

				<ion:items tag="ul" class="boxes">

					<li>

						<h4><ion:item:index /></h4>

						Field : <b><ion:url:label /></b><br/>
						Type : <b><ion:url:type /></b><br/>
						Content : <b><ion:url:content /></b><br/>
						Default value : <b><ion:url:default_value /></b>
						<hr/>

						Field : <b><ion:style:label /></b><br/>
						Type : <b><ion:style:type /></b><br/>
                        Content : <b><ion:style:content /></b><br/>
                        Default value : <b><ion:style:default_value /></b>

						<!-- Display selected values (checkbox, radio, select) -->
						<hr/>
                        Field : <b><ion:colors:label /></b><br/>
                        Type : <b><ion:colors:type /></b><br/>
                        Content : <b><ion:colors:content /></b><br/>


						<ion:colors>
							Options : <br/>
							<ion:options>
                                &nbsp;&nbsp;&nbsp;<ion:value/> : <ion:label/><br/>
							</ion:options>
							Selected values :<br/>
							<ion:values>
                                &nbsp;&nbsp;&nbsp;<ion:value/> : <ion:label/><br/>
							</ion:values>
						</ion:colors>
					</li>

				</ion:items>

			</ion:element>

</ion:page:articles:article>
