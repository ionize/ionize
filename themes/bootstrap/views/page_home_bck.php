<ion:partial view="header" />

    <section class="container">

        <!-- Page : Current | Medias From Current Page -->
        <ion:page>
            <section class="slider mt10">
                <div id="ei-slider" class="ei-slider">
                    <ul class="ei-slider-large">
                        <ion:medias type="picture">
                            <ion:media>
                                <li>
                                    <img src="<ion:src />" alt="<ion:alt />" />
                                    <ion:if key="title,description" expression="title != '' && description != ''">
                                        <div class="ei-title">
                                            <ion:title tag="h2" />
                                            <ion:description tag="h3" />
                                        </div>
                                    </ion:if>
                                </li>
                            </ion:media>
                        </ion:medias>
                    </ul>
                    <ul class="ei-slider-thumbs">
                        <li class="ei-slider-element"><ion:lang key="currentSlide" /></li>
                        <ion:medias type="picture">
                            <ion:media size="150,60" method="adaptive">
                                <li><a href="#"><ion:title /></a><img src="<ion:src />" alt="<ion:alt />" /></li>
                            </ion:media>
                        </ion:medias>
                    </ul>
                </div>
            </section>
            <script type="text/javascript" src="<ion:theme_url />assets/js/jquery.eislideshow.js"></script>
            <script type="text/javascript">
                !function ($) {
                    // Slideshow
                    $('#ei-slider').eislideshow({
                        animation			: 'center',
                        autoplay			: true,
                        slideshow_interval	: 3000,
                        titlesFactor		: 0
                    });
                }(window.jQuery)
            </script>
        </ion:page>

        <section class="home-page">

            <!-- Page : Current -->
            <ion:page>
                <div class="span12 mt20 dotted-box-tb clearfix">
                    <ion:articles limit="1" type="bloc">
                        <ion:article>
                            <ion:title tag="h2" />
                            <ion:content />
                        </ion:article>
                    </ion:articles>
                </div>

                <div class="clearfix mb20"></div>

                <div class="span12">
                    <ion:lang key="title_welcome" tag="h2" class="dotted-title" />
                    <div class="bg-white">
                        <div class="p10">
                            <ion:articles type="">
                                <ion:article>

                                    <ion:medias type="picture" limit="1">
                                        <ion:media size="280,120" method="adaptive">
                                            <img class="img-polaroid" src="<ion:src />" alt="<ion:alt />" />
                                        </ion:media>
                                    </ion:medias>

                                    <ion:title tag="h3" />
                                    <ion:content helper="text:word_limiter:20" />

                                </ion:article>
                            </ion:articles>
                        </div>
                    </div>
                </div>
            </ion:page>

            <!-- Page : Blog | Articles Limit : 3 -->
            <ion:page id="blog">
                <div class="span12 latestArticles">
                    <ion:lang key="home_last_post" tag="h2" class="dotted-title" />
                    <ul class="thumbnails">
                        <ion:articles limit="3">
                            <li class="span4">
                                <ion:article>
                                    <div class="thumbnail">
                                        <ion:medias type="picture" limit="1">
                                            <ion:media size="280,193" method="adaptive">
                                                <img class="img-polaroid" src="<ion:src />" alt="<ion:alt />" />
                                            </ion:media>
                                        </ion:medias>
                                        <div class="caption">
                                            <ion:title tag="h3" />
                                            <ion:content helper="text:word_limiter:10" />
                                            <p class="right"><a href="<ion:url />" title="<ion:title />" class="btn"><ion:lang key="button_read_more" /></a></p>
                                        </div>
                                    </div>
                                </ion:article>
                            </li>
                        </ion:articles>
                    </ul>
                </div>
            </ion:page>

        </section>

    </section> <!-- Container Section End -->

<ion:partial view="footer" />
