<?php

/**
 * Modal window for Article's page context edition
 *
 */

$article_title = ($article['title'] != '') ? $article['title'] : $article['name'];
$page_title = ($page['title'] != '') ? $page['title'] : $page['name'];



?>

<form name="formArticleContext<?=$id_context?>" id="formArticleContext<?=$id_context?>" action="<?= admin_url() ?>article/save_context">

	<!-- Hidden fields -->
	<input id="id_article" name="id_article" type="hidden" value="<?= $article['id_article'] ?>" />
	<input id="id_page" name="id_page" type="hidden" value="<?= $page['id_page'] ?>" />

	<!-- Infos -->
	<div class="summary">
	
		<dl class="small">
			<dt><label><?= lang('ionize_label_article') ?></label></dt>
			<dd class="lite"><strong><?= $article_title ?></strong></dd>
		</dl>

		<dl class="small">
			<dt><label><?= lang('ionize_label_page') ?></label></dt>
			<dd class="lite"><strong><?= $page_title ?></strong></dd>
		</dl>
	
	</div>

	<!-- Online / Offline -->
	<dl class="small">
		<dt>
			<label for="online<?=$id_context?>"><?= lang('ionize_label_online') ?></label>
		</dt>
		<dd>
			<div>
				<input id="online<?=$id_context?>" <?php if ($article['online'] == 1):?> checked="checked" <?php endif;?> name="online" class="inputcheckbox" type="checkbox" value="1"/>
			</div>
		</dd>
	</dl>


	<!-- Article Template -->
	<?php if (isset($article_views)) :?>
		<dl class="small">
			<dt>
				<label for="view"><?= lang('ionize_label_template') ?></label>
			</dt>
			<dd>
				<?= $article_views ?>
			</dd>
		</dl>
	<?php endif ;?>
	
	<!-- Article Type -->
	<dl class="small">
		<dt>
			<label title="<?= lang('ionize_help_articles_types') ?>"><?= lang('ionize_label_type') ?></label>
		</dt>
		<dd>
			<div id="article_types">
				<?php if (isset($article_types)) :?>
					<?= $article_types ?>
				<?php endif ;?>
			</div>
			
			<!-- Types list -->
			<a onclick="javascript:MUI.dataWindow('Types', 'ionize_title_types', 'article_type/get_types/article/<?= $article['id_article'] ?>', {width:350, height:200});"><?= lang('ionize_label_edit_types') ?></a><br/>
			
			<!-- Type create button -->
			<a onclick="javascript:MUI.formWindow('Type', 'typeForm', '<?= lang('ionize_title_article_type_new') ?>', 'article_type/get_form/article/<?= $article['id_article'] ?>', {width:360, height:75})"><?= lang('ionize_label_new_type') ?></a>
			
		</dd>
	</dl>
	

</form>


<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through MUI.formWindow()
--> 
<div class="buttons">
	<button id="bSaveArticleContext<?=$id_context?>" type="button" class="button yes right mr40"><?= lang('ionize_button_save_close') ?></button>
	<button id="bCancelArticleContext<?=$id_context?>"  type="button" class="button no right"><?= lang('ionize_button_cancel') ?></button>
</div>

<script type="text/javascript">



</script>
