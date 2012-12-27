<ion:partial view="header" />

<section class="container">

    <section>
        <div class="span12 page-header m0 pos-rel">
            <h1>
                <ion:page:title />
                <ion:page:subtitle tag="small" />
            </h1>
        </div>
        <div class="clearfix"></div>
<!--        <ion:page:breadcrumb tag="ul" article="true" child-tag="li" class="breadcrumb" />-->

        <section class="span12 mb20 bg-white">
            <ion:page>

                <div class="p10">
                    <ion:articles type="" limit="1">
                        <ion:article>

                            <ion:title tag="h2" class="dotted-title" />
                            <ion:content />

                        </ion:article>
                        <div class="clearfix"></div>
                    </ion:articles>
                    <ion:articles type="services-list">
                        <div class="span4 pull-left ml5">
                            <ion:article>

                                <ion:title tag="h2" class="dotted-title" />
                                <ion:content />

                            </ion:article>
                            <div class="clearfix"></div>
                        </div>
                    </ion:articles>
                    <div class="clearfix"></div>
                    <div class="accordion" id="servicesAccordion">
                        <div class="accordion-group">
                            <ion:articles type="services-accordion">
                                <ion:article>
                                    <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#servicesAccordion" href="#<ion:name />">
                                            <i class="icon-chevron-right"></i> <ion:title />
                                        </a>
                                    </div>
                                    <div id="<ion:name />" class="accordion-body collapse<ion:if key="index" expression="index==1"> in</ion:if>">
                                        <div class="accordion-inner">
                                            <ion:content />
                                        </div>
                                    </div>
                                </ion:article>
                            </ion:articles>
                        </div>
                    </div>
                </div>

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

<ion:partial view="footer" />
