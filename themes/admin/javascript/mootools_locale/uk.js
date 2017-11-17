/*
 ---

 name: Locale.uk-UA.Date

 description: Date messages for Ukrainian (utf-8).

 license: MIT-style license

 authors:
 - Slik

 requires:
 - /Locale

 provides: [Locale.uk-UA.Date]

 ...
 */

(function(){

	var pluralize = function(n, one, few, many, other){
		var d = (n / 10).toInt(),
			z = n % 10,
			s = (n / 100).toInt();

		if (d == 1 && n > 10) return many;
		if (z == 1) return one;
		if (z > 0 && z < 5) return few;
		return many;
	};

	Locale.define('uk', 'Date', {

		months: ['Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень'],
		months_abbr: ['Січ', 'Лют', 'Бер', 'Квіт', 'Трав', 'Черв', 'Лип', 'Серп', 'Вер', 'Жовт', 'Лист', 'Груд' ],
		days: ['Неділя', 'Понеділок', 'Вівторок', 'Середа', 'Четвер', "П'ятниця", 'Субота'],
		days_abbr: ['Нд', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],

		// Culture's date order: DD/MM/YYYY
		dateOrder: ['date', 'month', 'year'],
		shortDate: '%d/%m/%Y',
		shortTime: '%H:%M',
		AM: 'до полудня',
		PM: 'по полудню',
		firstDayOfWeek: 1,

		// Date.Extras
		ordinal: '',

		lessThanMinuteAgo: 'меньше хвилини тому',
		minuteAgo: 'хвилину тому',
		minutesAgo: function(delta){ return '{delta} ' + pluralize(delta, 'хвилину', 'хвилини', 'хвилин') + ' тому'; },
		hourAgo: 'годину тому',
		hoursAgo: function(delta){ return '{delta} ' + pluralize(delta, 'годину', 'години', 'годин') + ' тому'; },
		dayAgo: 'вчора',
		daysAgo: function(delta){ return '{delta} ' + pluralize(delta, 'день', 'дня', 'днів') + ' тому'; },
		weekAgo: 'тиждень тому',
		weeksAgo: function(delta){ return '{delta} ' + pluralize(delta, 'тиждень', 'тижні', 'тижнів') + ' тому'; },
		monthAgo: 'місяць тому',
		monthsAgo: function(delta){ return '{delta} ' + pluralize(delta, 'місяць', 'місяці', 'місяців') + ' тому'; },
		yearAgo: 'рік тому',
		yearsAgo: function(delta){ return '{delta} ' + pluralize(delta, 'рік', 'роки', 'років') + ' тому'; },

		lessThanMinuteUntil: 'за мить',
		minuteUntil: 'через хвилину',
		minutesUntil: function(delta){ return 'через {delta} ' + pluralize(delta, 'хвилину', 'хвилини', 'хвилин'); },
		hourUntil: 'через годину',
		hoursUntil: function(delta){ return 'через {delta} ' + pluralize(delta, 'годину', 'години', 'годин'); },
		dayUntil: 'завтра',
		daysUntil: function(delta){ return 'через {delta} ' + pluralize(delta, 'день', 'дня', 'днів'); },
		weekUntil: 'через тиждень',
		weeksUntil: function(delta){ return 'через {delta} ' + pluralize(delta, 'тиждень', 'тижні', 'тижнів'); },
		monthUntil: 'через місяць',
		monthesUntil: function(delta){ return 'через {delta} ' + pluralize(delta, 'місяць', 'місяці', 'місяців'); },
		yearUntil: 'через рік',
		yearsUntil: function(delta){ return 'через {delta} ' + pluralize(delta, 'рік', 'роки', 'років'); }

	});

})();


/*
 ---

 name: Locale.uk-UA.Form.Validator

 description: Form Validator messages for Ukrainian (utf-8).

 license: MIT-style license

 authors:
 - Slik

 requires:
 - /Locale

 provides: [Locale.uk-UA.Form.Validator]

 ...
 */

Locale.define('uk', 'FormValidator', {

	required: 'Це поле повинне бути заповненим.',
	minLength: 'Введіть хоча б {minLength} символів (Ви ввели {length}).',
	maxLength: 'Кількість символів не може бути більше {maxLength} (Ви ввели {length}).',
	integer: 'Введіть в це поле число. Дробові числа (наприклад 1.25) не дозволені.',
	numeric: 'Введіть в це поле число (наприклад "1" або "1.1", або "-1", або "-1.1").',
	digits: 'В цьому полі ви можете використовувати лише цифри і знаки пунктіації (наприклад, телефонний номер з знаками дефізу або з крапками).',
	alpha: 'В цьому полі можна використовувати лише латинські літери (a-z). Пробіли і інші символи заборонені.',
	alphanum: 'В цьому полі можна використовувати лише латинські літери (a-z) і цифри (0-9). Пробіли і інші символи заборонені.',
	dateSuchAs: 'Введіть коректну дату {date}.',
	dateInFormatMDY: 'Введіть дату в форматі ММ/ДД/РРРР (наприклад "12/31/2009").',
	email: 'Введіть коректну адресу електронної пошти (наприклад "name@domain.com").',
	url: 'Введіть коректне інтернет-посилання (наприклад http://www.example.com).',
	currencyDollar: 'Введіть суму в доларах (наприклад "$100.00").',
	oneRequired: 'Заповніть одне з полів.',
	errorPrefix: 'Помилка: ',
	warningPrefix: 'Увага: ',

	noSpace: 'Пробіли заборонені.',
	reqChkByNode: 'Не відмічено жодного варіанту.',
	requiredChk: 'Це поле повинне бути віміченим.',
	reqChkByName: 'Будь ласка, відмітьте {label}.',
	match: 'Це поле повинно відповідати {matchName}',
	startDate: 'початкова дата',
	endDate: 'кінцева дата',
	currendDate: 'сьогоднішня дата',
	afterDate: 'Ця дата повинна бути такою ж, або пізнішою за {label}.',
	beforeDate: 'Ця дата повинна бути такою ж, або ранішою за {label}.',
	startMonth: 'Будь ласка, виберіть початковий місяць',
	sameMonth: 'Ці дати повинні відноситись одного і того ж місяця. Будь ласка, змініть одну з них.',
	creditcard: 'Номер кредитної карти введений неправильно. Будь ласка, перевірте його. Введено {length} символів.'

});
