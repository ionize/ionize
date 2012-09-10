<ion:partial view="header" />


<div class="span-24">

	<div class="span-14 prepend-1 colborder blog">
	
        <!-- If category is active show the active category name -->
        <ion:categories:category:is_active>

                <p id="category_highlight"><em>//</em> <ion:translation term="you_are_browsing_category" /> : <span><ion:category:title /></span></p>

        </ion:categories:category:is_active>


		<!-- 
			We explicitely get the articles which don't have any type set.
		-->
        <ion:page>

            <ion:articles type="">

                <!--
                    In the "Blog" page edition panel of Ionize, we set the views of articles for this page :

                    List view : 	"Blog Post List"
                                    This view will be used for the post list

                    Article View : 	"Blog Post"
                                    This view will be used for one post single view

                -->
                <ion:partial view="article_blog_post_list" />

            </ion:articles>

        </ion:page>
	
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
			
					<li><a <ion:archive:is_active>class="active"</ion:archive:is_active> href="<ion:archive:url />"><ion:archive:period /></a></li>
			
				</ion:archives>
			</ul>
			
		</div>


		<div class="side-block">
			
			<ion:widget name="weather" id="FRXX0076" unit="c" />

		</div>
		
		<div class="side-block">
			
			<ion:widget name="rss" url="http://www.ecrans.fr/spip.php?page=backend" nb="3" />

		</div>
		
	</div>

</div>


<!-- Partial : Footer -->
<ion:partial view="footer" />