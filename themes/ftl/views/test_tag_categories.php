
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
	The context fo the parent page isn't set.<br/>
	Counts all articles / category
</p>
<pre>
&lt;ion:categories all='true' tag="ul">
	&lt;li>
		&lt;a &lt;ion:category:is_active> class="&lt;ion:category:active_class />" &lt;/ion:category:is_active>  href="&lt;ion:category:url />">
			&lt;ion:category:title /> - (&lt;ion:nb_articles /> articles)
		&lt;/a>
	&lt;/li>
&lt;/ion:categories>
</pre>
<ion:categories tag="ul" active_class="my-active-class">
	<li>
		<a <ion:category:is_active> class="<ion:category:active_class />" </ion:category:is_active>  href="<ion:category:url />">
			<ion:category:title /> - (<ion:nb_articles /> articles)
		</a>
	</li>
</ion:categories>



<hr />


<h2>Categories used by articles linked to the current page</h2>
<pre>
&lt;ion:page>
	&lt;ion:categories tag="ul">
		&lt;ion:category>
			&lt;li>
				&lt;a href="&lt;ion:url />">&lt;ion:title /> - (&lt;ion:nb_articles /> articles)&lt;/a>
			&lt;/li>
		&lt;/ion:category>
	&lt;/ion:categories>
&lt;/ion:page>
</pre>

<ion:page>
	<ion:categories tag="ul">
		<ion:category>
			<li>
				<a href="<ion:url />"><ion:title /> - (<ion:nb_articles /> articles)</a>
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
<p>
	Filter on Category : <b class="red"><ion:category:current:title /></b>
</p>
<ion:articles>
	<ion:article:title tag="h3" />
	<ion:article:categories link="true" separator=" &bull; "/><br/>
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
