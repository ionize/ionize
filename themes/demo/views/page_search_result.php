<ion:partial path="header" />


<div class="span-22 prepend-1 append-1">

	<div class="span-16">
	
		<h2 id="page_title"><ion:title /></h2>

		
		<!-- Search Module enclosing tag
		-->
		<ion:search>
			
			<!-- Results tags : Display results only if POST data are catched by the module -->
			<ion:searchresults >
				
				<div class="text_content">
					<h3 class="bold"><ion:translation term="module_search_results_title" /> "<span class="highlight"><ion:realm /></span>"</h3>
				</div>
				
				<div id="search-results">
					
					<!-- Loop through results -->
					<ion:results>
		
						<div class="event">
							
							<div class="datetag"><span class="day"><ion:date format="d" /></span><span><ion:date format="month_year" /></span></div>
			
							<div class="event_content nopic">
								
								<span class="event_category"><ion:translation term="result_in" /> <ion:result field="page_title" /></span>
								
								<h3><a href="<ion:url />"><ion:title /></a></h3>
								
								<span class="event_date"><ion:field name="date-event" /></span>
								
								<ion:content paragraph="1" />
						
								<a href="<ion:url />" class="readmore"><ion:translation term="read_complete_article" /></a>
							</div>
						</div>
					</ion:results>
					
					<!-- If no result, display what is between these tags -->
					<ion:no_results>
					
						<div class="text_content">
							
							<!-- This translation term is a static one the user can easily change in Ionize -->
							<p><ion:translation term="module_search_message_no_results" /></p>
							
						</div>
						
						<!-- Here we also display the page articles if no results are found.
							 It is a nice way to give control on the content to editor.
						-->
						<ion:articles type="">
							<ion:article />
						</ion:articles>
					
					</ion:no_results>
				
				</div>
				
				<!-- Some JS to highlight the search term -->
				<script type="text/javascript">
					if ('<ion:realm/>' != '')
					{
						$('#search-results').highlight('<ion:realm/>');
					}
				</script>
				
			</ion:searchresults>
		
		</ion:search>

	</div>

	<div class="span-8 last">
		
	</div>

</div>

<ion:partial view="footer" />
