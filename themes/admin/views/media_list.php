<?php

/** 
 * Media list
 *
 */
    log_message('error', 'View File Loaded : media_list.php');
//    log_message('error', print_r($items, TRUE));
?>

<?php if ( !empty($items)) :?>

	<?php foreach ($items as $media) :?>
	
		<?php
		
		$path = substr($media['path'], strpos($media['path'], '/') + 1);
		
		// $edit_href = "javascript:ION.formWindow('". $media['type'].$media['id_media'] ."', 'mediaForm". $media['id_media'] ."', '" . $media['file_name'] ."', '". admin_url(TRUE) ."media/edit/picture/". $media['id_media'] . "/" . $parent ."/" . $id_parent ."', {width:520,height:430,resize:false})";

		$edit_href = "javascript:ION.formWindow(
						'". $media['type'].$media['id_media'] ."',
						'mediaForm". $media['id_media'] ."', 
						'". $media['file_name'] ."',
						'media/edit/".$media['type'] ."/". $media['id_media'] ."/". $parent ."/". $id_parent ."',
						{width:500,height:460}
					 );";
		
		if (file_exists($media['path']))
		{
			$weight = sprintf('%01.2f', filesize($media['path']) / (1024 )) . 'ko';
			
			$details = $weight;
		}
		else
		{
			$details = lang('ionize_exception_no_source_file');
		}
		
		?>
		
		<li class="sortme" id="<?= $media['type'] ?>_<?= $media['id_media'] ?>">
			<a class="icon right unlink" onclick="javascript:mediaManager.detachMedia('<?= $media['type'] ?>', '<?= $media['id_media'] ?>');" title="<?= lang('ionize_label_detach_media') ?>"></a>
			<?php if( ! empty($media['lang_display'])) :?>
				<a class="right mr10 " href="<?= $edit_href ?>"><?=lang('ionize_label_media_display_limited_to_lang')?> : <img src="<?= theme_url() ?>/images/world_flags/flag_<?= $media['lang_display']; ?>.gif" /></a>
			<?php endif ;?>
			<span class="icon left drag"></span>
			<a class="icon edit left mr5 ml5 help" href="<?= $edit_href ?>" title="<?= lang('ionize_label_edit') ?>"></a>
			<a class="icon info left help ml5" title="<?= $media['path'] ?>" rel="<?= $details ?>"></a>
			<a class="left ml10 help" href="<?= $edit_href ?>" title="<?= lang('ionize_label_edit') ?>"><?php if ($this->connect->is('super-admins') ) :?><?= $media['id_media'] ?> : <?php endif ;?><?= $media['file_name'] ?></a>
		</li>

	<?php endforeach ;?>

<?php endif ;?>

