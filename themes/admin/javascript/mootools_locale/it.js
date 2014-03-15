/*
 ---

 name: Locale.it-IT.Date

 description: Date messages for Italian.

 license: MIT-style license.

 authors:
 - Andrea Novero
 - Valerio Proietti

 requires:
 - /Locale

 provides: [Locale.it-IT.Date]

 ...
 */

Locale.define('it', 'Date', {

	months: ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'],
	months_abbr: ['gen', 'feb', 'mar', 'apr', 'mag', 'giu', 'lug', 'ago', 'set', 'ott', 'nov', 'dic'],
	days: ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'],
	days_abbr: ['dom', 'lun', 'mar', 'mer', 'gio', 'ven', 'sab'],

	// Culture's date order: DD/MM/YYYY
	dateOrder: ['date', 'month', 'year'],
	shortDate: '%d/%m/%Y',
	shortTime: '%H.%M',
	AM: 'AM',
	PM: 'PM',
	firstDayOfWeek: 1,

	// Date.Extras
	ordinal: 'º',

	lessThanMinuteAgo: 'meno di un minuto fa',
	minuteAgo: 'circa un minuto fa',
	minutesAgo: 'circa {delta} minuti fa',
	hourAgo: "circa un'ora fa",
	hoursAgo: 'circa {delta} ore fa',
	dayAgo: 'circa 1 giorno fa',
	daysAgo: 'circa {delta} giorni fa',
	weekAgo: 'una settimana fa',
	weeksAgo: '{delta} settimane fa',
	monthAgo: 'un mese fa',
	monthsAgo: '{delta} mesi fa',
	yearAgo: 'un anno fa',
	yearsAgo: '{delta} anni fa',

	lessThanMinuteUntil: 'tra meno di un minuto',
	minuteUntil: 'tra circa un minuto',
	minutesUntil: 'tra circa {delta} minuti',
	hourUntil: "tra circa un'ora",
	hoursUntil: 'tra circa {delta} ore',
	dayUntil: 'tra circa un giorno',
	daysUntil: 'tra circa {delta} giorni',
	weekUntil: 'tra una settimana',
	weeksUntil: 'tra {delta} settimane',
	monthUntil: 'tra un mese',
	monthsUntil: 'tra {delta} mesi',
	yearUntil: 'tra un anno',
	yearsUntil: 'tra {delta} anni'

});


/*
 ---

 name: Locale.it-IT.Form.Validator

 description: Form Validator messages for Italian.

 license: MIT-style license

 authors:
 - Leonardo Laureti
 - Andrea Novero

 requires:
 - /Locale

 provides: [Locale.it-IT.Form.Validator]

 ...
 */

Locale.define('it', 'FormValidator', {

	required: 'Il campo &egrave; obbligatorio.',
	minLength: 'Inserire almeno {minLength} caratteri (ne sono stati inseriti {length}).',
	maxLength: 'Inserire al massimo {maxLength} caratteri (ne sono stati inseriti {length}).',
	integer: 'Inserire un numero intero. Non sono consentiti decimali (es.: 1.25).',
	numeric: 'Inserire solo valori numerici (es.: "1" oppure "1.1" oppure "-1" oppure "-1.1").',
	digits: 'Inserire solo numeri e caratteri di punteggiatura. Per esempio &egrave; consentito un numero telefonico con trattini o punti.',
	alpha: 'Inserire solo lettere (a-z). Non sono consentiti spazi o altri caratteri.',
	alphanum: 'Inserire solo lettere (a-z) o numeri (0-9). Non sono consentiti spazi o altri caratteri.',
	dateSuchAs: 'Inserire una data valida del tipo {date}',
	dateInFormatMDY: 'Inserire una data valida nel formato MM/GG/AAAA (es.: "12/31/1999")',
	email: 'Inserire un indirizzo email valido. Per esempio "nome@dominio.com".',
	url: 'Inserire un indirizzo valido. Per esempio "http://www.example.com".',
	currencyDollar: 'Inserire un importo valido. Per esempio "$100.00".',
	oneRequired: 'Completare almeno uno dei campi richiesti.',
	errorPrefix: 'Errore: ',
	warningPrefix: 'Attenzione: ',

	// Form.Validator.Extras
	noSpace: 'Non sono consentiti spazi.',
	reqChkByNode: 'Nessuna voce selezionata.',
	requiredChk: 'Il campo &egrave; obbligatorio.',
	reqChkByName: 'Selezionare un(a) {label}.',
	match: 'Il valore deve corrispondere al campo {matchName}',
	startDate: "data d'inizio",
	endDate: 'data di fine',
	currendDate: 'data attuale',
	afterDate: 'La data deve corrispondere o essere successiva al {label}.',
	beforeDate: 'La data deve corrispondere o essere precedente al {label}.',
	startMonth: "Selezionare un mese d'inizio",
	sameMonth: 'Le due date devono essere dello stesso mese - occorre modificarne una.'

});