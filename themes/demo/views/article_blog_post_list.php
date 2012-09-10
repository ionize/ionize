<ion:article>

    <!-- View used for articles in blog's article list -->

    <div class="post">

        <h2><a href="<ion:url />"><ion:title class="pagetitle" /></a></h2>

        <p class="date"><ion:date format="complete" /></p>

        <ion:medias type="picture">

            <ion:media size="540" master="width" unsharp="true">

                <img src="<ion:src />" />

            </ion:media>

        </ion:medias>

        <!-- This article categories -->
        <p class="categories">
            <ion:translation term="categories" /> : <ion:categories separator=", " />
        </p>

        <!-- We limit the display to to first paragraph (first <p></p>) -->
        <ion:content paragraph="1" />

    </div>

</ion:article>
