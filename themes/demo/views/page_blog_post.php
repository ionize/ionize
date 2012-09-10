<ion:partial view="header" />


<div class="span-24">

	<div class="span-14 prepend-1 colborder blog">



        <!-- If category is active show the active category name -->
        <ion:categories:category:is_active>

            <p id="category_highlight"><em>//</em> <ion:translation term="you_are_browsing_category" /> : <span><ion:category:title /></span></p>

        </ion:categories:category:is_active>



            <ion:article>
                <!--
                    We explicitely get the articles which don't have any type set.
                -->

                <div class="post">

                    <ion:article:title tag="h2" />

                    <ion:article:date format="complete" />

                    <ion:article:medias type="picture">

                        <ion:media>
                            <img src="<ion:src size="540" master="width" unsharp="true" />" />
                        </ion:media>

                    </ion:article:medias>

                    <!-- Categories -->
                    <p class="categories">
                        <ion:translation term="categories" /> : <ion:article:categories separator=", " />
                    </p>


                    <ion:article:content />

                </div>

            </ion:article>


	</div>
	

	<div class="span-7">
		
		<div class="side-block">
		
			<h2><ion:translation term="title_categories" /></h2>

            <ion:page>

                <ion:categories all='true' tag="ul" class="links" active_class="active">
                    <li>
                        <a <ion:category:is_active> class="<ion:category:active_class />" </ion:category:is_active>  href="<ion:category:url />"><ion:category:title /></a>
                    </li>
                </ion:categories>

            </ion:page>
		
		</div>
		
		<div class="side-block">
			
			<h2><ion:translation term="title_archives" /></h2>
			
			<ul class="links">

				<ion:archives with_month="true">
			
					<li><a class="<ion:active_class />" href="<ion:url />"><ion:period /></a></li>
			
				</ion:archives>
			</ul>
			
		</div>


	</div>

</div>


<!-- Partial : Footer -->
<ion:partial view="footer" />