
	
<!-- Existing elements -->
<h2 class="main elements"><?= lang('ionize_title_edit_content_element') ?></h2>


<ul id="elementEditContainer" class="mt20"></ul>
	


<script type="text/javascript">


	ION.HTML('element_definition/get_element_list', {'parent':'<?= $parent ?>', 'id_parent': '<?= $id_parent ?>'}, {'update': 'elementEditContainer' });
	
	
</script>
