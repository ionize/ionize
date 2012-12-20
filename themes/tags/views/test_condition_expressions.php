<h1>Conditions : Use of expressions</h1>
<p>
	Tests can be done when using one value tag.<br/>
	Remind : "Value" tags are supposed to return one value, by opposition to "Loop" tags, which are
	used to loop inside a data collection.
</p>
<ion:tree_navigation />

<hr/>




<h2>Expression</h2>


<ion:page>
    <ul class="boxes">
        <ion:medias type="picture" size='150' method='square'>
            <ion:media >
                <li>
                    <img src="<ion:src />" /><br/>
					<p>
						<ion:index />
						<ion:title />
						<ion:index expression="index.gt 3 OR index.eq 3">
							youpiii
						</ion:index>
					</p>
                </li>
        	</ion:media>
        </ion:medias>
    </ul>
</ion:page>
<hr />
<ion:article:title function="strtolower" />






