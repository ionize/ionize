<ion:partial view="header" />

<ion:partial view="page_header" />

<div class="row">
	<div class="large-9 columns">
	
		<?php if ('<ion:article:category field="title" />' != '') :?>

			<p id="category_highlight"><em>//</em> <ion:lang key="you_are_browsing_category" /> : <span><ion:article:category field="title" /></span></p>
	
		<?php endif; ?>

        <div class="post">

			<ion:page:article>

				<h2><ion:title /></h2>

				<ion:date format="complete" tag="p" class="date"/>

                <!-- This article categories -->
                <p class="categories">
                	<ion:lang key="categories" /> : <ion:categories:list link="true" separator=", " />
				</p>

				<!-- Pictures slider -->
					<div id="slider-container">
						<div id="slider">
							<ion:medias type="picture,video">

								<div class="slider-panel">

									<ion:media:type is="picture">
										<img src="<ion:media:src size='720,400' method='adaptive' />" />
									</ion:media:type>

									<ion:media:type is="video">
										<ion:media:provider  is='youtube'><iframe class="video" width="100%" height="400" src="<ion:media:src />" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></ion:media:provider>
										<ion:media:provider  is='vimeo'><iframe class="video" width="100%" height="400" src="<ion:media:src />" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></ion:media:provider>
										<ion:media:provider  is='dailymotion'><iframe class="video" width="100%" height="400" src="<ion:media:src />" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></ion:media:provider>
										<ion:media:extension is="mp4"><video class="video" width="100%" height="400"controls><source src="<ion:media:src />" type="video/mp4" ></video></ion:media:extension>
										<ion:media:extension is="ogv"><video class="video" width="100%" height="400" controls><source src="<ion:media:src />" type="video/ogg" ></video></ion:media:extension>
									</ion:media:type>
								</div>
							</ion:medias>
						</div>
					</div>

				<!-- content -->
				<ion:content link_key="title" link_title="subtitle" />

            </ion:page:article>
        </div>
		
	</div>

	<div class="large-3 columns">

		<div class="side-block">
		
			<h3><ion:lang key="title_categories" /></h3>

            <ul class="side-nav">
                <ion:categories>
                    <li>
                        <a <ion:category:is_active> class="<ion:category:active_class />" </ion:category:is_active> href="<ion:category:url />"><ion:category:title /></a>
                    </li>
                </ion:categories>
            </ul>
		
		</div>
		
		<div class="side-block">
			
			<h3><ion:lang key="title_archives" /></h3>

            <ul class="side-nav">
                <ion:archives with_month="true">
                    <li><a <ion:archive:is_active>class="active"</ion:archive:is_active> href="<ion:archive:url />"><ion:archive:period /></a></li>
                </ion:archives>
            </ul>
			
		</div>
	</div>
</div>


<!-- Partial : Footer -->
<ion:partial view="footer" />