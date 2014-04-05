<ion:partial view="header" />

	<header id="homepage">
        <div class="row">

			<div class="twelve columns">

				<?php
				/*
				 * Page's title
				 *
				 * Displayed with the tag "h1"
				 * If no title is set, no empty tag will be rendered
				 *
				 */
				?>
				<ion:page:title tag="h1"/>

				<?php
				/*
				 * Page's subtitle
				 *
				 * We use the native PHP method "nl2br" to render the newlines
                 * in the subtitle
				 *
				 */
				?>
                <ion:page:subtitle tag="h2" function="nl2br" />

			</div>

        </div>
	</header>

	<section id="main-content">

		<div class="row">

			<?php
			/*
			 * Current page article
			 * -> Home page
			 *
			 * We limit to the 4 first, as we want 4 blocks.
			 *
			 */
			?>
			<ion:page:articles limit="4">
				<div class="medium-6 large-3 columns">

					<?php
					/*
					 * Article's title
					 *
					 */
					?>
					<h3><ion:article:title /></h3>

					<?php
					/*
					 * Article's content
					 *
					 */
					?>
					<div class="home-block">
						<ion:article:content />
					</div>

					<?php
					/*
					 * Article's URL
					 * The URL of one article is either its own URL or the URL of the target article.
					 * (See create links in the doc)
					 *
					 */
					?>
					<p>
						<a class="expand button" title="<ion:article:title />" href="<ion:article:url />">Read more</a>
					</p>
				</div>
			</ion:page:articles>
		</div>



		<?php
		/*
		 * Latest post : From "blog" page
		 *
		 */
		?>
		<div class="row">

			<div class="large-12 columns">


				<?php
				/*
				 * Lang key
				 * These translations are globally available.
				 * They are editable from panel Content > Translations
				 *
				 * Once you declare one in one view, you are able to set its translation in this panel.
				 *
				 */
				?>
				<h2 class="title">
					<ion:lang key="home_last_post" />
				</h2>


				<?php
				/*
				 * Articles from another page : Blog
				 *
				 * The "name" of the page is "blog".
				 * We use this name as ID to get the page.
				 *
				 * This name can be found in the page options, when editing it from ionize.
				 *
				 */
				?>
				<ion:page id="blog">

					<ion:articles limit="2">

						<ion:article>

							<div class="home-last-post row">

								<a href="<ion:url />">

									<?php
									/*
									 * Static Item : Flag
									 * If one Static item called "flag" is linked to the article,
									 * the content of the field called "text" will be displayed.
									 *
									 * As we limit to 1, only the first flag will be used.
									 *
									 */
									?>
									<ion:article:static:flag:items limit="1">
										<div class="flag"><span><ion:text:value /></span></div>
									</ion:article:static:flag:items>

									<h3><ion:title /></h3>

									<div class="hide-for-small medium-4 large-4 columns">

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


									</div>

									<div class="medium-8 large-8 columns">
										<?php
										/*
										 * Content, limited to the first paragraph
										 */
										?>
										<ion:content paragraph="1" />

									</div>

								</a>

							</div>
						</ion:article>
					</ion:articles>
				</ion:page>
			</div>
 	 	</div>
	</section>

<ion:partial view="footer" />

