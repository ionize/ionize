<h1>Pages : &lt;ion:medias/>, &lt;ion:media /> (This example about sizing images and if image has title, alt, description info show or don't show)</h1>

<h3>Get 1 Media Type "Picture" From Current Page (Different Sizing Methods)</h3>
<pre>
    &lt;h3>Get 1 Media Type "Picture" From Current Page (Different Sizing Methods)&lt;/h3>
    &lt;h4>Result : &lt;/h4>
    &lt;ul class="boxes">
        &lt;li>
            &lt;ion:page>
                &lt;ion:medias type="picture" limit="1">
                    &lt;ion:media>
                        &lt;img src="&lt;ion:media:src />" />
                        &lt;p>
                            &lt;ion:media:if key="title" expression="title != ''">
                                Title : &lt;ion:media:title />&lt;br />
                            &lt;/ion:media:if>
                            &lt;ion:media:if key="alt" expression="alt != ''">
                                Alt : &lt;ion:media:alt />&lt;br />
                            &lt;/ion:media:if>
                            &lt;ion:media:if key="description" expression="description != ''">
                                Description : &lt;ion:media:description />&lt;br />
                            &lt;/ion:media:if>
                            Type : &lt;b>Picture&lt;/b>&lt;br/>
                            Limit : &lt;b>1&lt;/b>&lt;br/>
                            Resize Method : &lt;b>-&lt;/b>&lt;br/>
                            Size : &lt;b>-&lt;/b>&lt;br/>
                            Unsharp : &lt;b>true&lt;/b>&lt;br/>
                        &lt;/p>
                    &lt;/ion:media>
                &lt;/ion:medias>
            &lt;/ion:page>
        &lt;/li>
    &lt;/ul>
    &lt;ul class="boxes">
        &lt;li>
            &lt;ion:page>
                &lt;ion:medias type="picture" limit="1">
                    &lt;ion:media>
                        &lt;img src="&lt;ion:media:src size="200" master="width" unsharp="true" />" />
                        &lt;p>
                            &lt;ion:media:if key="title" expression="title != ''">
                                Title : &lt;ion:media:title />&lt;br />
                            &lt;/ion:media:if>
                            &lt;ion:media:if key="alt" expression="alt != ''">
                                Alt : &lt;ion:media:alt />&lt;br />
                            &lt;/ion:media:if>
                            &lt;ion:media:if key="description" expression="description != ''">
                                Description : &lt;ion:media:description />&lt;br />
                            &lt;/ion:media:if>
                            Type : &lt;b>Picture&lt;/b>&lt;br/>
                            Limit : &lt;b>1&lt;/b>&lt;br/>
                            Resize Method (Master) : &lt;b>width&lt;/b>&lt;br/>
                            Size : &lt;b>200&lt;/b>&lt;br/>
                            Unsharp : &lt;b>true&lt;/b>&lt;br/>
                        &lt;/p>
                    &lt;/ion:media>
                &lt;/ion:medias>
            &lt;/ion:page>
        &lt;/li>
        &lt;li>
            &lt;ion:page>
                &lt;ion:medias type="picture" limit="1">
                    &lt;ion:media>
                        &lt;img src="&lt;ion:media:src size="250" master="height" unsharp="true" />" />
                        &lt;p>
                            &lt;ion:media:if key="title" expression="title != ''">
                                Title : &lt;ion:media:title />&lt;br />
                            &lt;/ion:media:if>
                            &lt;ion:media:if key="alt" expression="alt != ''">
                                Alt : &lt;ion:media:alt />&lt;br />
                            &lt;/ion:media:if>
                            &lt;ion:media:if key="description" expression="description != ''">
                                Description : &lt;ion:media:description />&lt;br />
                            &lt;/ion:media:if>
                            Type : &lt;b>Picture&lt;/b>&lt;br/>
                            Limit : &lt;b>1&lt;/b>&lt;br/>
                            Resize Method (Master) : &lt;b>height&lt;/b>&lt;br/>
                            Size : &lt;b>250&lt;/b>&lt;br/>
                            Unsharp : &lt;b>false&lt;/b>&lt;br/>
                        &lt;/p>
                    &lt;/ion:media>
                &lt;/ion:medias>
            &lt;/ion:page>
        &lt;/li>
        &lt;li>
            &lt;ion:page>
                &lt;ion:medias type="picture" limit="1">
                    &lt;ion:media>
                        &lt;img src="&lt;ion:media:src size="250,150" adaptive="adaptive_resize" unsharp="true" />" />
                        &lt;lt;p>
                            &lt;ion:media:if key="title" expression="title != ''">
                                Title : &lt;ion:media:title />&lt;br />
                            &lt;/ion:media:if>
                            &lt;ion:media:if key="alt" expression="alt != ''">
                                Alt : &lt;ion:media:alt />&lt;br />
                            &lt;/ion:media:if>
                            &lt;ion:media:if key="description" expression="description != ''">
                                Description : &lt;ion:media:description />&lt;br />
                            &lt;/ion:media:if>
                            Type : &lt;b>Picture&lt;/b>&lt;br/>
                            Limit : &lt;b>1&lt;/b>&lt;br/>
                            Resize Method (adaptive) : &lt;b>adaptive_resize&lt;/b>&lt;br/>
                            Size : &lt;b>250,150&lt;/b>&lt;br/>
                            Unsharp : &lt;b>true&lt;/b>&lt;br/>
                        &lt;/p>
                    &lt;/ion:media>
                &lt;/ion:medias>
            &lt;/ion:page>
        &lt;/li>
        &lt;li>
            &lt;ion:page>
                &lt;ion:medias type="picture" limit="1">
                    &lt;ion:media>
                        &lt;img src="&lt;ion:media:src size="250" square="true" unsharp="true" />" />
                        &lt;p>
                            &lt;ion:media:if key="title" expression="title != ''">
                                Title : &lt;ion:media:title />&lt;br />
                            &lt;/ion:media:if>
                            &lt;ion:media:if key="alt" expression="alt != ''">
                                Alt : &lt;ion:media:alt />&lt;br />
                            &lt;/ion:media:if>
                            &lt;ion:media:if key="description" expression="description != ''">
                                Description : &lt;ion:media:description />&lt;br />
                            &lt;/ion:media:if>
                            Type : &lt;b>Picture&lt;/b>&lt;br/>
                            Limit : &lt;b>1&lt;/b>&lt;br/>
                            Resize Method (square) : &lt;b>true&lt;/b>&lt;br/>
                            Size : &lt;b>250&lt;/b>&lt;br/>
                            Unsharp : &lt;b>true&lt;/b>&lt;br/>
                        &lt;/p>
                    &lt;/ion:media>
                &lt;/ion:medias>
            &lt;/ion:page>
        &lt;/li>
    &lt;/ul>
</pre>

<h4>Result : </h4>
<ul class="boxes">
    <li>
        <ion:page>
            <ion:medias type="picture" limit="1">
                <ion:media>
                    <img src="<ion:media:src />" />
                    <p>
                        <ion:media:if key="title" expression="title != ''">
                            Title : <ion:media:title /><br />
                        </ion:media:if>
                        <ion:media:if key="alt" expression="alt != ''">
                            Alt : <ion:media:alt /><br />
                        </ion:media:if>
                        <ion:media:if key="description" expression="description != ''">
                            Description : <ion:media:description /><br />
                        </ion:media:if>
                        Type : <b>Picture</b><br/>
                        Limit : <b>1</b><br/>
                        Resize Method : <b>-</b><br/>
                        Size : <b>-</b><br/>
                        Unsharp : <b>true</b><br/>
                    </p>
                </ion:media>
            </ion:medias>
        </ion:page>
    </li>
</ul>
<ul class="boxes">
    <li>
        <ion:page>
            <ion:medias type="picture" limit="1">
                <ion:media>
                    <img src="<ion:media:src size="200" master="width" unsharp="true" />" />
                    <p>
                        <ion:media:if key="title" expression="title != ''">
                            Title : <ion:media:title /><br />
                        </ion:media:if>
                        <ion:media:if key="alt" expression="alt != ''">
                            Alt : <ion:media:alt /><br />
                        </ion:media:if>
                        <ion:media:if key="description" expression="description != ''">
                            Description : <ion:media:description /><br />
                        </ion:media:if>
                        Type : <b>Picture</b><br/>
                        Limit : <b>1</b><br/>
                        Resize Method (Master) : <b>width</b><br/>
                        Size : <b>200</b><br/>
                        Unsharp : <b>true</b><br/>
                    </p>
                </ion:media>
            </ion:medias>
        </ion:page>
    </li>
    <li>
        <ion:page>
            <ion:medias type="picture" limit="1">
                <ion:media>
                    <img src="<ion:media:src size="250" master="height" unsharp="true" />" />
                    <p>
                        <ion:media:if key="title" expression="title != ''">
                            Title : <ion:media:title /><br />
                        </ion:media:if>
                        <ion:media:if key="alt" expression="alt != ''">
                            Alt : <ion:media:alt /><br />
                        </ion:media:if>
                        <ion:media:if key="description" expression="description != ''">
                            Description : <ion:media:description /><br />
                        </ion:media:if>
                        Type : <b>Picture</b><br/>
                        Limit : <b>1</b><br/>
                        Resize Method (Master) : <b>height</b><br/>
                        Size : <b>250</b><br/>
                        Unsharp : <b>false</b><br/>
                    </p>
                </ion:media>
            </ion:medias>
        </ion:page>
    </li>
    <li>
        <ion:page>
            <ion:medias type="picture" limit="1">
                <ion:media>
                    <img src="<ion:media:src size="250,150" adaptive="adaptive_resize" unsharp="true" />" />
                    <p>
                        <ion:media:if key="title" expression="title != ''">
                            Title : <ion:media:title /><br />
                        </ion:media:if>
                        <ion:media:if key="alt" expression="alt != ''">
                            Alt : <ion:media:alt /><br />
                        </ion:media:if>
                        <ion:media:if key="description" expression="description != ''">
                            Description : <ion:media:description /><br />
                        </ion:media:if>
                        Type : <b>Picture</b><br/>
                        Limit : <b>1</b><br/>
                        Resize Method (adaptive) : <b>adaptive_resize</b><br/>
                        Size : <b>250,150</b><br/>
                        Unsharp : <b>true</b><br/>
                    </p>
                </ion:media>
            </ion:medias>
        </ion:page>
    </li>
    <li>
        <ion:page>
            <ion:medias type="picture" limit="1">
                <ion:media>
                    <img src="<ion:media:src size="250" square="true" unsharp="true" />" />
                    <p>
                        <ion:media:if key="title" expression="title != ''">
                            Title : <ion:media:title /><br />
                        </ion:media:if>
                        <ion:media:if key="alt" expression="alt != ''">
                            Alt : <ion:media:alt /><br />
                        </ion:media:if>
                        <ion:media:if key="description" expression="description != ''">
                            Description : <ion:media:description /><br />
                        </ion:media:if>
                        Type : <b>Picture</b><br/>
                        Limit : <b>1</b><br/>
                        Resize Method (square) : <b>true</b><br/>
                        Size : <b>250</b><br/>
                        Unsharp : <b>true</b><br/>
                    </p>
                </ion:media>
            </ion:medias>
        </ion:page>
    </li>
</ul>