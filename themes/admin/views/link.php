
<?php if( ! empty($link)) :?>

	<dl class="small dropArticleAsLink dropPageAsLink">
		<dt>
			<label title="<?= lang('ionize_help_page_link') ?>"><?= lang('ionize_label_linkto') ?></label>
			<br/>
		</dt>
		<dd>
			<ul class="sortable-container mr20" id="linkList">
			
				<li class="sortme">
		
					<a class="left link-img <?= $link_type ?>" ></a>
			
					<!-- Unlink icon -->
					<a class="icon unlink right"></a>
			
					<!-- Title -->
					<a id="link_title" style="overflow:hidden;height:16px;display:block;" class="pl5 pr10" title="<?= $link ?>"><?= $link ?></a>
		
				</li>
		
			</ul>
		</dd>
	</dl>

	<script type="text/javascript">
		
		$$('#linkList li .unlink').each(function(item)
		{
			ION.initRequestEvent(item, '<?= $parent ?>/remove_link', {'rel':'<?= $rel ?>'}, {'update':'linkContainer'});
		});
		
		if ('<?= $link_type ?>' == 'external')
		{
			$('link_title').addEvent('click', function(e){window.open(this.get('text'))});
		}
		else
		{
			$('link_title').addEvent('click', function(e){
				MUI.Content.update({
					'element': $(ION.mainpanel),
					'url': '<?= $link_type ?>/edit/<?= $link_id ?>'
				});
			});
		}
		
	
	</script>
	
<?php else :?>

	<dl class="small dropArticleAsLink dropPageAsLink">
		<dt>
			<label for="link" title="<?= lang('ionize_help_page_link') ?>"><?= lang('ionize_label_link') ?></label>
			<br/>
		</dt>
		<dd>
			<textarea id="link" class="inputtext h40 droppable" alt="<?= lang('ionize_label_drop_link_here') ?>"></textarea>
			<br />
			<a id="add_link"><?= lang('ionize_label_add_link') ?></a>
		</dd>
	</dl>

	<script type="text/javascript">
		
		
		ION.initDroppable();

		$('add_link').addEvent('click', function()
		{
			ION.JSON('<?= $parent ?>/add_link', {'receiver_rel': $('rel').value, 'link_type': 'external', 'url': $('link').value});
		})
		

	</script>


<?php endif ;?>



