<?php if(!Authority::can('create', 'admin/article')) :?>

<h2 class="main protected"><?php echo lang('ionize_title_resource_protected')?></h2>
    <p><?php echo lang("ionize_subtitle_resource_protected");?></p>

<?php endif;?>

<?php if(Authority::can('create', 'admin/article')) :?>
<div id="maincolumn">

	<h2 class="main articles" id="main-title"><?php echo lang('ionize_title_articles'); ?></h2>

	<!-- Filter -->
	<div class="form-bloc">
		<form name="articleFilter" id="articleFilter" method="post">

			<label class="over"><?php echo lang('ionize_label_menu'); ?>
				<?php echo $menus; ?>
			</label>

			<label class="over"><?php echo lang('ionize_label_page'); ?>
				<span id="parentSelectContainer" class="ml10"></span>
			</label>

			<label class="over">
				<?php echo lang('ionize_label_title') ?>
				<input alt="<?php echo lang('ionize_label_title') ?>" type="text" class="inputtext w120" name="title" value="" />
			</label>

			<label class="over">
				<?php echo lang('ionize_label_content') ?>
				<input type="text" class="inputtext w120" name="content" value="" />
			</label>

			<label class="over">
				<?php echo lang('ionize_label_nb_per_page') ?>
				<input type="text" class="inputtext w30" name="nb" value="<?php echo $nb ?>" />
			</label>

			<a id="btnSubmitFilter" class="button green"><?php echo lang('ionize_button_filter') ?></a>

		</form>
	</div>


	<!-- Articles List -->
	<div id="articleList"></div>

</div>




<script type="text/javascript">

	// Panel toolbox
	ION.initToolbox('articles_toolbox');

	// Filter
	$('btnSubmitFilter').addEvent('click', function(e)
	{
		ION.HTML('article/get_articles_list', $('articleFilter'), {'update':$('articleList')});
	});

	$('id_menu').addEvent('change', function()
	{
		ION.HTML(
			ION.adminUrl + 'page/get_parents_select',
			{
				'id_menu' : $('id_menu').value,
				'id_current': 0,
				'id_parent': 0,
				'check_add_page' : true
			},
			{
				'update': 'parentSelectContainer'
			}
		);
	});
	$('id_menu').fireEvent('change');

	$('btnSubmitFilter').fireEvent('click');

</script>
<?php endif;?>



