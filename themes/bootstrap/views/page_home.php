<ion:partial view="header" />


	<!-- Page : Current | Medias From Current Page -->
	<ion:page>
		<div class="carousel slide" id="myCarousel">
			<div class="carousel-inner">
				<ion:medias type="picture" size="800,350" method="adaptive">
					<ion:media>
						<div class="item<ion:index is='1'> active</ion:index>">
							<img src="<ion:src />" alt="<ion:alt />" />
							<div class="container">
								<div class="carousel-caption">
									<h2><ion:title/></h2>
									<p class="lead"><ion:description /></p>
								</div>
							</div>
						</div>
					</ion:media>
				</ion:medias>
			</div>
			<a data-slide="prev" href="#myCarousel" class="left carousel-control">‹</a>
			<a data-slide="next" href="#myCarousel" class="right carousel-control">›</a>
		</div>

		<script type="text/javascript">
			$('.carousel').carousel();
		</script>

	</ion:page>

	<div class="container">

		<!-- Page title -->
        <ion:page:title tag="h1"/>

        <!-- Page's subtitle -->
        <ion:page:subtitle tag="h2"/>


        <!-- Page : Current -->
		<ion:page>

			<div class="row-fluid">
				<div class="span12">
					<ion:articles type="" limit=4>
						<ion:article>
                            <div class="span3">
								<ion:title tag="h3" />
								<ion:content helper="text:word_limiter:20" />
							</div>
						</ion:article>
					</ion:articles>

                </div>
            </div>

		</ion:page>

		<!-- Page : Blog | Articles Limit : 3 -->
		<ion:page id="blog">
			<div class="row-fluid">
				<div class="span12">
                    <ion:lang key="home_last_post" tag="h2" class="dotted-title" />
					<ul class="thumbnails">
						<ion:articles limit="3">
							<li class="span4">
								<ion:article>
									<div class="thumbnail">
										<ion:medias type="picture" limit="1">
											<ion:media size="280,193" method="adaptive">
												<img src="<ion:src />" alt="<ion:alt />" />
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
			</div>
		</ion:page>

	</div>

<ion:partial view="footer" />
