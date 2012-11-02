<h1 xmlns="http://www.w3.org/1999/html">Medias from current page</h1>
<ion:tree_navigation />
<hr/>
<p>Get medias from the current page</p>

<pre>
&lt;ion:page>
    &lt;h4>Media from page ID : &lt;ion:page:id />&lt;/h4>
    &lt;ul class="boxes">
        &lt;ion:medias size="200" method="square" unsharp="true">
            &lt;ion:media>
                &lt;li>
                    &lt;img src="&lt;ion:src />" />
                    &lt;p>
                        Media ID &lt;b>&lt;ion:id />&lt;/b>&lt;br/>
                        Title : &lt;b>&lt;ion:title />&lt;/b>&lt;br/>
                    &lt;/p>
                &lt;/li>
            &lt;/ion:media>
        &lt;/ion:medias>
    &lt;/ul>
&lt;/ion:page>	
</pre>

<ion:page>
    <h4>Media from page ID : <ion:page:id /></h4>

        <ion:medias size="200" method="square" unsharp="true" tag="ul" class="boxes">
            <ion:media>
                <li>
                    <img src="<ion:src />" />
                    <p>
                        Media ID <b><ion:id /></b><br/>
                        Title : <b><ion:title /></b><br/>
                    </p>
                </li>
            </ion:media>
        </ion:medias>
</ion:page>


