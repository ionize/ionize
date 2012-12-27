<ion:partial view="header" />

<section class="container">

    <section class="page-blog">
        <div class="span12 page-header m0">
            <h1>
                <ion:page:title />
                <ion:page:subtitle tag="small" />
            </h1>
        </div>
        <div class="clearfix"></div>

        <section class="span12 bg-white mb20">
            <ion:page>
                <ion:articles type="">
                    <div class="pos-rel p10">
                        <ion:article>

                            <ion:title tag="h2" class="dotted-title" />
                            <ion:content />

                            <ion:medias type="picture" tag="ul" class="thumbnails">
                                <ion:media>
                                    <li class="span4 <ion:index /> <ion:if key="index" expression="index != 1 && (index-1) %3!=0">ml10</ion:if>">
                                        <a href="<ion:src />" class="thumbnail fancybox" data-fancybox-group="thumb<ion:article:get key="id_article" />">
                                            <img src="<ion:src method="adaptive" size="300,200" unsharp="true" />" title="<ion:title />" alt="<ion:alt />" />
                                        </a>
                                    </li>
                                </ion:media>
                            </ion:medias>
                            <ion:medias type="picture" limit="1">
                                <script type="text/javascript" src="<ion:theme_url />assets/js/jquery.fancybox.pack.js"></script>
                                <script type="text/javascript" src="<ion:theme_url />assets/js/jquery.mousewheel.min.js"></script>
                                <script type="text/javascript">
                                    $('.fancybox').fancybox({
                                        helpers : {
                                            buttons : {}
                                        }
                                    });
                                </script>
                            </ion:medias>
                        </ion:article>
                        <div class="clearfix"></div>
                    </div>
                </ion:articles>


                <ion:articles type="bloc">
                    <ion:article>
                        <div class="span4<ion:if key="index" expression="index%3==0"> last</ion:if>">
                            <div class="p10">
                                <ion:medias type="picture" limit="1">
                                    <ion:media size="280,120" method="adaptive">
                                        <img class="img-polaroid" src="<ion:src />" alt="<ion:alt />" />
                                    </ion:media>
                                </ion:medias>
                                <div class="caption">
                                    <ion:title tag="h3" class="dotted-title" />
                                    <ion:content helper="text:word_limiter:20" />
                                </div>
                            </div>
                        </div>
                    </ion:article>
                </ion:articles>

            </ion:page>
        </section>

    </section>

</section> <!-- Container Section End -->

<ion:partial view="footer" />
