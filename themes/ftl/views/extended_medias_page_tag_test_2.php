<h1>Pages : &lt;ion:medias/>, &lt;ion:media /></h1>

<h2>Get All Medias From Current Page (Type = Picture, Sizing Method = Master => width)</h2>
<pre>
    &lt;ul class="boxes">
        &lt;ion:page:medias type="picture">
            &lt;ion:media>
                &lt;li>
                    &lt;ion:media>
                        &lt;img src="&lt;ion:media:src size="250" master="width" unsharp="true" />" />
                        &lt;p>
                            &lt;ion:media:if key="title" expression="'title' != ''">
                                &lt;b>Title : &lt;/b>&lt;ion:media:title />&lt;br />
                            &lt;/ion:media:if>
                            &lt;ion:media:if key="alt" expression="'alt' != ''">
                                &lt;b>Alt : &lt;/b>&lt;ion:media:alt />&lt;br />
                            &lt;/ion:media:if>
                            &lt;ion:media:if key="description" expression="'description' != ''">
                                &lt;b>Description : &lt;/b>&lt;ion:media:description />&lt;br />
                            &lt;/ion:media:if>
                            &lt;b>Fie Extention : &lt;/b>&lt;ion:media:extension />&lt;/b>&lt;br />
                            &lt;b>Type : &lt;/b>Picture&lt;br/>
                            &lt;b>Resize Method : &lt;/b>Master (width)&lt;br/>
                            &lt;b>Size : &lt;/b>250px&lt;br/>
                            &lt;b>Unsharp : &lt;/b>true&lt;br/>
                        &lt;/p>
                    &lt;/ion:media>
                &lt;/li>
            &lt;/ion:media>
        &lt;/ion:page:medias>
    &lt;/ul>
</pre>

<h2>Result (Medias From Page - Type = Picture) </h2>
<?php if('<ion:page:medias:media:src type="picture" limit="1" />' != ''):?>
    <ul class="boxes">
        <ion:page:medias type="picture">
            <ion:media>
                <li>
                    <ion:media>
                        <img src="<ion:media:src size="250" master="width" unsharp="true" />" />
                        <p>
                            <ion:media:if key="title" expression="'title' != ''">
                                <b>Title : </b><ion:media:title /><br />
                            </ion:media:if>
                            <ion:media:if key="alt" expression="'alt' != ''">
                                <b>Alt : </b><ion:media:alt /><br />
                            </ion:media:if>
                            <ion:media:if key="description" expression="'description' != ''">
                                <b>Description : </b><ion:media:description /><br />
                            </ion:media:if>
                            <b>Fie Extention : </b><ion:media:extension /></b><br />
                            <b>Type : </b>Picture<br/>
                            <b>Resize Method : </b>Master (width)<br/>
                            <b>Size : </b>250px<br/>
                            <b>Unsharp : </b>true<br/>
                        </p>
                    </ion:media>
                </li>
            </ion:media>
        </ion:page:medias>
    </ul>
<?php endif; ?>
<h2>Videos : From Youtube and Local Video (Jquery Media Element Plugin Used)</h2>
<pre>
    &lt;ul class="boxes">
        &lt;ion:page:medias type="video">
            &lt;ion:media>
                &lt;ion:media:if key="base_path" expression="'base_path' == 'http://www.youtube.com/'">
                    &lt;li>
                        &lt;video width="560" height="315" id="player&lt;ion:media:get key="id_media" />">
                        &lt;source type="video/youtube" src="&lt;ion:media:get key="path" />" />
                        &lt;/video>
                        &lt;ion:media:if key="title|description" expression="'title' != '' || 'description' != ''">
                            &lt;p>
                                &lt;ion:media:title tag="h3" />
                                &lt;ion:alt tag="p" />
                                &lt;ion:media:description tag="p" />
                            &lt;/p>
                        &lt;/ion:media:if>
                    &lt;/li>
                &lt;/ion:media:if>
                &lt;ion:media:if key="base_path" expression="'base_path' != 'http://www.youtube.com/'">
                    &lt;li>
                        &lt;video width="320" height="240" poster="&lt;ion:base_url />&lt;ion:media:get key="base_path" />&lt;?= current(explode(".", '&lt;ion:media:file_name />')) ?>.jpg" controls="controls" preload="none">
                        &lt;!-- MP4 source must come first for iOS -->
                        &lt;ion:media:if key="extension" expression="'extension' == 'mp4'">
                            &lt;source type="video/mp4" src="&lt;ion:src />" />
                        &lt;/ion:media:if>
                        &lt;!-- WebM for Firefox 4 and Opera -->
                        &lt;ion:media:if key="extension" expression="'extension' == 'ogg'">
                            &lt;source type="video/ogg" src="&lt;ion:src />" />
                        &lt;/ion:media:if>
                        &lt;!-- OGG for Firefox 3 -->
                        &lt;ion:media:if key="extension" expression="'extension' == 'webm'">
                            &lt;source type="video/webm" src="&lt;ion:src />" />
                        &lt;/ion:media:if>
                        &lt;!-- Fallback flash player for no-HTML5 browsers with JavaScript turned off -->
                        &lt;ion:media:if key="extension" expression="'extension' == 'mp4'">
                            &lt;object width="320" height="240" type="application/x-shockwave-flash" data="&lt;ion:theme_url />assets/media_element/flashmediaelement.swf">
                                &lt;param name="movie" value="&lt;ion:theme_url />assets/media_element/flashmediaelement.swf" />
                                &lt;param name="flashvars" value="controls=true&poster=&lt;ion:base_url />&lt;ion:media:get key="base_path" />&lt;?= current(explode(".", '&lt;ion:media:file_name />')) ?>.jpg&file=&lt;ion:src />" />
                                &lt;img src="&lt;ion:base_url />&lt;ion:media:get key="base_path" />&lt;?= current(explode(".", '&lt;ion:media:file_name />')) ?>.jpg" width="320" height="240" title="No video playback capabilities" />
                            &lt;/object>
                        &lt;/ion:media:if>
                        &lt;/video>
                        &lt;ion:media:if key="title|description" expression="'title' != '' || 'description' != ''">
                            &lt;p>
                                &lt;ion:media:title tag="h3" />
                                &lt;ion:alt tag="p" />
                                &lt;ion:media:description tag="p" />
                            &lt;/p>
                        &lt;/ion:media:if>
                    &lt;/li>
                &lt;/ion:media:if>
            &lt;/ion:media>
        &lt;/ion:page:medias>
    &lt;/ul>
    &lt;link rel="stylesheet" href="&lt;ion:theme_url />assets/media_element/mediaelementplayer.css" />
    &lt;script src="&lt;ion:theme_url />assets/javascript/jquery.min.js">&lt;/script>
    &lt;script src="&lt;ion:theme_url />assets/media_element/mediaelement-and-player.min.js">&lt;/script>
    &lt;script>
        $('audio,video').mediaelementplayer();
    &lt;/script>
