/*
 ---

 name: Locale.si-SI.Date

 description: Date messages for Slovenian.

 license: MIT-style license

 authors:
 - Radovan Lozej

 requires:
 - /Locale

 provides: [Locale.si-SI.Date]

 ...
 */

(function(){

	var pluralize = function(n, one, two, three, other){
		return (n >= 1 && n <= 3) ? arguments[n] : other;
	};

	Locale.define('si', 'Date', {

		months: ['januar', 'februar', 'marec', 'april', 'maj', 'junij', 'julij', 'avgust', 'september', 'oktober', 'november', 'december'],
		months_abbr: ['jan', 'feb', 'mar', 'apr', 'maj', 'jun', 'jul', 'avg', 'sep', 'okt', 'nov', 'dec'],
		days: ['nedelja', 'ponedeljek', 'torek', 'sreda', 'četrtek', 'petek', 'sobota'],
		days_abbr: ['ned', 'pon', 'tor', 'sre', 'čet', 'pet', 'sob'],

		// Culture's date order: DD.MM.YYYY
		dateOrder: ['date', 'month', 'year'],
		shortDate: '%d.%m.%Y',
		shortTime: '%H.%M',
		AM: 'AM',
		PM: 'PM',
		firstDayOfWeek: 1,

		// Date.Extras
		ordinal: '.',

		lessThanMinuteAgo: 'manj kot minuto nazaj',
		minuteAgo: 'minuto nazaj',
		minutesAgo: function(delta){ return '{delta} ' + pluralize(delta, 'minuto', 'minuti', 'minute', 'minut') + ' nazaj'; },
		hourAgo: 'uro nazaj',
		hoursAgo: function(delta){ return '{delta} ' + pluralize(delta, 'uro', 'uri', 'ure', 'ur') + ' nazaj'; },
		dayAgo: 'dan nazaj',
		daysAgo: function(delta){ return '{delta} ' + pluralize(delta, 'dan', 'dneva', 'dni', 'dni') + ' nazaj'; },
		weekAgo: 'teden nazaj',
		weeksAgo: function(delta){ return '{delta} ' + pluralize(delta, 'teden', 'tedna', 'tedne', 'tednov') + ' nazaj'; },
		monthAgo: 'mesec nazaj',
		monthsAgo: function(delta){ return '{delta} ' + pluralize(delta, 'mesec', 'meseca', 'mesece', 'mesecov') + ' nazaj'; },
		yearthAgo: 'leto nazaj',
		yearsAgo: function(delta){ return '{delta} ' + pluralize(delta, 'leto', 'leti', 'leta', 'let') + ' nazaj'; },

		lessThanMinuteUntil: 'še manj kot minuto',
		minuteUntil: 'še minuta',
		minutesUntil: function(delta){ return 'še {delta} ' + pluralize(delta, 'minuta', 'minuti', 'minute', 'minut'); },
		hourUntil: 'še ura',
		hoursUntil: function(delta){ return 'še {delta} ' + pluralize(delta, 'ura', 'uri', 'ure', 'ur'); },
		dayUntil: 'še dan',
		daysUntil: function(delta){ return 'še {delta} ' + pluralize(delta, 'dan', 'dneva', 'dnevi', 'dni'); },
		weekUntil: 'še tedn',
		weeksUntil: function(delta){ return 'še {delta} ' + pluralize(delta, 'teden', 'tedna', 'tedni', 'tednov'); },
		monthUntil: 'še mesec',
		monthsUntil: function(delta){ return 'še {delta} ' + pluralize(delta, 'mesec', 'meseca', 'meseci', 'mesecov'); },
		yearUntil: 'še leto',
		yearsUntil: function(delta){ return 'še {delta} ' + pluralize(delta, 'leto', 'leti', 'leta', 'let'); }

	});

})();


/*
 ---

 name: Locale.si-SI.Form.Validator

 description: Form Validator messages for Slovenian.

 license: MIT-style license

 authors:
 - Radovan Lozej

 requires:
 - /Locale

 provides: [Locale.si-SI.Form.Validator]

 ...
 */

Locale.define('si', 'FormValidator', {

	required: 'To polje je obvezno',
	minLength: 'Prosim, vnesite vsaj {minLength} znakov (vnesli ste {length} znakov).',
	maxLength: 'Prosim, ne vnesite več kot {maxLength} znakov (vnesli ste {length} znakov).',
	integer: 'Prosim, vnesite celo število. Decimalna števila (kot 1,25) niso dovoljena.',
	numeric: 'Prosim, vnesite samo numerične vrednosti (kot "1" ali "1.1" ali "-1" ali "-1.1").',
	digits: 'Prosim, uporabite številke in ločila le na tem polju (na primer, dovoljena je telefonska številka z pomišlaji ali pikami).',
	alpha: 'Prosim, uporabite le črke v tem plju. Presledki in drugi znaki niso dovoljeni.',
	alphanum: 'Prosim, uporabite samo črke ali številke v tem polju. Presledki in drugi znaki niso dovoljeni.',
	dateSuchAs: 'Prosim, vnesite pravilen datum kot {date}',
	dateInFormatMDY: 'Prosim, vnesite pravilen datum kot MM.DD.YYYY (primer "12.31.1999")',
	email: 'Prosim, vnesite pravilen email naslov. Na primer "fred@domain.com".',
	url: 'Prosim, vnesite pravilen URL kot http://www.example.com.',
	currencyDollar: 'Prosim, vnesit epravilno vrednost €. Primer 100,00€ .',
	oneRequired: 'Prosimo, vnesite nekaj za vsaj eno izmed teh polj.',
	errorPrefix: 'Napaka: ',
	warningPrefix: 'Opozorilo: ',

	// Form.Validator.Extras
	noSpace: 'To vnosno polje ne dopušča presledkov.',
	reqChkByNode: 'Nič niste izbrali.',
	requiredChk: 'To polje je obvezno',
	reqChkByName: 'Prosim, izberite {label}.',
	match: 'To polje se mora ujemati z poljem {matchName}',
	startDate: 'datum začetka',
	endDate: 'datum konca',
	currendDate: 'trenuten datum',
	afterDate: 'Datum bi moral biti isti ali po {label}.',
	beforeDate: 'Datum bi moral biti isti ali pred {label}.',
	startMonth: 'Prosim, vnesite začetni datum',
	sameMonth: 'Ta dva datuma morata biti v istem mesecu - premeniti morate eno ali drugo.',
	creditcard: 'Številka kreditne kartice ni pravilna. Preverite številko ali poskusite še enkrat. Vnešenih {length} znakov.'

});
