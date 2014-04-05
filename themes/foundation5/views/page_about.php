<ion:partial view="header" />

	<ion:partial view="page_header" />

	<div class="row">
		<div class="large-7 columns">
		
		    <!-- Page title -->
			<ion:title tag="h2" />  
    
		    <!-- Articles : No type -->
		    <ion:articles type="">

		    	<ion:article>

                    <div class="article">

                        <!-- Article title -->
						<ion:title tag="h3" />

                        <!-- Article content -->
                        <ion:content />

                        <div class="article-pictures">

                            <!-- Articles linked pictures -->
                            <ion:medias type="picture">
                                <img src="<ion:media:src />" />
                            </ion:medias>

                            <!-- Articles linked files -->
                            <ion:medias type="file">
                                <a href="<ion:media:src />"><ion:media:title /></a>
                            </ion:medias>

                        </div>
                    </div>

				</ion:article>

		    </ion:articles>

		</div>

		<div class="large-5 columns">

			<ion:medias type="picture" limit="1">

				<img src="<ion:media:src />" />

			</ion:medias>

		</div>

 	</div>

	<div class="row">

		<ion:articles type="bloc">
			<ion:article>
				<div class="large-4 columns">
					<div class="panel">
						<ion:title tag="h5" />
						<ion:content />
					</div>
				</div>
			</ion:article>
		</ion:articles>

	</div>


<ion:partial view="footer" />
