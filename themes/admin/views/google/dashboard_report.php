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

<div id="analyticsChart"></div>

<?php if ( ! empty($data)) :?>
<div id="page-analytics">
	<div class="metric"><div><strong><?php echo $data['visitors'] ?></strong><span><?php echo lang('ionize_ga_visitors') ?></span></div></div>
<!--
	<div class="metric"><div><strong><?php /*echo $data['pageViews'] */?></strong><span><?php /*echo lang('ionize_ga_pageviews') */?></span></div></div>
	<div class="metric"><div><strong><?php /*echo $data['avgTimeOnPage'] */?></strong><span><?php /*echo lang('ionize_ga_avg_time_on_page') */?></span></div></div>
	<div class="metric"><div><strong><?php /*echo $data['exitRate'] */?></strong><span><?php /*echo lang('ionize_ga_exit_rate') */?></span></div></div>
-->
	<div class="metric"><div><strong><?php echo $data['visits'] ?></strong><span><?php echo lang('ionize_ga_visits') ?></span></div></div>
	<div class="metric"><div><strong><?php echo $data['newVisits'] ?></strong><span><?php echo lang('ionize_ga_newvisits') ?></span></div></div>
	<div class="metric"><div><strong><?php echo $data['uniquePageViews'] ?></strong><span><?php echo lang('ionize_ga_unique_pageviews') ?></span></div></div>
	<div class="metric"><div><strong><?php echo $data['bounceRate'] ?></strong><span><?php echo lang('ionize_ga_bounce_rate') ?></span></div></div>
</div>
<?php endif ;?>


<script type="text/javascript">



	function drawChart()
	{
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Day');
		data.addColumn('number', Lang.get('ionize_ga_visits'));
		data.addColumn('number', Lang.get('ionize_ga_newvisits'));
		data.addRows(<?php echo $dataRows ?>);

		// var chart = new google.visualization.AreaChart(document.getElementById('analyticsChart'));
		var chart = new google.visualization.LineChart(document.getElementById('analyticsChart'));

		chart.draw(data, {
//			curveType: 'function',
			vAxis: {
				gridlines:{color: '#ddd'},
				textPosition: 'out',
				textStyle: { color: '#999', fontSize: 10 },
				baselineColor:'#ddd'
			},
			height: 230,
			title: '<?php echo date('M j, Y',strtotime('-30 day')).' - '.date('M j, Y'); ?>',
			colors:['#058dc7','#bf2626','#e2d9d9'],
			areaOpacity: 0.1,
			hAxis: {
				textPosition: 'out',
				showTextEvery: 5,
				// direction:1, slantedText:true, slantedTextAngle:45,
				textStyle: { color: '#999', fontSize: 10 }
			},
			pointSize: 5,
			legend: 'none',
			chartArea:{left:40,top:30,bottom:20,width:"100%",height:180}
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
		}
		// google.load("visualization", "1", {packages:["corechart"], "callback" : drawChart});
	//	google.load("visualization", "1", {packages:["corechart"]});
	//	google.setOnLoadCallback(drawChart);

	});



</script>