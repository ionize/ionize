<?php

/**
 * Modal window for Picture Crop
 *
 */
$minSize = (Settings::get('media_thumb_size') !='') ? Settings::get('media_thumb_size') : 120;
?>

<div id="imgCropContainer<?php echo $id_media; ?>">
	<div id="imgouter" class="imgouter">

		<div id="cropframe<?php echo $id_media; ?>" class="cropframe" style="background-image: url('<?php echo base_url().$path; ?>?t=<?php echo $UNIQ; ?>')">
			<div id="draghandle<?php echo $id_media; ?>" class="draghandle"></div>
			<div id="resizeHandleXY<?php echo $id_media; ?>" class="resizeHandle resizeHandleXY"></div>
			<div id="cropinfo<?php echo $id_media; ?>" class="cropinfo">
				<div title="Click to crop" id="cropbtn<?php echo $id_media; ?>" class="cropbtn"></div>
				<div id="cropdims<?php echo $id_media; ?>" class="cropdims"></div>
			</div>
		</div>
	
		<div id="imglayer<?php echo $id_media; ?>" class="imglayer" style="width: <?php echo $size['width']; ?>px; height: <?php echo $size['height']; ?>px; padding: 1px; background-position: center center; background-image: url('<?php echo base_url().$path; ?>?t=<?php echo $UNIQ; ?>')"></div>
	</div>
</div>

<script type="text/javascript">

var ch<?php echo $id_media; ?>;
window.addEvent("domready", function()
{
	ch<?php echo $id_media; ?> = new CwCrop(
	{
		cropframe: 'cropframe<?php echo $id_media; ?>',
		imgframe: 'imglayer<?php echo $id_media; ?>',
		cropdims: 'cropdims<?php echo $id_media; ?>',
		cropbtn: 'cropbtn<?php echo $id_media; ?>',
		draghandle: 'draghandle<?php echo $id_media; ?>',
		resizehandle: 'resizeHandleXY<?php echo $id_media; ?>',
		
		initialposition: {x: 0, y: 0},
		minsize: {x: <?php echo $minSize; ?>, y: <?php echo $minSize; ?>},
		maxratio: {x: 10, y: 10},
		maxsize: {x: <?php echo $size['width']; ?>, y:<?php echo $size['height']; ?>},
		onCrop: function(values)
		{
			ION.JSON('media/crop', {'path':'<?php echo $path; ?>', 'coords': values, 'id_media': '<?php echo $id_media; ?>'});
		}
	});
	var size = $('imgCropContainer<?php echo $id_media; ?>').getParent('div.mochaContentWrapper').getSize();
	if (Browser.ie7)
	{
		$('imgCropContainer<?php echo $id_media; ?>').getParent('div.mochaContentWrapper').setStyles({
			'position': 'relative'
		});
	}
});



</script>
