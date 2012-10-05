<?php
    log_message('error', 'View File Loaded : element_edit.php');
?>
	
<!-- Existing elements -->
<h2 class="main elements"><?php echo lang('ionize_title_edit_content_element'); ?></h2>


<ul id="elementEditContainer" class="mt20"></ul>
	


<script type="text/javascript">


	ION.HTML('element_definition/get_element_list', {'parent':'<?php echo $parent; ?>', 'id_parent': '<?php echo $id_parent; ?>'}, {'update': 'elementEditContainer' });
	
	
</script>
