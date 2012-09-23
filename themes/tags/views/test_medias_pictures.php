<h1 xmlns="http://www.w3.org/1999/html">Pictures</h1>
<ion:tree_navigation />
<hr/>
<p>
	For these examples, we use the <b>"medias"</b> tag in standalone mode.<br/>
	That means it returns all the pictures linked at least to one page or article.
</p>

<p>
    <strong>IMPORTANT :</strong><br/>
	<span class="red">The attribute <b>"refresh"</b> must not be used in a production environment !</span><br/>
    It will force the small picture to be refreshed, which is nice in development.
    Because this needs CPU, we strongly advice to not use it in production.
</p>
<hr/>



<h2>Simple sized pictures</h2>
<p>
	The wider side of the picture will be used to resize the picture.
</p>
<pre>
&lt;div>
    &lt;ion:medias type="picture" size="200" refresh="true">
        &lt;img src="&lt;ion:media:src  />" />
    &lt;/ion:medias>
&lt;/div>	
</pre>
<div>
    <ion:medias type="picture" size="200" refresh="true">
        <img src="<ion:media:src  />" />
    </ion:medias>
</div>



<h2>Set the height or width as "master size"</h2>
<p>
	The given side will be set to the given size.
</p>

<h3>All pictures will be 150px height</h3>

<pre>
&lt;div>
    &lt;ion:medias type="picture" size="150" method="height" refresh="true">
        &lt;img src="&lt;ion:media:src  />" />
    &lt;/ion:medias>
&lt;/div>	
</pre>
	
<div>
    <ion:medias type="picture" size="150" method="height" refresh="true">
        <img src="<ion:media:src  />" />
    </ion:medias>
</div>

<h3>All pictures will be 150px width</h3>
<pre>
&lt;div>
    &lt;ion:medias type="picture" size="150" method="width" refresh="true">
        &lt;img src="&lt;ion:media:src  />" />
    &lt;/ion:medias>
&lt;/div>
</pre>
<div>
    <ion:medias type="picture" size="150" method="width" refresh="true">
        <img src="<ion:media:src  />" />
    </ion:medias>
</div>

	

<h2>Resized in square colored background</h2>
<p>
	Pictures are resized using the larger side as "master" and and colored border is added
</p>
<pre>
&lt;div>
    &lt;ion:medias type="picture" size="200" method="border" refresh="true" color="#930031" >
        &lt;img src="&lt;ion:media:src  />" style="border-radius: 10px;"/>
    &lt;/ion:medias>
&lt;/div>
</pre>

<div>
	<ion:medias type="picture" size="200" method="border" refresh="true" color="#930031" >
		<img src="<ion:media:src  />" style="border-radius: 10px;"/>
	</ion:medias>
</div>



<h2>Adaptive resize</h2>
<p>
	Each picture will fit the given canvas size.
</p>
<pre>
&lt;div>
    &lt;ion:medias type="picture" size="250,180" method="adaptive" refresh="true" >
        &lt;img src="&lt;ion:media:src  />" />
    &lt;/ion:medias>
&lt;/div>
</pre>
<div>
    <ion:medias type="picture" size="250,180" method="adaptive" refresh="true" >
        <img src="<ion:media:src  />" />
    </ion:medias>
</div>



<h2>Last 3 added pictures, in square</h2>
<p>
    Not really true, as we order by "id_media DESC", but if we consider that the last added media
    has the last ID in DB, this can be acceptable.
</p>
<p>
    The <b>"square"</b> method will use the Ionize's picture setting <b>"Square crop area"</b>,
    to decide from where to start the square crop.<br />
    Default is "middle".
</p>
<p>
    In Ionize, the <b>"Square crop area"</b> setting can be found when editing one picture, in the <b>"Options"</b> tab.
</p>

<pre>
&lt;div>
    &lt;ion:medias type="picture" size="200" method="square" refresh="true" limit="3" order_by="id_media DESC">
        &lt;img src="&lt;ion:media:src  />" />
    &lt;/ion:medias>
&lt;/div>
</pre>

<div>
    <ion:medias type="picture" size="200" method="square" refresh="true" limit="3" order_by="id_media DESC">
        <img src="<ion:media:src  />" />
    </ion:medias>
</div>
