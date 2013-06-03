<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Portable PHP password hashing framework configuration
|--------------------------------------------------------------------------
|
| 'iteration_count_log2' = The number of iterations, 8 means the password is
|                          hashed 256 times. Use value between 4 and 31.
| 'portable_hashes'      = If bcrypt is not available on the system, phpass will
|                          use PHP_VERSION during the generation if the hash.
|                          Turn off for PHP 5.3 or above.
*/

$config['iteration_count_log2'] = 8;
$config['portable_hashes']      = FALSE;
?>