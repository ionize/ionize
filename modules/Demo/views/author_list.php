<?php
/**
 * Ionize Author Demo Module
 * Frontend Authors List view
 *
 * Receives :
 * $authors :	Array of authors
 */
?>

<ul>
	<?php foreach($authors as $author): ?>
		<li>
			<?php echo $author['name'] ?>
		</li>
	<?php endforeach ;?>
</ul>