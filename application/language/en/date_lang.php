<?php
/*
|--------------------------------------------------------------------------
| Ionize Date Language file
|
| To use with the a date field through tags
| This format uses days and months translation like defined in this file, if the PHP date format code is used.
|
| Example of usage :
|  		$lang['dateformat_short'] = 'd m Y';	View call : <ion:date format="short" />		Output : 15 01 2011
| 		$lang['dateformat_medium'] = 'd M Y'; 	View call : <ion:date format="medium" />	Output : 15 jan 2011
|  		$lang['dateformat_long'] = 'l F d Y';	View call : <ion:date format="long" />		Output : saturday january 15 2011
|
| You can create your own format and call it in views :
| 		$lang['dateformat_complete'] = 'l F d Y H:i:s';		View call : <ion:date format="complete" />		Output : saturday january 15 2011 20:54:21
|
|--------------------------------------------------------------------------
*/

$lang['dateformat_short'] = 'm d Y';
$lang['dateformat_medium'] = 'M d Y';
$lang['dateformat_long'] = 'F d Y';

$lang['dateformat_complete'] = 'l d F Y \a\t H\hi';

/*
|--------------------------------------------------------------------------
| PHP day date format 'D' translations
| Lowercase. For other day date format (Ucase, etc.) use the "manip" tag attribute
| Ex : <articles:date format="D" manip="ucase" />
|--------------------------------------------------------------------------
*/
$lang['mon'] = 'mon';
$lang['tue'] = 'tue';
$lang['wed'] = 'wed';
$lang['thu'] = 'thu';
$lang['fri'] = 'fri';
$lang['sat'] = 'sat';
$lang['sun'] = 'sun';

/*
|--------------------------------------------------------------------------
| PHP day date format 'l' translations
|--------------------------------------------------------------------------
*/
$lang['monday'] = 'monday';
$lang['tuesday'] = 'tuesday';
$lang['wednesday'] = 'wednesday';
$lang['thursday'] = 'thursday';
$lang['friday'] = 'friday';
$lang['saturday'] = 'saturday';
$lang['sunday'] = 'sunday';

/*
|--------------------------------------------------------------------------
| PHP month date format 'M' translations
|--------------------------------------------------------------------------
*/
$lang['jan'] = 'jan';
$lang['feb'] = 'feb';
$lang['mar'] = 'mar';
$lang['apr'] = 'apr';
$lang['may'] = 'may';
$lang['jun'] = 'jun';
$lang['jul'] = 'jul';
$lang['aug'] = 'aug';
$lang['sep'] = 'sep';
$lang['oct'] = 'oct';
$lang['nov'] = 'nov';
$lang['dec'] = 'dec';

/*
|--------------------------------------------------------------------------
| PHP month date format 'F' translations
|--------------------------------------------------------------------------
*/
$lang['january'] = 'january';
$lang['february'] = 'february';
$lang['march'] = 'march';
$lang['april'] = 'april';
$lang['may'] = 'may';
$lang['june'] = 'june';
$lang['july'] = 'july';
$lang['august'] = 'august';
$lang['september'] = 'september';
$lang['october'] = 'october';
$lang['november'] = 'november';
$lang['december'] = 'december';

