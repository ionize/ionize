<ion:partial view="header" />

	<ion:partial view="page_header" />


	<!-- Articles : No type -->
	<ion:page:articles type="">

		<ion:article>
			<div class="row article">
				<div class="seven columns">

					<div class="article">

						<!-- Article title -->
						<ion:title tag="h3" />

						<!-- Article content -->
						<ion:content />

						<div class="article-pictures">

							<!-- Articles linked files -->
							<ion:medias type="file">
								<a href="<ion:media:src />"><ion:media:title /></a>
							</ion:medias>

						</div>
					</div>
				</div>

				<div class="five columns">

					<ion:medias type="picture" limit="1">
						<img src="<ion:media:src />" />
					</ion:medias>

				</div>
			</div>
		</ion:article>

	</ion:page:articles>




	<div class="row">

		<ion:articles type="bloc">
			<ion:article>
				<div class="four columns">
					<div class="panel">
						<ion:title tag="h5" />
						<ion:content />
					</div>
				</div>
			</ion:article>
		</ion:articles>

	</div>


<ion:partial view="footer" />
