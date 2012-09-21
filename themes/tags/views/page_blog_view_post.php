<ion:partial view="header" />

<h1>Page Blog One post view</h1>
<p>This page is working like a blog and is using the views as defined in the Ionize backend :</p>
<ol>
    <li>One view to display list of articles</li>
    <li>One view to display the article detail <b>(the current view)</b></li>
</ol>
<hr/>

<!-- Breadcrumb -->
<ion:breadcrumb home="true" article="true" prefix="lang('you_are_here')" />

<p>
	Page view : <ion:page:view tag="b" />
</p>

<ion:article>
    <ion:title tag="h3" />
	<p>
		<ion:view prefix="Article view : " tag="b"/>
	</p>
    <ion:content />
</ion:article>

<hr/>

<ion:page:articles limit="3">
	<ion:article>
		<ion:title tag="h4"/>
		<ion:content ellipsize="32,1" />
	</ion:article>
</ion:page:articles>

<ion:partial view="footer" />

