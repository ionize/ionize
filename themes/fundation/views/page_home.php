<ion:partial path="header" />

	<div class="row">
		<div class="eight columns">
		
		    <!-- Page title -->
			<ion:title tag="h2" />  
    
    		<!-- Page's subtitle -->
			<p class="subtitle"><ion:subtitle /></p>
	

    
			<ion:articles limit="3" type="">
		
				<div class="span-33 home <?php if('<ion:index />' == 3) :?> last<?php endif;?>">
			
					<!-- We display only one picture of each article
						 So, even the editor makes a mistake by linking 2 pictures, the website design will stay correct
					-->
					<ion:medias type="picture" limit="1">
						<div class="imgborder" >
							<div class="img" style="background:url(<ion:src size="280" />);height:130px;"></div>
						</div>
					</ion:medias>
		
					<!-- Article's title 
						 Could also be written <ion:title tag="h2" /> 
						 if we don't want to display an empty tag if the article has no title
					-->
					<h3><ion:title /></h3>
					
					<!-- Article's content -->
					<ion:content />
					
				</div>
				
			</ion:articles>
		</div>

		<div class="four columns">
	    	<!-- Article in the one-fourth column of the home page.
	    		 We limit the displayed article to this type
	    	-->
	    	<ion:articles type="one-fourth">
	    	
	    		<!-- Article's title
	    			 In this case, the H2 tag is only displayed if one title is set for the article
	    		-->
	    		<ion:title tag="h3" />
	    		
	    		<ion:content />
	    	
	    	</ion:articles>



	    	<!-- Static translated title
	    		 This title is static in this view : It has nothing to do with articles.
	    		 We call a translated "static term".
	    		 Calling a term through a tag makes this term available in the "Static translations" panel of Ionize
	    	-->
	 		<h2 class="title"><ion:translation term="home_last_post" /></h2>
	   		
	   		
	    	<!-- Articles from another page : Blog
	    		 We display only 2 articles : limit attrbiute of the articles tag
	    		 For each article, we only display the first paragrph of the content.
	    		 We also limit the picture display to the first one.
	    	-->
	    	<ion:articles from="blog" limit="2">
	    	
				<!-- In this pecial case, we don't care about article's title -->
				<div class="home-last-post">
					
					<ion:medias type="picture" limit="1">
						<img src="<ion:src folder="150" />" alt="" class="left imgborder"/>				
					</ion:medias>
					
					<!-- The title is a link to the post -->
					<h3><a href="<ion:url />"><ion:title /></a></h3>
					
					<!-- Content limited to the first paragraph -->
					<ion:content paragraph="1" />
	
				</div>
	
	    	</ion:articles>


		
		</div>

 	 </div>

<ion:partial path="footer" />

