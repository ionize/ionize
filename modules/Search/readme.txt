
Ionize Search Module

Author : 			Ionize Dev Team
Creation date : 	2010.09.10
Last update :		2010.09.10
Ionize version :	0.9.5

Important :			This module is a demonstration module wich comes with Ionize 0.9.5.
					It is delivered as it is, with the purpose of showing to developpers how to build modules in Ionize.
					
					Because Ionize has not reached the 1.0 version at this time, this module is of course incomplete.
					
					The dev team will improve this module demo for the next versions of Ionize.
					
					Have fun !
					

--------------------------------------------------------------------------------


General informations about modules
---------------------------------------

Modules can be used in 2 modes : 

1. "Integrated mode"
   The module is called through dedicated tags, in standard Ionize page or article views.
   The module contains tags definitions and these tags are called by your page or article views.
   The module's tags returns data as defined by the module.
   
2. "URL mode"
   The module is used like a single CodeIgniter application, using the standard CodeIgniter MVC approach :
   The module's controller is called through the URL, gets the data and prints out the view.
   In this case, the page and article data available through tags are not available
   This way of using modules is great when refreshing a page element through XHR (Ajax) 



Installation
---------------------------------------

1. Register the module through the Ionize Modules panel (click on "install")

2. Create an article view (called for example "search_form_article"), put it in your theme view folder and register it in the Ionize Theme panel.

3. Depending on the mode you choose, edit your view :

   3.1 Integrated mode
   
       Your view will typically contain :
       
		
		---------------------------------------
		<div class="article">
	
			<ion:title tag="h2" />
			
			<ion:subtitle tag="h3" class="subtitle" />
			
			<ion:content />
			
			<ion:search>
				
				<!-- Form tag : displays the form only of no POST data are catched by the module -->
				<ion:searchform />
				
				<!-- Results tags : Display results only if POST data are catched by the module -->
				<ion:results >
				
					<p><ion:title /> :: <ion:url /> </p>
				
				</ion:results>
			
			</ion:search>
			
		</div>
		---------------------------------------


   3.1 URL mode

       Your view will typically contain :

		---------------------------------------
		<div class="article">
		
			<ion:title tag="h2" />
		
			<ion:subtitle tag="h3" class="subtitle" />
				
			<ion:content />
		
			<!-- 
				The form will be processed by the function find_pure_php() of the /modules/Search/controllers/search.php controller
				In this example, the "find_pure_php" function will display the view /modules/Search/views/results_pure_php.php
			-->
			<form method="post" action="<ion:base_url lang="true" />search/find_pure_php">
			
				<input id="search-input" type="text" name="realm" value="" />
			
				<input type="submit" class="searchbutton" value="<ion:translation term="module_search_button_start" />" />
			
			</form>
		
		</div>
		---------------------------------------

		The example does not include the XHR javascript code to update just a part of your page.
		You will need to develop this part of code.


