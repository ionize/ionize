<ion:partial view="header" />

<!-- Home Picture Slider -->
<div class="span-22 prepend-1 append-1">
	<div id="home-slider" >
		<div id="slideshow">
			<div id="s3slider">
				<ul id="s3sliderContent">
				
					<ion:medias type="picture">
						<li class="s3sliderImage">
							<img src="<ion:src folder="940" />" alt="" />
							<div>
								<h1><ion:title /></h1>
								<p><ion:description /></p>
								
								<!-- The copyright will be displayed inside a "p" tag with class"copyright"
									 Nothing will be displayed if no copyright is filled on the picture infos -->
								<ion:copyright tag="p" class="copyright" prefix="© " /><ion:date format="Y" tag="p" class="date" />
							</div>
						</li>
					</ion:medias>
	
					<li class="clear s3sliderImage"></li>
				</ul>
			</div>
		</div>
	</div><!-- /home-bloc -->
</div>


<!-- Home Content -->
<div id="content" class="span-22 prepend-1 append-1">

	<!-- Page's subtitle -->
	<h2 class="title"><ion:subtitle /></h2>
	
	<!-- Home page articles
		 Loop into articles without linked view : Article's fields are directly displayed
	-->
	<ion:articles limit="3">

		<div class="span-33 home <?php if('<ion:index />' == 3) :?> last<?php endif;?>">
	
		
			<!-- We display only one picture of each article
				 So, even the editor makes a mistake by linking 2 pictures, the website design will stay correct
			-->
			<ion:medias type="picture" limit="1">
				<div class="imgborder" >
					<div class="img" style="background:url(<ion:src folder="280" />);height:130px;"></div>
				</div>
			</ion:medias>

			<!-- Article's title 
				 Could also be written <ion:title tag="h2" /> 
				 if we don't want to display an empty tag if the article has no title
			-->
			<h2><ion:title /></h2>
			
			<!-- Article's content -->
			<ion:content />
			
		</div>
		
	</ion:articles>
	
	
	<hr />
		
    <div class="span-6 colborder">
    	<h2 class="title">Service News</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae risus dolor. Duis vehicula fermentum eros ut tempor.</p>
        <ul>
            <li>Website &amp; Development System</li>
            <li>Animation Photoshop</li>
            <li>Network &amp; Web 2.1 Company</li>
            <li>eCommerce &amp; Portfolio</li>
            <li>Landingpage &amp; Branding</li>
            <li>More...</li>
        </ul>
    </div>
    
    
    <div class="span-15 last">
    	
		<h2 class="title">Recent Activity</h2>
		<div>
			<img src="images/content/rp1.jpg" alt="" class="alignleft imgborder"/>
        	<h5>Curabitur quis felis at lacus ultricies rhoncus.</h5>
            <p>Sed imperdiet tellus id risus rutrum nec feugiat ante pretium. Etiam massa arcu, molestie ac dapibus nec, posuere sit amet arcu. Phasellus cursus, dolor ac venenatis fermentum, metus sem pellentesque eros. <a href="#">Read more...</a></p>					
		</div>
		<div>
			<img src="images/content/rp1.jpg" alt="" class="alignleft imgborder"/>
        	<h5>Curabitur quis felis at lacus ultricies rhoncus.</h5>
            <p>Sed imperdiet tellus id risus rutrum nec feugiat ante pretium. Etiam massa arcu, molestie ac dapibus nec, posuere sit amet arcu. Phasellus cursus, dolor ac venenatis fermentum, metus sem pellentesque eros. <a href="#">Read more...</a></p>					
		</div>
    </div>
</div>



<!-- Partial : Footer -->
<ion:partial view="footer" />