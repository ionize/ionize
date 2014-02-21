<?php

/** 
 * Media list
 *
 */

$_uniq = 'i'.uniqid();

?>
<?php if ( !empty($items)) :?>
	<?php foreach ($items as $media) :?>
		<?php
		
		$path = substr($media['path'], strpos($media['path'], '/') + 1);
		$thumbUrl =	$thumb_base_url.$path;
		$type = $media['type'];
		$provider = $media['provider'];

		$ext = pathinfo($media['file_name'], PATHINFO_EXTENSION);

		if ( ! empty($provider) && $type == 'video')
			$background_url = theme_url() . 'javascript/filemanager/assets/images/icons/large/mpg.png';
		else
			$background_url = theme_url() . 'javascript/filemanager/assets/images/icons/large/'.$ext.'.png';
	
		$details = '';

		$title = $media['file_name'];
		if (strlen($title) > 25) $title = substr($media['file_name'], 0, 25) . '...';

		if (file_exists($media['path']))
		{
			if ($type == 'picture')
			{
				$background_url = admin_url(TRUE) . 'media/get_thumb/'.$media['id_media'].'/'.time() ;

				list($width, $height, $img_type, $attr) = @getimagesize($media['path']);
				$details.= $width.' x '.$height.' px<br/>';
			}

			$weight = sprintf('%01.2f', filesize($media['path']) / (1024 )) . 'ko';
			$details .= $weight;
		}
		else
		{
			$details = lang('ionize_exception_no_source_file');
		}
		?>		
		<div id="<?php echo $_uniq.$media['id_media'] ?>" class="media picture drag <?php echo $_uniq ?>" data-id="<?php echo $media['id_media'] ?>" data-filename="<?php echo $media['file_name'] ?>">
			<div class="thumb" style="width:<?php echo $thumb_size; ?>px;height:<?php echo $thumb_size; ?>px; background-image:url(<?php echo $background_url ; ?>);">
				<?php if ($type !== 'picture') :?>
					<span class="title lite"><?php echo $title ?></span>
				<?php endif ;?>
			</div>
			<p class="icons">
				<?php if(Authority::can('unlink', 'admin/'.$parent.'/media')) :?>
					<a class="icon unlink right help" href="javascript:mediaManager.detachMedia('<?php echo $media["id_media"]; ?>');" title="<?php echo lang('ionize_label_detach_media'); ?>"></a>
				<?php endif ;?>
				<?php if(Authority::can('edit', 'admin/'.$parent.'/media')) :?>
					<a class="icon edit left mr5" title="<?php echo lang('ionize_label_edit'); ?>"></a>
				<?php endif ;?>
				<?php if ($type == 'picture') :?>
					<a class="icon refresh left mr5 help" href="javascript:mediaManager.initThumbs('<?php echo $media["id_media"]; ?>');" title="<?php echo lang('ionize_label_init_thumb'); ?>"></a>
					<?php endif ;?>
				<a class="icon info left help" title="<?php echo $media['id_media']; ?> : <?php echo $path; ?>"></a>
			</p>
		</div>
	<?php endforeach ;?>
<?php endif ;?>

