/*
 ---

 name: Locale.et-EE.Date

 description: Date messages for Estonian.

 license: MIT-style license

 authors:
 - Kevin Valdek

 requires:
 - /Locale

 provides: [Locale.et-EE.Date]

 ...
 */

Locale.define('et', 'Date', {

	months: ['jaanuar', 'veebruar', 'märts', 'aprill', 'mai', 'juuni', 'juuli', 'august', 'september', 'oktoober', 'november', 'detsember'],
	months_abbr: ['jaan', 'veebr', 'märts', 'apr', 'mai', 'juuni', 'juuli', 'aug', 'sept', 'okt', 'nov', 'dets'],
	days: ['pühapäev', 'esmaspäev', 'teisipäev', 'kolmapäev', 'neljapäev', 'reede', 'laupäev'],
	days_abbr: ['pühap', 'esmasp', 'teisip', 'kolmap', 'neljap', 'reede', 'laup'],

	// Culture's date order: MM.DD.YYYY
	dateOrder: ['month', 'date', 'year'],
	shortDate: '%m.%d.%Y',
	shortTime: '%H:%M',
	AM: 'AM',
	PM: 'PM',
	firstDayOfWeek: 1,

	// Date.Extras
	ordinal: '',

	lessThanMinuteAgo: 'vähem kui minut aega tagasi',
	minuteAgo: 'umbes minut aega tagasi',
	minutesAgo: '{delta} minutit tagasi',
	hourAgo: 'umbes tund aega tagasi',
	hoursAgo: 'umbes {delta} tundi tagasi',
	dayAgo: '1 päev tagasi',
	daysAgo: '{delta} päeva tagasi',
	weekAgo: '1 nädal tagasi',
	weeksAgo: '{delta} nädalat tagasi',
	monthAgo: '1 kuu tagasi',
	monthsAgo: '{delta} kuud tagasi',
	yearAgo: '1 aasta tagasi',
	yearsAgo: '{delta} aastat tagasi',

	lessThanMinuteUntil: 'vähem kui minuti aja pärast',
	minuteUntil: 'umbes minuti aja pärast',
	minutesUntil: '{delta} minuti pärast',
	hourUntil: 'umbes tunni aja pärast',
	hoursUntil: 'umbes {delta} tunni pärast',
	dayUntil: '1 päeva pärast',
	daysUntil: '{delta} päeva pärast',
	weekUntil: '1 nädala pärast',
	weeksUntil: '{delta} nädala pärast',
	monthUntil: '1 kuu pärast',
	monthsUntil: '{delta} kuu pärast',
	yearUntil: '1 aasta pärast',
	yearsUntil: '{delta} aasta pärast'

});


/*
 ---

 name: Locale.et-EE.Form.Validator

 description: Form Validator messages for Estonian.

 license: MIT-style license

 authors:
 - Kevin Valdek

 requires:
 - /Locale

 provides: [Locale.et-EE.Form.Validator]

 ...
 */

Locale.define('et', 'FormValidator', {

	required: 'Väli peab olema täidetud.',
	minLength: 'Palun sisestage vähemalt {minLength} tähte (te sisestasite {length} tähte).',
	maxLength: 'Palun ärge sisestage rohkem kui {maxLength} tähte (te sisestasite {length} tähte).',
	integer: 'Palun sisestage väljale täisarv. Kümnendarvud (näiteks 1.25) ei ole lubatud.',
	numeric: 'Palun sisestage ainult numbreid väljale (näiteks "1", "1.1", "-1" või "-1.1").',
	digits: 'Palun kasutage ainult numbreid ja kirjavahemärke (telefoninumbri sisestamisel on lubatud kasutada kriipse ja punkte).',
	alpha: 'Palun kasutage ainult tähti (a-z). Tühikud ja teised sümbolid on keelatud.',
	alphanum: 'Palun kasutage ainult tähti (a-z) või numbreid (0-9). Tühikud ja teised sümbolid on keelatud.',
	dateSuchAs: 'Palun sisestage kehtiv kuupäev kujul {date}',
	dateInFormatMDY: 'Palun sisestage kehtiv kuupäev kujul MM.DD.YYYY (näiteks: "12.31.1999").',
	email: 'Palun sisestage kehtiv e-maili aadress (näiteks: "fred@domain.com").',
	url: 'Palun sisestage kehtiv URL (näiteks: http://www.example.com).',
	currencyDollar: 'Palun sisestage kehtiv $ summa (näiteks: $100.00).',
	oneRequired: 'Palun sisestage midagi vähemalt ühele antud väljadest.',
	errorPrefix: 'Viga: ',
	warningPrefix: 'Hoiatus: ',

	// Form.Validator.Extras
	noSpace: 'Väli ei tohi sisaldada tühikuid.',
	reqChkByNode: 'Ükski väljadest pole valitud.',
	requiredChk: 'Välja täitmine on vajalik.',
	reqChkByName: 'Palun valige üks {label}.',
	match: 'Väli peab sobima {matchName} väljaga',
	startDate: 'algkuupäev',
	endDate: 'lõppkuupäev',
	currendDate: 'praegune kuupäev',
	afterDate: 'Kuupäev peab olema võrdne või pärast {label}.',
	beforeDate: 'Kuupäev peab olema võrdne või enne {label}.',
	startMonth: 'Palun valige algkuupäev.',
	sameMonth: 'Antud kaks kuupäeva peavad olema samas kuus - peate muutma ühte kuupäeva.'

});
