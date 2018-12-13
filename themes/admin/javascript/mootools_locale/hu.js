/*
 ---

 name: Locale.hu-HU.Date

 description: Date messages for Hungarian.

 license: MIT-style license

 authors:
 - Zsolt Szegheő

 requires:
 - /Locale

 provides: [Locale.hu-HU.Date]

 ...
 */

Locale.define('hu', 'Date', {

	months: ['Január', 'Február', 'Március', 'Április', 'Május', 'Június', 'Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December'],
	months_abbr: ['jan.', 'febr.', 'márc.', 'ápr.', 'máj.', 'jún.', 'júl.', 'aug.', 'szept.', 'okt.', 'nov.', 'dec.'],
	days: ['Vasárnap', 'Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat'],
	days_abbr: ['V', 'H', 'K', 'Sze', 'Cs', 'P', 'Szo'],

	// Culture's date order: YYYY.MM.DD.
	dateOrder: ['year', 'month', 'date'],
	shortDate: '%Y.%m.%d.',
	shortTime: '%I:%M',
	AM: 'de.',
	PM: 'du.',
	firstDayOfWeek: 1,

	// Date.Extras
	ordinal: '.',

	lessThanMinuteAgo: 'alig egy perce',
	minuteAgo: 'egy perce',
	minutesAgo: '{delta} perce',
	hourAgo: 'egy órája',
	hoursAgo: '{delta} órája',
	dayAgo: '1 napja',
	daysAgo: '{delta} napja',
	weekAgo: '1 hete',
	weeksAgo: '{delta} hete',
	monthAgo: '1 hónapja',
	monthsAgo: '{delta} hónapja',
	yearAgo: '1 éve',
	yearsAgo: '{delta} éve',

	lessThanMinuteUntil: 'alig egy perc múlva',
	minuteUntil: 'egy perc múlva',
	minutesUntil: '{delta} perc múlva',
	hourUntil: 'egy óra múlva',
	hoursUntil: '{delta} óra múlva',
	dayUntil: '1 nap múlva',
	daysUntil: '{delta} nap múlva',
	weekUntil: '1 hét múlva',
	weeksUntil: '{delta} hét múlva',
	monthUntil: '1 hónap múlva',
	monthsUntil: '{delta} hónap múlva',
	yearUntil: '1 év múlva',
	yearsUntil: '{delta} év múlva'

});


/*
 ---

 name: Locale.hu-HU.Form.Validator

 description: Form Validator messages for Hungarian.

 license: MIT-style license

 authors:
 - Zsolt Szegheő

 requires:
 - /Locale

 provides: [Locale.hu-HU.Form.Validator]

 ...
 */

Locale.define('hu', 'FormValidator', {

	required: 'A mező kitöltése kötelező.',
	minLength: 'Legalább {minLength} karakter megadása szükséges (megadva {length} karakter).',
	maxLength: 'Legfeljebb {maxLength} karakter megadása lehetséges (megadva {length} karakter).',
	integer: 'Egész szám megadása szükséges. A tizedesjegyek (pl. 1.25) nem engedélyezettek.',
	numeric: 'Szám megadása szükséges (pl. "1" vagy "1.1" vagy "-1" vagy "-1.1").',
	digits: 'Csak számok és írásjelek megadása lehetséges (pl. telefonszám kötőjelek és/vagy perjelekkel).',
	alpha: 'Csak betűk (a-z) megadása lehetséges. Szóköz és egyéb karakterek nem engedélyezettek.',
	alphanum: 'Csak betűk (a-z) vagy számok (0-9) megadása lehetséges. Szóköz és egyéb karakterek nem engedélyezettek.',
	dateSuchAs: 'Valós dátum megadása szükséges (pl. {date}).',
	dateInFormatMDY: 'Valós dátum megadása szükséges ÉÉÉÉ.HH.NN. formában. (pl. "1999.12.31.")',
	email: 'Valós e-mail cím megadása szükséges (pl. "fred@domain.hu").',
	url: 'Valós URL megadása szükséges (pl. http://www.example.com).',
	currencyDollar: 'Valós pénzösszeg megadása szükséges (pl. 100.00 Ft.).',
	oneRequired: 'Az alábbi mezők legalább egyikének kitöltése kötelező.',
	errorPrefix: 'Hiba: ',
	warningPrefix: 'Figyelem: ',

	// Form.Validator.Extras
	noSpace: 'A mező nem tartalmazhat szóközöket.',
	reqChkByNode: 'Nincs egyetlen kijelölt elem sem.',
	requiredChk: 'A mező kitöltése kötelező.',
	reqChkByName: 'Egy {label} kiválasztása szükséges.',
	match: 'A mezőnek egyeznie kell a(z) {matchName} mezővel.',
	startDate: 'a kezdet dátuma',
	endDate: 'a vég dátuma',
	currendDate: 'jelenlegi dátum',
	afterDate: 'A dátum nem lehet kisebb, mint {label}.',
	beforeDate: 'A dátum nem lehet nagyobb, mint {label}.',
	startMonth: 'Kezdeti hónap megadása szükséges.',
	sameMonth: 'A két dátumnak ugyanazon hónapban kell lennie.',
	creditcard: 'A megadott bankkártyaszám nem valódi (megadva {length} számjegy).'

});
