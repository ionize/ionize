
<h3><?php echo($title) ?></h3>

<?php foreach($posts as $post) :?>

	<h4><a href="<?= $post['link'] ?>"><?= $post['title'] ?></a></h4>
	<p><?= $post['description'] ?></p>
	
<?php endforeach ;?>