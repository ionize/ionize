<?php

/** 
 * Media picture list view
 * Used by ionizeMediaManager to display current aticles picture list
 *
 */

// Basic vars
$file_path = Settings::get('files_path').'/';

$thumb_base_url = base_url().$file_path.'.thumbs/';

$thumb_size = (Settings::get('media_thumb_size') != '') ? Settings::get('media_thumb_size') : '120';

?>

<?php foreach ($items as $media) :?>
	<?php
	
//	$path = str_replace($file_path, '', $media['path']);
	
	$path = substr($media['path'], strpos($media['path'], '/') + 1);
	$thumbUrl =	$thumb_base_url.$path;
	// Get picture thumb size	
	$details = '';

	$edit_href = "javascript:ION.formWindow('". $media['type'].$media['id_media'] ."', 'mediaForm". $media['id_media'] ."', '" . $media['file_name'] ."', '". admin_url(TRUE) ."media/edit/picture/". $media['id_media'] . "/" . $parent ."/" . $id_parent ."', {width:520,height:430,resize:false})";

	if (file_exists($media['path']))
	{
		$weight = sprintf('%01.2f', filesize($media['path']) / (1024 )) . 'ko';

		list($width, $height, $type, $attr) = @getimagesize($media['path']);
		
		$details = $width.' x '.$height.' px<br/>'.$weight;
	}
	else
	{
		$details = lang('ionize_exception_no_source_file');
	}
	?>
	<div class="picture drag" id="picture_<?php echo $media['id_media']; ?>">
		<div class="thumb" style="width:<?php echo $thumb_size; ?>px;height:<?php echo $thumb_size; ?>px; background-image:url(<?php echo admin_url(TRUE) . 'media/get_thumb/'.$media['id_media'].'/'.time() ; ?>);"></div>
		<p class="icons">
			<a class="icon unlink right help" href="javascript:mediaManager.detachMedia('<?php echo $media['type']; ?>', '<?php echo $media['id_media']; ?>');" title="<?php echo lang('ionize_label_detach_media'); ?>"></a>
			<a class="icon edit left mr5" href="<?php echo $edit_href; ?>" title="<?php echo lang('ionize_label_edit'); ?>"></a>
			<a class="icon process left mr5 help" href="javascript:mediaManager.initThumbs('<?php echo $media['id_media']; ?>');" title="<?php echo lang('ionize_label_init_thumb'); ?>"></a>
			<a class="icon info left help" title="<?php if ($this->connect->is('super-admins') ) :?><?php echo $media['id_media']; ?> : <?php endif ;?><?php echo $path; ?>" rel="<?php echo $details; ?>"></a>
			<?php if( ! empty($media['lang_display'])) :?>
				<a class="icon left ml5 help"  title="<?php echo lang('ionize_label_media_display_limited_to_lang'); ?> : <?php echo $media['lang_display']; ?>" href="<?php echo $edit_href; ?>"><img alt="<?php echo lang('ionize_label_media_display_limited_to_lang'); ?> : <?php echo $media['lang_display']; ?>" src="<?php echo theme_url(); ?>/images/world_flags/flag_<?php echo $media['lang_display']; ?>.gif" /></a>
			<?php endif ;?>
		</p>
	</div>
<?php endforeach ;?>

