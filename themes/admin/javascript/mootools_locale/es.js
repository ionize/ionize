/*
 ---

 name: Locale.es-ES.Date

 description: Date messages for Spanish.

 license: MIT-style license

 authors:
 - Ãlfons Sanchez

 requires:
 - /Locale

 provides: [Locale.es-ES.Date]

 ...
 */

Locale.define('es', 'Date', {

	months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
	months_abbr: ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'],
	days: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
	days_abbr: ['dom', 'lun', 'mar', 'mié', 'juv', 'vie', 'sáb'],

	// Culture's date order: DD/MM/YYYY
	dateOrder: ['date', 'month', 'year'],
	shortDate: '%d/%m/%Y',
	shortTime: '%H:%M',
	AM: 'AM',
	PM: 'PM',
	firstDayOfWeek: 1,

	// Date.Extras
	ordinal: '',

	lessThanMinuteAgo: 'hace menos de un minuto',
	minuteAgo: 'hace un minuto',
	minutesAgo: 'hace {delta} minutos',
	hourAgo: 'hace una hora',
	hoursAgo: 'hace unas {delta} horas',
	dayAgo: 'hace un día',
	daysAgo: 'hace {delta} días',
	weekAgo: 'hace una semana',
	weeksAgo: 'hace unas {delta} semanas',
	monthAgo: 'hace un mes',
	monthsAgo: 'hace {delta} meses',
	yearAgo: 'hace un año',
	yearsAgo: 'hace {delta} años',

	lessThanMinuteUntil: 'menos de un minuto desde ahora',
	minuteUntil: 'un minuto desde ahora',
	minutesUntil: '{delta} minutos desde ahora',
	hourUntil: 'una hora desde ahora',
	hoursUntil: 'unas {delta} horas desde ahora',
	dayUntil: 'un día desde ahora',
	daysUntil: '{delta} días desde ahora',
	weekUntil: 'una semana desde ahora',
	weeksUntil: 'unas {delta} semanas desde ahora',
	monthUntil: 'un mes desde ahora',
	monthsUntil: '{delta} meses desde ahora',
	yearUntil: 'un año desde ahora',
	yearsUntil: '{delta} años desde ahora'

});

/*
 ---

 name: Locale.es-ES.Form.Validator

 description: Form Validator messages for Spanish.

 license: MIT-style license

 authors:
 - Ãlfons Sanchez

 requires:
 - /Locale

 provides: [Locale.es-ES.Form.Validator]

 ...
 */

Locale.define('es', 'FormValidator', {

	required: 'Este campo es obligatorio.',
	minLength: 'Por favor introduce al menos {minLength} caracteres (has introducido {length} caracteres).',
	maxLength: 'Por favor introduce no m&aacute;s de {maxLength} caracteres (has introducido {length} caracteres).',
	integer: 'Por favor introduce un n&uacute;mero entero en este campo. N&uacute;meros con decimales (p.e. 1,25) no se permiten.',
	numeric: 'Por favor introduce solo valores num&eacute;ricos en este campo (p.e. "1" o "1,1" o "-1" o "-1,1").',
	digits: 'Por favor usa solo n&uacute;meros y puntuaci&oacute;n en este campo (por ejemplo, un n&uacute;mero de tel&eacute;fono con guiones y puntos no esta permitido).',
	alpha: 'Por favor usa letras solo (a-z) en este campo. No se admiten espacios ni otros caracteres.',
	alphanum: 'Por favor, usa solo letras (a-z) o n&uacute;meros (0-9) en este campo. No se admiten espacios ni otros caracteres.',
	dateSuchAs: 'Por favor introduce una fecha v&aacute;lida como {date}',
	dateInFormatMDY: 'Por favor introduce una fecha v&aacute;lida como DD/MM/YYYY (p.e. "31/12/1999")',
	email: 'Por favor, introduce una direcci&oacute;n de email v&aacute;lida. Por ejemplo, "fred@domain.com".',
	url: 'Por favor introduce una URL v&aacute;lida como http://www.example.com.',
	currencyDollar: 'Por favor introduce una cantidad v&aacute;lida de €. Por ejemplo €100,00 .',
	oneRequired: 'Por favor introduce algo para por lo menos una de estas entradas.',
	errorPrefix: 'Error: ',
	warningPrefix: 'Aviso: ',

	// Form.Validator.Extras
	noSpace: 'No pueden haber espacios en esta entrada.',
	reqChkByNode: 'No hay elementos seleccionados.',
	requiredChk: 'Este campo es obligatorio.',
	reqChkByName: 'Por favor selecciona una {label}.',
	match: 'Este campo necesita coincidir con el campo {matchName}',
	startDate: 'la fecha de inicio',
	endDate: 'la fecha de fin',
	currendDate: 'la fecha actual',
	afterDate: 'La fecha debe ser igual o posterior a {label}.',
	beforeDate: 'La fecha debe ser igual o anterior a {label}.',
	startMonth: 'Por favor selecciona un mes de origen',
	sameMonth: 'Estas dos fechas deben estar en el mismo mes - debes cambiar una u otra.'

});
