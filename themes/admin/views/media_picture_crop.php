<?php

/**
 * Modal window for Picture Crop
 *
 */
$minSize = (Settings::get('media_thumb_size') !='') ? Settings::get('media_thumb_size') : 120;
?>

<div id="imgouter" class="imgouter">

	<div id="cropframe<?= $id_media ?>" class="cropframe" style="background-image: url('<?= base_url().$path ?>?t=<?= $UNIQ ?>')">
		<div id="draghandle<?= $id_media ?>" class="draghandle"></div>
		<div id="resizeHandleXY<?= $id_media ?>" class="resizeHandle resizeHandleXY"></div>
		<div id="cropinfo<?= $id_media ?>" class="cropinfo" rel="Click to crop">
			<div title="Click to crop" id="cropbtn<?= $id_media ?>" class="cropbtn"></div>
			<div id="cropdims<?= $id_media ?>" class="cropdims"></div>
		</div>
	</div>
	
	<div id="imglayer<?= $id_media ?>" class="imglayer" style="width: <?= $size['width'] ?>px; height: <?= $size['height'] ?>px; padding: 1px; background-position: center center; background-image: url('<?= base_url().$path ?>?t=<?= $UNIQ ?>')"></div>
</div>

<script type="text/javascript">

var ch<?= $id_media ?>;
window.addEvent("domready", function()
{
	ch<?= $id_media ?> = new CwCrop(
	{
		cropframe: 'cropframe<?= $id_media ?>',
		imgframe: 'imglayer<?= $id_media ?>',
		cropdims: 'cropdims<?= $id_media ?>',
		cropbtn: 'cropbtn<?= $id_media ?>',
		draghandle: 'draghandle<?= $id_media ?>',
		resizehandle: 'resizeHandleXY<?= $id_media ?>',
		
		initialposition: {x: 0, y: 0},
		minsize: {x: <?= $minSize ?>, y: <?= $minSize ?>},
		maxratio: {x: 10, y: 10},
		maxsize: {x: <?= $size['width'] ?>, y:<?= $size['height'] ?>},
		onCrop: function(values)
		{
			ION.JSON('media/crop', {'path':'<?= $path ?>', 'coords': values, 'id_media': '<?= $id_media ?>'});
		}
	});
});



</script>
