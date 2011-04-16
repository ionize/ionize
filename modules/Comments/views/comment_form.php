


<!-- Comments module's form -->


<div class="divider">
</div>


	<div id="respond">
		<h3><ion:translation term="module_comments_reply"/></h3>
		
		<form method="post" action="<ion:name/>#reply">

	
			<p><input type="text" name="author" id="author" size="22" tabindex="1" aria-required='true' />
			<label for="author"><span><ion:translation term="module_comments_name"/></span></label></p>
	
			<p><input type="text" name="email" id="email" size="22" tabindex="2" aria-required='true' />
			<label for="email"><span><ion:translation term="module_comments_email"/></span></label></p>
	
			<p><input type="text" name="url" id="url" size="22" tabindex="3" />
			<label for="url"><span><ion:translation term="module_comments_website"/></span></label></p>
	
	
	
			<p><textarea name="content" id="comment" cols="50" rows="10" tabindex="4"></textarea></p>
	
			<button class="button" type="submit"><ion:translation term="module_comments_send" /></button>
		</form>
	</div>