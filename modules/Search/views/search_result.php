<!-- Search Module enclosing tag
-->
<ion:search>
	
	<!-- Results tags : Display results only if POST data are catched by the module -->
	<ion:searchresults >
		
		<h3><ion:translation term="module_search_results_title" /> "<ion:realm />"</h3>
		
			
			<!-- 
				Loop through results
				Each result is an article, so article's tags can be used to display content.
			-->
			<ion:results>

				<ion:title tag="h3" />
						
				<ion:content paragraph="1" />
				
				<a href="<ion:url />" class="readmore"><ion:translation term="read_complete_article" /></a>

			</ion:results>
			
			<!-- If no result, display what is between these tags -->
			<ion:no_results>
			
				<!-- This translation term is a static one the user can easily change in Ionize -->
				<p><ion:translation term="module_search_message_no_results" /></p>
					
				
				<!-- Here we display the "Search Page" articles if no results are found.
					 It is a nice way to give control on the content to editor.
				-->
				<ion:articles type="">
					<ion:article />
				</ion:articles>
			
			</ion:no_results>
		
		</div>
		
		<!-- Some JS to highlight the search term -->
		<script type="text/javascript">
			
			/**
			 * This is a example supposing the "highlight" method is implemented to each DOM object
			 * with javascript (mootools or jQuery)
			 * You will need to implement a JS library which highlights text, like : 
			 * http://johannburkard.de/blog/programming/javascript/highlight-javascript-text-higlighting-jquery-plugin.html
			 *
			 */
			if ('<ion:realm/>' != '')
			{
				$('#search-results').highlight('<ion:realm/>');
			}
		</script>
		
	</ion:searchresults>

</ion:search>

