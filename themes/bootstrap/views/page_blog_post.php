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

	<div class="row-fluid">
		<div class="span12">
			<ion:page:breadcrumb tag="ul" article="true" child-tag="li" class="breadcrumb" />
		</div>
	</div>

	<div class="row-fluid">

		<div class="span8">
			<ion:page>
				<ion:article>

					<ion:medias type="picture" limit="1" method="width">
						<img class="img-polaroid" src="<ion:media:src method='width' size='760' unsharp='true' />" alt="<ion:media:alt />" />
					</ion:medias>

					<ion:title tag="h2" />
					<ion:content />

					<hr />

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

					<hr />

					<p class="center">
						<ion:article:writer><ion:name /></ion:article:writer> |
						<ion:date format="long" /> |
						<ion:article:categories:list link="true" separator=", " prefix="lang('categories') : " /> |
						<ion:article:tags:list link="true" separator=", " prefix="lang('title_tags') : " />

					</p>

				</ion:article>
			</ion:page>
		</div>

		<div class="span4">

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

		</div>
	</div>

</div> <!-- Container End -->

<ion:partial view="footer" />
