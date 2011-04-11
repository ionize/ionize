<?php

/*
 * Date format, translated
 * To use with the a date field through tags
 * This format uses days and months translation like defined in this file, if the PHP date format code is used.
 *
 *
 * Example of usage : 
 * 		$lang['dateformat_short'] = 'd m Y';	View call : <ion:date format="short" />		Output : 15 01 2011
 * 		$lang['dateformat_medium'] = 'd M Y'; 	View call : <ion:date format="medium" />	Output : 15 jan 2011
 * 		$lang['dateformat_long'] = 'l d F Y';	View call : <ion:date format="long" />		Output : samedi 15 janvier 2011
 *
 * You can create your own format and call it in views :
 *		$lang['dateformat_complete'] = 'l d F Y H:i:s';		View call : <ion:date format="complete" />		Output : samedi 15 janvier 2011 20:54:21

 *
 */
$lang['dateformat_short'] = 'd m Y';
$lang['dateformat_medium'] = 'd M Y';
$lang['dateformat_long'] = 'd F Y';

$lang['dateformat_complete'] = 'l d F Y à H\hi';


/* 
 * PHP day date format 'D' translations
 * Lowercase. For other day date format (Ucase, etc.) use the "manip" tag attribute
 * Ex : <articles:date format="D" manip="ucase" />
 */
$lang['mon'] = 'lun';
$lang['tue'] = 'mar';
$lang['wed'] = 'mer';
$lang['thu'] = 'jeu';
$lang['fri'] = 'ven';
$lang['sat'] = 'sam';
$lang['sun'] = 'dim';

/* 
 * PHP day date format 'l' translations
 */
$lang['monday'] = 'lundi';
$lang['tuesday'] = 'mardi';
$lang['wednesday'] = 'mercredi';
$lang['thursday'] = 'jeudi';
$lang['friday'] = 'vendredi';
$lang['saturday'] = 'samedi';
$lang['sunday'] = 'dimanche';

/* 
 * PHP month date format 'M' translations
 */
$lang['jan'] = 'jan';
$lang['feb'] = 'fév';
$lang['mar'] = 'mar';
$lang['apr'] = 'avr';
$lang['may'] = 'mai';
$lang['jun'] = 'jun';
$lang['jul'] = 'jul';
$lang['aug'] = 'aoû';
$lang['sep'] = 'sep';
$lang['oct'] = 'oct';
$lang['nov'] = 'nov';
$lang['dec'] = 'déc';

/* 
 * PHP month date format 'F' translations
 */
$lang['january'] = 'janvier';
$lang['february'] = 'février';
$lang['march'] = 'mars';
$lang['april'] = 'avril';
$lang['may'] = 'mai';
$lang['june'] = 'juin';
$lang['july'] = 'juillet';
$lang['august'] = 'août';
$lang['september'] = 'septembre';
$lang['october'] = 'octobre';
$lang['november'] = 'novembre';
$lang['december'] = 'décembre';


?>