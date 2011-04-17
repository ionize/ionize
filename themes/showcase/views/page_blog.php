<ion:partial path="header"/>
	
	<!-- content -->
	<div id="content-outer" class="clear"><div id="content-wrap">
	
		<div id="content">
		
			<div id="left">			
				<ion:articles>
				<div class="entry">
				
					<ion:article/>
				
				</div>
				</ion:articles>
				
				
			</div>
		
			<div id="right">
							
				<div class="sidemenu">	
					<h3>Categories</h3>
					<ul>				
						<ion:categories>
				
							<li><a href="<ion:url />"><ion:title /></a></li>
				
						</ion:categories>
					</ul>	
				</div>
							
				<div class="sidemenu">
					<h3>Archive</h3>
					<ul>
						<ion:archives with_month="true">
				
							<li><a class="<ion:active_class />" href="<ion:url />"><ion:period /> - (<ion:nb /> articles)</a></li>
				
						</ion:archives>
						
					</ul>
				</div>		
				
				<h3>Search</h3>
			
				<form id="quick-search" action="index.html" method="get" >
					<p>
					<label for="qsearch">Search:</label>
					<input class="tbox" id="qsearch" type="text" name="qsearch" value="type and hit enter..." title="Start typing and hit ENTER" />
					<input class="btn" alt="Search" type="image" name="searchsubmit" title="Search" src="<ion:theme_url/>assets/images/search.gif" />
					</p>
				</form>	
					
			</div>		
		
		</div>	
			
	<!-- content end -->	
	</div></div>
<ion:partial path="footer"/>