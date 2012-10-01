<h1>Buggy tag</h1>
<ion:tree_navigation />

<h3>Articles</h3>
<ion:page:articles tag="ul">
    <ion:article:url href="true" tag="li" display="title"/>
</ion:page:articles>

<h2>Current Article details</h2>
<ion:article>
    <ion:medias type="picture" tag="ul" class="boxes">

        <ion:media tag="li">

            <img src="<ion:src size='200' />" />

            <ion:if key='alt' expression="alt != ''">
				ALT is SET !!!
			</ion:if>
			<ion:else>
                <ion:get key="file_name" tag="p"/>
			</ion:else>

        </ion:media>

    </ion:medias>

</ion:article>


