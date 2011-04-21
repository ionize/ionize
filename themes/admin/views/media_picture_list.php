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
	
	<div class="picture drag" id="picture_<?= $media['id_media'] ?>">
		<div class="thumb" style="width:<?= $thumb_size ?>px;height:<?= $thumb_size ?>px; background-image:url(<?= admin_url(TRUE) . 'media/get_thumb/'.$media['id_media'].'/'.time()  ?>);"></div>
		<p class="icons">
			<a class="icon delete right" href="javascript:mediaManager.detachMedia('<?= $media['type'] ?>', '<?= $media['id_media'] ?>');" title="<?= lang('ionize_label_detach_media') ?>"></a>
			<a class="icon edit left mr5 " href="javascript:ION.formWindow('<?= $media['type'].$media['id_media'] ?>', 'mediaForm<?= $media['id_media'] ?>', '<?= $media['file_name'] ?>', '<?= admin_url(TRUE) ?>media/edit/picture/<?= $media['id_media'] ?>', {width:500,height:430,resize:true});" title="<?= lang('ionize_label_edit') ?>"></a>
			<a class="icon refresh left mr5 " href="javascript:mediaManager.initThumbs('<?= $media['id_media'] ?>');" title="<?= lang('ionize_label_init_thumb') ?>"></a>
			<a class="icon info left help" title="<?= $path ?>" rel="<?= $details ?>"></a>
		</p>
	</div>
	
<?php endforeach ;?>

