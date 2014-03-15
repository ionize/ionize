
/*
 ---

 name: Locale.ja-JP.Date

 description: Date messages for Japanese.

 license: MIT-style license

 authors:
 - Noritaka Horio

 requires:
 - /Locale

 provides: [Locale.ja-JP.Date]

 ...
 */

Locale.define('ja', 'Date', {

	months: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
	months_abbr: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
	days: ['日曜日', '月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日'],
	days_abbr: ['日', '月', '火', '水', '木', '金', '土'],

	// Culture's date order: YYYY/MM/DD
	dateOrder: ['year', 'month', 'date'],
	shortDate: '%Y/%m/%d',
	shortTime: '%H:%M',
	AM: '午前',
	PM: '午後',
	firstDayOfWeek: 0,

	// Date.Extras
	ordinal: '',

	lessThanMinuteAgo: '1分以内前',
	minuteAgo: '約1分前',
	minutesAgo: '約{delta}分前',
	hourAgo: '約1時間前',
	hoursAgo: '約{delta}時間前',
	dayAgo: '1日前',
	daysAgo: '{delta}日前',
	weekAgo: '1週間前',
	weeksAgo: '{delta}週間前',
	monthAgo: '1ヶ月前',
	monthsAgo: '{delta}ヶ月前',
	yearAgo: '1年前',
	yearsAgo: '{delta}年前',

	lessThanMinuteUntil: '今から約1分以内',
	minuteUntil: '今から約1分',
	minutesUntil: '今から約{delta}分',
	hourUntil: '今から約1時間',
	hoursUntil: '今から約{delta}時間',
	dayUntil: '今から1日間',
	daysUntil: '今から{delta}日間',
	weekUntil: '今から1週間',
	weeksUntil: '今から{delta}週間',
	monthUntil: '今から1ヶ月',
	monthsUntil: '今から{delta}ヶ月',
	yearUntil: '今から1年',
	yearsUntil: '今から{delta}年'

});


/*
 ---

 name: Locale.ja-JP.Form.Validator

 description: Form Validator messages for Japanese.

 license: MIT-style license

 authors:
 - Noritaka Horio

 requires:
 - /Locale

 provides: [Locale.ja-JP.Form.Validator]

 ...
 */

Locale.define("ja", "FormValidator", {

	required: '入力は必須です。',
	minLength: '入力文字数は{minLength}以上にしてください。({length}文字)',
	maxLength: '入力文字数は{maxLength}以下にしてください。({length}文字)',
	integer: '整数を入力してください。',
	numeric: '入力できるのは数値だけです。(例: "1", "1.1", "-1", "-1.1"....)',
	digits: '入力できるのは数値と句読記号です。 (例: -や+を含む電話番号など).',
	alpha: '入力できるのは半角英字だけです。それ以外の文字は入力できません。',
	alphanum: '入力できるのは半角英数字だけです。それ以外の文字は入力できません。',
	dateSuchAs: '有効な日付を入力してください。{date}',
	dateInFormatMDY: '日付の書式に誤りがあります。YYYY/MM/DD (i.e. "1999/12/31")',
	email: 'メールアドレスに誤りがあります。',
	url: 'URLアドレスに誤りがあります。',
	currencyDollar: '金額に誤りがあります。',
	oneRequired: 'ひとつ以上入力してください。',
	errorPrefix: 'エラー: ',
	warningPrefix: '警告: ',

	// FormValidator.Extras
	noSpace: 'スペースは入力できません。',
	reqChkByNode: '選択されていません。',
	requiredChk: 'この項目は必須です。',
	reqChkByName: '{label}を選択してください。',
	match: '{matchName}が入力されている場合必須です。',
	startDate: '開始日',
	endDate: '終了日',
	currendDate: '今日',
	afterDate: '{label}以降の日付にしてください。',
	beforeDate: '{label}以前の日付にしてください。',
	startMonth: '開始月を選択してください。',
	sameMonth: '日付が同一です。どちらかを変更してください。'

});


/*
 ---

 name: Locale.ja-JP.Number

 description: Number messages for Japanese.

 license: MIT-style license

 authors:
 - Noritaka Horio

 requires:
 - /Locale

 provides: [Locale.ja-JP.Number]

 ...
 */

Locale.define('ja', 'Number', {

	decimal: '.',
	group: ',',

	currency: {
		decimals: 0,
		prefix: '\\'
	}

});
