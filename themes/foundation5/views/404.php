<!-- 
	404 page view.
	This view has to be linked to the 404 page in Ionize.
-->

<ion:partial view="header" />

<ion:partial view="page_header" />

<!--
	Articles : No type
-->
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


<ion:partial view="footer" />
