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

        <ion:page:breadcrumb tag="ul" article="true" child-tag="li" class="breadcrumb" />

        <section class="span8 pull-left">
            <ion:page>

                <div class="span8 bg-white mb20">
                    <div class="pos-rel p10">
                        <ion:article>

                            <ion:medias type="picture" limit="1" method="width" size="345" unsharp='true'>
                                <img class="img-polaroid" src="<ion:media:src method='width' size='590' unsharp='true' />" alt="<ion:media:alt />" />
                            </ion:medias>

                            <ion:title tag="h2" class="dotted-title" />
                            <ion:content />

                            <hr class="mt10 mb10" />

                            <ion:medias type="picture" tag="ul" class="thumbnails">
                                <ion:media>
                                    <li class="span2 <ion:index /> <ion:if key="index" expression="index != 1 && (index-1) %4!=0">ml10</ion:if>">
                                        <a href="<ion:src />" class="thumbnail fancybox" data-fancybox-group="thumb<ion:article:get key="id_article" />">
                                            <img src="<ion:src method="adaptive" size="160,100" unsharp="true" />" title="<ion:title />" alt="<ion:alt />" />
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

                            <hr class="mt10 mb10" />

                            <p class="center">
                                <i class="icon-user"></i> <ion:article:writer><ion:name /></ion:article:writer> | <i class="icon-calendar"></i> <ion:date format="long" /><?php if('<ion:categories:list link="true" seperator=", " />' != ''): ?> | <i class="icon-magnet"></i> <ion:categories:list link="true" seperator=", " /><?php endif; ?>
                            </p>

                        </ion:article>
                        <div class="clearfix"></div>
                    </div>
                </div>

            </ion:page>
        </section>

        <section class="span4 pull-right">

            <h3 class="dotted-title"><ion:lang key="title_categories" /></h3>

            <ion:page:categories tag="ul" class="nav nav-pills nav-stacked">
                <ion:category>
                    <li><a href="<ion:url />"><ion:title /></a></li>
                </ion:category>
            </ion:page:categories>

            <h3 class="dotted-title"><ion:lang key="title_archives" /></h3>

            <ion:archives with_month="true" tag="ul" class="nav nav-pills nav-stacked">
                <li><a class="<ion:active_class />" href="<ion:archive:url />"><ion:archive:period /></a></li>
            </ion:archives>

        </section>

    </section>

</section> <!-- Container Section End -->

<ion:partial view="footer" />
