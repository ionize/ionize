<!-- Article Duplication - Modal window -->

<form name="newArticleForm" id="newArticleForm" action="<?php echo admin_url(); ?>article/save_duplicate">

	<!-- Hidden fields -->
	<input name="id_article" type="hidden" value="<?php echo $id_article; ?>" />
	<input name="name" type="hidden" value="<?php echo $name; ?>" />

	<!-- Context page ID -->
	<?php if( !empty($page)) :?>
		<input name="id_page" type="hidden" value="<?php echo $page['id_page']; ?>" />
	<?php endif ;?>

	<div class="summary">

		<dl class="small">
			<dt><label><?php echo lang('ionize_label_title'); ?></label></dt>
			<dd class="lite"><strong><?php echo $title; ?></strong></dd>
		</dl>

		<dl class="small">
			<dt><label></dt>
			<dd class="lite"><?php echo lang('ionize_help_duplicate_article');?></dd>
		</dl>

	</div>


	<!-- Name / URL -->
	<dl class="small required">
		<dt>
			<label for="dup_url"><?php echo lang('ionize_label_name'); ?></label>
		</dt>
		<dd>
			<input id="dup_url" name="dup_url" class="inputtext w260" type="text" value="<?php echo $name; ?>" />
		</dd>
	</dl>
	
	<!-- Menu -->
	<dl class="small">
		<dt>
			<label for="dup_id_menu"><?php echo lang('ionize_label_menu'); ?></label>
		</dt>
		<dd>
			<?php echo $menus; ?>
		</dd>
	</dl>	

	<!-- Parent page -->
	<dl class="small">
		<dt>
			<label for="dup_id_page"><?php echo lang('ionize_label_page'); ?></label>
		</dt>
		<dd>
			<div id="dupArticleParentSelectContainer"></div>
		</dd>
	</dl>	

	<!-- Order in the new page -->
	<dl class="small mt20">
		<dt>
			<label for="ordering"><?php echo lang('ionize_label_ordering'); ?></label>
		</dt>
		<dd>
			<select name="ordering_select" id="ordering_select" class="select">
				<option value="first"><?php echo lang('ionize_label_ordering_first'); ?></option>
				<option value="last"><?php echo lang('ionize_label_ordering_last'); ?></option>
			</select>
		</dd>
	</dl>
	
	<!-- View in the new page -->
	<dl class="small">
		<dt>
			<label for="view<?php echo $UNIQ; ?>"><?php echo lang('ionize_label_view'); ?></label>
		</dt>
		<dd>
			<select name="view" id="view<?php echo $UNIQ; ?>" class="select">
				<?php foreach($all_views as $idx => $view_name) :?>
					<option <?php if ($view == $idx) :?>selected="selected"<?php endif; ?> value="<?php echo $idx; ?>"><?php echo $view_name; ?></option>
				<?php endforeach ;?>
			</select>
		</dd>
	</dl>
	
	<!-- Type in the new page -->
	<dl class="small">
		<dt>
			<label for="id_type<?php echo $UNIQ; ?>"><?php echo lang('ionize_label_type'); ?></label>
		</dt>
		<dd>
			<select name="id_type" id="id_type<?php echo $UNIQ; ?>" class="select">
				<?php foreach($all_types as $idx => $type_name) :?>
					<option <?php if ($id_type == $idx) :?>selected="selected"<?php endif; ?>  value="<?php echo $idx; ?>"><?php echo $type_name; ?></option>
				<?php endforeach ;?>
			</select>
		</dd>
	</dl>
	

	

</form>


<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through ION.formWindow()
--> 
<div class="buttons">
	<button id="bSaveDuplicateArticle" type="button" class="button yes right"><?php echo lang('ionize_button_save_close'); ?></button>
	<button id="bCancelDuplicateArticle"  type="button" class="button no right"><?php echo lang('ionize_button_cancel'); ?></button>
</div>


<script type="text/javascript">

	$('dup_id_menu').addEvent('change', function()
	{
		ION.HTML(
			admin_url + 'page/get_parents_select',
			{
				'id_menu' : $('dup_id_menu').value,
				'id_current': 0,
				'id_parent': 0,
				'element_id': 'dup_id_page'
			},
			{'update': 'dupArticleParentSelectContainer'}
		);
	});
	$('dup_id_menu').fireEvent('change');


</script>
