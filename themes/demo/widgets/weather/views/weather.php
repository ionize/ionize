
<link rel="stylesheet" href="<?= $widget_path ?>css/weather.css" />

<h2><?=lang('widget_weather_at') ?> <?= $city ?></h2>

<div id="yw-forecast">
	<div class="forecast-temp" style="background:transparent url(<?= $widget_path ?>images/wdgt_day.png) no-repeat scroll 0 0;">
		<h3><?= $temp ?>°</h3>
		<p>Min: <?= $low ?>° Max: <?= $high ?>°</p>
	</div>
	<div class="forecast-icon" style="background: transparent url(<?= $widget_path ?>images/<?= $code ?>.png) repeat scroll 0% 0%;" ></div>	
</div>
