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
<ion:page>
	<ion:articles>
		<ion:article>
			<ion:title tag="h3" />

			<ion:element key="link" display="false" >

				<h4>Element Definition : <ion:title /></h4>

				<ion:items>

					<h4><ion:url:label /></h4>
                    <ion:url:content tag="p" prefix="Content : " />
                    <ion:url:default_value tag="p" prefix="Default Value : " />

					<h4><ion:style:label /></h4>
                    <ion:style:content tag="p" prefix="Content : " />
                    <ion:style:default_value tag="p" prefix="Default Value : " />

					<!-- Display selected values (checkbox, radio, select) -->
                    <ion:skills>
						<ion:values>
							<ion:label tag="p" />
							<ion:value tag="p" />
						</ion:values>
					</ion:skills>

				</ion:items>

			</ion:element>



		</ion:article>
	</ion:articles>
</ion:page>
