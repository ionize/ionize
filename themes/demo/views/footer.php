
</div> <!-- /#container -->


<!-- Footer -->
<div id="footer" class="container">

	<!-- Articles from one defined page
		 Take care : The page "footer" must not change its URL !
	-->
	<ion:articles from="footer" limit="3">
	
		<div class="span-33<?php if('<ion:index />' == '1') :?> prepend-1<?php endif ;?>">

			<!-- Each article uses its own view, as set in Ionize -->
			<ion:article />
			
		</div>
		
	</ion:articles>

</div>


<!-- Footer Copyright -->
<div class="container footer">
	
	<div id="footer-copyright">
		
		<div id="copyright">More info on <a href="http://ionizecms.com" onclick="window.open(this.href,'_blank');return false;">Ionize CMS website</a>.</div>
		
		<div id="footermenu">
			<ul>
				<!-- Footer navigation menu : Simply the base level -->
				<ion:navigation tag="ul" level="0" />
			</ul>
		</div>
	
	</div>
	
</div>


<!-- Google Analytics code -->

</body>
</html>
