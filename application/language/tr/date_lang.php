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
 * 		$lang['dateformat_long'] = 'l F d Y';	View call : <ion:date format="long" />		Output : saturday january 15 2011
 *
 * You can create your own format and call it in views :
 *		$lang['dateformat_complete'] = 'l F d Y H:i:s';		View call : <ion:date format="complete" />		Output : saturday january 15 2011 20:54:21

 *
 */
$lang['dateformat_short'] = 'm d Y';
$lang['dateformat_medium'] = 'M d Y';
$lang['dateformat_long'] = 'F d Y';

$lang['dateformat_complete'] = 'l d F Y \a\t H\hi';

/* 
 * PHP day date format 'D' translations
 * Lowercase. For other day date format (Ucase, etc.) use the "manip" tag attribute
 * Ex : <articles:date format="D" manip="ucase" />
 */

$lang['mon'] = 'pt';
$lang['tue'] = 'sa';
$lang['wed'] = 'ça';
$lang['thu'] = 'pe';
$lang['fri'] = 'cu';
$lang['sat'] = 'ct';
$lang['sun'] = 'pz';

/* 
 * PHP day date format 'l' tranlations
 */
$lang['monday'] = 'pazartesi';
$lang['tuesday'] = 'salı';
$lang['wednesday'] = 'çarşamba';
$lang['thursday'] = 'perşembe';
$lang['friday'] = 'cuma';
$lang['saturday'] = 'cumartesi';
$lang['sunday'] = 'pazar';

/* 
 * PHP month date format 'M' tranlations
 */
$lang['jan'] = 'oca';
$lang['feb'] = 'şub';
$lang['mar'] = 'mar';
$lang['apr'] = 'nis';
$lang['may'] = 'may';
$lang['jun'] = 'haz';
$lang['jul'] = 'tem';
$lang['aug'] = 'ağu';
$lang['sep'] = 'eyl';
$lang['oct'] = 'eki';
$lang['nov'] = 'kas';
$lang['dec'] = 'arl';

/* 
 * PHP month date format 'F' tranlations
 */
$lang['january'] = 'ocak';
$lang['februar'] = 'şubat';
$lang['march'] = 'mart';
$lang['april'] = 'nisan';
$lang['may'] = 'mayıs';
$lang['june'] = 'haziran';
$lang['july'] = 'temmuz';
$lang['august'] = 'ağustos';
$lang['september'] = 'eylül';
$lang['october'] = 'ekim';
$lang['november'] = 'kasım';
$lang['december'] = 'aralık';


?>