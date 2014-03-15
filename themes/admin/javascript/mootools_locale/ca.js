/*
 ---

 name: Locale.ca-CA.Date

 description: Date messages for Catalan.

 license: MIT-style license

 authors:
 - Ãlfons Sanchez

 requires:
 - /Locale

 provides: [Locale.ca-CA.Date]

 ...
 */

Locale.define('ca', 'Date', {

	months: ['Gener', 'Febrer', 'Març', 'Abril', 'Maig', 'Juny', 'Juli', 'Agost', 'Setembre', 'Octubre', 'Novembre', 'Desembre'],
	months_abbr: ['gen.', 'febr.', 'març', 'abr.', 'maig', 'juny', 'jul.', 'ag.', 'set.', 'oct.', 'nov.', 'des.'],
	days: ['Diumenge', 'Dilluns', 'Dimarts', 'Dimecres', 'Dijous', 'Divendres', 'Dissabte'],
	days_abbr: ['dg', 'dl', 'dt', 'dc', 'dj', 'dv', 'ds'],

	// Culture's date order: DD/MM/YYYY
	dateOrder: ['date', 'month', 'year'],
	shortDate: '%d/%m/%Y',
	shortTime: '%H:%M',
	AM: 'AM',
	PM: 'PM',
	firstDayOfWeek: 0,

	// Date.Extras
	ordinal: '',

	lessThanMinuteAgo: 'fa menys d`un minut',
	minuteAgo: 'fa un minut',
	minutesAgo: 'fa {delta} minuts',
	hourAgo: 'fa un hora',
	hoursAgo: 'fa unes {delta} hores',
	dayAgo: 'fa un dia',
	daysAgo: 'fa {delta} dies',

	lessThanMinuteUntil: 'menys d`un minut des d`ara',
	minuteUntil: 'un minut des d`ara',
	minutesUntil: '{delta} minuts des d`ara',
	hourUntil: 'un hora des d`ara',
	hoursUntil: 'unes {delta} hores des d`ara',
	dayUntil: '1 dia des d`ara',
	daysUntil: '{delta} dies des d`ara'

});


/*
 ---

 name: Locale.ca-CA.Form.Validator

 description: Form Validator messages for Catalan.

 license: MIT-style license

 authors:
 - Miquel Hudin
 - Ãlfons Sanchez

 requires:
 - /Locale

 provides: [Locale.ca-CA.Form.Validator]

 ...
 */

Locale.define('ca', 'FormValidator', {

	required: 'Aquest camp es obligatori.',
	minLength: 'Per favor introdueix al menys {minLength} caracters (has introduit {length} caracters).',
	maxLength: 'Per favor introdueix no mes de {maxLength} caracters (has introduit {length} caracters).',
	integer: 'Per favor introdueix un nombre enter en aquest camp. Nombres amb decimals (p.e. 1,25) no estan permesos.',
	numeric: 'Per favor introdueix sols valors numerics en aquest camp (p.e. "1" o "1,1" o "-1" o "-1,1").',
	digits: 'Per favor usa sols numeros i puntuacio en aquest camp (per exemple, un nombre de telefon amb guions i punts no esta permes).',
	alpha: 'Per favor utilitza lletres nomes (a-z) en aquest camp. No s´admiteixen espais ni altres caracters.',
	alphanum: 'Per favor, utilitza nomes lletres (a-z) o numeros (0-9) en aquest camp. No s´admiteixen espais ni altres caracters.',
	dateSuchAs: 'Per favor introdueix una data valida com {date}',
	dateInFormatMDY: 'Per favor introdueix una data valida com DD/MM/YYYY (p.e. "31/12/1999")',
	email: 'Per favor, introdueix una adreça de correu electronic valida. Per exemple, "fred@domain.com".',
	url: 'Per favor introdueix una URL valida com http://www.example.com.',
	currencyDollar: 'Per favor introdueix una quantitat valida de €. Per exemple €100,00 .',
	oneRequired: 'Per favor introdueix alguna cosa per al menys una d´aquestes entrades.',
	errorPrefix: 'Error: ',
	warningPrefix: 'Avis: ',

	// Form.Validator.Extras
	noSpace: 'No poden haver espais en aquesta entrada.',
	reqChkByNode: 'No hi han elements seleccionats.',
	requiredChk: 'Aquest camp es obligatori.',
	reqChkByName: 'Per favor selecciona una {label}.',
	match: 'Aquest camp necessita coincidir amb el camp {matchName}',
	startDate: 'la data de inici',
	endDate: 'la data de fi',
	currendDate: 'la data actual',
	afterDate: 'La data deu ser igual o posterior a {label}.',
	beforeDate: 'La data deu ser igual o anterior a {label}.',
	startMonth: 'Per favor selecciona un mes d´orige',
	sameMonth: 'Aquestes dos dates deuen estar dins del mateix mes - deus canviar una o altra.'

});
