<h1>Previous / Next article</h1>
<p>
	Click on one following article to display the previous / next article data.
</p>
<ion:tree_navigation />

<h3>Articles</h3>
<ion:articles tag="ul">
	<ion:article:url href="true" tag="li" display="title"/>
</ion:articles>

<hr/>

<h2>Current article (for info)</h2>	
<h4><ion:article:id /> : <ion:article:title /></h4>
	

<h2>Previous article :</h2>
<pre>
	&lt;ion:article:prev>
        &lt;h4>&lt;ion:get key="id_article" /> : &lt;ion:title />&lt;/h4>
        &lt;a href="&lt;ion:url />">&lt;ion:title />&lt;/a>
    &lt;/ion:article:prev>
</pre>

<ion:article:prev>
    <h4><ion:get key="id_article" /> : <ion:title /></h4>
    <a href="<ion:url />"><ion:title /></a>
</ion:article:prev>

<h2>Next article :</h2>
<pre>
	&lt;ion:article:next>
        &lt;h4>&lt;ion:id /> : &lt;ion:title />&lt;/h4>
        &lt;a href="&lt;ion:url />">&lt;ion:title />&lt;/a>
    &lt;/ion:article:next>
</pre>

<ion:article:next>
    <h4><ion:id /> : <ion:title /></h4>
    <a href="<ion:url />"><ion:title /></a>
</ion:article:next>




