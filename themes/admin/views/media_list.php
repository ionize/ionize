<?php

/** 
 * Media list
 *
 */


?>

<?php if ( !empty($items)) :?>

	<?php foreach ($items as $media) :?>
	
		<?php
		
		$path = substr($media['path'], strpos($media['path'], '/') + 1);
		$details = '';
		
		if ( ! file_exists($media['path']))
		{
			$details = lang('ionize_exception_no_source_file');
		}
		
		?>
		
		<li class="sortme" id="<?= $media['type'] ?>_<?= $media['id_media'] ?>">
			<img class="icon right" onclick="javascript:mediaManager.detachMedia('<?= $media['type'] ?>', '<?= $media['id_media'] ?>');" src="<?= theme_url() ?>images/icon_16_delete.png" alt="<?= lang('ionize_label_detach_media') ?>" />
			<a class="icon left drag" ></a>
			<a class="icon info left help ml10" title="<?= $path ?>" rel="<?= $details?>"></a>
			<a class="left ml10" href="javascript:ION.formWindow('<?= $media['type'].$media['id_media'] ?>', 'mediaForm<?= $media['id_media'] ?>', '<?= $media['file_name'] ?>', 'media/edit/<?= $media['type'] ?>/<?= $media['id_media'] ?>', {width:500,height:460});" title="edit"><?php if ($this->connect->is('super-admins') ) :?><?= $media['id_media'] ?> : <?php endif ;?><?= $media['file_name'] ?></a>
		</li>

	<?php endforeach ;?>

<?php endif ;?>

