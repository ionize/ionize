/*
 ---

 name: Locale.cs-CZ.Date

 description: Date messages for Czech.

 license: MIT-style license

 authors:
 - Jan Černý chemiX
 - Christopher Zukowski

 requires:
 - /Locale

 provides: [Locale.cs-CZ.Date]

 ...
 */
(function(){

// Czech language pluralization rules, see http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html
// one -> n is 1;            1
// few -> n in 2..4;         2-4
// other -> everything else  0, 5-999, 1.31, 2.31, 5.31...
	var pluralize = function (n, one, few, other){
		if (n == 1) return one;
		else if (n == 2 || n == 3 || n == 4) return few;
		else return other;
	};

	Locale.define('cs', 'Date', {

		months: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
		months_abbr: ['ledna', 'února', 'března', 'dubna', 'května', 'června', 'července', 'srpna', 'září', 'října', 'listopadu', 'prosince'],
		days: ['Neděle', 'Pondělí', 'Úterý', 'Středa', 'Čtvrtek', 'Pátek', 'Sobota'],
		days_abbr: ['ne', 'po', 'út', 'st', 'čt', 'pá', 'so'],

		// Culture's date order: DD.MM.YYYY
		dateOrder: ['date', 'month', 'year'],
		shortDate: '%d.%m.%Y',
		shortTime: '%H:%M',
		AM: 'dop.',
		PM: 'odp.',
		firstDayOfWeek: 1,

		// Date.Extras
		ordinal: '.',

		lessThanMinuteAgo: 'před chvílí',
		minuteAgo: 'přibližně před minutou',
		minutesAgo: function(delta){ return 'před {delta} ' + pluralize(delta, 'minutou', 'minutami', 'minutami'); },
		hourAgo: 'přibližně před hodinou',
		hoursAgo: function(delta){ return 'před {delta} ' + pluralize(delta, 'hodinou', 'hodinami', 'hodinami'); },
		dayAgo: 'před dnem',
		daysAgo: function(delta){ return 'před {delta} ' + pluralize(delta, 'dnem', 'dny', 'dny'); },
		weekAgo: 'před týdnem',
		weeksAgo: function(delta){ return 'před {delta} ' + pluralize(delta, 'týdnem', 'týdny', 'týdny'); },
		monthAgo: 'před měsícem',
		monthsAgo: function(delta){ return 'před {delta} ' + pluralize(delta, 'měsícem', 'měsíci', 'měsíci'); },
		yearAgo: 'před rokem',
		yearsAgo: function(delta){ return 'před {delta} ' + pluralize(delta, 'rokem', 'lety', 'lety'); },

		lessThanMinuteUntil: 'za chvíli',
		minuteUntil: 'přibližně za minutu',
		minutesUntil: function(delta){ return 'za {delta} ' + pluralize(delta, 'minutu', 'minuty', 'minut'); },
		hourUntil: 'přibližně za hodinu',
		hoursUntil: function(delta){ return 'za {delta} ' + pluralize(delta, 'hodinu', 'hodiny', 'hodin'); },
		dayUntil: 'za den',
		daysUntil: function(delta){ return 'za {delta} ' + pluralize(delta, 'den', 'dny', 'dnů'); },
		weekUntil: 'za týden',
		weeksUntil: function(delta){ return 'za {delta} ' + pluralize(delta, 'týden', 'týdny', 'týdnů'); },
		monthUntil: 'za měsíc',
		monthsUntil: function(delta){ return 'za {delta} ' + pluralize(delta, 'měsíc', 'měsíce', 'měsíců'); },
		yearUntil: 'za rok',
		yearsUntil: function(delta){ return 'za {delta} ' + pluralize(delta, 'rok', 'roky', 'let'); }
	});

})();


/*
 ---

 name: Locale.cs-CZ.Form.Validator

 description: Form Validator messages for Czech.

 license: MIT-style license

 authors:
 - Jan Černý chemiX

 requires:
 - /Locale

 provides: [Locale.cs-CZ.Form.Validator]

 ...
 */

Locale.define('cs', 'FormValidator', {

	required: 'Tato položka je povinná.',
	minLength: 'Zadejte prosím alespoň {minLength} znaků (napsáno {length} znaků).',
	maxLength: 'Zadejte prosím méně než {maxLength} znaků (nápsáno {length} znaků).',
	integer: 'Zadejte prosím celé číslo. Desetinná čísla (např. 1.25) nejsou povolena.',
	numeric: 'Zadejte jen číselné hodnoty (tj. "1" nebo "1.1" nebo "-1" nebo "-1.1").',
	digits: 'Zadejte prosím pouze čísla a interpunkční znaménka(například telefonní číslo s pomlčkami nebo tečkami je povoleno).',
	alpha: 'Zadejte prosím pouze písmena (a-z). Mezery nebo jiné znaky nejsou povoleny.',
	alphanum: 'Zadejte prosím pouze písmena (a-z) nebo číslice (0-9). Mezery nebo jiné znaky nejsou povoleny.',
	dateSuchAs: 'Zadejte prosím platné datum jako {date}',
	dateInFormatMDY: 'Zadejte prosím platné datum jako MM / DD / RRRR (tj. "12/31/1999")',
	email: 'Zadejte prosím platnou e-mailovou adresu. Například "fred@domain.com".',
	url: 'Zadejte prosím platnou URL adresu jako http://www.example.com.',
	currencyDollar: 'Zadejte prosím platnou částku. Například $100.00.',
	oneRequired: 'Zadejte prosím alespoň jednu hodnotu pro tyto položky.',
	errorPrefix: 'Chyba: ',
	warningPrefix: 'Upozornění: ',

	// Form.Validator.Extras
	noSpace: 'V této položce nejsou povoleny mezery',
	reqChkByNode: 'Nejsou vybrány žádné položky.',
	requiredChk: 'Tato položka je vyžadována.',
	reqChkByName: 'Prosím vyberte {label}.',
	match: 'Tato položka se musí shodovat s položkou {matchName}',
	startDate: 'datum zahájení',
	endDate: 'datum ukončení',
	currendDate: 'aktuální datum',
	afterDate: 'Datum by mělo být stejné nebo větší než {label}.',
	beforeDate: 'Datum by mělo být stejné nebo menší než {label}.',
	startMonth: 'Vyberte počáteční měsíc.',
	sameMonth: 'Tyto dva datumy musí být ve stejném měsíci - změňte jeden z nich.',
	creditcard: 'Zadané číslo kreditní karty je neplatné. Prosím opravte ho. Bylo zadáno {length} čísel.'

});

