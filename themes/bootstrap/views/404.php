<ion:partial view="pages/header" />

<section class="container">

    <section class="page-blog">
        <div class="span12 page-header m0">
            <h1>
                <ion:page:title />
                <ion:page:subtitle tag="small" />
            </h1>
        </div>
        <div class="clearfix"></div>

        <div class="page404"><!-- 404 Page --></div>

        <section class="span12 bg-white mb20 pull-left">
            <ion:page>
                <ion:articles>
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
                            <script type="text/javascript" src="<ion:theme_url />assets/js/jquery.fancybox.pack.js"></script>
                            <script type="text/javascript" src="<ion:theme_url />assets/js/jquery.mousewheel.min.js"></script>
                            <script type="text/javascript">
                                $('.fancybox').fancybox({
                                    helpers : {
                                        buttons : {}
                                    }
                                });
                            </script>

                        </ion:article>
                        <div class="clearfix"></div>
                    </div>
                </ion:articles>
            </ion:page>
        </section>

    </section>

</section> <!-- Container Section End -->

<!-- Page : Home Page | Articles By Type : testimonial-article -->
<ion:page id="2">
    <section class="testimonials clearfix mb20">
        <div class="container">
            <ul>
                <ion:articles type="testimonial-article">
                    <ion:article>
                        <li>
                            <ion:title tag="h2" />
                            <ion:content />
                        </li>
                    </ion:article>
                </ion:articles>
            </ul>
        </div>
    </section>
</ion:page>

<ion:partial view="pages/footer" />
