<ion:partial view="header" />

	<header id="homepage">
        <div class="row">

			<div class="twelve columns">

				<ion:page:title tag="h1"/>

                <!-- Page's subtitle -->
                <ion:page:subtitle tag="h2"/>

			</div>

        </div>
	</header>

	<div class="row">
		<div class="eight columns">
		
			<!-- Articles from current page -->
			<ion:page:articles limit="3">

				<ion:article>
					<div class="span-33 home <?php if('<ion:index />' == 3) :?> last<?php endif;?>">

						<!-- We display only one picture of each article
							 So, even the editor makes a mistake by linking 2 pictures, the website design will stay correct
						-->
						<ion:medias type="picture" limit="1">
							<div class="imgborder" >
								<div class="img" style="background:url(<ion:media:src size='280' />);height:130px;"></div>
							</div>
						</ion:medias>

						<!-- Article's title
							 Could also be written <ion:title tag="h3" />
							 if we don't want to display an empty tag if the article has no title
						-->
						<h3><ion:title /></h3>

						<!-- Article's content -->
						<ion:content />

					</div>
                </ion:article>
				
			</ion:page:articles>
		</div>

		<div class="four columns">
	    	<!-- Article in the one-fourth column of the home page.
	    		 We limit the displayed article to this type
	    	-->
	    	<ion:articles type="one-fourth">
	    	
	    		<!--
	    			Article's title
	    			In this case, the H3 tag is only displayed if one title is set for the article
	    		-->
	    		<ion:article:title tag="h3" />
	    		
	    		<ion:article:content />


	    	</ion:articles>


	    	<!-- Static translated title
	    		 This title is static in this view : It has nothing to do with articles.
	    		 We call a translated "static term".
	    		 Calling a term through a tag makes this term available in the "Static translations" panel of Ionize
	    	-->
	 		<h2 class="title"><ion:lang key="home_last_post" /></h2>
	   		
	   		
	    	<!-- Articles from another page : Blog
	    		 We display only 2 articles : limit attribute of the articles tag
	    		 For each article, we only display the first paragraph of the content.
	    		 We also limit the picture display to the first one.
	    	-->
	    	<ion:page:articles id="blog" limit="2">

                <ion:article>

					<div class="home-last-post">

						<a href="<ion:url />">

							<!-- The title is a link to the post -->
							<h3><ion:title /></h3>

							<!-- Content limited to the first paragraph -->
							<ion:content paragraph="1" />

                        </a>

					</div>
                </ion:article>
	
	    	</ion:page:articles>

		</div>
 	 </div>

<ion:partial view="footer" />