</pre>
<?php if('<ion:page:medias:media:src type="video" limit="1" />' != ''):?>
    <ul class="boxes">
        <ion:page:medias type="video">
            <ion:media>
                <ion:media:if key="base_path" expression="'base_path' == 'http://www.youtube.com/'">
                    <li>
                        <video width="560" height="315" id="player<ion:media:get key="id_media" />">
                            <source type="video/youtube" src="<ion:media:get key="path" />" />
                        </video>
                        <ion:media:if key="title|description" expression="'title' != '' || 'description' != ''">
                            <p>
                                <ion:media:title tag="h3" />
                                <ion:alt tag="p" />
                                <ion:media:description tag="p" />
                            </p>
                        </ion:media:if>
                    </li>
                </ion:media:if>
                <ion:media:if key="base_path" expression="'base_path' != 'http://www.youtube.com/'">
                    <li>
                        <video width="320" height="240" poster="<ion:base_url /><ion:media:get key="base_path" /><?= current(explode(".", '<ion:media:file_name />')) ?>.jpg" controls="controls" preload="none">
                            <!-- MP4 source must come first for iOS -->
                            <ion:media:if key="extension" expression="'extension' == 'mp4'">
                                <source type="video/mp4" src="<ion:src />" />
                            </ion:media:if>
                            <!-- WebM for Firefox 4 and Opera -->
                            <ion:media:if key="extension" expression="'extension' == 'ogg'">
                                <source type="video/ogg" src="<ion:src />" />
                            </ion:media:if>
                            <!-- OGG for Firefox 3 -->
                            <ion:media:if key="extension" expression="'extension' == 'webm'">
                                <source type="video/webm" src="<ion:src />" />
                            </ion:media:if>
                            <!-- Fallback flash player for no-HTML5 browsers with JavaScript turned off -->
                            <ion:media:if key="extension" expression="'extension' == 'mp4'">
                                <object width="320" height="240" type="application/x-shockwave-flash" data="<ion:theme_url />assets/media_element/flashmediaelement.swf">
                                    <param name="movie" value="<ion:theme_url />assets/media_element/flashmediaelement.swf" />
                                    <param name="flashvars" value="controls=true&poster=<ion:base_url /><ion:media:get key="base_path" /><?= current(explode(".", '<ion:media:file_name />')) ?>.jpg&file=<ion:src />" />
                                    <img src="<ion:base_url /><ion:media:get key="base_path" /><?= current(explode(".", '<ion:media:file_name />')) ?>.jpg" width="320" height="240" title="No video playback capabilities" />
                                </object>
                            </ion:media:if>
                        </video>
                        <ion:media:if key="title|description" expression="'title' != '' || 'description' != ''">
                            <p>
                                <ion:media:title tag="h3" />
                                <ion:alt tag="p" />
                                <ion:media:description tag="p" />
                            </p>
                        </ion:media:if>
                    </li>
                </ion:media:if>
            </ion:media>
        </ion:page:medias>
    </ul>
    <link rel="stylesheet" href="<ion:theme_url />assets/media_element/mediaelementplayer.css" />
    <script src="<ion:theme_url />assets/javascript/jquery.min.js"></script>
    <script src="<ion:theme_url />assets/media_element/mediaelement-and-player.min.js"></script>
    <script>
        $('audio,video').mediaelementplayer();
    </script>
<?php endif; ?>