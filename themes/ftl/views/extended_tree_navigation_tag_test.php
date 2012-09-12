<h1>Extended Usage : &lt;ion:tree_navigation /></h1>
<pre>
    &lt;!--
        Menu : Your menu name want to show.
        Helper : If you need different tree navigation view. You can use "helper" attribute "your_helper_name:your_function_name" like on example.
        Articles : true / false (Default False) If you want show articles on your tree navigation make it true
        Tag : Html tag for your tree navigation. Your tree navigation will start and end with this tag.
        ID : (Default / Empty) Id of your tree navigation menu
        CLASS : (Default / Empty) Class of your tree navigation menu
        active_class : (Default / Empty) you can set active class to your tree navigation menu for show active links

        NOTICE : This example need helpers/custom_navigation_helper.php file.
    -->
</pre>
<pre>
&lt;ion:tree_navigation menu="main" helper="custom_navigation_helper:get_custom_tree_navigation" articles="true" tag="ul" class="main-menu" active_class="active" />
</pre>
<!--
    Menu : Your menu name want to show.
    Helper : If you need different tree navigation view. You can use "helper" attribute "your_helper_name:your_function_name" like on example.
    Articles : true / false (Default False) If you want show articles on your tree navigation make it true
    Tag : Html tag for your tree navigation. Your tree navigation will start and end with this tag.
    ID : Id of your tree navigation menu (Default / Empty)
    CLASS : Class of your tree navigation menu (Default / Empty)
    active_class : (Default / Empty) you can set active class to your tree navigation menu for show active links
-->
<ion:tree_navigation menu="menu" helper="custom_navigation_helper:get_custom_tree_navigation" articles="true" tag="ul" id="main-menu" class="main-menu" active_class="active" />