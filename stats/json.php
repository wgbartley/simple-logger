<?
if(!isset($_REQUEST['u']))
	exit;

$_REQUEST['u'] = intval($_REQUEST['u']);

if(!file_exists('../logs/uptime'.$_REQUEST['u'].'.txt'))
	exit;

require 'functions.php';

header('Content-type: application/json');

$last_ts = 0;
$values = array();
$raw_values = array();

if(($fh = fopen('../logs/uptime'.$_REQUEST['u'].'.txt', 'r')) !== FALSE) {
	while(($data = fgetcsv($fh)) !== FALSE) {
		$ts = strtotime($data[0]);

		if($ts-$last_ts!=$ts) {
			$values[] = array($data[0], $ts-$last_ts);
			$raw_values[] = $ts-$last_ts;
		}

		$last_ts = $ts;
	}

	fclose($fh);
}

$stats = stats($raw_values);

$min = $stats['CleanedAvg']-($stats['CleanedStD']*2);
$max = $stats['CleanedAvg']+($stats['CleanedStD']*2);

$output = array(
	'stats' => stats($raw_values),
	'data' => array()
);

foreach($values as $v) {
	if($v[1]>=$min && $v[1]<=$max)
		$output['data'][] = array($v[0], $v[1]);
}

echo json_encode($output);
?>
