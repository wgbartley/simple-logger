<?
require 'functions.php';

$logs = '../logs/*.txt';
$uptime = array();

foreach(glob($logs) as $log) {
	$lines = explode("\n", file_get_contents(trim($log)));

	$diff = 0;
	$count = 0;
	$values = array();

	$name = basename($log, '.txt');

	switch(strtoupper($name)) {
		case 'UPTIME5':
			$target_interval = 120;
			break;

		case 'UPTIME6':
			$target_interval = 300;
			break;

		case 'UPTIME7':
			$target_interval = 600;
			break;

		case 'UPTIME8':
			$target_interval = 900;
			break;

		case 'UPTIME9':
			$target_interval = 1800;
			break;

		default:
			$target_interval = 60;
	}

	if(isset($last_line))
		unset($last_line);

	for($i=0; $i<count($lines); $i++) {
		$line = trim($lines[$i]);
		$line = str_getcsv($line);
		$line = strtotime($line[0]);

		if(strlen($line)==0)
			continue;

		$count++;

		if(isset($last_line)) {
			$diff += $line-$last_line;
			$values[] = $line-$last_line;
		}

		$last_line = $line;

		if($i==0) {
			$start = $line;
			continue;
		}


		$end = $line;
	}

	$uptime[] = array(
		'name' => $name,
		'start' => $start,
		'end' => $end,
		'diff' => $diff,
		'count' => $count,
		'interval' => round($diff/($count-1)),
		'target_interval' => $target_interval,
		'uptime' => $end-$start,
		'uptime_str' => secs_to_h($end-$start),
		'std' => stats($values)
	);

	if(time()-intval($diff/$count)*1.1<=$end)
		$uptime[count($uptime)-1]['uptime_str'] .= ' +';
}


// Borrowed from http://csl.name/php-secs-to-human-text/
function secs_to_h($secs) {
        $units = array(
                "week"   => 7*24*3600,
                "day"    =>   24*3600,
                "hour"   =>      3600,
                "minute" =>        60,
                "second" =>         1,
        );

	// specifically handle zero
        if ( $secs == 0 ) return "0 seconds";

        $s = "";

        foreach ( $units as $name => $divisor ) {
                if ( $quot = intval($secs / $divisor) ) {
                        $s .= "$quot $name";
                        $s .= (abs($quot) > 1 ? "s" : "") . ", ";
                        $secs -= $quot * $divisor;
                }
        }

        return substr($s, 0, -2);
}
?>
<!DOCTYPE html>
<html>
<head>

<title>Spark Core 5V 600mAh Battery Statistics</title>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="http://code.highcharts.com/highcharts.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	function make_graph(n, sub) {
		$.get('json.php?u='+n, function(resp) {
			var data_avg = [];
			var data_std_1 = [];
			var data_std1 = [];

			for(var i=0; i<resp.data.length; i++) {
				data_avg.push([resp.data[i][0], resp.stats.CleanedAvg]);
				data_std_1.push([resp.data[i][0], (resp.stats.CleanedAvg-resp.stats.CleanedStD)]);
				data_std1.push([resp.data[i][0], (resp.stats.CleanedAvg+resp.stats.CleanedStD)]);
			}

			$('#uptime'+n).highcharts({
				title: {
					text: 'Test '+n
				},
				subtitle: {
					text: sub
				},
				legend: {
					enabled: false
				},
				yAxis: {
					title: {
						text: 'Interval (s)'
					}
				},
				series: [{
					name: 'Average',
					data: data_avg,
					color: 'green',
					marker: { enabled: false },
					enableMouseTracking: false
				}, {
					name: 'Standard Deviation',
					data: data_std_1,
					color: 'red',
					marker: { enabled: false },
					enableMouseTracking: false
				}, {
					name: 'Standard Deviation',
					data: data_std1,
					color: 'red',
					marker: { enabled: false },
					enableMouseTracking: false
				}, {
					name: 'Interval',
					data: resp.data,
					marker: { enabled: false }
				}]
			});
		});
	}

	make_graph(1, 'No sleep. 60-second update interval.');
	make_graph(2, 'No sleep. RGB LED disabled. 60-second update interval.');
	make_graph(3, 'Deep sleep. 60-second update interval.');
	make_graph(4, 'Deep sleep. 60-second update interval. Chip antenna.');
	make_graph(5, 'Deep sleep. 120-second (2 minutes) update interval.');
	make_graph(6, 'Deep sleep. 300-second (5 minutes) update interval.');
	make_graph(7, 'Deep sleep. 600-second (10 minutes) update interval.');
	make_graph(8, 'Deep sleep. 900-second (15 minutes) update interval.');
	make_graph(9, 'Deep sleep. 1800-second (30 minutes) update interval.');
});
</script>

<style type="text/css">
table {
	font-family: sans-serif;
	text-align: left;
	border-collapse: collapse;
	border: 1px solid #000000;
	width: 100%;
}

table thead tr th {
	background-color: #000000;
	color: #ffffff;
	border-bottom: 1px solid #000000;
}

table td, table th {
	/* min-width: 10em; */
	margin-right: 2em;
	padding: 2px;
}

table tbody tr:nth-child(even) td {
	background-color: #cccccc;
}

table tbody tr:hover td {
	background-color: lightblue;
}


#uptime1,
#uptime2,
#uptime3,
#uptime4,
#uptime5,
#uptime6,
#uptime7,
#uptime8,
#uptime9 {
	float: left;
	width: 33%;
	height: 33%;
}
</style>
</head>
<body>

<table>
	<thead>
		<tr>
			<th>Name</th>
			<th>Start Time</th>
			<th>End Time</th>
			<th>Target Interval</th>
			<th>Cleaned Avg</th>
			<th>Cleaned Standard Dev</th>
			<th>Original Avg</th>
			<th>Original Standard Dev</th>
			<th>Total Updates</th>
			<th>Total Approx Uptime</th>
		</tr>
	</thead>
	<tbody>
<? foreach($uptime as $u) { ?>
		<tr>
			<td><?= ucfirst($u['name']); ?></td>
			<td><?= date('Y-m-d H:i:s', $u['start']); ?></td>
			<td><?= date('Y-m-d H:i:s', $u['end']); ?></td>
			<td><?= $u['target_interval']; ?> s</td>
			<td><?= sprintf("%01.2f", round($u['std']['CleanedAvg'], 2)); ?> s</td>
			<td><?= sprintf("%01.2f", round($u['std']['CleanedStD'], 2)); ?></td>
			<td><?= sprintf("%01.2f", round($u['std']['OriginalAvg'], 2)); ?>  s</td>
			<td><?= sprintf("%01.2f", round($u['std']['OriginalStD'], 2)); ?></td>
			<td><?= $u['count']; ?></td>
			<td><?= $u['uptime_str']; ?></td>
		</tr>
<? } ?>
	</tbody>
</table>

<div id="uptime1"></div>
<div id="uptime2"></div>
<div id="uptime3"></div>
<div id="uptime4"></div>
<div id="uptime5"></div>
<div id="uptime6"></div>
<div id="uptime7"></div>
<div id="uptime8"></div>
<div id="uptime9"></div>

</body>
</html>
