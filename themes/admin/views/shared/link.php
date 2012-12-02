
<?php if( ! empty($link)) :?>

	<dl class="small dropArticleAsLink dropPageAsLink">
		<dt>
			<label title="<?php echo lang('ionize_help_page_link'); ?>"><?php echo lang('ionize_label_linkto'); ?></label>
			<br/>
		</dt>
		<dd>
			<ul class="sortable-container mr20" id="linkList">
			
				<li class="sortme">
		
					<a class="left link-img <?php echo $link_type; ?>" ></a>
			
					<!-- Unlink icon -->
					<a class="icon unlink right"></a>
			
					<!-- Title -->
					<a id="link_title" style="overflow:hidden;height:16px;display:block;" class="pl5 pr10" title="<?php echo $link; ?>"><?php echo $link; ?></a>
		
				</li>
		
			</ul>
		</dd>
	</dl>

	<script type="text/javascript">
		
		$$('#linkList li .unlink').each(function(item)
		{
			ION.initRequestEvent(item, '<?php echo $parent; ?>/remove_link', {'rel':'<?php echo $rel; ?>'}, {'update':'linkContainer'});
		});
		
		if ('<?php echo $link_type; ?>' == 'external')
		{
			$('link_title').addEvent('click', function(e){window.open(this.get('text'))});
		}
		else
		{
			$('link_title').addEvent('click', function(e){
                ION.contentUpdate({
					'element': $(ION.mainpanel),
					'url': '<?php echo $link_type; ?>/edit/<?php echo $link_id; ?>'
				});
			});
		}
		
	
	</script>
	
<?php else :?>

	<dl class="small dropArticleAsLink dropPageAsLink">
		<dt>
			<label for="link" title="<?php echo lang('ionize_help_page_link'); ?>"><?php echo lang('ionize_label_link'); ?></label>
			<br/>
		</dt>
		<dd>
			<textarea id="link" class="inputtext h40 droppable" alt="<?php echo lang('ionize_label_drop_link_here'); ?>"></textarea>
			<br />
			<a id="add_link"><?php echo lang('ionize_label_add_link'); ?></a>
		</dd>
	</dl>

	<script type="text/javascript">
		
		
		ION.initDroppable();

		$('add_link').addEvent('click', function()
		{
			ION.JSON('<?php echo $parent; ?>/add_link', {'receiver_rel': $('rel').value, 'link_type': 'external', 'url': $('link').value});
		})
		

	</script>


<?php endif ;?>



