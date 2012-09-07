<html>
<head>
	<title>FTL tests</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<style>
		html{
			font-size: 0.9em;
		}
		pre {
			background: #f3f3f3;
			font-size: 0.9em;
			padding:10px;
			tab-size:4;
			-moz-tab-size: 4;
			-o-tab-size:   4;
		}
		pre * {
			line-height: 0.9em;
			margin:0; padding:0;
		}

		h2 {
			border-bottom: solid 1px #eee;
		}
		.red {
			color:#b00;
		}
		li a.active:after{
			content:" (is active)";
			color: #b00;
		}
		ul.boxes {
			list-style: none;
			margin: 0;padding: 0;
			overflow: hidden;
		}
		ul.boxes li {
			float: left;
			margin: 0 10px 10px 0;
			padding:5px;
			background-color: #f3f3f3;
		}
		ul.boxes li.first {
			float:none;
		}
		ul.boxes li.last {
			border-right: 5px solid #b00;
		}
	</style>
</head>

<body>

