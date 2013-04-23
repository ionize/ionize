<?php
/**
 * Google Dashboard Report
 * Receives :
 * - $pageViews
 * - $uniquePageViews
 * - $avgTimeOnPage
 * - $exitRate
 */

?>

<style type="text/css">
	#page-analtyics {
		clear: left;
	}
	#page-analtyics .metric {
		float: left;
		font-size: 12px;
		margin: 8px 0 5px 0;
		width: 20%;
	}
	#page-analtyics .metric div{
		background: #fff;
		margin-right: 1px;
		padding: 10px;
		border-radius: 4px;
	}
	#page-analtyics .metric:first-child div{
		/*
		border-left: 1px solid #ccc;
		*/
	}
	#page-analtyics .metric span {
		font-size:0.9em;
		line-height: 1.2em;
		color:#999;
		display: block;
		padding-bottom: 3px;
		text-align: center;
	}
	#page-analtyics .metric strong {
		font-size: 16px;
		font-weight: bold;
		display: block;
		text-align: center;
	}
</style>

<div id="analyticsChart"></div>

<?php if ( ! empty($data)) :?>
<div id="page-analtyics">
	<div class="metric"><div><span><?php echo lang('ionize_ga_pageviews') ?></span><strong><?php echo $data['pageViews'] ?></strong></div></div>
	<div class="metric"><div><span><?php echo lang('ionize_ga_unique_pageviews') ?></span><strong><?php echo $data['uniquePageViews'] ?></strong></div></div>
	<div class="metric"><div><span><?php echo lang('ionize_ga_avg_time_on_page') ?></span><strong><?php echo $data['avgTimeOnPage'] ?></strong></div></div>
	<div class="metric"><div><span><?php echo lang('ionize_ga_bounce_rate') ?></span><strong><?php echo $data['bounceRate'] ?></strong></div></div>
	<div class="metric"><div><span><?php echo lang('ionize_ga_exit_rate') ?></span><strong><?php echo $data['exitRate'] ?></strong></div></div>
</div>
<?php endif ;?>


<script type="text/javascript">

	function drawChart()
	{
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Day');
		data.addColumn('number', 'Pageviews');
		data.addRows(<?php echo $chartRows ?>);

		var chart = new google.visualization.AreaChart(document.getElementById('analyticsChart'));

		chart.draw(data, {
			backgroundColor:'transparent',
			vAxis: {gridlines:{color: '#ddd'}},
			height: 180,
			title: '<?php echo date('M j, Y',strtotime('-30 day')).' - '.date('M j, Y'); ?>',
			colors:['#058dc7','#e6f4fa'],
			areaOpacity: 0.1,
			hAxis: {textPosition: 'in', showTextEvery: 5, slantedText: false, textStyle: { color: '#058dc7', fontSize: 10 } },
			pointSize: 5,
			legend: 'none',
			chartArea:{left:0,top:30,width:"100%",height:"100%"}
		});
	}


	function loadGoogle()
	{
		if(typeof google != 'undefined' && google && google.load)
			drawChart();
		else
			setTimeout(loadGoogle, 100);
	}

	loadGoogle();

	window.addEvent('resize', function()
	{
		if ($('analyticsChart'))
		{
			drawChart();
			console.log('resizing....');
		}
		// google.load("visualization", "1", {packages:["corechart"], "callback" : drawChart});
	//	google.load("visualization", "1", {packages:["corechart"]});
	//	google.setOnLoadCallback(drawChart);

	});



</script>