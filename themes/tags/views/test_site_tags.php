<h1>Website tags</h1>
<ion:tree_navigation />

<ul>
	<ion:page:articles:article tag="li">
		<a href="<ion:url/>"><ion:title /></a>
	</ion:page:articles:article>
</ul>

<hr/>

<pre>
&lt;p>Site title 			&lt;b>&lt;ion:site_title />&lt;/b>&lt;/p>
&lt;p>Window title 			&lt;b>&lt;ion:meta_title />&lt;/b>&lt;/p>
&lt;p>Meta keywords : 		&lt;b>&lt;ion:meta_keywords />&lt;/b>&lt;/p>
&lt;p>Meta description : 	&lt;b>&lt;ion:meta_description />&lt;/b>&lt;/p>
</pre>

<p>Site title : 		<b><ion:site_title /></b></p>
<p>Base URL : 			<b><ion:base_url /></b></p>
<p>Home page URL : 		<b><ion:home_url /></b></p>
<p>Window title :		<b><ion:meta_title /></b></p>
<p>Meta keywords : 		<b><ion:meta_keywords /></b></p>
<p>Meta description : 	<b><ion:meta_description /></b></p>
<p>Current language code : 	<b><ion:current_lang /></b></p>
<p>Language : 				<b><ion:language:code /></b></p>
<p>Language name : 				<b><ion:language:name /></b></p>
<p>Browser : 			<b><ion:browser /></b></p>

<ion:browser method="browser" is="Firefox">
	<p>You're using Firefox</p>
</ion:browser>

<ion:browser method="is_mobile" is="true">
    <p>You're using one mobile device</p>
</ion:browser>
