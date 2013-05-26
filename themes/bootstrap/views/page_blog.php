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

		<!-- Current category -->
		<ion:page:category:current:title expression="!=''">
			<div class="row-fluid">
				<div class="span12">
					<ion:lang key="you_are_browsing_category" /> : <ion:page:category:current:title/>
				</div>
			</div>
		</ion:page:category:current:title>

		<!-- Current Archive : show the active category name -->
		<ion:archives:archive:is_active>
			<p><ion:translation term="you_are_browsing_archive" /> : <span><ion:archive:period /></span></p>
		</ion:archives:archive:is_active>

		<!-- Current tag -->
		<ion:page:tag:current:title expression="!=''">
			<div class="row-fluid">
				<div class="span12">
					<ion:lang key="you_are_browsing_tag" /> : <ion:page:tag:current:title/>
				</div>
			</div>
		</ion:page:tag:current:title>


		<div class="row-fluid">

			<div class="span9">
				<ion:page>
					<ion:articles>
						<ion:article>

							<!--
								Picture : Limit to 1
							-->
							<div class="row-fluid" style="margin-bottom:20px;">
								<ion:medias type="picture" limit="1">
									<div class="span5"><img class="img-polaroid" src="<ion:media:src method='width' size='290' unsharp='true' />" alt="<ion:media:alt />" /></div>
								</ion:medias>

								<!--
									Article intro
								-->
								<div class="span7">

									<h2><a href="<ion:url />"><ion:title /></a></h2>

									<ion:writer><ion:name /></ion:writer><br />
									<ion:date format="long" /><br />

									<!-- Categories -->
									<ion:lang key="categories" /> : <ion:categories:list link="true" separator=", " /><br />

									<!-- Tags -->
									<ion:article:tags:list link="true" separator=", " tag="p" class="categories" prefix="lang('title_tags') : " />

									<ion:content helper="text:word_limiter:10" />

									<p class="right">
										<a class="btn" href="<ion:url />" title="<ion:title />"><ion:lang key="button_read_more" /></a>
									</p>
								</div>

							</div>
						</ion:article>

					</ion:articles>
				</ion:page>

				<div class="row-fluid">
					<!--
						Pagination
					-->
					<ion:page:articles:pagination loop="false" />
				</div>

			</div>

			<div class="span3">

				<!--
					Categories
				-->
				<h3><ion:lang key="title_categories" /></h3>

				<ion:page:categories tag="ul" class="nav nav-pills nav-stacked">
					<ion:category>
						<li>
							<a <ion:category:is_active> class="<ion:category:active_class />" </ion:category:is_active> href="<ion:category:url />"><ion:category:title /> (<ion:category:nb_articles />)</a>
						</li>
					</ion:category>
				</ion:page:categories>

				<!--
					Archives
				-->
				<h3><ion:lang key="title_archives" /></h3>

				<ion:archives with_month="true" tag="ul" class="nav nav-pills nav-stacked">
					<li><a class="<ion:archive:active_class />" href="<ion:archive:url />"><ion:archive:period /></a></li>
				</ion:archives>


				<!--
					Tags Cloud : Through CSS
				-->
				<h3><ion:lang key="title_tags" /></h3>

				<style type="text/css">

					#tags{
						margin:0;
					}
					#tags li{
						list-style:none;
						display:inline-block;
						margin:0 1px 5px 0;
						line-height: inherit;
					}
					#tags li a{
						text-decoration:none;
						color:#2BA6CB;
						white-space: nowrap;
						padding: 3px;
						line-height: inherit;
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

				<p>CSS tags cloud</p>
				<ul id="tags">
					<ion:page:tags>
						<li ><a class="tag<ion:tag:nb_articles />" href="<ion:tag:url />"><ion:tag:title /></a></li>
					</ion:page:tags>
				</ul>



				<!-- Tags -->
				<h3><ion:lang key="title_tags" /></h3>

				<ul class="nav nav-pills nav-stacked">
					<ion:page:tags>
						<li><a class="<ion:tag:active_class />" href="<ion:tag:url />"><ion:tag:title /> (<ion:tag:nb_articles />)</a></li>
					</ion:page:tags>
				</ul>



			</div>

		</div>

	</div> <!-- Container End -->

<ion:partial view="footer" />
