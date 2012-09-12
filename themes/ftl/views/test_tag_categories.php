
<h1>Categories</h1>
<ion:tree_navigation />
<hr/>

<h2>Categories Settings</h2>
<?php
	$uri_config = array_flip(config_item('special_uri'));
	$uri_config = $uri_config['category'];
?>
<p>Category base URI (config/ionize.php) : <b><?php echo $uri_config; ?></b></p>

<h2>All categories</h2>
<p>
	Use of the tag outside from any page tag.<br/>
	The context fo the parent page isn't set !
</p>
<pre>
&lt;ion:categories all='true' tag="ul">
	&lt;li>
		&lt;a href="&lt;ion:category:url />">Code : &lt;ion:category:name /> -  Title : &lt;ion:category:title />&lt;/a>
	&lt;/li>
&lt;/ion:categories>
</pre>
<ion:categories all='true' tag="ul" active_class="my-active-class">
	<li>
		<a <ion:category:is_active> class="<ion:category:active_class />" </ion:category:is_active>  href="<ion:category:url />">Code : <ion:category:name /> -  Title : <ion:category:title /></a>
	</li>
</ion:categories>



<hr />


<h2>Categories used by articles linked to the current page</h2>
<p>
	We simply don't use the attribute <b>"all"</b>.
</p>
<pre>
&lt;ion:page>
	&lt;ion:categories tag="ul">
		&lt;ion:category>
			&lt;li>
				&lt;a href="&lt;ion:url />">Code : &lt;ion:name /> - Title : &lt;ion:title />&lt;/a>
			&lt;/li>
		&lt;/ion:category>
	&lt;/ion:categories>
&lt;/ion:page>
</pre>

<ion:page>
	<ion:categories tag="ul">
		<ion:category>
			<li>
				<a href="<ion:url />">Code : <ion:name /> - Title : <ion:title /></a>
			</li>
		</ion:category>
	</ion:categories>
</ion:page>

<hr/>


<h2>Current category articles list</h2>
<p>
	If one category link is clicked in the previous link list,
	this articles list will only display the articles linked to this category
</p>
<ion:articles>
	<ion:article:title tag="h3" />
	<ion:article:content paragraph="1" />
</ion:articles>

<hr/>

<h2>Categories count</h2>

<h3>Number of categories</h3>
<p>
	The use of the attribute <b>loop="false"</b> tells the <b>categories</b> tag to not loop through its children.<br/>
	If not set, it will display the number of categories ... the number of categories time !
</p>
<pre>
	&lt;ion:categories:count loop="false" />	
</pre>
<ion:categories:count loop="false" />


<h3>Number of categories used by articles in the current page</h3>

<pre>
&lt;ion:page:categories:count loop="false" />
</pre>
<ion:page:categories:count loop="false" />
