<h1 xmlns="http://www.w3.org/1999/html">Medias from  articles</h1>

<p>If you limit the display of one media to one language, it will not be displayed for the other languages.
<br/>If no limit is set to one media (by default), it will be displayed for all languages.</p>
<ion:languages tag="ul">
    <li>
        <a href="<ion:language:url />">
            <ion:language:name />
        </a>
    </li>
</ion:languages>


<ion:tree_navigation />
<hr/>


<pre>
&lt;ion:page>
    &lt;ion:articles>
        &lt;ion:article:title tag="h3"/>
        &lt;ul class="boxes">
            &lt;ion:article:medias size="200" method="square" unsharp="true">
                &lt;ion:media>
                    &lt;li>
                        &lt;img src="&lt;ion:src />" />
                        &lt;p>
                            Media ID &lt;b>&lt;ion:id />&lt;/b>&lt;br/>
                            Title : &lt;b>&lt;ion:title />&lt;/b>&lt;br/>
                        &lt;/p>
                    &lt;/li>
                &lt;/ion:media>
            &lt;/ion:article:medias>
        &lt;/ul>
    &lt;/ion:articles>
&lt;/ion:page>
</pre>

<h2>All medias</h2>

<ion:page>
    <ion:articles>
        <ion:article:title tag="h3"/>
        <ul class="boxes">
            <ion:article:medias type="picture" size="100" method="square" unsharp="true">
                <ion:media>
                    <li>
                        <img src="<ion:src />" />
                        <p>
                            Media ID <b><ion:id /></b><br/>
                            Title : <b><ion:title /></b><br/>
                        </p>
                    </li>
                </ion:media>
            </ion:article:medias>
        </ul>
    </ion:articles>
</ion:page>

<h2>Range of medias</h2>
<ion:page>
    <ion:articles>
        <ion:article:title tag="h3"/>
        <ul class="boxes">
            <ion:article:medias type="picture" size="100" method="square" unsharp="true" range="2,3">
                <ion:media>
                    <li>
                        <img src="<ion:src />" />
                        <p>
                            Media ID <b><ion:id /></b><br/>
                            Title : <b><ion:title /></b><br/>
                        </p>
                    </li>
                </ion:media>
            </ion:article:medias>
        </ul>
    </ion:articles>
</ion:page>

