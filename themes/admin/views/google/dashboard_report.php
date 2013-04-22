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
		background: #fefefe; /* Old browsers */
		background: -moz-linear-gradient(top, #fefefe 0%, #f2f3f2 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fefefe), color-stop(100%,#f2f3f2)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top, #fefefe 0%,#f2f3f2 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top, #fefefe 0%,#f2f3f2 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top, #fefefe 0%,#f2f3f2 100%); /* IE10+ */
		background: linear-gradient(top, #fefefe 0%,#f2f3f2 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fefefe', endColorstr='#f2f3f2',GradientType=0 ); /* IE6-9 */
		border: 1px solid #ccc;
		float: left;
		font-size: 12px;
		margin: -4px 0 1em -1px;
		padding: 10px;
		width: 105px;
	}
	#page-analtyics .metric:hover {
		background: #fff;
		border-bottom-color: #b1b1b1;
	}
	#page-analtyics .metric .legend {
		background-color: #058DC7;
		border-radius: 5px;
		-moz-border-radius: 5px;
		-webkit-border-radius: 5px;
		font-size: 0;
		margin-right: 5px;
		padding: 10px 5px 0;
	}
	#page-analtyics .metric strong {
		font-size: 16px;
		font-weight: bold;
	}
	#page-analtyics .range {
		color: #686868;
		font-size: 11px;
		margin-bottom: 7px;
		width: 100%;
	}
</style>

<div id="analytics-chart"></div>

<?php if ( ! empty($data)) :?>
<div id="page-analtyics">
	<div class="metric"><span>Pageviews</span><br /><strong><?php echo $data['pageViews'] ?></strong></div>
	<div class="metric"><span>Unique pageviews</span><br /><strong><?php echo $data['uniquePageViews'] ?></strong></div>
	<div class="metric"><span>Avg time on page</span><br /><strong><?php echo $data['avgTimeOnPage'] ?></strong></div>
	<div class="metric"><span>Bounce rate</span><br /><strong><?php echo $data['bounceRate'] ?></strong></div>
	<div class="metric"><span>Exit rate</span><br /><strong><?php echo $data['exitRate'] ?></strong></div>
	<div style="clear: left;"></div>
</div>
<?php endif ;?>


<script type="text/javascript">


	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(drawChart);
	function drawChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Day');
		data.addColumn('number', 'Pageviews');
		data.addRows([
			<?php echo implode(',', $chartRows); ?>
		]);
		var chart = new google.visualization.AreaChart(document.getElementById('chart'));
		chart.draw(data, {width: 630, height: 180, title: '<?php echo date('M j, Y',strtotime('-30 day')).' - '.date('M j, Y'); ?>',
			colors:['#058dc7','#e6f4fa'],
			areaOpacity: 0.1,
			hAxis: {textPosition: 'in', showTextEvery: 5, slantedText: false, textStyle: { color: '#058dc7', fontSize: 10 } },
			pointSize: 5,
			legend: 'none',
			chartArea:{left:0,top:30,width:"100%",height:"100%"}
		});
	} // End of drawChart()
</script>