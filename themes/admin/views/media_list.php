<?php

/** 
 * Media list
 *
 */
// trace($items);
?>

<?php if ( !empty($items)) :?>

	<?php foreach ($items as $media) :?>
	
		<?php
		
		$path = substr($media['path'], strpos($media['path'], '/') + 1);
		$details = $path;
		
		if ( ! file_exists($media['path']))
		{
			$details = lang('ionize_exception_no_source_file');
		}
		
		?>
		
		<li class="sortme" id="<?= $media['type'] ?>_<?= $media['id_media'] ?>">
			<img class="icon right" onclick="javascript:mediaManager.detachMedia('<?= $media['type'] ?>', '<?= $media['id_media'] ?>');" src="<?= theme_url() ?>images/icon_16_delete.png" alt="<?= lang('ionize_label_detach_media') ?>" />
			<?php if( ! empty($media['lang_display'])) :?>
				<span class="right mr10"><?=lang('ionize_label_media_display_limited_to_lang')?> : <img alt="<?= $media['lang_display']; ?>" src="<?= theme_url() ?>/images/world_flags/flag_<?= $media['lang_display']; ?>.gif" /></span>
			<?php endif ;?>
			<img class="icon left drag pr5" src="<?= theme_url() ?>images/icon_16_ordering.png" />
			<a class="icon edit left mr5 ml5" href="javascript:ION.formWindow('<?= $media['type'].$media['id_media'] ?>', 'mediaForm<?= $media['id_media'] ?>', '<?= $media['file_name'] ?>', '<?= admin_url(TRUE) ?>media/edit/picture/<?= $media['id_media'] ?>/<?= $parent ?>/<?= $id_parent ?>', {width:520,height:430,resize:false});" title="<?= lang('ionize_label_edit') ?>"></a>
			<a class="icon info left help ml5" title="<?= $path ?>" rel="<?= $details?>"></a>
			<a class="left ml10" href="javascript:ION.formWindow('<?= $media['type'].$media['id_media'] ?>', 'mediaForm<?= $media['id_media'] ?>', '<?= $media['file_name'] ?>', 'media/edit/<?= $media['type'] ?>/<?= $media['id_media'] ?>/<?= $parent ?>/<?= $id_parent ?>', {width:500,height:460});" title="edit"><?php if ($this->connect->is('super-admins') ) :?><?= $media['id_media'] ?> : <?php endif ;?><?= $media['file_name'] ?></a>
		</li>

	<?php endforeach ;?>

<?php endif ;?>

