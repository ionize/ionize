
<?php if( ! empty($link)) :?>

	<dl class="small dropArticleAsLink dropPageAsLink">
		<dt>
			<label title="<?php echo lang('ionize_help_page_link'); ?>"><?php echo lang('ionize_label_linkto'); ?></label>
			<br/>
		</dt>
		<dd>
			<ul class="sortable-container mr20" id="linkList">
			
				<li class="sortme">
		
					<a class="left link-img <?php echo $link_type; ?>" title="<?php echo $breadcrumb ?>"></a>
			
					<!-- Unlink icon -->
					<a class="icon unlink right"></a>
			
					<!-- Title -->
					<a id="link_title" class="pl5 pr10" title="<?php echo $breadcrumb; ?>"><?php echo $link; ?></a>
		
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
			var id = '<?php echo $link_id; ?>',
				type = '<?php echo $link_type; ?>',
				title = '<?php echo $link; ?>'
			;

			$('link_title').addEvent('click', function()
			{
				ION.splitPanel({
					'urlMain': ION.adminUrl + type + '/edit/' + id,
					'urlOptions': ION.adminUrl + type +'/get_options/' + id,
					'title': Lang.get('ionize_title_edit_' + type) + ' : ' + title
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



