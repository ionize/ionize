
<h2><?php echo($title) ?></h2>

<?php foreach($posts as $post) :?>

	<h3><a href="<?= $post['link'] ?>"><?= $post['title'] ?></a></h3>
	<p><?= $post['description'] ?></p>
	
<?php endforeach ;?>