<?php
/**
 * Authors linked to one parent
 * Called by : /modules/Demo/controllers/admin/author.php
 *
 * This view receives :
 * - $authors : 	Array of authors linked to the current edited parent
 * - $parent : 		Parent code ('article', 'page')
 * - $id_parent : 	Parent's ID
 *
 */
?>

<?php if ( ! empty($authors)) :?>

	<ul id="demoAuthorsList">

		<?php foreach($authors as $author) :?>

			<li class="sortme">
				<a class="title"><?php echo $author['name'] ;?></a>
				<!-- Unlink icon -->
				<a class="icon unlink right" data-id="<?php echo $author['id_author'] ;?>"></a>
			</li>

		<?php endforeach; ?>

	</ul>

	<script type="text/javascript">

		$$('#demoAuthorsList li').each(function(item)
		{
			var unlinkIcon = item.getElement('.unlink');

			ION.initRequestEvent(
				// Element to add the request on
				unlinkIcon,
				// URL to send the data
				'<?= admin_url() ?>/module/demo/author/unlink',
				// Data send by POST to the URL
				{
					'parent': '<?php echo $parent ?>',
					'id_parent': '<?= $id_parent ?>',
					'id_author': unlinkIcon.getProperty('data-id')
				}
			);
		});

	</script>

<?php endif; ?>