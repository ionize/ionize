<ion:partial view="header" />

<ion:partial view="page_header" />


<div class="row">
	<div class="nine columns">

		<!-- Current category -->
        <ion:page:category:current:title expression="!=''">
            <ion:lang key="you_are_browsing_category" /> : <ion:page:category:current:title/>
        </ion:page:category:current:title>


		<!--
			We get the articles which don't have any type set.
		-->
		<ion:page:articles>
			
			<ion:article>

                <div class="post-list">

                    <ion:medias type="picture" limit="1">
                        <img src="<ion:media:src size='300,200' method='adaptive' />" />
                    </ion:medias>

                    <h2><a href="<ion:url />"><ion:title class="pagetitle" /></a></h2>

                    <p class="date"><ion:date format="complete" /></p>


                    <!-- This article categories -->
                    <p class="categories">
                        <ion:lang key="categories" /> : <ion:categories:list link="true" separator=", " />
                    </p>

                    <!-- We limit the display to to first paragraph (first <p></p>) -->
                    <ion:content paragraph="1" />

                </div>

			</ion:article>
		
		</ion:page:articles>


		<!-- Pagination -->
		<ion:page:articles:pagination tag="div" class="pagination" />


	</div>

	<div class="three columns">

		<div class="side-block">
		
			<h3><ion:lang key="title_categories" /></h3>

			<ul class="side-nav">
				<ion:page:categories>
					<li>
						<a <ion:category:is_active> class="<ion:category:active_class />" </ion:category:is_active> href="<ion:category:url />"><ion:category:title /></a>
					</li>
				</ion:page:categories>
			</ul>
		
		</div>
		
		<div class="side-block">
			
			<h3><ion:lang key="title_archives" /></h3>
			
			<ul class="side-nav">
				<ion:archives with_month="true">
					<li><a class="<ion:active_class />" href="<ion:archive:url />"><ion:archive:period /></a></li>
				</ion:archives>
			</ul>
			
		</div>

	</div>
</div>


<!-- Partial : Footer -->
<ion:partial view="footer" />