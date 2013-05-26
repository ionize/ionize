<!--
	404 page view.
	This view has to be linked to the 404 page in Ionize.
-->

<ion:partial view="header" />

<div class="container">

	<div class="row-fluid">
		<div class="span12 page-header">
			<h1>
				<ion:page:title />
				<ion:page:subtitle tag="small" />
			</h1>
		</div>
	</div>

	<ion:page>

		<ion:articles type="">

			<ion:article>
				<div class="row-fluid">
					<ion:title tag="h2" />

					<ion:content />

					<ion:medias type="picture" tag="ul" class="thumbnails">
						<ion:media>
							<li class="span4">
								<a href="<ion:src />" class="thumbnail fancybox" data-fancybox-group="thumb<ion:article:get key="id_article" />">
								<img src="<ion:src method="adaptive" size="300,200" unsharp="true" />" title="<ion:title />" alt="<ion:alt />" />
								</a>
							</li>
						</ion:media>
					</ion:medias>
					<ion:medias type="picture" limit="1">
						<script type="text/javascript" src="<ion:theme_url />assets/js/jquery.fancybox.pack.js"></script>
						<script type="text/javascript" src="<ion:theme_url />assets/js/jquery.mousewheel.min.js"></script>
						<script type="text/javascript">
							$('.fancybox').fancybox({
								helpers : {
									buttons : {}
								}
							});
						</script>
					</ion:medias>
				</div>
			</ion:article>

		</ion:articles>

	</ion:page>


</div> <!-- Container End -->

<ion:partial view="footer" />
