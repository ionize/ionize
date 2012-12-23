<ion:partial view="header" />

<ion:partial view="page_header" />

<div class="row">
	<div class="nine columns">
	
		<?php if ('<ion:article:category field="title" />' != '') :?>

			<p id="category_highlight"><em>//</em> <ion:lang key="you_are_browsing_category" /> : <span><ion:article:category field="title" /></span></p>
	
		<?php endif; ?>

        <div class="post">

			<ion:page:article>

				<h2><ion:title /></h2>

				<ion:date format="complete" tag="p" class="date"/>

                <!-- This article categories -->
                <p class="categories">
                	<ion:lang key="categories" /> : <ion:categories:list link="true" separator=", " />
				</p>

				<!-- Pictures slider -->
                <div id="slider">
                    <ion:medias type="picture">
                        <img src="<ion:media:src size='720,400' method='adaptive' />" />
                    </ion:medias>
                </div>

				<!-- content -->
				<ion:content />

            </ion:page:article>
        </div>
		
	</div>

	<div class="three columns">

		<div class="side-block">
		
			<h3><ion:lang key="title_categories" /></h3>

            <ul class="side-nav">
                <ion:categories>
                    <li>
                        <a <ion:category:is_active> class="<ion:category:active_class />" </ion:category:is_active> href="<ion:category:url />"><ion:category:title /></a>
                    </li>
                </ion:categories>
            </ul>
		
		</div>
		
		<div class="side-block">
			
			<h3><ion:lang key="title_archives" /></h3>

            <ul class="side-nav">
                <ion:archives with_month="true">
                    <li><a class="<ion:active_class />" href="<ion:archive:url />"><ion:archive:period /></a></li>
                </ion:archives>
            </ul>
			
		</div>
	</div>
</div>


<script type="text/javascript">
    $(window).load(function() {
        $("#slider").orbit();
    });
</script>



<!-- Partial : Footer -->
<ion:partial view="footer" />