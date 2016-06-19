<?php

/**
 * Language helper
 *
 */

function lang($line)
{
	GLOBAL $installer;
	
	return $installer->get_translation($line);
}


