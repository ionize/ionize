<h1>Extended Usage : &lt;ion:tree_navigation /></h1>
<pre>
&lt;!--
	Tag attributes : 
	- menu : 		The menu name want to show (main, system, etc.)
	- helper : 		If you need different tree navigation view. You can use "helper" attribute "your_helper_name:your_function_name" like on example.
	- articles : 	true / false (Default False) If you want show articles on your tree navigation, set it to true.
	- tag : 		HTML enclosing tag for your tree navigation. Your tree navigation will start and end with this tag.
	- id : 			(Default / Empty) Id of your tree navigation menu HTML enclosing tag (set with "tag" attribute)
	- class :		(Default / Empty) Class of your tree navigation menu
	- active_class : (Default / Empty) you can set active class to your tree navigation menu for show active links

	NOTICE : This example needs the helper file : "themes/&lt;your_theme>/helpers/custom_navigation_helper.php".
-->

&lt;ion:tree_navigation menu="main" helper="custom_navigation_helper:get_custom_tree_navigation" articles="true" tag="ul" class="main-menu" active_class="active" />

</pre>

<ion:tree_navigation menu="menu" helper="custom_navigation_helper:get_custom_tree_navigation" articles="true" tag="ul" id="main-menu" class="main-menu" active_class="active" />

<ion:article>
	<ion:title/>
</ion:article>