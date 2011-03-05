<?php

/** 
 * Media list
 *
 */

?>

<?php if ( !empty($items)) :?>

	<?php foreach ($items as $media) :?>
	
		<li class="sortme" id="<?= $media['type'] ?>_<?= $media['id_media'] ?>">
			<img class="icon right" onclick="javascript:mediaManager.detachMedia('<?= $media['type'] ?>', '<?= $media['id_media'] ?>');" src="<?= theme_url() ?>images/icon_16_delete.png" alt="<?= lang('ionize_label_detach_media') ?>" />
			<img class="icon left drag" src="<?= theme_url() ?>images/icon_16_ordering.png" />
			<a class="left pl5" href="javascript:MUI.formWindow('<?= $media['type'].$media['id_media'] ?>', 'mediaForm<?= $media['id_media'] ?>', '<?= $media['file_name'] ?>', 'media/edit/<?= $media['type'] ?>/<?= $media['id_media'] ?>', {width:500,height:460});" title="edit"><?= $media['file_name'] ?></a>
		</li>

	<?php endforeach ;?>

<?php endif ;?>

