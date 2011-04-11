<!-- Article Duplication - Modal window -->

<form name="newArticleForm" id="newArticleForm" action="<?= admin_url() ?>article/save_duplicate">

	<!-- Hidden fields -->
	<input name="id_article" type="hidden" value="<?= $id_article ?>" />
	<input name="name" type="hidden" value="<?= $name ?>" />

	<!-- Context page ID -->
	<?php if( !empty($page)) :?>
		<input name="id_page" type="hidden" value="<?= $page['id_page'] ?>" />
	<?php endif ;?>

	<div class="summary">
	
		<dl class="small">
			<dt>
				<label><?=lang('ionize_label_title')?></label>
			</dt>
			<dd class="lite">
				<strong><?= $title ?></strong>
			</dd>
		</dl>
		
	</div>


	<!-- Name / URL -->
	<dl class="small required">
		<dt>
			<label for="dup_url"><?=lang('ionize_label_url')?></label>
		</dt>
		<dd>
			<input id="dup_url" name="dup_url" class="inputtext w260" type="text" value="<?= $name ?>" />
		</dd>
	</dl>
	
	<!-- Menu -->
	<dl class="small">
		<dt>
			<label for="dup_id_menu"><?= lang('ionize_label_menu') ?></label>
		</dt>
		<dd>
			<?= $menus ?>
		</dd>
	</dl>	

	<!-- Parent page -->
	<dl class="small">
		<dt>
			<label for="dup_id_page"><?= lang('ionize_label_page') ?></label>
		</dt>
		<dd>
			<?= $parent_select ?>
		</dd>
	</dl>	

	<!-- Order in the new page -->
	<dl class="small mt20">
		<dt>
			<label for="ordering"><?= lang('ionize_label_ordering') ?></label>
		</dt>
		<dd>
			<select name="ordering_select" id="ordering_select" class="select">
				<option value="first"><?= lang('ionize_label_ordering_first') ?></option>
				<option value="last"><?= lang('ionize_label_ordering_last') ?></option>
			</select>
		</dd>
	</dl>
	
	<!-- View in the new page -->
	<dl class="small">
		<dt>
			<label for="view<?= $UNIQ ?>"><?= lang('ionize_label_view') ?></label>
		</dt>
		<dd>
			<select name="view" id="view<?= $UNIQ ?>" class="select">
				<?php foreach($all_views as $idx => $view_name) :?>
					<option <?php if ($view == $idx) :?>selected="selected"<?php endif; ?> value="<?= $idx ?>"><?= $view_name ?></option>
				<?php endforeach ;?>
			</select>
		</dd>
	</dl>
	
	<!-- Type in the new page -->
	<dl class="small">
		<dt>
			<label for="id_type<?= $UNIQ ?>"><?= lang('ionize_label_type') ?></label>
		</dt>
		<dd>
			<select name="id_type" id="id_type<?= $UNIQ ?>" class="select">
				<?php foreach($all_types as $idx => $type_name) :?>
					<option <?php if ($id_type == $idx) :?>selected="selected"<?php endif; ?>  value="<?= $idx ?>"><?= $type_name ?></option>
				<?php endforeach ;?>
			</select>
		</dd>
	</dl>
	

	

</form>


<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through MUI.formWindow()
--> 
<div class="buttons">
	<button id="bSaveDuplicateArticle" type="button" class="button yes right mr40"><?= lang('ionize_button_save_close') ?></button>
	<button id="bCancelDuplicateArticle"  type="button" class="button no right"><?= lang('ionize_button_cancel') ?></button>
</div>


<script type="text/jaavscript">

	// Update parent select list when menu change
	$('dup_id_menu').addEvent('change', function(e)
	{
		e.stop();
		
		var xhr = new Request.HTML(
		{
			url: admin_url + 'page/get_parents_select/' + $('dup_id_menu').value,
			method: 'post',
			update: 'dup_id_page'
		}).send();
	});

</script>
