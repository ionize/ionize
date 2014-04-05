<ion:partial view="header" />

	<ion:partial view="page_header" />

	<!--
		Articles : No type
	-->
	<ion:page:articles type="">

		<ion:article>
			<div class="row article">
				<div class="large-7 columns">

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

				<div class="large-5 columns">

					<ion:medias type="picture" limit="1">
						<img src="<ion:media:src />" />
					</ion:medias>

				</div>
			</div>
		</ion:article>

	</ion:page:articles>

	<div class="row">

		<!--
			Articles : type 'bloc'
			authorization : not set : Apply filtering
							all : displays all articles (includes all deny_codes)
							401 : display only 401 articles
							403 : display only 403 articles
							404 : display only 404 articles
			usage :
			authorization="all" : 			All articles, with or without authorizations
			authorization="all,401,403" : 	Only free access articles + 401 + 403
			authorization="401,403" : 		Only 401 + 403
			authorization="401" : 			Only 401

		-->
		<ion:articles type="bloc" authorization="all">
			<ion:article >
				<div class="large-4 columns">
					<div class="panel">

						<ion:title tag="h5" />

						<ion:deny is=''>
							<ion:content  />
						</ion:deny>
						<ion:else>

							<ion:deny is='401'>
								<ion:content paragraph="1"/>
								<p><b>Restriction : 401</b></p>
							</ion:deny>

							<ion:deny is='403'>
								<ion:content paragraph="1"/>
								<p><b>Restriction : 403</b></p>
							</ion:deny>

						</ion:else>

					</div>
				</div>


			</ion:article>
		</ion:articles>

	</div>


<ion:partial view="footer" />
