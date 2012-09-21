<ion:partial view="header" />

<h1>Page Blog view</h1>
<p>This page is working like a blog and is using the views as defined in the Ionize backend :</p>
<ol>
	<li>One view to display list of articles <b>(the current view)</b></li>
	<li>One view to display the article detail (the complete post in fact)</li>
</ol>
<hr/>

<!-- Breadcrumb -->
<ion:breadcrumb home="true" prefix="lang('you_are_here')" />
<p>
    Page view : <ion:page:view tag="b" />
</p>

<ion:page:articles pagination="3">

	<ion:article>

		<ion:title tag="h3" />
		<ion:content paragraph="1" />
		<p>
			<a href="<ion:url />"><ion:translation item="read_more" /></a>
		</p>

	</ion:article>

</ion:page:articles>

<ion:articles:pagination pagination="3" />


<ion:partial view="footer" />

