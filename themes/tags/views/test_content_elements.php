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

<h2>Nouveau : </h2>

			<ion:element:link>

				<ion:title tag="h3" />
				<ion:description tag="p" />

                <ion:items tag="ul" class="boxes">

                    <li>
                        <h4><ion:index /></h4>

                        Field : <b><ion:name-surname:label /></b><br/>
                        Type : <b><ion:name-surname:type /></b><br/>
                        Value : <b><ion:name-surname:value helper="url:auto_link" /></b><br/>
                        Default value : <b><ion:name-surname:default_value /></b>
                        <hr/>

                        Field : <b><ion:style:label /></b><br/>
                        Type : <b><ion:style:type /></b><br/>
                        Value : <b><ion:style:content /></b><br/>
                        Default value : <b><ion:style:default_value /></b>
                        <!-- Display selected values (checkbox, radio, select) -->
                        <hr/>
                        Field : <b><ion:colors:label /></b><br/>
                        Type : <b><ion:colors:type /></b><br/>
                        Value : <b><ion:colors:value /></b><br/>


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


			</ion:element:link>




</ion:page:articles:article>
