<ion:article>

<!--
	Article used as a picture gallery
	The Mootools plugin "Milkbox" is used for this example (http://reghellin.com/milkbox)
	but you can use any other system, from any framework.
-->


	
	<ion:article:title tag="h2" />

	<!-- The content as introduction text -->
	<ion:article:content />
	
	<!-- Loop in pictures linked to the article -->
	<ion:article:medias type="picture">

        <!-- Link to the full sized picture -->
        <a href="<ion:media:src />" rel="lightbox-<ion:article:get key="id_article" />" title="<ion:media:title function="addslashes" /> : <ion:media:description function="addslashes" />" class="imgborder gallery-thumb<ion:media:if key="index" expression="index%5 == 0"> last</ion:media:if>">

            <!--
                The displayed thumb comes from the folder 145
                Thumb Folder name in Ionize (Settings > Advanced Settings > Thumbails) : 145
                Physical thumb folder : /files/<picture_folder>/thumb_145
            -->
            <img src="<ion:media:src size="150" square="true" unsharp="true" />" alt="<ion:media:alt />" />
        </a>
				
	</ion:article:medias>

</ion:article>