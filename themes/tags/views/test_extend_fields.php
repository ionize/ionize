<h1>Extend fields</h1>

<p>
To use this view, you need :
</p>
<ul>
	<li>One Extend field, not translated, with the name "colors" :
		<ul>
			<li>Parent : Articles</li>
			<li>Name : colors</li>
			<li>Type : "checkbox"</li>
			<li>Values :<br/>
				1:Red<br/>
				2:Green<br/>
				3:Blue<br/>
			</li>
			<li>Default Value : 2</li>
		</ul>
	</li>
	<li>One Extend field, translated, with the name "description":
		<ul>
			<li>Parent : Articles</li>
			<li>Name : description</li>
		</ul>
	</li>
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

			<h4>Extend "colors"</h4>
            <ion:extend:colors>
                <p>
                    <b>Available options</b> :<br/>
					<ion:options>
						&nbsp;&nbsp;&nbsp;<ion:value/> : <ion:label/><br/>
					</ion:options>
				</p>
                <p>
					<b>Values</b> :<br/>
                	<ion:values>
                    	&nbsp;&nbsp;&nbsp;<ion:value/> : <ion:label/><br/>
                	</ion:values>
                </p>
            </ion:extend:colors>

            <h4>Extend "description"</h4>
            <ion:extend:description>
				<p><b>Label : </b><ion:label /></p>
				<p><b>Content : </b></p>
				<ion:value />
            </ion:extend:description>

		</ion:article>
	</ion:articles>
</ion:page>
