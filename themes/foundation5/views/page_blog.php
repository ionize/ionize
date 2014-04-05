<ion:partial view="header" />

<ion:partial view="page_header" />


<div class="row">
	<div class="large-9 columns">

		<!-- Current category -->
        <ion:page:category:current:title expression="!=''">
            <ion:lang key="you_are_browsing_category" /> : <ion:page:category:current:title/>
        </ion:page:category:current:title>

		<!-- Current tag -->
        <ion:page:tag:current:title expression="!=''">
            <ion:lang key="you_are_browsing_tag" /> : <ion:page:tag:current:title/>
        </ion:page:tag:current:title>

		<!-- Current Archive : show the active category name -->
		<ion:archives:archive:is_active>
			<p><ion:translation term="you_are_browsing_archive" /> : <span><ion:archive:period /></span></p>
		</ion:archives:archive:is_active>

		<!--
			We get the articles which don't have any type set.
		-->
		<ion:page:articles>
			
			<ion:article>

                <div class="post-list">

					<!--
						Display the first media, if it is one video or picture
					-->
					<ion:medias type="picture,video" limit="1">

						<ion:media:type is="video">
							<div class="media">
								<ion:media:provider  is='youtube'><iframe class="video" width="100%" height="200" src="<ion:media:src />" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></ion:media:provider>
								<ion:media:provider  is='vimeo'><iframe class="video" width="100%" height="200" src="<ion:media:src />" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></ion:media:provider>
								<ion:media:provider  is='dailymotion'><iframe class="video" width="100%" height="200" src="<ion:media:src />" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></ion:media:provider>
								<ion:media:extension is="mp4"><video class="video" width="100%" height="200"controls><source src="<ion:media:src />" type="video/mp4" ></video></ion:media:extension>
								<ion:media:extension is="ogv"><video class="video" width="100%" height="200" controls><source src="<ion:media:src />" type="video/ogg" ></video></ion:media:extension>
							</div>
						</ion:media:type>

						<ion:media:type is="picture">
							<img src="<ion:media:src size='300,200' method='adaptive' />" />
						</ion:media:type>

                    </ion:medias>

                    <h2><a href="<ion:url />"><ion:title class="pagetitle" /></a></h2>

                    <p class="date"><ion:date format="complete" /></p>


                    <!-- This article categories -->
                    <p class="categories">
                        <ion:lang key="categories" /> : <ion:categories:list link="true" separator=", " />
                    </p>

                    <!-- We limit the display to to first paragraph (first <p></p>) -->
                    <ion:content paragraph="1" />

					<!-- Tags -->
					<ion:article:tags:list link="true" separator=", " tag="p" class="categories" prefix="lang('title_tags') : " />

				</div>

			</ion:article>
		
		</ion:page:articles>


		<!-- Pagination -->
		<ion:page:articles:pagination tag="div" class="pagination" />


	</div>

	<div class="large-3 columns">

		<!--
			Categories :
			Only categories used by articles linked to the current page are displayed
		-->
		<div class="side-block">
		
			<h3><ion:lang key="title_categories" /></h3>

			<ul class="side-nav">
				<ion:page:categories>
					<li>
						<a <ion:category:is_active> class="<ion:category:active_class />" </ion:category:is_active> href="<ion:category:url />"><ion:category:title /> (<ion:category:nb_articles />)</a>
					</li>
				</ion:page:categories>
			</ul>
		
		</div>

		<!-- Archives -->
		<div class="side-block">
			
			<h3><ion:lang key="title_archives" /></h3>
			
			<ul class="side-nav">
				<ion:archives with_month="true">
					<li><a class="<ion:archive:active_class />" href="<ion:archive:url />"><ion:archive:period /></a></li>
				</ion:archives>
			</ul>
			
		</div>

		<!-- Tags Cloud : Through CSS -->
		<div class="side-block">

			<style type="text/css">

				#tags{
					/*text-align:center;*/
				}
				#tags li{
					list-style:none;
					display:inline-block;
					margin:0 1px 1px 0;
				}
				#tags li a{
					text-decoration:none;
					color:#2BA6CB;
					white-space: nowrap;
					padding: 3px;
					color:#fff;
					background: #2BA6CB;
				}
				#tags li a:hover{
					color:#fff;
					background: #2BA6CB;
					opacity: 1 !important;
				}
				.tag1{font-size:90%;opacity: .65;}
				.tag2{font-size:100%;opacity: .7;}
				.tag3{font-size:110%;opacity: .75;}
				.tag4{font-size:120%;opacity: .8;}
				.tag5{font-size:130%;opacity: .85;}
				.tag6{font-size:140%;opacity: .9;}
				.tag7{font-size:150%;opacity: .95;}

			</style>

			<h3><ion:lang key="title_tags" /></h3>
			<p>CSS tags cloud</p>
			<ul id="tags">
				<ion:page:tags>
					<li ><a class="tag<ion:tag:nb_articles />" href="<ion:tag:url />"><ion:tag:title /></a></li>
				</ion:page:tags>
			</ul>

		</div>


		<!-- Tags -->
		<div class="side-block">

			<h3><ion:lang key="title_tags" /></h3>

			<ul class="side-nav">
				<ion:page:tags>
					<li><a class="<ion:tag:active_class />" href="<ion:tag:url />"><ion:tag:title /> (<ion:tag:nb_articles />)</a></li>
				</ion:page:tags>
			</ul>

		</div>
	</div>
</div>


<!-- Partial : Footer -->
<ion:partial view="footer" />