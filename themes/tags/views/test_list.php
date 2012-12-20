<h1 xmlns="http://www.w3.org/1999/html">List tag</h1>
<ion:tree_navigation />
<hr/>

<ion:page>
    <ion:articles>
		<ion:article>
            <ion:title tag="h3"/>

			<p>Medias in one list</p>
            <ion:medias:list key='src' type="picture"/>

		</ion:article>
    </ion:articles>
</ion:page>

<ion:page>
    <ion:articles>
		<ion:article>
            <ion:title tag="h3"/>

			<p>Build one data array for a javascript slider</p>
            <ion:medias type="picture">

				<ion:media:index expression="!=1">,<br/></ion:media:index>
                {image : '<ion:media:src />', title : '<ion:media:title />'}

            </ion:medias>


		</ion:article>
    </ion:articles>
</ion:page>
