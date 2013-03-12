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

		$title = $media['file_name'];
		if (strlen($title) > 25)
			$title = substr($media['file_name'], 0, 25) . '...';

		$edit_href = "javascript:ION.formWindow(
						'". $media['type'].$media['id_media'] ."',
						'mediaForm". $media['id_media'] ."', 
						'". $title ."',
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
		
		<li class="sortme" id="<?php echo $media['type']; ?>_<?php echo $media['id_media']; ?>">
			<a class="icon right unlink" onclick="javascript:mediaManager.detachMedia('<?php echo $media['type']; ?>', '<?php echo $media['id_media']; ?>');" title="<?php echo lang('ionize_label_detach_media'); ?>"></a>
			<?php if( ! empty($media['lang_display'])) :?>
				<a class="right mr10 " href="<?php echo $edit_href; ?>"><?php echo lang('ionize_label_media_display_limited_to_lang'); ?> : <img src="<?php echo theme_url(); ?>/images/world_flags/flag_<?php echo $media['lang_display']; ?>.gif" /></a>
			<?php endif ;?>
			<span class="icon left drag"></span>
			<a class="icon edit left mr5 ml5 help" href="<?php echo $edit_href; ?>" title="<?php echo lang('ionize_label_edit'); ?>"></a>
			<a class="icon info left help ml5" title="<?php echo $media['path']; ?>" rel="<?php echo $details; ?>"></a>
			<a class="left ml10 help" href="<?php echo $edit_href; ?>" title="<?php echo lang('ionize_label_edit'); ?>">
				<?php echo $media['id_media']; ?> :
				<?php if ($media['provider'] !== ''): ?>
					<?php echo $media['provider']; ?> :
				<?php endif ;?>
				<?php echo $media['file_name']; ?>
			</a>
		</li>

	<?php endforeach ;?>

<?php endif ;?>

