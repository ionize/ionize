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
                    <ion:articles>
                        <div class="span8 bg-white mb20">
                            <div class="pos-rel p10">
                                <ion:article>
                                    <ion:medias type="picture" limit="1" method="width" size="345" unsharp='true'>
                                        <div class="span5 pull-left"><img class="img-polaroid" src="<ion:media:src method='width' size='345' unsharp='true' />" alt="<ion:media:alt />" /></div>
                                    </ion:medias>
                                    <div class="span3 pull-right">
                                        <ion:title tag="h2" class="dotted-title" />
                                        <i class="icon-user"></i> <ion:writer><ion:name /></ion:writer><br />
                                        <i class="icon-calendar"></i> <ion:date format="long" /><br />
                                        <?php if('<ion:categories:list link="true" seperator=", " />' != ''): ?><i class="icon-magnet"></i> <ion:categories:list link="true" seperator=", " /><br /><?php endif; ?>
                                        <ion:content helper="text:word_limiter:10" />
                                        <p class="right">
                                            <a class="btn" href="<ion:url />" title="<ion:title />"><ion:lang key="button_read_more" /></a>
                                        </p>
                                    </div>
                                </ion:article>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </ion:articles>
                </ion:page>
                <ion:page:articles:pagination loop="false" />
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
